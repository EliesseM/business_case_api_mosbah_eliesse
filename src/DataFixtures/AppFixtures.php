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

use function Symfony\Component\String\s;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }



    public function load(ObjectManager $manager): void
    {


        //ADMIN

        $adminData = json_decode(file_get_contents(__DIR__ . '/data/admin.json'), true);


        $admins = [];

        foreach ($adminData as $data) {
            $admin = new Utilisateur();
            $hashedPassword = $this->passwordHasher->hashPassword($admin, $data['motDePasse']);

            $admin->setUsername($data['username'])
                ->setGenre($data['genre'])
                ->setNom($data['nom'])
                ->setPrenom($data['prenom'])
                ->setEmail($data['email'])
                ->setMotDePasse($hashedPassword)
                ->setDateNaissance(new \DateTime($data['dateNaissance']))
                ->setCreatedAt(new \DateTimeImmutable($data['createdAt']))
                ->setIsVerified($data['isVerified'])
                ->setProfilPicture($data['profilPicture'])
                ->setBillingAdress($data['billingAdress'])
                ->setRoles(['ROLE_ADMIN']); // Set admin role
            $manager->persist($admin);
            $admins[] = $admin; // Store the admin for later use in other entities
        }


        // PROPRIETAIRE

        $proprietaireData = json_decode(file_get_contents(__DIR__ . '/data/proprietaire.json'), true);



        $proprietaires = [];

        foreach ($proprietaireData as $data) {
            $proprietaire = new Utilisateur();
            $hashedPassword = $this->passwordHasher->hashPassword($proprietaire, $data['motDePasse']);

            $proprietaire->setUsername($data['username'])
                ->setGenre($data['genre'])
                ->setNom($data['nom'])
                ->setPrenom($data['prenom'])
                ->setEmail($data['email'])
                ->setMotDePasse($hashedPassword)
                ->setDateNaissance(new \DateTime($data['dateNaissance']))
                ->setCreatedAt(new \DateTimeImmutable($data['createdAt']))
                ->setIsVerified($data['isVerified'])
                ->setProfilPicture($data['profilPicture'])
                ->setBillingAdress($data['billingAdress'])
                ->setRoles(['ROLE_PROPRIETAIRE']);

            $manager->persist($proprietaire);
            $proprietaires[] = $proprietaire;
        }


        // UTILISATEURS

        $usersData = json_decode(file_get_contents(__DIR__ . '/data/user.json'), true);


        $utilisateurs = [];

        foreach ($usersData as $data) {
            $utilisateur = new Utilisateur();
            $hashedPassword = $this->passwordHasher->hashPassword($utilisateur, $data['motDePasse']);
            $utilisateur->setUsername($data['username'])
                ->setGenre($data['genre'])
                ->setNom($data['nom'])
                ->setPrenom($data['prenom'])
                ->setEmail($data['email'])
                ->setMotDePasse($hashedPassword)
                ->setDateNaissance(new \DateTime($data['dateNaissance']))
                ->setCreatedAt(new \DateTimeImmutable($data['createdAt']))
                ->setIsVerified($data['isVerified'])
                ->setProfilPicture($data['profilPicture'])
                ->setBillingAdress($data['billingAdress'])
                ->setRoles(['ROLE_USER']); // Default role for all users

            $manager->persist($utilisateur);
            $utilisateurs[] = $utilisateur; // Store the user for later use in other entities
        }

        // LOGEMENT

        $logementsData = json_decode(file_get_contents(__DIR__ . '/data/logements.json'), true);

        $logements = [];

        foreach ($logementsData as $data) {
            $logement = (new Logement())
                ->setNomRue($data['nomRue'])
                ->setNumeroRue($data['numeroRue'])
                ->setComplementAdresse1($data['complementAdresse1'])
                ->setComplementAdresse2($data['complementAdresse2'])
                ->setVille($data['ville'])
                ->setCodePostal($data['codePostal'])
                ->setPays($data['pays'])
                ->setLatitude($data['latitude'])
                ->setLongitude($data['longitude'])
                ->setSuperficie($data['superficie'])
                ->setLogementUtilisateur($utilisateur);



            $manager->persist($logement);
            $logements[] = $logement; // Store the logement for later use in other entities
        }

        // SERVICE

        $serviceData = json_decode(file_get_contents(__DIR__ . '/data/service.json'), true);;

        $services = [];

        foreach ($serviceData as $data) {
            $service = (new Service())
                ->setNom($data['nom'])
                ->setDescription($data['description']);
            $manager->persist($service);
            $services[] = $service; // Store the service for later use in other entities
        }

        // ANNONCE

        $annoncesData = json_decode(file_get_contents(__DIR__ . '/data/annonce.json'), true);


        $annonces = [];

        foreach ($annoncesData as $data) {
            $annonce = (new Annonce())
                ->setTitre($data['titre'])
                ->setDescription($data['description'])
                ->setPrixJournee($data['prixJournee'])
                ->setNbPlaces($data['nbPlaces'])
                ->setMixte($data['mixte'])
                ->setAnnonceUtilisateur($utilisateur)
                ->setAnnonceLogement($logement); // Assuming the annonce is linked to a logement
            $annonce->addService($services[0]);
            $annonce->addService($services[1]);



            $manager->persist($annonce);
            $annonces[] = $annonce; // Store the annonce for later use in other entities
        }

        // RESERVATION

        $reservationData = json_decode(file_get_contents(__DIR__ . '/data/reservation.json'), true);


        $reservations = [];

        foreach ($utilisateurs as $i => $utilisateur) {
            $data = $reservationData[$i % count($reservationData)];
            $reservation = (new Reservation())
                ->setDateDebut(new \Datetime($data['dateDebut']))
                ->setDateFin(new \DateTime($data['dateFin']))
                ->setStatus($data['status'])
                ->setPrixTotal($data['prixTotal'])
                ->setCreatedAt(new \DateTimeImmutable($data['createdAt']))
                ->setReservationUtilisateur($utilisateur)
                ->setReservationAnnonce($annonces[$i % count($annonces)]);
            $manager->persist($reservation);
            $reservations[] = $reservation; // Store the reservation for later use in other entities

        }

        //COMMENTAIRE

        $commentaireData = json_decode(file_get_contents(__DIR__ . '/data/commentaire.json'), true);


        $commentaires = [];

        foreach ($commentaireData as $data) {
            $commentaire = (new Commentaire())
                ->setCommentaire($data['commentaire'])
                ->setNote($data['note'])
                ->setDatePublication(new \DateTime($data['datePublication']))
                ->setCommentaireUtilisateur($utilisateur)
                ->setCommentaireReservation($reservation);
            $manager->persist($commentaire);
            $commentaires[] = $commentaire; // Store the commentaire for later use in other entities
        }

        //EQUIPEMENTS

        $equipementsData = json_decode(file_get_contents(__DIR__ . '/data/equipement.json'), true);


        $equipements = [];

        foreach ($equipementsData as $data) {
            $equipement = (new Equipement())
                ->setNom($data['nom'])
                ->setDescription($data['description']);
            $equipement->addLogement($logement);
            $manager->persist($equipement);
            $equipements[] = $equipement; // Store the equipement for later use in other entities
        }
        // IMAGE

        $imageData = json_decode(file_get_contents(__DIR__ . '/data/image.json'), true);

        $images = [];

        foreach ($imageData as $data) {
            $image = (new Image())
                ->setPath($data['path'])
                ->setLogementImage($logement);
            $manager->persist($image);
            $images[] = $image; // Store the image for later use in other entities
        }

        // INDISPONIBILITE

        $indisponibiliteData = json_decode(file_get_contents(__DIR__ . '/data/indisponibilite.json'), true);


        $indisponibilities = [];

        foreach ($indisponibiliteData as $data) {
            $indisponibilite = (new Indisponibilite())
                ->setDateDebut(new \Datetime($data['dateDebut']))
                ->setDateFin(new \DateTime($data['dateFin']))
                ->setDescription($data['description'])
                ->setAnnonceIndisponibilite($annonce);
            $manager->persist($indisponibilite);
            $indisponibilities[] = $indisponibilite; // Store the indisponibilite for later use in other entities
        }

        // MESSAGE
        $messageData = json_decode(file_get_contents(__DIR__ . '/data/message.json'), true);

        $messages = [];

        foreach ($messageData as $data) {
            $message = (new Message())
                ->setContenu($data['contenu'])
                ->setCreatedAt(new \DateTimeImmutable($data['createdAt']))
                ->setMessageReceiver($utilisateur)
                ->setMessageSender($utilisateur);
            $manager->persist($message);
            $messages[] = $message; // Store the message for later use in other entities
        }


        $manager->flush();
    }
}
