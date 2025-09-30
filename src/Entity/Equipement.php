<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\EquipementRepository;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;



#[ORM\Entity(repositoryClass: EquipementRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['equipement:read']],
    denormalizationContext: ['groups' => ['equipement:write']],
    operations: [
        new \ApiPlatform\Metadata\Get(
            normalizationContext: ['groups' => ['equipement:read']],
            security: "is_granted('ROLE_USER')"
        ),
        new \ApiPlatform\Metadata\GetCollection(
            normalizationContext: ['groups' => ['equipement:list']],
            security: "is_granted('ROLE_USER')"
        ),
        new \ApiPlatform\Metadata\Post(
            denormalizationContext: ['groups' => ['equipement:write']],
            security: "is_granted('ROLE_ADMIN')"
        ),

        new \ApiPlatform\Metadata\Patch(
            denormalizationContext: ['groups' => ['equipement:write']],
            security: "is_granted('ROLE_ADMIN')"
        ),

        new \ApiPlatform\Metadata\Put(
            denormalizationContext: ['groups' => ['equipement:write']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new \ApiPlatform\Metadata\Delete(
            security: "is_granted('ROLE_ADMIN')"
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'nom' => 'partial',
    'logementEquipement.id' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['nom'], arguments: ['orderParameterName' => 'order'])]
class Equipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['equipement:list', 'equipement:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['equipement:list', 'equipement:read', 'equipement:write'])]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['equipement:read', 'equipement:write'])]
    private ?string $description = null;


    #[ORM\ManyToMany(targetEntity: Logement::class, mappedBy: 'equipements')]
    #[Groups(['equipement:read'])]
    private Collection $logements;

    public function __construct()
    {
        $this->logements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
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

    /**
     * @return Collection<int, Logement>
     */
    public function getLogements(): Collection
    {
        return $this->logements;
    }

    public function addLogement(Logement $logement): static
    {
        if (!$this->logements->contains($logement)) {
            $this->logements->add($logement);
            $logement->addEquipement($this);
        }
        return $this;
    }

    public function removeLogement(Logement $logement): static
    {
        if ($this->logements->removeElement($logement)) {
            $logement->removeEquipement($this);
        }
        return $this;
    }
}
