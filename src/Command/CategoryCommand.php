<?php

namespace App\Command;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:addCategory',
    description: 'Add a short description for your command',
)]
class CategoryCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private CategoryRepository $categoryRepository;


    public function __construct(
        EntityManagerInterface $entityManager, CategoryRepository $categoryRepository
    )
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->categoryRepository = $categoryRepository;
    }

    protected function configure(): void
    {
        $this->setName('app:addCategory');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Command starting...');

        $categoriesToDelete = $this->categoryRepository->findAll();

        foreach ($categoriesToDelete as $childCategory) {
            if ($childCategory->getParent() !== null) {
                $this->entityManager->remove($childCategory);
            }
        }
        foreach ($categoriesToDelete as $parentCategory) {
            $this->entityManager->remove($parentCategory);
        }
        $this->entityManager->flush();

        $categories = [
            ['name' => 'Divers',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'parent' => null,
            ],
            ['name' => 'Informatique',
                'description' => 'Odio ut sem nulla pharetra diam sit. Nec ullamcorper sit amet risus nullam eget felis eget nunc.',
                'parent' => null,
            ],
            ['name' => 'Tavernes',
                'description' => 'Egestas sed sed risus pretium quam. Ultrices mi tempus imperdiet nulla.',
                'parent' => 'Divers',
            ],
            ['name' => 'Films & Séries',
                'description' => 'Egestas sed sed risus pretium quam. Ultrices mi tempus imperdiet nulla.',
                'parent' => 'Divers',
            ],
            ['name' => 'Recettes de cuisine',
                'description' => 'Egestas sed sed risus pretium quam. Ultrices mi tempus imperdiet nulla.',
                'parent' => 'Divers',
            ],
            ['name' => 'SQL',
                'description' => 'Odio ut sem nulla pharetra diam sit. Nec ullamcorper sit amet risus nullam eget felis eget nunc.',
                'parent' => 'Informatique',
            ],
            ['name' => 'Angular',
                'description' => 'Odio ut sem nulla pharetra diam sit. Nec ullamcorper sit amet risus nullam eget felis eget nunc.',
                'parent' => 'Informatique',
            ],
            ['name' => 'Symfony',
                'description' => 'Odio ut sem nulla pharetra diam sit. Nec ullamcorper sit amet risus nullam eget felis eget nunc.',
                'parent' => 'Informatique',
            ],
        ];

        $progressBar = new ProgressBar($output, count($categories));
        $progressBar->start();

        foreach ($categories as $category) {
            $cat = new Category();
            $cat->setName($category['name']);
            $cat->setDescription($category['description']);
            $cat->setParent($this->categoryRepository->findOneBy(['name' => $category['parent']]));
            $this->entityManager->persist($cat);
            $this->entityManager->flush();
            $progressBar->advance();
        }
        $progressBar->finish();
        $output->writeln('Command finished !');

        return Command::SUCCESS;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return CategoryRepository
     */
    public function getCategoryRepository(): CategoryRepository
    {
        return $this->categoryRepository;
    }

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function setCategoryRepository(CategoryRepository $categoryRepository): void
    {
        $this->categoryRepository = $categoryRepository;
    }


}
