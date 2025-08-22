<?php

namespace App\Repository;

use App\Entity\Annonce;
use App\Entity\Indisponibilite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Indisponibilite>
 */
class IndisponibiliteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Indisponibilite::class);
    }
    public function hasUnavailability(Annonce $annonce, \DateTimeInterface $dateDebut, \DateTimeInterface $dateFin): bool
    {
        return (bool) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->andWhere('u.annonce = :annonce')
            ->andWhere('(:dateDebut BETWEEN u.dateDebut AND u.dateFin OR :dateFin BETWEEN u.dateDebut AND u.dateFin)')
            ->setParameter('annonce', $annonce)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }
}
