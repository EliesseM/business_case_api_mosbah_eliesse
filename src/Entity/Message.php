<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['message:read']],
    denormalizationContext: ['groups' => ['message:write']],
    operations: [
        new Get(security: "is_granted('ROLE_USER') and (object.getMessageSender() == user or object.getMessageReceiver() == user)", securityMessage: "Vous ne pouvez voir que vos messages."),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(
            normalizationContext: ['groups' => ['message:read:item']],
            denormalizationContext: ['groups' => ['message:write']],
            security: "is_granted('ROLE_USER')"
        ),

        new Put(security: "is_granted('ROLE_USER') and object.getMessageSender() == user", securityMessage: "Vous ne pouvez modifier que vos propres messages."),
        new Delete(security: "is_granted('ROLE_USER') and (object.getMessageSender() == user or object.getMessageReceiver() == user)", securityMessage: "Vous ne pouvez supprimer que vos propres messages.")
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'messageSender.id' => 'exact',
    'messageReceiver.id' => 'exact',
    'contenu' => 'partial',
])]
#[ApiFilter(DateFilter::class, properties: ['createdAt'])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt'], arguments: ['orderParameterName' => 'order'])]
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
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd/m/Y'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'messagesReceived')]
    #[Groups(['message:read', 'message:write'])]
    private ?Utilisateur $messageReceiver = null;

    #[ORM\ManyToOne(inversedBy: 'messagesSent')]
    #[Groups(['message:read', 'message:write'])]
    private ?Utilisateur $messageSender = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

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
        return $this->messageReceiver;
    }

    public function setMessageReceiver(?Utilisateur $messageReceiver): static
    {
        $this->messageReceiver = $messageReceiver;
        return $this;
    }

    public function getMessageSender(): ?Utilisateur
    {
        return $this->messageSender;
    }

    public function setMessageSender(?Utilisateur $messageSender): static
    {
        $this->messageSender = $messageSender;
        return $this;
    }
}
