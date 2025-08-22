<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class MessagePostProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof Message) {
            return $data;
        }

        $user = $this->security->getUser();

        if (!$user) {
            throw new AccessDeniedHttpException('Vous devez être connecté pour envoyer un message.');
        }

        // Vérifier si quelqu’un a essayé de définir un autre sender
        if ($data->getMessageSender() !== null && $data->getMessageSender() !== $user) {
            throw new AccessDeniedHttpException('Vous ne pouvez pas définir un expéditeur différent de votre compte.');
        }

        // Forcer l’expéditeur
        $data->setMessageSender($user);

        // Forcer createdAt si absent
        if (!$data->getCreatedAt()) {
            $data->setCreatedAt(new \DateTimeImmutable());
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}
