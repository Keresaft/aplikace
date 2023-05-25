<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\User;
use App\Form\CustomerType;
use App\Form\InvoiceType;
use App\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
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

    private function isDateCorrect($dueDate, $creationDate)
    {
        if ($dueDate > $creationDate) {
            return true;
        } elseif ($dueDate < $creationDate) {
            return false;
        } else {
            return true;
        }
    }

    #[Route('/invoice/new/{id}', name: 'invoice_new', methods: ['POST'])]
    public function new(Request $request, Customer $customer)
    {
        if ($customer->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        if (empty($customer->getUser()->getDetails())) {
            throw $this->createAccessDeniedException('Uživatel nemá nastavené fakturační údaje');
        }
        $invoice = new Invoice();
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Invoice $invoice */
            $invoice = $form->getData();
            if ($this->isDateCorrect($invoice->getDueDate(), $invoice->getCreationDate())) {
                $invoice->setCustomerID($customer);
                $invoice->setPaymentStatus(false);
                $invoice->setCustomerJson($customer->getDetails()->toArray());
                $invoice->setUserJson($customer->getUser()->getDetails()->toArray());
                $this->invoiceRepository->save($invoice, true);
                return $this->redirectToRoute('invoice_list', ['id' => $customer->getId()]);
            } else {
                $form->addError(new FormError('Datum splatnosti musí být větší než datum vytvoření '));
            }
        }
        return $this->render('invoice/newInvoice.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/invoice/list/{id}', name: 'invoice_list')]
    public function listById(Request $request, Customer $customer): Response
    {
        if ($customer->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        $filter = $request->query->get('filter', 'all');
        /** @var Invoice $invoices */
        $invoices = $customer->getInvoiceID();
        /** @var Invoice $filteredInvoices */
        $filteredInvoices;

        if ($filter === 'paid') {
            $filteredInvoices = $invoices->filter(function (Invoice $invoice) {
                return $invoice->isPaymentStatus();
            });
        } elseif ($filter === 'unpaid') {
            $filteredInvoices = $invoices->filter(function (Invoice $invoice) {
                return !$invoice->isPaymentStatus();
            });
        } else {
            $filteredInvoices = $invoices;
        }

        return $this->render('invoice/invoiceForCustomer.html.twig', [
            'invoices' => $filteredInvoices,
            'customer' => $customer,
            'filter' => $filter,
        ]);
    }

    #[Route('/invoice/delete/{id}', name: 'invoice_delete', methods: ['POST'])]
    public function deleteInvoice(Invoice $invoice, Request $request): Response
    {
        $this->invoiceRepository->remove($invoice, true);
        return new RedirectResponse($request->headers->get('referer'));
    }

    #[Route('/invoice/edit/{id}', name: 'invoice_edit', methods: ['POST'])]
    public function edit(Invoice $invoice, Request $request)
    {
        if ($invoice->getCustomerID()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);
        $customer = $invoice->getCustomerID();
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Invoice $invoice */
            $invoice = $form->getData();
            if ($this->isDateCorrect($invoice->getDueDate(), $invoice->getCreationDate())) {

                $invoice->setCustomerID($customer);
                $this->invoiceRepository->save($invoice, true);
                return $this->redirectToRoute('invoice_list', ['id' => $customer->getId()]);
            } else {
                $form->addError(new FormError('Datum splatnosti musí být větší než datum vytvoření '));
            }
        }
        return $this->render('invoice/newInvoice.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/invoice/gen/{id}', name: 'pdf_gen')]
    public function genPdf(Invoice $invoice)
    {
        if ($invoice->getCustomerID()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        if (empty($invoice->getCustomerID()->getUser()->getDetails())) {
            throw $this->createAccessDeniedException('User details are not set.');
        }
        $html = $this->renderView('invoice/pdfGen.html.twig', [
            'invoice' => $invoice,
        ]);
//        $options = new Options();
//        $options->set('isHtml5ParserEnabled', true);
//        $options->set('defaultEncoding', 'UTF-8');
//        $options->setDefaultFont('Verdana');


        $dompdf = new Dompdf();
        $dompdf->loadHtml(utf8_decode($html), 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $response = new Response();
        $response->setContent($dompdf->output());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment;filename="faktura.pdf"');

        return $response;
    }

}