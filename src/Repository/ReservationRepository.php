<?php

namespace App\Repository;

use App\Entity\Annonce;
use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function hasReservation(Annonce $reservationAnnonce, \DateTimeInterface $dateDebut, \DateTimeInterface $dateFin): bool
    {
        $qb = $this->createQueryBuilder('r');

        $count = (int) $qb->select('COUNT(r.id)')
            ->where('r.reservationAnnonce = :annonce')
            ->andWhere('r.dateDebut < :dateFin')
            ->andWhere('r.dateFin > :dateDebut')
            ->setParameter('annonce', $reservationAnnonce)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getSingleScalarResult();
        return $count > 0;
    }
}
