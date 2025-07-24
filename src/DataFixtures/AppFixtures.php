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
use function Symfony\Component\String\s;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // UTILISATEURS

        $usersData = [
            ['username' => 'john_doe', 'genre' => 'Homme', 'nom' => 'Doe', 'prenom' => 'John', 'email' => 'john@test.com', 'motDePasse' => 'password', 'dateNaissance' => new \DateTime('1993-01-01'), 'createdAt' => new \DateTimeImmutable(), 'isVerified' => true, 'profilPicture' => 'path/to/profile/picture.jpg', 'billingAdress' => '123 Main St, City, Country',]

        ];
        foreach ($usersData as $data)
            $utilisateur = (new Utilisateur())
                ->setUsername($data['username'])
                ->setGenre($data['genre'])
                ->setNom($data['nom'])
                ->setPrenom($data['prenom'])
                ->setEmail($data['email'])
                ->setMotDePasse($data['motDePasse'])
                ->setDateNaissance($data['dateNaissance'])
                ->setCreatedAt($data['createdAt'])
                ->setIsVerified($data['isVerified'])
                ->setProfilPicture($data['profilPicture'])
                ->setBillingAdress($data['billingAdress']);

        $manager->persist($utilisateur);

        // LOGEMENT

        $logementsData = [
            ['nomRue' => 'Logement Title', 'numeroRue' => 'This is a description of the logement.', 'complementAdresse1' => '456 Another St, City, Country', 'complementAdresse2' => 'Apt 789', 'ville' => 'City Name', 'codePostal' => '12345', 'pays' => 'Country Name', 'latitude' => 12.345678, 'longitude' => 98.765432, 'superficie' => 100]
        ];
        foreach ($logementsData as $data)
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

        // ANNONCE
        $annoncesData = [
            ['titre' => 'Annonce Title', 'description' => 'This is a description of the annonce.', 'prixJournee' => 100.00, 'nbPlaces' => 4, 'mixte' => true]
        ];
        foreach ($annoncesData as $data)
            $annonce = (new Annonce())
                ->setTitre($data['titre'])
                ->setDescription($data['description'])
                ->setPrixJournee($data['prixJournee'])
                ->setNbPlaces($data['nbPlaces'])
                ->setMixte($data['mixte'])
                ->setAnnonceUtilisateur($utilisateur);

        $manager->persist($annonce);

        // RESERVATION

        $reservationData = [
            ['dateDebut' => new \DateTime(), 'dateFin' => new \DateTime('+1 day'), 'status' => 'confirmed', 'prixTotal' => 1000.00, 'createdAt' => new \DateTimeImmutable()]
        ];
        foreach ($reservationData as $data)
            $reservation = (new Reservation())
                ->setDateDebut($data['dateDebut'])
                ->setDateFin($data['dateFin'])
                ->setStatus($data['status'])
                ->setPrixTotal($data['prixTotal'])
                ->setCreatedAt($data['createdAt'])
                ->setReservationUtilisateur($utilisateur)
                ->setReservationAnnonce($annonce);

        $manager->persist($reservation);

        //COMMENTAIRE
        $commentaireData = [
            ['commentaire' => 'This is a comment.', 'note' => 5, 'datePublication' => new \DateTime()]
        ];
        foreach ($commentaireData as $data)
            $commentaire = (new Commentaire())
                ->setCommentaire($data['commentaire'])
                ->setNote($data['note'])
                ->setDatePublication($data['datePublication'])
                ->setCommentaireUtilisateur($utilisateur)
                ->setCommentaireReservation($reservation);

        $manager->persist($commentaire);

        //EQUIPEMENTS
        $equipementsData = [
            ['nom' => 'Equipement Name', 'description' => 'This is a description of the equipement.']
        ];
        foreach ($equipementsData as $data) {
            $equipement = (new Equipement())
                ->setNom($data['nom'])
                ->setDescription($data['description']);
            $equipement->addLogement($logement);

            $manager->persist($equipement);
        }

        // IMAGE
        $imageData = [
            ['path' => 'path/to/image.jpg']
        ];
        foreach ($imageData as $data)
            $image = (new Image())
                ->setPath($data['path'])
                ->setLogementImage($logement);

        $manager->persist($image);

        // INDISPONIBILITE
        $indisponibiliteData = [
            ['dateDebut' => new \DateTime(), 'dateFin' => new \DateTime('+1 week'), 'description' => 'Indisponibility description.']
        ];
        foreach ($indisponibiliteData as $data)
            $indisponibilite = (new Indisponibilite())
                ->setDateDebut($data['dateDebut'])
                ->setDateFin($data['dateFin'])
                ->setDescription($data['description'])
                ->setAnnonceIndisponibilite($annonce);

        $manager->persist($indisponibilite);

        // MESSAGE
        $messageData = [
            ['contenu' => 'This is a message.', 'createdAt' => new \DateTimeImmutable()]
        ];
        foreach ($messageData as $data)
            $message = (new Message())
                ->setContenu($data['contenu'])
                ->setCreatedAt($data['createdAt'])
                ->setMessageReceiver($utilisateur)
                ->setMessageSender($utilisateur);

        $manager->persist($message);

        // SERVICE
        $serviceData = [
            ['nom' => 'Service Name', 'description' => 'This is a description of the service.']
        ];
        foreach ($serviceData as $data)
            $service = (new Service())
                ->setNom($data['nom'])
                ->setDescription($data['description']);

        $manager->persist($service);

        $manager->flush();
    }
}
