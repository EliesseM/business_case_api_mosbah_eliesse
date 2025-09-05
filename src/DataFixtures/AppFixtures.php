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
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // j'importe mes données en json et les décode pour travailler avec un tableau associatif
        $logements = json_decode(file_get_contents(__DIR__ . '/data/logements.json'), true);
        $services = json_decode(file_get_contents(__DIR__ . '/data/service.json'), true);
        $annonces = json_decode(file_get_contents(__DIR__ . '/data/annonce.json'), true);
        $reservations = json_decode(file_get_contents(__DIR__ . '/data/reservation.json'), true);
        $commentaires = json_decode(file_get_contents(__DIR__ . '/data/commentaire.json'), true);
        $users = json_decode(file_get_contents(__DIR__ . '/data/users.json'), true);

        $faker = Factory::create('fr_FR');

        //  UTILISATEURS

        $utilisateurs = [];
        for ($i = 0; $i < 20; $i++) {
            $utilisateur = new Utilisateur();
            $password = $this->passwordHasher->hashPassword($utilisateur, 'password123');

            $roles = ['ROLE_USER'];
            if ($i % 3 === 0) { // 1/3 sont aussi propriétaires
                $roles[] = 'ROLE_PROPRIETAIRE';
            }

            $utilisateur
                ->setUsername($faker->userName())
                ->setGenre($faker->randomElement(['Homme', 'Femme']))
                ->setNom($faker->lastName())
                ->setPrenom($faker->firstName())
                ->setEmail($faker->unique()->safeEmail())
                ->setPassword($password)
                ->setDateNaissance($faker->dateTimeBetween('-60 years', '-18 years'))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setIsVerified($faker->boolean(80))
                ->setProfilPicture($faker->imageUrl(200, 200, 'people'))
                ->setBillingAddress($faker->address())
                ->setRoles(array_unique($roles));

            $manager->persist($utilisateur);
            $utilisateurs[] = $utilisateur;
        }

        foreach ($users as $userData) {
            $utilisateur = new Utilisateur();

            // Hash du mot de passe (pris depuis JSON)
            $password = $this->passwordHasher->hashPassword($utilisateur, $userData['motDePasse']);

            $utilisateur
                ->setUsername($userData['username'])
                ->setGenre($userData['genre'])
                ->setNom($userData['nom'])
                ->setPrenom($userData['prenom'])
                ->setEmail($userData['email'])
                ->setPassword($password)
                ->setDateNaissance(new \DateTime($userData['dateNaissance']))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setIsVerified($userData['isVerified'] ?? false)
                ->setProfilPicture($userData['profilPicture'] ?? null)
                ->setBillingAddress($userData['billingAdress'] ?? null)
                ->setRoles($userData['roles'] ?? ['ROLE_USER']);

            $manager->persist($utilisateur);
        }

        //  LOGEMENTS
        $logements = [];
        for ($i = 0; $i < 15; $i++) {
            $logement = new Logement();
            $logement
                ->setNomRue($faker->streetName())
                ->setNumeroRue($faker->buildingNumber())
                ->setComplementAdresse1($faker->optional()->secondaryAddress())
                ->setComplementAdresse2($faker->optional()->secondaryAddress())
                ->setVille($faker->city())
                ->setCodePostal($faker->postcode())
                ->setPays($faker->country())
                ->setLatitude($faker->latitude())
                ->setLongitude($faker->longitude())
                ->setSuperficie($faker->numberBetween(20, 300))
                ->setLogementUtilisateur($faker->randomElement($utilisateurs));

            $manager->persist($logement);
            $logements[] = $logement;
        }

        //  SERVICES
        $services = [];
        for ($i = 0; $i < 5; $i++) {
            $service = new Service();
            $service
                ->setNom($faker->word())
                ->setDescription($faker->sentence());

            $manager->persist($service);
            $services[] = $service;
        }

        // ANNONCES
        $annoncesData = json_decode(file_get_contents(__DIR__ . '/data/annonce.json'), true);

        $annonces = [];
        foreach ($annoncesData as $item) {
            $annonce = new Annonce();
            $annonce
                ->setTitre($item['titre'] ?? '')
                ->setDescription($item['description'] ?? '')
                ->setPrixJournee($item['prixJournee'] ?? 0)
                ->setNbPlaces($item['nbPlaces'] ?? 1)
                ->setMixte($item['mixte'] ?? false)
                ->setAnnonceUtilisateur($faker->randomElement($utilisateurs))
                ->setAnnonceLogement($faker->randomElement($logements));


            foreach ($faker->randomElements($services, rand(1, 3)) as $service) {
                $annonce->addService($service);
            }
            // Services associés
            if (!empty($item['services'])) {
                foreach ($item['services'] as $serviceId) {
                    if (isset($services[$serviceId])) {
                        $annonce->addService($services[$serviceId]);
                    }
                }
            }

            $manager->persist($annonce);
            $annonces[] = $annonce;
        }


        // RESERVATIONS
        $reservations = [];
        for ($i = 0; $i < 20; $i++) {
            $dateDebut = $faker->dateTimeBetween('now', '+1 month');
            $dateFin = (clone $dateDebut)->modify('+' . rand(1, 14) . ' days');

            // Convert to DateTimeImmutable
            $dateDebutImmutable = \DateTimeImmutable::createFromMutable($dateDebut);
            $dateFinImmutable = \DateTimeImmutable::createFromMutable($dateFin);

            $reservation = new Reservation();
            $reservation
                ->setDateDebut($dateDebutImmutable)
                ->setDateFin($dateFinImmutable)
                ->setStatus($faker->randomElement(['pending', 'confirmed', 'cancelled']))
                ->setPrixTotal($faker->numberBetween(100, 5000))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setReservationUtilisateur($faker->randomElement($utilisateurs))
                ->setReservationAnnonce($faker->randomElement($annonces));

            $manager->persist($reservation);
            $reservations[] = $reservation;
        }

        // COMMENTAIRES
        $commentaires = $faker->randomElement($commentaires);
        $commentaire = new Commentaire();
        $commentaire
            ->setCommentaire($faker->sentence(10))
            ->setNote($faker->numberBetween(1, 5))
            ->setDatePublication(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months')))
            ->setCommentaireUtilisateur($faker->randomElement($utilisateurs))
            ->setCommentaireReservation($faker->randomElement($reservations));

        $manager->persist($commentaire);


        // EQUIPEMENTS
        for ($i = 0; $i < 10; $i++) {
            $equipement = new Equipement();
            $equipement
                ->setNom($faker->word())
                ->setDescription($faker->sentence())
                ->addLogement($faker->randomElement($logements));

            $manager->persist($equipement);
        }

        //IMAGES

        for ($i = 0; $i < 20; $i++) {
            $image = new Image();
            $image
                ->setPath($faker->imageUrl(640, 480, 'house', true))
                ->setLogementImage($faker->randomElement($logements));

            $manager->persist($image);
        }

        //INDISPONIBILITES

        for ($i = 0; $i < 10; $i++) {
            $dateDebut = $faker->dateTimeBetween('now', '+2 months');
            $dateFin = (clone $dateDebut)->modify('+' . rand(1, 14) . ' days');

            $indispo = new Indisponibilite();
            $indispo
                ->setDateDebut($dateDebut)
                ->setDateFin($dateFin)
                ->setDescription($faker->sentence())
                ->setAnnonceIndisponibilite($faker->randomElement($annonces));

            $manager->persist($indispo);
        }

        // MESSAGES
        for ($i = 0; $i < 20; $i++) {
            $sender = $faker->randomElement($utilisateurs);
            $receiver = $faker->randomElement(array_filter($utilisateurs, fn($u) => $u !== $sender));

            $message = new Message();
            $message
                ->setContenu($faker->sentence(12))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setMessageSender($sender)
                ->setMessageReceiver($receiver);

            $manager->persist($message);
        }

        $manager->flush();
    }
}
