<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['image:read']],
    denormalizationContext: ['groups' => ['image:write']],
    operations: [
        new Get(
            normalizationContext: ['groups' => ['image:read']],
            security: "is_granted('ROLE_USER')"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['image:list']],
            security: "is_granted('ROLE_USER')"
        ),
        new Post(
            denormalizationContext: ['groups' => ['image:write']],
            security: "is_granted('ROLE_USER')"
        ),
        new Put(
            denormalizationContext: ['groups' => ['image:write']],
            security: "is_granted('ROLE_USER')"
        ),
        new Delete(
            security: "is_granted('ROLE_USER')"
        )
    ]
)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['message:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['message:read', 'message:write'])]
    private ?string $contenu = null;

    #[ORM\Column]
    #[Groups(['message:read'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[Groups(['message:read', 'message:write'])]
    private ?Utilisateur $message_receiver = null;

    #[ORM\ManyToOne(inversedBy: 'messagesend')]
    #[Groups(['message:read', 'message:write'])]
    private ?Utilisateur $message_sender = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getMessageReceiver(): ?Utilisateur
    {
        return $this->message_receiver;
    }

    public function setMessageReceiver(?Utilisateur $message_receiver): static
    {
        $this->message_receiver = $message_receiver;

        return $this;
    }

    public function getMessageSender(): ?Utilisateur
    {
        return $this->message_sender;
    }

    public function setMessageSender(?Utilisateur $message_sender): static
    {
        $this->message_sender = $message_sender;

        return $this;
    }
}
