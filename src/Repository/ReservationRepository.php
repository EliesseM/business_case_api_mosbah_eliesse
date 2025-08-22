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

    public function hasReservation(Annonce $annonce, \DateTimeInterface $dateDebut, \DateTimeInterface $dateFin): bool
    {
        return (bool) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.reservationAnnonce = :annonce')
            ->andWhere('r.status = :status') // uniquement les réservations validées
            ->andWhere('(:dateDebut BETWEEN r.dateDebut AND r.dateFin OR :dateFin BETWEEN r.dateDebut AND r.dateFin)')
            ->setParameter('annonce', $annonce)
            ->setParameter('status', 'validee')
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }
}
