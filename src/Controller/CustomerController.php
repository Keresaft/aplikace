<?php

namespace App\Controller;


use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class CustomerController extends AbstractController
{
    public function __construct(private readonly CustomerRepository $customerRepository)
    {
    }

    #[Route('/customer/new', name: 'customer_new')]
    public function new (Request $request){
        $customer = new Customer();
        $form = $this -> createForm(CustomerType::class, $customer);
        $form -> handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            /** @var Customer $customer */
            $customer = $form -> getData();
            $customer -> setUser($this -> getUser());
            $this -> customerRepository -> save($customer, true);
            return $this->redirectToRoute('lucky_number');
        }
        return $this->render('form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}