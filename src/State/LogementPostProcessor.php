<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Image;
use App\Entity\Logement;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class LogementPostProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Logement
    {
        if (!$data instanceof Logement) {
            return $data;
        }

        $utilisateur = $this->security->getUser();
        if (!$utilisateur instanceof Utilisateur) {
            throw new AccessDeniedHttpException('Aucun utilisateur connecté.');
        }

        if (!in_array('ROLE_PROPRIETAIRE', $utilisateur->getRoles(), true)) {
            $roles = $utilisateur->getRoles();
            $roles[] = 'ROLE_PROPRIETAIRE';
            $utilisateur->setRoles(array_unique($roles));
            $this->em->persist($utilisateur);
        }

        // Assigne le logement à l'utilisateur connecté (propriétaire)
        $data->setLogementUtilisateur($utilisateur);


        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}
