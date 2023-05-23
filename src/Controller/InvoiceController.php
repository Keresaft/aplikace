<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\User;
use App\Form\CustomerType;
use App\Form\InvoiceType;
use App\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Dompdf\Dompdf;
use Dompdf\Options;


#[IsGranted('ROLE_USER')]
class InvoiceController extends AbstractController
{
    public function __construct(private readonly InvoiceRepository $invoiceRepository)
    {
    }

    #[Route('/invoice/new/{id}', name: 'invoice_new', methods: ['POST'])]
    public function new(Request $request, Customer $customer)
    {
        if($customer -> getUser() !== $this->getUser()){
            throw $this->createAccessDeniedException();
        }
        $invoice = new Invoice();
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Invoice $invoice */
            $invoice = $form->getData();
            $invoice->setCustomerID($customer);
            $invoice->setPaymentStatus(false);
            $invoice->setCustomerJson($customer->getDetails()->toArray());
            $invoice->setUserJson($customer->getUser()->getDetails()->toArray());
            $this->invoiceRepository->save($invoice, true);
            return $this->redirectToRoute('invoice_list', ['id' => $customer->getId()]);
        }
        return $this->render('invoice/newInvoice.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/invoice/list/{id}', name: 'invoice_list')]
    public function listById(Customer $customer): Response
    {
        if($customer -> getUser() !== $this->getUser()){
            throw $this->createAccessDeniedException();
        }
        /** @var Invoice $invoices */
        $invoices = $customer->getInvoiceID();
        return $this->render('invoice/invoiceForCustomer.html.twig', [
            'invoices' => $invoices,
            'customer' => $customer,
        ]);
    }

    #[Route('/invoice/delete/{id}', name: 'invoice_delete', methods: ['POST'])]
    public function deleteInvoice(Invoice $invoice, Request $request):Response
    {
        $this -> invoiceRepository -> remove($invoice, true);
        return new RedirectResponse($request->headers->get('referer'));
    }

    #[Route('/invoice/edit/{id}', name: 'invoice_edit', methods: ['POST'])]
    public function edit(Invoice $invoice, Request $request)
    {
        if($invoice->getCustomerID()->getUser() !== $this->getUser()){
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);
        $customer = $invoice -> getCustomerID();
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Invoice $invoice */
            $invoice = $form->getData();
            $invoice->setCustomerID($customer);
            $this->invoiceRepository->save($invoice, true);
            return $this->redirectToRoute('invoice_list', ['id' => $customer->getId()]);
        }
        return $this->render('invoice/newInvoice.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/invoice/gen/{id}', name: 'pdf_gen')]
    public function genPdf(Invoice $invoice):Response
    {
        if($invoice->getCustomerID()->getUser() !== $this->getUser()){
            throw $this->createAccessDeniedException();
        }
        $html = $this->render('invoice/pdfGen.html.twig', [
            'invoice' => $invoice,
        ]);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Arial');
        $options->set('defaultEncoding', 'UTF-8');
        $dompdf = new Dompdf($options);
        $dompdf ->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $response = new Response();
        $response->setContent($dompdf->output());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment;filename="faktura.pdf"');

        return $response;
    }

}