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
        $utilisateur = (new Utilisateur())
            ->setUsername('john_doe')
            ->setGenre('Homme')
            ->setNom('Doe')
            ->setPrenom('John')
            ->setEmail('john@test.com')
            ->setMotDePasse('password')
            ->setDateNaissance(new \DateTime('1993-01-01'))
            ->setCreatedAt(new \DateTimeImmutable())
            ->setIsVerified(true)
            ->setProfilPicture('path/to/profile/picture.jpg')
            ->setBillingAdress('123 Main St, City, Country');

        $manager->persist($utilisateur);

        // ANNONCE
        $annonce = (new Annonce())
            ->setTitre('Annonce Title')
            ->setDescription('This is a description of the annonce.')
            ->setPrixJournee(100.00)
            ->setNbPlaces(4)
            ->setMixte(true);

        $manager->persist($annonce);

        //COMMENTAIRE
        $commentaire = (new Commentaire())
            ->setCommentaire('This is a comment.')
            ->setNote(5)
            ->setDatePublication(new \DateTime());

        $manager->persist($commentaire);

        //EQUIPEMENTS
        $equipement = (new Equipement())
            ->setNom('Equipement Name')
            ->setDescription('This is a description of the equipement.');

        $manager->persist($equipement);

        // IMAGE
        $image = (new Image())
            ->setPath('path/to/image.jpg');

        $manager->persist($image);

        // INDISPONIBILITE
        $indisponibilite = (new Indisponibilite())
            ->setDateDebut(new \DateTime())
            ->setDateFin(new \DateTime())
            ->setDescription('Indisponibility description.');

        $manager->persist($indisponibilite);

        // LOGEMENT
        $logement = (new Logement())
            ->setNomRue('Logement Title')
            ->setNumeroRue('This is a description of the logement.')
            ->setComplementAdresse1('456 Another St, City, Country')
            ->setComplementAdresse2('Apt 789')
            ->setVille('City Name')
            ->setCodePostal('12345')
            ->setPays('Country Name')
            ->setLatitude(12.345678)
            ->setLongitude(98.765432)
            ->setSuperficie(100);

        $manager->persist($logement);

        // MESSAGE
        $message = (new Message())
            ->setContenu('This is a message.')
            ->setCreatedAt(new \DateTimeImmutable());

        $manager->persist($message);

        // RESERVATION
        $reservation = (new Reservation())
            ->setDateDebut(new \DateTime())
            ->setDateFin(new \DateTime())
            ->setStatus('confirmed')
            ->setPrixTotal(1000.00)
            ->setCreatedAt(new \DateTimeImmutable());

        $manager->persist($reservation);

        // SERVICE
        $service = (new Service())
            ->setNom('Service Name')
            ->setDescription('This is a description of the service.');

        $manager->persist($service);

        $manager->flush();
    }
}
