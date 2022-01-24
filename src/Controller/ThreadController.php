<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Thread;
use App\Form\PostFormType;
use App\Form\ThreadFormType;
use App\Repository\PostRepository;
use App\Repository\ThreadRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/thread', name: '')]
class ThreadController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private ThreadRepository $threadRepository;
    private PostRepository $postRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param ThreadRepository $threadRepository
     * @param PostRepository $postRepository
     */
    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, ThreadRepository $threadRepository, PostRepository $postRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->threadRepository = $threadRepository;
        $this->postRepository = $postRepository;
    }

    #[Route('/', name: 'thread')]
    public function index(): Response
    {
        return $this->render('thread/index.html.twig', [
            'controller_name' => 'ThreadController',
        ]);
    }

    #[Route('/new', name: 'new_thread')]
    public function createPost(Request $request): Response {

        $thread = new Thread();
        $thread->setCreatedAt(new DateTime());
        $form = $this->createForm(ThreadFormType::class, $thread);
        $form->handleRequest($request);


        if ($this->getUser()) {
            $user = $this->userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

            $thread->setUser($user);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->entityManager->persist($form->getData());
                $this->entityManager->flush();
                return $this->redirectToRoute('home_index');
            }

            return $this->render('thread/new.html.twig', [
                'form' => $form->createView(),
            ]);

        }

        return $this->redirectToRoute('app_login');
    }

    #[Route('/{id}', name: 'thread_show')]
    public function show($id): Response
    {
        $thread = $this->threadRepository->findOneBy(['id' => $id]);
        $posts = $this->postRepository->findOrderedPosts($id);
        $user = null;
        if ($this->getUser()){
            $user = $this->userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        }

        return $this->render('thread/show.html.twig', [
            'thread' => $thread,
            'posts' => $posts,
            'user' => $user
        ]);
    }

    #[Route('/{id}/new-post', name: 'thread_new_post')]
    public function createThreadPost(Request $request, $id): Response {

        $post = new Post();
        $post->setCreatedAt(new DateTime());
        $post->setUpVote(0);
        $post->setDownVote(0);
        $post->setThread($this->threadRepository->findOneBy(['id' => $id]));
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

            return $this->render('thread/new_post.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        return $this->redirectToRoute('app_login');
    }
}
