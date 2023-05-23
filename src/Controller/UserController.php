<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use App\Form\CustomerType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class UserController extends AbstractController
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    #[Route('/lucky/number', name: 'lucky_number')]
    public function numberOfCustomers(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $customerCount = $user->getCustomerCount();
        $customers = $user ->getCustomers();
        $costsCount = $user ->getCosts();

        return $this->render('lucky/number.html.twig', [
            'customerCount' => $customerCount,
            'email' => $user -> getEmail(),
            'details' => $user ->getDetails(),
            'costsCount' => $costsCount,
            'customers' => $customers,
            'user' => $user,
        ]);
    }

    #[Route('/user/detail', name: 'user_detail')]
    public function detail(Request $request)
    {
        $user = $this -> getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $this-> userRepository->save($user, true);
            return $this->redirectToRoute('lucky_number');
        }
        return $this->render('form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/detail/edit/{id}', name: 'user_detail_edit')]
    public function detailEdit(User $user, Request $request)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $this-> userRepository->save($user, true);
            return $this->redirectToRoute('lucky_number');
        }
        return $this->render('form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
