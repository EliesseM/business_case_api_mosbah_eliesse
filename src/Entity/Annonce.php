<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\AnnonceRepository;
use App\State\AnnoncePostProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AnnonceRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['annonce:read']],
    denormalizationContext: ['groups' => ['annonce:write']],
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(
            processor: AnnoncePostProcessor::class,
            security: "is_granted('ROLE_USER')"
        ),
        new Patch(
            security: "is_granted('ROLE_USER') and object.getAnnonceUtilisateur() == user",
            securityMessage: "Vous ne pouvez modifier que vos propres annonces."
        ),
        new Put(
            security: "is_granted('ROLE_USER') and object.getAnnonceUtilisateur() == user",
            securityMessage: "Vous ne pouvez modifier que vos propres annonces."
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN') or object.getAnnonceUtilisateur() == user",
            securityMessage: "Vous ne pouvez supprimer que vos propres annonces."
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'titre' => 'partial',
    'description' => 'partial',
    'mixte' => 'exact',
    'annonceUtilisateur.id' => 'exact',
    'annonceLogement.id' => 'exact',
    'services.nom' => 'partial'
])]
#[ApiFilter(RangeFilter::class, properties: ['prixJournee', 'nbPlaces'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'updatedAt'])]
#[ApiFilter(OrderFilter::class, properties: ['prixJournee', 'createdAt'], arguments: ['orderParameterName' => 'order'])]
class Annonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['annonce:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['annonce:read', 'annonce:write'])]
    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['annonce:read', 'annonce:write'])]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['annonce:read', 'annonce:write'])]
    #[Assert\Positive(message: "Le prix doit être positif.")]
    private ?float $prixJournee = null;

    #[ORM\Column]
    #[Groups(['annonce:read', 'annonce:write'])]
    #[Assert\Range(
        min: 1,
        max: 10,
        notInRangeMessage: "Le nombre de places doit être entre {{ min }} et {{ max }}."
    )]
    private ?int $nbPlaces = null;

    #[ORM\Column]
    #[Groups(['annonce:read', 'annonce:write'])]
    private ?bool $mixte = null;

    #[ORM\ManyToMany(targetEntity: Service::class, inversedBy: 'annonces')]
    #[Groups(['annonce:read', 'annonce:write'])]
    private Collection $services;

    #[ORM\OneToMany(targetEntity: Indisponibilite::class, mappedBy: 'annonceIndisponibilite')]
    #[Groups(['annonce:read'])]
    private Collection $indisponibilites;

    #[ORM\ManyToOne(inversedBy: 'annonces')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['annonce:read', 'annonce:write'])]
    private ?Logement $annonceLogement = null;

    #[ORM\ManyToOne(inversedBy: 'annonces')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['annonce:read'])]
    private ?Utilisateur $annonceUtilisateur = null;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'reservationAnnonce')]
    #[Groups(['annonce:read'])]
    private Collection $reservations;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    #[Groups(['annonce:read'])]
    private ?string $slug = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['annonce:read'])]
    private bool $isPublished = false;

    #[ORM\Column(nullable: true)]
    #[Groups(['annonce:read', 'annonce:write'])]
    private ?string $imageUrl = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['annonce:read'])]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['annonce:read'])]
    private ?\DateTime $updatedAt = null;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->indisponibilites = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

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
            $service->addAnnonce($this); // Relation inverse si implémentée
        }
        return $this;
    }
    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            $service->removeAnnonce($this); // Relation inverse si implémentée
        }
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
        return $this->annonceLogement;
    }
    public function setAnnonceLogement(?Logement $annonceLogement): static
    {
        $this->annonceLogement = $annonceLogement;
        return $this;
    }
    public function getAnnonceUtilisateur(): ?Utilisateur
    {
        return $this->annonceUtilisateur;
    }
    public function setAnnonceUtilisateur(?Utilisateur $annonceUtilisateur): static
    {
        $this->annonceUtilisateur = $annonceUtilisateur;
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
    public function isPublished(): bool
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
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }
    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }
    public function setUpdatedAt(\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
