<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use App\Repository\IndisponibiliteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ReservationPostProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
        private ReservationRepository $reservationRepo,
        private IndisponibiliteRepository $indisponibiliteRepo
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof Reservation) {
            return $data;
        }

        $user = $this->security->getUser();
        if (!$user) {
            throw new AccessDeniedHttpException('Vous devez être connecté pour créer une réservation.');
        }
        // Force l’utilisateur connecté
        $data->setReservationUtilisateur($user);

        // ajout de status pending a la creation de la reservation
        $data->setStatus('pending');

        // Vérifie qu’une annonce est associée
        $annonce = $data->getReservationAnnonce();
        if (!$annonce) {
            throw new BadRequestHttpException('Une annonce doit être associée à la réservation.');
        }

        // Interdit au propriétaire de réserver sa propre annonce
        if ($annonce->getAnnonceUtilisateur() === $user) {
            throw new AccessDeniedHttpException('Vous ne pouvez pas réserver votre propre annonce.');
        }

        // Vérifie la date de début
        $dateDebut = $data->getDateDebut();
        $today = new \DateTimeImmutable('today');

        if ($dateDebut < $today) {
            throw new BadRequestHttpException('La date de début doit être aujourd’hui ou dans le futur.');
        }

        // Vérifie la durée
        $duree = $data->getDuree();
        if (!$duree || $duree < 1) {
            throw new BadRequestHttpException('La durée de la réservation doit être renseignée et être au minimum d’un mois.');
        }

        // Calcule la date de fin automatiquement
        $dateFin = $dateDebut->modify("+{$duree} month");
        $data->setDateFin($dateFin);

        // Vérifie qu’il n’y a pas déjà une réservation ou une indisponibilité sur ces dates
        if (
            $this->reservationRepo->hasReservation($annonce, $dateDebut, $dateFin)
            || $this->indisponibiliteRepo->hasUnavailability($annonce, $dateDebut, $dateFin)
        ) {
            throw new BadRequestHttpException('Le logement est déjà réservé ou indisponible pour ces dates.');
        }

        // Définit createdAt si absent
        if (!$data->getCreatedAt()) {
            $data->setCreatedAt(new \DateTimeImmutable());
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}
