<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/account', name: '')]
class AccountController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private PaginatorInterface $paginator;
    private PostRepository $postRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param PaginatorInterface $paginator
     * @param PostRepository $postRepository
     */
    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, PaginatorInterface $paginator, PostRepository $postRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->paginator = $paginator;
        $this->postRepository = $postRepository;
    }


    #[Route('/', name: 'account_show')]
    public function show(Request $request): Response
    {

        if ($this->getUser()) {
            $user = $this->userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
            $qb = $this->postRepository->getQbAll($user->getId());
            $pagination = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 2);

            return $this->render('account/show.html.twig', [
                'user' => $user,
                'pagination' => $pagination,
            ]);
        }
        return $this->redirectToRoute('home_index');

    }
}
