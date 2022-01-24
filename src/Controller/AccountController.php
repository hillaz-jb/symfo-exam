<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/account', name: '')]
class AccountController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     */
    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }


    #[Route('/', name: 'account_show')]
    public function show(): Response
    {
        if ($this->getUser()) {
            $user = $this->getUser();
            dump($user->getUserIdentifier());

            return $this->render('account/show.html.twig', [
                'user' => $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]),
            ]);
        }
        return $this->redirectToRoute('home_index');

    }
}
