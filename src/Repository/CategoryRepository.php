<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Thread;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function getParentCategories(): array {
        return $this->createQueryBuilder('category')
            ->select('category')
            ->where('category.parent IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function getChildrenCategories($Parent): array {
        return $this->createQueryBuilder('category')
            ->select('category')
            ->where('category.parent = :val')
            ->setParameter('val', $Parent)
            ->getQuery()
            ->getResult();
    }

}
