<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use App\Repository\ThreadRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private ThreadRepository $threadRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param ThreadRepository $threadRepository
     */
    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, ThreadRepository $threadRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->threadRepository = $threadRepository;
    }


    #[Route('/new-post', name: 'new_post')]
    public function createPost(Request $request): Response {
        $isThreads = false;
        if ($this->threadRepository->findAll() !== []){
            $isThreads = true;
        }

        $post = new Post();
        $post->setCreatedAt(new DateTime());
        $post->setUpVote(0);
        $post->setDownVote(0);
        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);


        if ($this->getUser()) {
            $user = $this->userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

            $post->setUser($user);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->entityManager->persist($form->getData());
                $this->entityManager->flush();
                return $this->redirectToRoute('home_index');
            }
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
            'isThreads' => $isThreads,
        ]);
    }

    #[Route('/post/edit/{id}', name: 'post_edit')]
    public function editPost(Request $request, Post $post): Response {
        if ($this->getUser()){
            $form = $this->createForm(PostFormType::class, $post);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->entityManager->persist($form->getData());
                $this->entityManager->flush();
                return $this->redirectToRoute('home_index');
            }
            return $this->render('post/edit.html.twig',[
                'form' => $form->createView(),
            ]);
        }
        else {
            return $this->redirectToRoute('app_login');
        }

    }
}
