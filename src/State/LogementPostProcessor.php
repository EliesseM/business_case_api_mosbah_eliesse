<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Logement;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class LogementPostProcessor implements ProcessorInterface
{
    /**
     * Le constructeur reçoit 2 services :
     * - Security : permet de récupérer l’utilisateur connecté
     * - EntityManagerInterface : permet de gérer les entités (persist/flush)
     */
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em
    ) {}

    /**
     * Méthode obligatoire de l’interface ProcessorInterface.
     * Elle est appelée lors du traitement d’une opération API (ex: POST sur un logement).
     *
     * @param mixed $data Données envoyées par l’utilisateur (ici, un Logement normalement)
     * @param Operation $operation L’opération API en cours (POST, PATCH, etc.)
     * @param array $uriVariables Variables éventuelles d’URL (pas utilisé ici)
     * @param array $context Contexte de sérialisation/désérialisation
     *
     * @return Logement Retourne l’entité Logement traitée
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Logement
    {
        // Vérifie que les données correspondent bien à un Logement
        if (!$data instanceof Logement) {
            return $data; // Si ce n’est pas un Logement, on ne fait rien
        }

        // Récupère l’utilisateur actuellement connecté
        $utilisateur = $this->security->getUser();

        // Si aucun utilisateur connecté → interdit
        if (!$utilisateur instanceof Utilisateur) {
            throw new AccessDeniedHttpException('Aucun utilisateur connecté.');
        }

        // Vérifie si l’utilisateur a déjà le rôle "ROLE_PROPRIETAIRE"
        if (!in_array('ROLE_PROPRIETAIRE', $utilisateur->getRoles(), true)) {
            // Si non, on lui ajoute ce rôle
            $roles = $utilisateur->getRoles();
            $roles[] = 'ROLE_PROPRIETAIRE';
            $utilisateur->setRoles(array_unique($roles)); // array_unique = évite doublons
            $this->em->persist($utilisateur); // Enregistre la modif de l’utilisateur
        }

        // Associe le logement créé à l’utilisateur connecté
        $data->setLogementUtilisateur($utilisateur);

        // Persiste le logement en BDD
        $this->em->persist($data);
        $this->em->flush(); // Sauvegarde effective des données

        // Retourne l’entité Logement (mise à jour)
        return $data;
    }
}
