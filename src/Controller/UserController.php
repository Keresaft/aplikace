<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function number(): Response
    {
        $number = random_int(0, 100);
        return $this->render('lucky/number.html.twig', [
            'number' => $number,
        ]);

    }

    #[Route('/lucky/number', name: 'lucky_number')]
    public function numberOfCustormers(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $customerCount = $user->getCustomerCount();
        return $this->render('lucky/number.html.twig', [
            'customerCount' => $customerCount,
        ]);
    }

}
