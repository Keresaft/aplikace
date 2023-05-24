<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Cost;
use App\Entity\User;
use App\Form\CategorySelectType;
use App\Form\CostType;
use App\Form\InvoiceType;
use App\Repository\CategoryRepository;
use App\Repository\CostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class CostController extends AbstractController
{
    public function __construct(private readonly CostRepository $costRepository)
    {
    }

    #[Route('/costs', name: 'costs')]
    public function allCosts(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Cost $costs */
        $costs = $user->getCosts();
        $categoryId = $request ->query->get('category');

        $categories = $user ->getCategories();

        if (!empty($categoryId)) {
            $category = $entityManager->getRepository(Category::class)->find($categoryId);
            $filteredCosts = $costs->filter(function (Cost $cost) use ($category) {
                return $cost->getCategories()->contains($category);
            });

            return $this->render('cost/cost.html.twig', [
                'costs' => $filteredCosts,
                'selectedCategory' => $category,
                'categories' => $categories,
            ]);
        }

        return $this->render('cost/cost.html.twig', [
            'costs' => $costs,
            'categories' => $categories,
            'selectedCategory' => null,
        ]);
    }

    #[Route('/cost/new', name: 'cost_new', methods: ['POST'])]
    public function newCost(Request $request)
    {
        $cost = new Cost();
        $form = $this->createForm(CostType::class, $cost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Cost $cost */
            $cost = $form->getData();
            $cost->setUser($this->getUser());
            $this->costRepository->save($cost, true);
            return $this->redirectToRoute('costs');
        }
        return $this->render('cost/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'cost_delete', methods: ['POST'])]
    public function deleteCost(Cost $cost): Response
    {
        $this->costRepository->remove($cost, true);
        return $this->redirectToRoute('costs');
    }

    #[Route('/cost/edit/{id}', name: 'cost_edit', methods: ['POST'])]
    public function editCost(Cost $cost, Request $request)
    {
        if($cost -> getUser()!== $this->getUser()){
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(CostType::class, $cost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Cost $cost */
            $cost = $form->getData();
            $cost->setUser($this->getUser());
            $this->costRepository->save($cost, true);
            return $this->redirectToRoute('costs');
        }
        return $this->render('cost/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/cost/{id}/category', name: 'cost_category', methods: ['POST'])]
    public function costCategory(Cost $cost, Request $request)
    {
        if($cost ->getUser() !== $this->getUser()){
            throw $this->createAccessDeniedException();
        }
        $costRequest = new Cost();
        foreach ($cost->getCategories() as $category){
            $costRequest->addCategory($category);
        }
        $form = $this->createForm(CategorySelectType::class, $costRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Cost $data */
            $data = $form->getData();
            $cost ->setCategories($data->getCategories());
            $this->costRepository->save($cost, true);
            return $this->redirectToRoute('costs');
        }
        return $this->render('cost/formCategorySelect.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}