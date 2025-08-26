<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\LogementRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\State\LogementPostProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LogementRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['logement:list']],
    denormalizationContext: ['groups' => ['logement:write']],
    operations: [
        new Get(
            normalizationContext: ['groups' => ['logement:read']],
            security: "is_granted('ROLE_USER') and object.getLogementUtilisateur() == user"
        ),
        new GetCollection(
            security: "is_granted('ROLE_USER')"
        ),
        new Post(
            processor: LogementPostProcessor::class,
            security: "is_granted('ROLE_USER')"

        ),
        new Patch(
            security: "is_granted('ROLE_USER') and object.getLogementUtilisateur() == user"
        ),
        new Put(
            security: "is_granted('ROLE_USER') and object.getLogementUtilisateur() == user"
        ),
        new Delete(
            security: "is_granted('ROLE_USER') and object.getLogementUtilisateur() == user"
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'type' => 'exact',
    'surface' => 'exact',
    'adresse.ville' => 'partial',
    'logement_utilisateur.id' => 'exact',
])]
#[ApiFilter(RangeFilter::class, properties: ['surface'])]
#[ApiFilter(OrderFilter::class, properties: ['surface'], arguments: ['orderParameterName' => 'order'])]

class Logement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['logement:list', 'logement:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['logement:list', 'logement:read', 'logement:write'])]
    private ?string $numeroRue = null;

    #[ORM\Column(length: 255)]
    #[Groups(['logement:list', 'logement:read', 'logement:write'])]
    private ?string $nomRue = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['logement:read', 'logement:write'])]
    private ?string $complementAdresse1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['logement:read', 'logement:write'])]
    private ?string $complementAdresse2 = null;

    #[ORM\Column(length: 255)]
    #[Groups(['logement:list', 'logement:read', 'logement:write'])]
    private ?string $ville = null;

    #[ORM\Column(length: 255)]
    #[Groups(['logement:list', 'logement:read', 'logement:write'])]
    private ?string $codePostal = null;

    #[ORM\Column(length: 255)]
    #[Groups(['logement:list', 'logement:read', 'logement:write'])]
    private ?string $pays = null;

    #[ORM\Column]
    #[Groups(['logement:read', 'logement:write'])]
    private ?float $longitude = null;

    #[ORM\Column]
    #[Groups(['logement:read', 'logement:write'])]
    private ?float $latitude = null;

    #[ORM\Column]
    #[Groups(['logement:list', 'logement:read', 'logement:write'])]
    private ?float $superficie = null;
    #[ORM\column(nullable: true)]
    #[ORM\ManyToMany(targetEntity: Equipement::class, inversedBy: 'logements')]
    #[Groups(['logement:read', 'logement:write'])]
    private Collection $equipements;

    #[ORM\column(nullable: true)]
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'logementImage', cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['logement:read', 'logement:write'])]
    private Collection $images;

    #[ORM\column(nullable: true)]
    #[ORM\OneToMany(targetEntity: Annonce::class, mappedBy: 'annonceLogement', cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['logement:read'])]
    private Collection $annonces;

    #[ORM\ManyToOne(inversedBy: 'logements')]
    #[Groups(['logement:read'])]
    private ?Utilisateur $logementUtilisateur = null;

    public function __construct()
    {
        $this->equipements = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->annonces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroRue(): ?string
    {
        return $this->numeroRue;
    }

    public function setNumeroRue(string $numeroRue): static
    {
        $this->numeroRue = $numeroRue;
        return $this;
    }

    public function getNomRue(): ?string
    {
        return $this->nomRue;
    }

    public function setNomRue(string $nomRue): static
    {
        $this->nomRue = $nomRue;
        return $this;
    }

    public function getComplementAdresse1(): ?string
    {
        return $this->complementAdresse1;
    }

    public function setComplementAdresse1(?string $complementAdresse1): static
    {
        $this->complementAdresse1 = $complementAdresse1;
        return $this;
    }

    public function getComplementAdresse2(): ?string
    {
        return $this->complementAdresse2;
    }

    public function setComplementAdresse2(?string $complementAdresse2): static
    {
        $this->complementAdresse2 = $complementAdresse2;
        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;
        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): static
    {
        $this->codePostal = $codePostal;
        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): static
    {
        $this->pays = $pays;
        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getSuperficie(): ?float
    {
        return $this->superficie;
    }

    public function setSuperficie(float $superficie): static
    {
        $this->superficie = $superficie;
        return $this;
    }

    public function getEquipements(): Collection
    {
        return $this->equipements;
    }

    public function addEquipement(Equipement $equipement): static
    {
        if (!$this->equipements->contains($equipement)) {
            $this->equipements->add($equipement);
        }
        return $this;
    }

    public function removeEquipement(Equipement $equipement): static
    {
        $this->equipements->removeElement($equipement);
        return $this;
    }

    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setLogementImage($this);
        }
        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            if ($image->getLogementImage() === $this) {
                $image->setLogementImage(null);
            }
        }
        return $this;
    }

    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonce(Annonce $annonce): static
    {
        if (!$this->annonces->contains($annonce)) {
            $this->annonces->add($annonce);
            $annonce->setAnnonceLogement($this);
        }
        return $this;
    }

    public function removeAnnonce(Annonce $annonce): static
    {
        if ($this->annonces->removeElement($annonce)) {
            if ($annonce->getAnnonceLogement() === $this) {
                $annonce->setAnnonceLogement(null);
            }
        }
        return $this;
    }

    public function getLogementUtilisateur(): ?Utilisateur
    {
        return $this->logementUtilisateur;
    }

    public function setLogementUtilisateur(?Utilisateur $logementUtilisateur): static
    {
        $this->logementUtilisateur = $logementUtilisateur;
        return $this;
    }
}
