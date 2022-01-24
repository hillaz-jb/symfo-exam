<?php

namespace App\Controller;


use App\Repository\CategoryRepository;
use App\Repository\ThreadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category', name: '')]
class CategoryController extends AbstractController
{
    private CategoryRepository $categoryRepository;
    private ThreadRepository $threadRepository;

    /**
     * @param CategoryRepository $categoryRepository
     * @param ThreadRepository $threadRepository
     */

    public function __construct(CategoryRepository $categoryRepository, ThreadRepository $threadRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->threadRepository = $threadRepository;
    }


    #[Route('/', name: 'category_index')]
    public function index(): Response
    {
        $parentcategories = $this->categoryRepository->getParentCategories();
        $arrayChildrenCategories = [];
        foreach ($parentcategories as $parentcategory)
        {
            $idParent = $parentcategory->getId();
            $ChildrenCategories = $this->categoryRepository->getChildrenCategories($parentcategory);
            $arrayChildrenCategories[$idParent] = $ChildrenCategories;

        }

        return $this->render('category/index.html.twig', [
            'parentsCategories' => $parentcategories,
            'arrayChildrenCategories' => $arrayChildrenCategories,

        ]);
    }

    #[Route('/{id}', name: 'category_show')]
    public function show($id): Response
    {
        $category = $this->categoryRepository->findOneBy(['id' => $id]);
        $threads = $this->threadRepository->findOrderedThreads($id);

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'threads' => $threads,
        ]);
    }
}
