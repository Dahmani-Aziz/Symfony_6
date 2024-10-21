<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CategoryType;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/category', name: 'app_category')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    #[Route("/categories", name: "category_list")]
    public function listCategories(): Response
    {
        $categories = $this->entityManager->getRepository(Category::class)->findAll();
    
        return $this->render('product/accueil.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/add-category', name: 'add_category')]
    public function addProduct(Request $request): Response 
    {
        $Category = new Category();

        $form = $this->createForm(CategoryType::class, $Category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($Category);
            $this->entityManager->flush();

            return $this->redirectToRoute('category_list'); 
        }

        return $this->render('product/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit-author/{id}', name: 'edit_cat')]
    public function editCat(Category $category, Request $request): Response
    {
        

        if (!$category) {
            throw $this->createNotFoundException('non trouveee');
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('category_list');
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    #[Route('/delete-category/{id}', name: 'delete_cat')]
    public function deleteCat(Category $category): Response
    {

        if ($category) {
            $this->entityManager->remove($category);
            $this->entityManager->flush();
            return $this->redirectToRoute('category_list');
        }

        throw $this->createNotFoundException('non trouvé');
    }
}
