<?php

namespace App\DataFixtures;

use App\Entity\Annonce;
use App\Entity\Commentaire;
use App\Entity\Equipement;
use App\Entity\Image;
use App\Entity\Indisponibilite;
use App\Entity\Logement;
use App\Entity\Message;
use App\Entity\Reservation;
use App\Entity\Service;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    // Injecteur du service pour hasher les mots de passe
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    // Méthode principale appelée pour charger les fixtures
    public function load(ObjectManager $manager): void
    {
        // Chargement de tous les utilisateurs depuis un seul fichier JSON
        $usersData = json_decode(file_get_contents(__DIR__ . '/data/users.json'), true);
        $utilisateurs = []; // Pour stockage éventuel d'utilisateurs liés

        foreach ($usersData as $data) {
            $utilisateur = new Utilisateur();

            $hashedPassword = $this->passwordHasher->hashPassword($utilisateur, $data['motDePasse']);

            $utilisateur->setUsername($data['username'])
                ->setGenre($data['genre'])
                ->setNom($data['nom'])
                ->setPrenom($data['prenom'])
                ->setEmail($data['email'])
                ->setPassword($hashedPassword)
                ->setDateNaissance(new \DateTime($data['dateNaissance']))
                ->setCreatedAt(new \DateTimeImmutable($data['createdAt']))
                ->setIsVerified($data['isVerified'])
                ->setProfilPicture($data['profilPicture'])
                ->setBillingAdress($data['billingAdress']);

            // Assigner les rôles depuis le JSON ou par défaut ROLE_USER
            if (isset($data['roles']) && is_array($data['roles']) && count($data['roles']) > 0) {
                $utilisateur->setRoles($data['roles']);
            } else {
                $utilisateur->setRoles(['ROLE_USER']);
            }

            $manager->persist($utilisateur);
            $utilisateurs[] = $utilisateur;
        }

        // --- CREATION LOGEMENTS ---
        $logementsData = json_decode(file_get_contents(__DIR__ . '/data/logements.json'), true);
        $logements = []; // Utile pour associer les annonces
        foreach ($logementsData as $i => $data) {
            $logement = new Logement();
            $logement->setNomRue($data['nomRue'])
                ->setNumeroRue($data['numeroRue'])
                ->setComplementAdresse1($data['complementAdresse1'])
                ->setComplementAdresse2($data['complementAdresse2'])
                ->setVille($data['ville'])
                ->setCodePostal($data['codePostal'])
                ->setPays($data['pays'])
                ->setLatitude($data['latitude'])
                ->setLongitude($data['longitude'])
                ->setSuperficie($data['superficie']);
            // Associer un utilisateur propriétaire à ce logement (par exemple premier utilisateur)
            $logement->setLogementUtilisateur($utilisateurs[$i % count($utilisateurs)]);
            $manager->persist($logement);
            $logements[] = $logement;
        }

        // --- CREATION SERVICES ---
        $serviceData = json_decode(file_get_contents(__DIR__ . '/data/service.json'), true);
        $services = []; // Utile pour associer les services aux annonces
        foreach ($serviceData as $data) {
            $service = new Service();
            $service->setNom($data['nom'])
                ->setDescription($data['description']);
            $manager->persist($service);
            $services[] = $service;
        }

        // --- CREATION ANNONCES ---
        $annoncesData = json_decode(file_get_contents(__DIR__ . '/data/annonce.json'), true);
        $annonces = []; // Utile pour les réservations et indisponibilités
        foreach ($annoncesData as $i => $data) {
            $annonce = new Annonce();
            $annonce->setTitre($data['titre'])
                ->setDescription($data['description'])
                ->setPrixJournee($data['prixJournee'])
                ->setNbPlaces($data['nbPlaces'])
                ->setMixte($data['mixte'])
                ->setAnnonceUtilisateur($utilisateurs[$i % count($utilisateurs)])
                ->setAnnonceLogement($logements[$i % count($logements)]);
            // Ajouter quelques services aux annonces
            if (count($services) > 1) {
                $annonce->addService($services[0]);
                $annonce->addService($services[1]);
            }
            $manager->persist($annonce);
            $annonces[] = $annonce;
        }

        // --- CREATION RESERVATIONS ---
        $reservationData = json_decode(file_get_contents(__DIR__ . '/data/reservation.json'), true);
        $reservations = []; // Utile pour associer les commentaires
        foreach ($utilisateurs as $i => $utilisateur) {
            $data = $reservationData[$i % count($reservationData)];
            $reservation = new Reservation();
            $reservation->setDateDebut(new \Datetime($data['dateDebut']))
                ->setDateFin(new \DateTime($data['dateFin']))
                ->setStatus($data['status'])
                ->setPrixTotal($data['prixTotal'])
                ->setCreatedAt(new \DateTimeImmutable($data['createdAt']))
                ->setReservationUtilisateur($utilisateur)
                ->setReservationAnnonce($annonces[$i % count($annonces)]);
            $manager->persist($reservation);
            $reservations[] = $reservation;
        }

        // --- CREATION COMMENTAIRES ---
        $commentaireData = json_decode(file_get_contents(__DIR__ . '/data/commentaire.json'), true);
        foreach ($commentaireData as $i => $data) {
            $commentaire = new Commentaire();
            $commentaire->setCommentaire($data['commentaire'])
                ->setNote($data['note'])
                ->setDatePublication(new \DateTime($data['datePublication']))
                ->setCommentaireUtilisateur($utilisateurs[$i % count($utilisateurs)])
                ->setCommentaireReservation($reservations[$i % count($reservations)]);
            $manager->persist($commentaire);
        }

        // --- CREATION EQUIPEMENTS ---
        $equipementsData = json_decode(file_get_contents(__DIR__ . '/data/equipement.json'), true);
        foreach ($equipementsData as $i => $data) {
            $equipement = new Equipement();
            $equipement->setNom($data['nom'])
                ->setDescription($data['description'])
                ->addLogement($logements[$i % count($logements)]);
            $manager->persist($equipement);
        }

        // --- CREATION IMAGES ---
        $imageData = json_decode(file_get_contents(__DIR__ . '/data/image.json'), true);
        foreach ($imageData as $i => $data) {
            $image = new Image();
            $image->setPath($data['path'])
                ->setLogementImage($logements[$i % count($logements)]);
            $manager->persist($image);
        }

        // --- CREATION INDISPONIBILITES ---
        $indisponibiliteData = json_decode(file_get_contents(__DIR__ . '/data/indisponibilite.json'), true);
        foreach ($indisponibiliteData as $i => $data) {
            $indisponibilite = new Indisponibilite();
            $indisponibilite->setDateDebut(new \Datetime($data['dateDebut']))
                ->setDateFin(new \DateTime($data['dateFin']))
                ->setDescription($data['description'])
                ->setAnnonceIndisponibilite($annonces[$i % count($annonces)]);
            $manager->persist($indisponibilite);
        }

        // --- CREATION MESSAGES ---
        $messageData = json_decode(file_get_contents(__DIR__ . '/data/message.json'), true);
        foreach ($messageData as $i => $data) {
            $message = new Message();
            $message->setContenu($data['contenu'])
                ->setCreatedAt(new \DateTimeImmutable($data['createdAt']))
                ->setMessageReceiver($utilisateurs[$i % count($utilisateurs)])
                ->setMessageSender($utilisateurs[$i % count($utilisateurs)]);
            $manager->persist($message);
        }

        // Valide et enregistre tout en base de données
        $manager->flush();
    }
}
