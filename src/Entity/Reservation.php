<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['reservation:read']],
    denormalizationContext: ['groups' => ['reservation:write']],
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_USER')"
        ),
        new Get(
            security: "is_granted('ROLE_USER') and object.getReservationUtilisateur() == user"
        ),
        new Post(
            security: "is_granted('ROLE_USER')"
        ),
        new Patch(
            denormalizationContext: ['groups' => ['reservation:patch']],
            security: "is_granted('ROLE_ADMIN') or object.getReservationUtilisateur() == user"
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'status' => 'exact',
    'reservationUtilisateur.id' => 'exact',
    'reservationAnnonce.id' => 'exact',
])]
#[ApiFilter(DateFilter::class, properties: ['dateDebut', 'dateFin'])]
#[ApiFilter(OrderFilter::class, properties: ['dateDebut', 'dateFin'], arguments: ['orderParameterName' => 'order'])]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['reservation:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['reservation:read', 'reservation:write'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd/m/Y'])]
    private ?\DateTime $dateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['reservation:read', 'reservation:write'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd/m/Y'])]
    private ?\DateTime $dateFin = null;

    #[ORM\Column(length: 255)]
    #[Groups(['reservation:read', 'reservation:write', 'reservation:patch'])]
    private ?string $status = null;

    #[ORM\Column]
    #[Groups(['reservation:read', 'reservation:write'])]
    private ?float $prixTotal = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['reservation:read'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd/m/Y'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[Groups(['reservation:read', 'reservation:write'])]
    private ?Annonce $reservationAnnonce = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[Groups(['reservation:read', 'reservation:write'])]
    private ?Utilisateur $reservationUtilisateur = null;

    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'commentaireReservation', cascade: ['persist', 'remove'])]
    #[Groups(['reservation:read'])]
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

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
        return $this;
    }

    public function getReservationAnnonce(): ?Annonce
    {
        return $this->reservationAnnonce;
    }

    public function setReservationAnnonce(?Annonce $reservationAnnonce): static
    {
        $this->reservationAnnonce = $reservationAnnonce;
        return $this;
    }

    public function getReservationUtilisateur(): ?Utilisateur
    {
        return $this->reservationUtilisateur;
    }

    public function setReservationUtilisateur(?Utilisateur $reservationUtilisateur): static
    {
        $this->reservationUtilisateur = $reservationUtilisateur;
        return $this;
    }

    /** @return Collection<int, Commentaire> */
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
            if ($commentaire->getCommentaireReservation() === $this) {
                $commentaire->setCommentaireReservation(null);
            }
        }
        return $this;
    }
}
