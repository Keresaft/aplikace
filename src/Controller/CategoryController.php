<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Cost;
use App\Entity\User;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    public function __construct(private readonly CategoryRepository $categoryRepository)
    {
    }

    #[Route('/category/new', name: 'category_new')]
    public function newCategory(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Category $category */
            $category = $form->getData();
            $category->setUser($this->getUser());
            $this->categoryRepository->save($category, true);
            return $this->redirectToRoute('category');
        }
        return $this->render('category/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category', name: 'category')]
    public function allCategory()
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Cost $costs */
        $categories = $user->getCategories();

        return $this->render('category/category.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/delete/{id}', name: 'category_delete', methods: ['POST'])]
    public function deleteCategory (Category $category):Response
    {
        $this ->categoryRepository->remove($category, true);
        return $this -> redirectToRoute('category');
    }

    #[Route('/category/edit/{id}', name: 'category_edit')]
    public function editCategory(Category $category, Request $request)
    {
        if($category->getUser() !== $this->getUser()){
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Category $category */
            $category = $form->getData();
            $category->setUser($this->getUser());
            $this->categoryRepository->save($category, true);
            return $this->redirectToRoute('category');
        }
        return $this->render('category/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}