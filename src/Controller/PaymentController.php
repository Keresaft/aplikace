<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\Payment;
use App\Form\PaymentType;
use App\Repository\InvoiceRepository;
use App\Repository\PaymentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class PaymentController extends AbstractController
{
    public function __construct(private readonly PaymentRepository $paymentRepository, private readonly InvoiceRepository $invoiceRepository)
    {
    }

    private function canBePayment(Invoice $invoice, Payment $paymentAmount)
    {
        $amount = 0;
        foreach ($invoice->getPayments() as $payment) {
            $amount += $payment->getPaymentAmount();
        }
        $amountTmp = $invoice->getPrice() - $amount;
        if ($paymentAmount->getPaymentAmount() > $amountTmp) {
            return false;
        } elseif ($paymentAmount->getPaymentAmount() == $amountTmp) {
            $invoice->setPaymentStatus(true);
            return true;
        } elseif($paymentAmount->getPaymentAmount() < $amountTmp){
            $invoice->setPaymentStatus(false);
            return true;
        } else {
            return true;
        }
    }


    private function paymentStatus (Invoice $invoice, Payment $payment):Invoice{
        $amount = 0;
        foreach ($invoice->getPayments() as $payment) {
            $amount += $payment->getPaymentAmount();
        }
        $amount -= $payment->getPaymentAmount();
        if ($invoice ->getPrice() > $amount){
            $invoice->setPaymentStatus(false);

        }
        return $invoice;
    }
    #[Route('/payment/new/{id}', name: 'payment_new', methods: ['POST'])]
    public function newPayment(Invoice $invoice, Request $request)
    {
        if($invoice->getCustomerID()->getUser() !== $this->getUser()){
            throw $this->createAccessDeniedException();
        }
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $paymentTmp = new Payment();
            $paymentTmp = $form->getData();
            if ($this->canBePayment($invoice, $paymentTmp)) {
                /** @var Payment $payment */
                $payment = $form->getData();
                $payment->setInvoice($invoice);
                $this->paymentRepository->save($payment, true);
                return $this->redirectToRoute('invoice_list', ['id' => $invoice->getCustomerID()->getId()]);
            }
            return $this->render('payment/paymentForm.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        return $this->render('payment/paymentForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/payment/list/{id}', name: 'payment_list')]
    public function listById(Invoice $invoice): Response
    {
        if($invoice->getCustomerID()->getUser() !== $this->getUser()){
            throw $this->createAccessDeniedException();
        }
        /** @var Payment $payments */
        $payments = $invoice->getPayments();
        return $this->render('payment/paymentList.html.twig', [
            'payments' => $payments,
            'invoice' => $invoice,
        ]);
    }

    #[Route('payment/delete/{id}', name: 'payment_delete', methods: ['POST'])]
    public function deletePayment(Payment $payment): Response
    {
        $invoice = $payment->getInvoice();
        $invoice = $this->paymentStatus($invoice, $payment);
        $this -> paymentRepository ->remove($payment, true);
        $this -> invoiceRepository ->save($invoice, true);
        return $this->redirectToRoute('payment_list', ['id' => $invoice->getId()]);
    }

    #[Route('/payment/edit/{id}', name: 'payment_edit', methods: ['POST'])]
    public function editPayment(Payment $payment, Request $request)
    {
        if($payment->getInvoice()->getCustomerID()->getUser() !== $this->getUser()){
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);
        $invoice = $payment -> getInvoice();
        if ($form->isSubmitted() && $form->isValid()) {
            $paymentTmp = new Payment();
            $paymentTmp = $form->getData();
            if ($this->canBePayment($invoice, $paymentTmp)) {
                /** @var Payment $payment */
                $payment = $form->getData();
                $payment->setInvoice($invoice);
                $this->paymentRepository->save($payment, true);
                return $this->redirectToRoute('invoice_list', ['id' => $invoice->getCustomerID()->getId()]);
            }
            return $this->render('payment/paymentForm.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        return $this->render('payment/paymentForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}