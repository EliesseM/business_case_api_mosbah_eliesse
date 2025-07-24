<?php

namespace App\Entity;

use App\Repository\AnnonceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnonceRepository::class)]
class Annonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $prixJournee = null;

    #[ORM\Column]
    private ?int $nbPlaces = null;

    #[ORM\Column]
    private ?bool $mixte = null;

    /**
     * @var Collection<int, Service>
     */
    #[ORM\ManyToMany(targetEntity: Service::class, inversedBy: 'annonces')]
    private Collection $services;

    /**
     * @var Collection<int, Indisponibilite>
     */
    #[ORM\OneToMany(targetEntity: Indisponibilite::class, mappedBy: 'annonce_indisponibilite')]
    private Collection $indisponibilites;

    #[ORM\ManyToOne(inversedBy: 'annonces')]
    private ?Logement $annonce_logement = null;

    #[ORM\ManyToOne(inversedBy: 'annonces')]
    private ?Utilisateur $annonce_utilisateur = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'reservation_annonce')]
    private Collection $reservations;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->indisponibilites = new ArrayCollection();
        $this->reservations = new ArrayCollection();
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

    /**
     * @return Collection<int, Service>
     */
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

    /**
     * @return Collection<int, Indisponibilite>
     */
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
            // set the owning side to null (unless already changed)
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

    /**
     * @return Collection<int, Reservation>
     */
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
            // set the owning side to null (unless already changed)
            if ($reservation->getReservationAnnonce() === $this) {
                $reservation->setReservationAnnonce(null);
            }
        }

        return $this;
    }
}
