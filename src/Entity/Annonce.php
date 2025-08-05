<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AnnonceRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;



#[ORM\Entity(repositoryClass: AnnonceRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['annonce:list']],
    denormalizationContext: ['groups' => ['annonce:write']],
    operations: [
        new \ApiPlatform\Metadata\Get(normalizationContext: ['groups' => ['annonce:read']]),
        new \ApiPlatform\Metadata\GetCollection(),
        new \ApiPlatform\Metadata\Post(),
        new \ApiPlatform\Metadata\Put(),
        new \ApiPlatform\Metadata\Delete(),
    ]
)]
class Annonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['annonce:list', 'annonce:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['annonce:list', 'annonce:read', 'annonce:write'])]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['annonce:read', 'annonce:write'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['annonce:list', 'annonce:read', 'annonce:write'])]
    private ?float $prixJournee = null;

    #[ORM\Column]
    #[Groups(['annonce:read', 'annonce:write'])]
    private ?int $nbPlaces = null;

    #[ORM\Column]
    #[Groups(['annonce:read', 'annonce:write'])]
    private ?bool $mixte = null;

    #[ORM\ManyToMany(targetEntity: Service::class, inversedBy: 'annonces')]
    #[Groups(['annonce:read', 'annonce:write'])]
    private Collection $services;

    #[ORM\OneToMany(targetEntity: Indisponibilite::class, mappedBy: 'annonce_indisponibilite')]
    #[Groups(['annonce:read'])]
    private Collection $indisponibilites;

    #[ORM\ManyToOne(inversedBy: 'annonces')]
    #[Groups(['annonce:read', 'annonce:write'])]
    private ?Logement $annonce_logement = null;

    #[ORM\ManyToOne(inversedBy: 'annonces')]
    #[Groups(['annonce:read', 'annonce:write'])]
    private ?Utilisateur $annonce_utilisateur = null;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'reservation_annonce')]
    #[Groups(['annonce:read'])]
    private Collection $reservations;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    #[Groups(['annonce:read'])]
    private ?string $slug = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['annonce:read', 'annonce:write'])]
    private ?bool $isPublished = false;

    #[ORM\Column(nullable: true)]
    #[Groups(['annonce:read', 'annonce:write'])]
    private ?string $imageUrl = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['annonce:read'])]
    #[SerializedName('createdAt')]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd/m/Y'])]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['annonce:read'])]
    #[SerializedName('updatedAt')]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd/m/Y'])]
    private ?\DateTime $updatedAt = null;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->indisponibilites = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    // --- Getters & Setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPrixJournee(): ?float
    {
        return $this->prixJournee;
    }

    public function setPrixJournee(float $prixJournee): static
    {
        $this->prixJournee = $prixJournee;
        return $this;
    }

    public function getNbPlaces(): ?int
    {
        return $this->nbPlaces;
    }

    public function setNbPlaces(int $nbPlaces): static
    {
        $this->nbPlaces = $nbPlaces;
        return $this;
    }

    public function isMixte(): ?bool
    {
        return $this->mixte;
    }

    public function setMixte(bool $mixte): static
    {
        $this->mixte = $mixte;
        return $this;
    }

    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
        }
        return $this;
    }

    public function removeService(Service $service): static
    {
        $this->services->removeElement($service);
        return $this;
    }

    public function getIndisponibilites(): Collection
    {
        return $this->indisponibilites;
    }

    public function addIndisponibilite(Indisponibilite $indisponibilite): static
    {
        if (!$this->indisponibilites->contains($indisponibilite)) {
            $this->indisponibilites->add($indisponibilite);
            $indisponibilite->setAnnonceIndisponibilite($this);
        }
        return $this;
    }

    public function removeIndisponibilite(Indisponibilite $indisponibilite): static
    {
        if ($this->indisponibilites->removeElement($indisponibilite)) {
            if ($indisponibilite->getAnnonceIndisponibilite() === $this) {
                $indisponibilite->setAnnonceIndisponibilite(null);
            }
        }
        return $this;
    }

    public function getAnnonceLogement(): ?Logement
    {
        return $this->annonce_logement;
    }

    public function setAnnonceLogement(?Logement $annonce_logement): static
    {
        $this->annonce_logement = $annonce_logement;
        return $this;
    }

    public function getAnnonceUtilisateur(): ?Utilisateur
    {
        return $this->annonce_utilisateur;
    }

    public function setAnnonceUtilisateur(?Utilisateur $annonce_utilisateur): static
    {
        $this->annonce_utilisateur = $annonce_utilisateur;
        return $this;
    }

    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setReservationAnnonce($this);
        }
        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getReservationAnnonce() === $this) {
                $reservation->setReservationAnnonce(null);
            }
        }
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): static
    {
        $this->isPublished = $isPublished;
        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    #[Groups(['read', 'user:read', 'annonce:read'])] // adapte les groupes selon ton API
    public function getCreatedAtFormatted(): ?string
    {
        return $this->createdAt?->format('Y-m-d H:i:s');
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAtFormatted(): ?string
    {
        return $this->updatedAt->format('d/m/Y');
    }

    public function setUpdatedAt(\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
