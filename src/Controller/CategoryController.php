<?php

namespace App\Controller;


use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Repository\ThreadRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category', name: '')]
class CategoryController extends AbstractController
{
    private CategoryRepository $categoryRepository;
    private ThreadRepository $threadRepository;
    private PostRepository $postRepository;

    /**
     * @param CategoryRepository $categoryRepository
     * @param ThreadRepository $threadRepository
     * @param PostRepository $postRepository
     */
    public function __construct(CategoryRepository $categoryRepository, ThreadRepository $threadRepository, PostRepository $postRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->threadRepository = $threadRepository;
        $this->postRepository = $postRepository;
    }


    #[Route('/', name: 'category_index')]
    public function index(): Response
    {
        $categories = $this->categoryRepository->findAll();
        /*foreach ($categories as $category)
        {
            if ($category->getParent() != null){
                $idParent = $category->getParent()->getId();
            }

        }*/
        return $this->render('category/index.html.twig', [
            'categories' => $this->categoryRepository->findAll(),
            //'child' => $this->categoryRepository->getHierarchizeCategories($idParent)
        ]);
    }

    #[Route('/{id}', name: 'category_show')]
    public function show($id): Response
    {
        $category = $this->categoryRepository->findOneBy(['id' => $id]);
        $threads = $this->threadRepository->findBy(["category" => $category]);

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'threads' => $threads,
        ]);
    }
}
