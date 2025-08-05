<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['commentaire:list']],
    denormalizationContext: ['groups' => ['commentaire:write']],
    operations: [
        new \ApiPlatform\Metadata\Get(normalizationContext: ['groups' => ['commentaire:read']]),
        new \ApiPlatform\Metadata\GetCollection(),
        new \ApiPlatform\Metadata\Post(),
        new \ApiPlatform\Metadata\Put(),
        new \ApiPlatform\Metadata\Delete(),
    ]
)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['commentaire:list', 'commentaire:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['commentaire:list', 'commentaire:read', 'commentaire:write'])]
    private ?int $note = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['commentaire:read', 'commentaire:write'])]
    private ?string $commentaire = null;

    #[ORM\Column]
    #[Groups(['commentaire:read', 'commentaire:write'])]
    #[SerializedName('datePublication')]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd/m/Y'])]
    private ?\DateTime $datePublication = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    #[Groups(['commentaire:read', 'commentaire:write'])]
    private ?Utilisateur $commentaire_utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    #[Groups(['commentaire:read', 'commentaire:write'])]
    private ?Reservation $commentaire_reservation = null;

    // Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getDatePublication(): ?\DateTime
    {
        return $this->datePublication;
    }

    public function setDatePublication(\DateTime $datePublication): static
    {
        $this->datePublication = $datePublication;
        return $this;
    }

    public function getCommentaireUtilisateur(): ?Utilisateur
    {
        return $this->commentaire_utilisateur;
    }

    public function setCommentaireUtilisateur(?Utilisateur $commentaire_utilisateur): static
    {
        $this->commentaire_utilisateur = $commentaire_utilisateur;
        return $this;
    }

    public function getCommentaireReservation(): ?Reservation
    {
        return $this->commentaire_reservation;
    }

    public function setCommentaireReservation(?Reservation $commentaire_reservation): static
    {
        $this->commentaire_reservation = $commentaire_reservation;
        return $this;
    }
}
