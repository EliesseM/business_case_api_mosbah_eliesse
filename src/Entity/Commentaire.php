<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $note = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $commentaire = null;

    #[ORM\Column]
    private ?\DateTime $datePublication = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    private ?Utilisateur $commentaire_utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    private ?Reservation $commentaire_reservation = null;

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
