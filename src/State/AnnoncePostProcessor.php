<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Annonce;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AnnoncePostProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Annonce
    {
        // Vérification de la cohérence des données
        if (!$data instanceof Annonce || null === $data->getAnnonceLogement()) {
            return $data;
        }

        // Récupération de l'utilisateur connecté
        $utilisateur = $this->security->getUser();
        if (!$utilisateur instanceof Utilisateur) {
            throw new AccessDeniedHttpException('Aucun utilisateur connecté.');
        }

        // Validation : un utilisateur ne peut créer une annonce que pour ses propres logements
        $proprietaire = $data->getAnnonceLogement()->getLogementUtilisateur();
        if ($proprietaire?->getId() !== $utilisateur->getId()) {
            throw new AccessDeniedHttpException('Vous ne pouvez créer une annonce que pour vos propres logements.');
        }

        // Donne le rôle PROPRIETAIRE si nécessaire
        if (!in_array('ROLE_PROPRIETAIRE', $utilisateur->getRoles(), true)) {
            $roles = $utilisateur->getRoles();
            $roles[] = 'ROLE_PROPRIETAIRE';
            $utilisateur->setRoles(array_unique($roles));
            $this->em->persist($utilisateur);
        }

        // Association de l’annonce à son créateur
        $data->setAnnonceUtilisateur($utilisateur);

        // Persistance en base
        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}
