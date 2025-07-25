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

        $adminData = [
            ['username' => 'admin', 'genre' => 'Homme', 'nom' => 'Admin', 'prenom' => 'Principal', 'email' => 'admin@example.com', 'motDePasse' => 'password', 'dateNaissance' => new \DateTime('1980-01-01'), 'createdAt' => new \DateTimeImmutable(), 'isVerified' => true, 'profilPicture' => 'path/to/admin.jpg', 'billingAdress' => '1 Rue de l\'Admin, AdminVille']
        ];

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
                ->setDateNaissance($data['dateNaissance'])
                ->setCreatedAt($data['createdAt'])
                ->setIsVerified($data['isVerified'])
                ->setProfilPicture($data['profilPicture'])
                ->setBillingAdress($data['billingAdress'])
                ->setRoles(['ROLE_ADMIN']); // Set admin role
            $manager->persist($admin);
            $admins[] = $admin; // Store the admin for later use in other entities
        }


        // PROPRIETAIRE


        $proprietaireData = [
            ['username' => 'admin', 'genre' => 'Homme', 'nom' => 'Admin', 'prenom' => 'Principal', 'email' => 'admin@example.com', 'motDePasse' => 'password', 'dateNaissance' => new \DateTime('1980-01-01'), 'createdAt' => new \DateTimeImmutable(), 'isVerified' => true, 'profilPicture' => 'path/to/admin.jpg', 'billingAdress' => '1 Rue de l\'Admin, AdminVille', 'roles' => ['ROLE_ADMIN']],
        ];


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
                ->setDateNaissance($data['dateNaissance'])
                ->setCreatedAt($data['createdAt'])
                ->setIsVerified($data['isVerified'])
                ->setProfilPicture($data['profilPicture'])
                ->setBillingAdress($data['billingAdress'])
                ->setRoles(['ROLE_PROPRIETAIRE']);


            $manager->persist($proprietaire);
            $proprietaires[] = $proprietaire; // Store the proprietaire for later use in other entities
        }

        // UTILISATEURS

        $usersData = [
            ['username' => 'john_doe', 'genre' => 'Homme', 'nom' => 'Doe', 'prenom' => 'John', 'email' => 'john@test.com', 'motDePasse' => 'password', 'dateNaissance' => new \DateTime('1993-01-01'), 'createdAt' => new \DateTimeImmutable(), 'isVerified' => true, 'profilPicture' => 'path/to/profile/picture.jpg', 'billingAdress' => '123 Main St, City, Country'],
            ['username' => 'alice_w', 'genre' => 'Femme', 'nom' => 'Williams', 'prenom' => 'Alice', 'email' => 'alice.williams@test.com', 'motDePasse' => 'password1', 'dateNaissance' => new \DateTime('1990-05-12'), 'createdAt' => new \DateTimeImmutable(), 'isVerified' => true, 'profilPicture' => 'path/to/alice.jpg', 'billingAdress' => '10 Downing St, London, UK'],
            ['username' => 'bob_s', 'genre' => 'Homme', 'nom' => 'Smith', 'prenom' => 'Bob', 'email' => 'bob.smith@test.com', 'motDePasse' => 'password2', 'dateNaissance' => new \DateTime('1985-08-23'), 'createdAt' => new \DateTimeImmutable(), 'isVerified' => false, 'profilPicture' => 'path/to/bob.jpg', 'billingAdress' => '221B Baker St, London, UK'],
            ['username' => 'carol_j', 'genre' => 'Femme', 'nom' => 'Jones', 'prenom' => 'Carol', 'email' => 'carol.jones@test.com', 'motDePasse' => 'password3', 'dateNaissance' => new \DateTime('1992-11-01'), 'createdAt' => new \DateTimeImmutable(), 'isVerified' => true, 'profilPicture' => 'path/to/carol.jpg', 'billingAdress' => '742 Evergreen Terrace, Springfield'],
            ['username' => 'dave_m', 'genre' => 'Homme', 'nom' => 'Miller', 'prenom' => 'Dave', 'email' => 'dave.miller@test.com', 'motDePasse' => 'password4', 'dateNaissance' => new \DateTime('1988-02-14'), 'createdAt' => new \DateTimeImmutable(), 'isVerified' => true, 'profilPicture' => 'path/to/dave.jpg', 'billingAdress' => '1600 Pennsylvania Ave, Washington DC'],
            ['username' => 'eve_b', 'genre' => 'Femme', 'nom' => 'Brown', 'prenom' => 'Eve', 'email' => 'eve.brown@test.com', 'motDePasse' => 'password5', 'dateNaissance' => new \DateTime('1995-07-30'), 'createdAt' => new \DateTimeImmutable(), 'isVerified' => false, 'profilPicture' => 'path/to/eve.jpg', 'billingAdress' => '221B Baker St, London, UK'],
            ['username' => 'frank_t', 'genre' => 'Homme', 'nom' => 'Taylor', 'prenom' => 'Frank', 'email' => 'frank.taylor@test.com', 'motDePasse' => 'password6', 'dateNaissance' => new \DateTime('1983-09-09'), 'createdAt' => new \DateTimeImmutable(), 'isVerified' => true, 'profilPicture' => 'path/to/frank.jpg', 'billingAdress' => '4 Privet Drive, Little Whinging'],
            ['username' => 'grace_l', 'genre' => 'Femme', 'nom' => 'Lee', 'prenom' => 'Grace', 'email' => 'grace.lee@test.com', 'motDePasse' => 'password7', 'dateNaissance' => new \DateTime('1991-12-12'), 'createdAt' => new \DateTimeImmutable(), 'isVerified' => true, 'profilPicture' => 'path/to/grace.jpg', 'billingAdress' => '10 Downing St, London, UK'],
            ['username' => 'henry_w', 'genre' => 'Homme', 'nom' => 'White', 'prenom' => 'Henry', 'email' => 'henry.white@test.com', 'motDePasse' => 'password8', 'dateNaissance' => new \DateTime('1986-04-04'), 'createdAt' => new \DateTimeImmutable(), 'isVerified' => false, 'profilPicture' => 'path/to/henry.jpg', 'billingAdress' => '742 Evergreen Terrace, Springfield'],
            ['username' => 'irene_k', 'genre' => 'Femme', 'nom' => 'King', 'prenom' => 'Irene', 'email' => 'irene.king@test.com', 'motDePasse' => 'password9', 'dateNaissance' => new \DateTime('1994-03-22'), 'createdAt' => new \DateTimeImmutable(), 'isVerified' => true, 'profilPicture' => 'path/to/irene.jpg', 'billingAdress' => '1600 Pennsylvania Ave, Washington DC'],
            ['username' => 'jack_c', 'genre' => 'Homme', 'nom' => 'Clark', 'prenom' => 'Jack', 'email' => 'jack.clark@test.com', 'motDePasse' => 'password10', 'dateNaissance' => new \DateTime('1989-06-15'), 'createdAt' => new \DateTimeImmutable(), 'isVerified' => true, 'profilPicture' => 'path/to/jack.jpg', 'billingAdress' => '4 Privet Drive, Little Whinging'],
        ];

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
                ->setDateNaissance($data['dateNaissance'])
                ->setCreatedAt($data['createdAt'])
                ->setIsVerified($data['isVerified'])
                ->setProfilPicture($data['profilPicture'])
                ->setBillingAdress($data['billingAdress'])
                ->setRoles(['ROLE_USER']); // Default role for all users

            $manager->persist($utilisateur);
            $utilisateurs[] = $utilisateur; // Store the user for later use in other entities
        }

        // LOGEMENT

        $logementsData = [
            ['nomRue' => 'Logement Title', 'numeroRue' => 'This is a description of the logement.', 'complementAdresse1' => '456 Another St, City, Country', 'complementAdresse2' => 'Apt 789', 'ville' => 'City Name', 'codePostal' => '12345', 'pays' => 'Country Name', 'latitude' => 12.345678, 'longitude' => 98.765432, 'superficie' => 100]
        ];

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

        $serviceData = [
            ['nom' => 'Service Name', 'description' => 'This is a description of the service.'],
            ['nom' => 'Service Name', 'description' => 'This is a description of the service.']
        ];

        $services = [];

        foreach ($serviceData as $data) {
            $service = (new Service())
                ->setNom($data['nom'])
                ->setDescription($data['description']);
            $manager->persist($service);
            $services[] = $service; // Store the service for later use in other entities
        }

        // ANNONCE

        $annoncesData = [
            ['titre' => 'Annonce Title', 'description' => 'This is a description of the annonce.', 'prixJournee' => 100.00, 'nbPlaces' => 4, 'mixte' => true]
        ];

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

        $reservationData = [
            ['dateDebut' => new \DateTime(), 'dateFin' => new \DateTime('+1 day'), 'status' => 'confirmed', 'prixTotal' => 1000.00, 'createdAt' => new \DateTimeImmutable()]
        ];

        $reservations = [];

        foreach ($reservationData as $data) {
            $reservation = (new Reservation())
                ->setDateDebut($data['dateDebut'])
                ->setDateFin($data['dateFin'])
                ->setStatus($data['status'])
                ->setPrixTotal($data['prixTotal'])
                ->setCreatedAt($data['createdAt'])
                ->setReservationUtilisateur($utilisateur)
                ->setReservationAnnonce($annonce);
            $manager->persist($reservation);
            $reservations[] = $reservation; // Store the reservation for later use in other entities
        }

        //COMMENTAIRE

        $commentaireData = [
            ['commentaire' => 'This is a comment.', 'note' => 5, 'datePublication' => new \DateTime()]
        ];

        $commentaires = [];

        foreach ($commentaireData as $data) {
            $commentaire = (new Commentaire())
                ->setCommentaire($data['commentaire'])
                ->setNote($data['note'])
                ->setDatePublication($data['datePublication'])
                ->setCommentaireUtilisateur($utilisateur)
                ->setCommentaireReservation($reservation);
            $manager->persist($commentaire);
            $commentaires[] = $commentaire; // Store the commentaire for later use in other entities
        }

        //EQUIPEMENTS

        $equipementsData = [
            ['nom' => 'Equipement Name', 'description' => 'This is a description of the equipement.']
        ];

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

        $imageData = [
            ['path' => 'path/to/image.jpg']
        ];
        $images = [];

        foreach ($imageData as $data) {
            $image = (new Image())
                ->setPath($data['path'])
                ->setLogementImage($logement);
            $manager->persist($image);
            $images[] = $image; // Store the image for later use in other entities
        }

        // INDISPONIBILITE

        $indisponibiliteData = [
            ['dateDebut' => new \DateTime(), 'dateFin' => new \DateTime('+1 week'), 'description' => 'Indisponibility description.']
        ];

        $indisponibilities = [];

        foreach ($indisponibiliteData as $data) {
            $indisponibilite = (new Indisponibilite())
                ->setDateDebut($data['dateDebut'])
                ->setDateFin($data['dateFin'])
                ->setDescription($data['description'])
                ->setAnnonceIndisponibilite($annonce);
            $manager->persist($indisponibilite);
            $indisponibilities[] = $indisponibilite; // Store the indisponibilite for later use in other entities
        }

        // MESSAGE

        $messageData = [
            ['contenu' => 'This is a message.', 'createdAt' => new \DateTimeImmutable()]
        ];

        $messages = [];

        foreach ($messageData as $data) {
            $message = (new Message())
                ->setContenu($data['contenu'])
                ->setCreatedAt($data['createdAt'])
                ->setMessageReceiver($utilisateur)
                ->setMessageSender($utilisateur);
            $manager->persist($message);
            $messages[] = $message; // Store the message for later use in other entities
        }


        $manager->flush();
    }
}
