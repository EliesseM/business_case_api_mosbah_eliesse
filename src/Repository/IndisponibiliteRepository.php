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
    public function hasUnavailability(Annonce $annonceIndisponibilite, \DateTimeInterface $dateDebut, \DateTimeInterface $dateFin): bool
    {
        $qb = $this->createQueryBuilder('i');

        $count = (int) $qb->select('COUNT(i.id)')
            ->where('i.annonceIndisponibilite = :annonce')
            ->andWhere('i.dateDebut < :dateFin')
            ->andWhere('i.dateFin > :dateDebut')
            ->setParameter('annonce', $annonceIndisponibilite)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getSingleScalarResult();
        return $count > 0;
    }
}
