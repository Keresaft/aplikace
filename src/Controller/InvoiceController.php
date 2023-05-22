<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\User;
use App\Form\CustomerType;
use App\Form\InvoiceType;
use App\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class InvoiceController extends AbstractController
{
    public function __construct(private readonly InvoiceRepository $invoiceRepository)
    {
    }

    #[Route('/invoice/new/{id}', name: 'invoice_new', methods: ['POST'])]
    public function new(Request $request, Customer $customer)
    {
        $invoice = new Invoice();
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Invoice $invoice */
            $invoice = $form->getData();
            $invoice->setCustomerID($customer);
            $this->invoiceRepository->save($invoice, true);
            return $this->redirectToRoute('lucky_number');
        }
        return $this->render('invoice/newInvoice.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/invoice/list/{id}', name: 'invoice_list')]
    public function listById(Customer $customer): Response
    {
        /** @var Invoice $invoice */
        $invoices = $customer->getInvoiceID();
        return $this->render('invoice/invoiceForCustomer.html.twig', [
            'invoices' => $invoices,
            'customer' => $customer->getDetails()->getName(),
        ]);
    }
}