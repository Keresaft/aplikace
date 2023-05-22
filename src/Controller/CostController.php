<?php

namespace App\Controller;

use App\Entity\Cost;
use App\Entity\User;
use App\Form\CostType;
use App\Form\InvoiceType;
use App\Repository\CostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CostController extends AbstractController
{
    public function __construct(private readonly CostRepository $costRepository)
    {
    }

    #[Route('/costs', name: 'costs')]
    public function allCosts(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Cost $costs */
        $costs = $user->getCosts();

        return $this->render('cost/cost.html.twig', [
            'costs' => $costs,
        ]);
    }

    #[Route('/cost/new', name: 'cost_new')]
    public function newCost(Request $request)
    {
        $cost = new Cost();
        $form = $this -> createForm(CostType::class, $cost);
        $form -> handleRequest($request);

        if ($form -> isSubmitted() && $form -> isValid()){
            /** @var Cost $cost */
            $cost = $form->getData();
            $cost->setUser($this->getUser());
            $this -> costRepository ->save($cost, true);
            return $this->redirectToRoute('costs');
        }
        return $this->render('cost/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}