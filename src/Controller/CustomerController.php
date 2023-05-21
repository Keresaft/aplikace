<?php

namespace App\Controller;


use App\Entity\Customer;
use App\Entity\User;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class CustomerController extends AbstractController
{
    public function __construct(private readonly CustomerRepository $customerRepository)
    {
    }

    #[Route('/customer/new', name: 'customer_new')]
    public function new(Request $request)
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Customer $customer */
            $customer = $form->getData();
            $customer->setUser($this->getUser());
            $this->customerRepository->save($customer, true);
            return $this->redirectToRoute('lucky_number');
        }
        return $this->render('form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/customer', name: 'customer')]
    public function allCustomers(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Customer $customer */
        $customers = $user->getCustomers();


        return $this->render('customer/customer.html.twig', [
            'customers' => $customers,
        ]);
    }

    #[Route('/{id}/delete', name: 'customer_delete', methods: ['POST'])]
    public function delete(Customer $customer): Response
    {
        $this->customerRepository->remove($customer, true);
        return $this->redirectToRoute('customer');

    }

}