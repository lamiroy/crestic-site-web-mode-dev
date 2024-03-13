<?php

namespace App\Repository;

use App\Entity\Activites;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * ActivitesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ActivitesRepository  extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activites::class);
    }

    public function findActivitesMembre($user)
    {
        return $this->findBy(['membreCrestic' => $user], ['created' => 'DESC']);
    }

    public function findLastActiviteMembre($user, $nb = 3)
    {
        return $this->createQueryBuilder('a')
            ->where('a.membreCrestic = :user')
            ->setParameter('user', $user)
            ->orderBy('a.created', 'DESC')
            ->setMaxResults($nb)
            ->getQuery()
            ->getResult();
    }
}
