<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?Utilisateur $message_receiver = null;

    #[ORM\ManyToOne(inversedBy: 'messagesend')]
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
