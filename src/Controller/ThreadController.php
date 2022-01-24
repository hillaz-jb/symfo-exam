<?php

namespace App\Controller;

use App\Entity\Thread;
use App\Form\ThreadFormType;
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
        }

        return $this->render('thread/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'thread_show')]
    public function show($id): Response
    {
        $thread = $this->threadRepository->findOneBy(['id' => $id]);

        return $this->render('thread/show.html.twig', [
            'thread' => $thread,
        ]);
    }
}
