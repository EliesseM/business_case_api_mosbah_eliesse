<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Commentaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CommentairePostProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof Commentaire) {
            return $data;
        }

        $user = $this->security->getUser();
        if (!$user) {
            throw new AccessDeniedHttpException('Vous devez être connecté pour publier un commentaire.');
        }

        // Vérifie si quelqu’un essaie d’usurper un autre utilisateur
        if ($data->getCommentaireUtilisateur() !== null && $data->getCommentaireUtilisateur() !== $user) {
            throw new AccessDeniedHttpException('Vous ne pouvez pas créer de commentaire au nom d’un autre utilisateur.');
        }

        // Force l’utilisateur connecté
        $data->setCommentaireUtilisateur($user);

        if (!$data->getDatePublication()) {
            $data->setDatePublication(new \DateTimeImmutable());
        }


        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}
