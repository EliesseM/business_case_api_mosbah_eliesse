<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            name: 'reservations',
            uriTemplate: '/reservations',
            normalizationContext: ['groups' => ['reservation:read']],
        ),
        new Post(
            denormalizationContext: ['groups' => ['reservation:write']],
            normalizationContext: ['groups' => ['reservation:read']],
        ),
        new Patch(
            denormalizationContext: ['groups' => ['reservation:patch']],
            normalizationContext: ['groups' => ['reservation:read']],
        )
    ]
)]
class Reservation
{
    #[Groups(['reservation:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['reservation:read', 'reservation:write'])]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateDebut = null;

    #[Groups(['reservation:read', 'reservation:write'])]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateFin = null;

    #[Groups(['reservation:read', 'reservation:write'])]
    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[Groups(['reservation:read', 'reservation:write'])]
    #[ORM\Column]
    private ?float $prixTotal = null;

    #[Groups(['reservation:read'])]
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[Groups(['reservation:read', 'reservation:write'])]
    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Annonce $reservation_annonce = null;

    #[Groups(['reservation:read', 'reservation:write'])]
    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Utilisateur $reservation_utilisateur = null;

    // Pas besoin d'exposer les commentaires ici sauf si tu veux
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'commentaire_reservation')]
    private Collection $commentaires;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): ?\DateTime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTime $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTime $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPrixTotal(): ?float
    {
        return $this->prixTotal;
    }

    public function setPrixTotal(float $prixTotal): static
    {
        $this->prixTotal = $prixTotal;

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

    public function getReservationAnnonce(): ?Annonce
    {
        return $this->reservation_annonce;
    }

    public function setReservationAnnonce(?Annonce $reservation_annonce): static
    {
        $this->reservation_annonce = $reservation_annonce;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setCommentaireReservation($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getCommentaireReservation() === $this) {
                $commentaire->setCommentaireReservation(null);
            }
        }

        return $this;
    }

    public function getReservationUtilisateur(): ?Utilisateur
    {
        return $this->reservation_utilisateur;
    }

    public function setReservationUtilisateur(?Utilisateur $reservation_utilisateur): static
    {
        $this->reservation_utilisateur = $reservation_utilisateur;

        return $this;
    }
}
