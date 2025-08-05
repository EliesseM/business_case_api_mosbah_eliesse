<?php

namespace App\Entity;

use App\Repository\IndisponibiliteRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: IndisponibiliteRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['indisponibilite:list']],
    denormalizationContext: ['groups' => ['indisponibilite:write']],
    operations: [
        new Get(normalizationContext: ['groups' => ['indisponibilite:read']]),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete()
    ]
)]
class Indisponibilite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['indisponibilite:list', 'indisponibilite:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['indisponibilite:list', 'indisponibilite:read', 'indisponibilite:write'])]
    #[SerializedName('dateDebut')]
    private ?\DateTime $dateDebut = null;

    #[ORM\Column]
    #[Groups(['indisponibilite:list', 'indisponibilite:read', 'indisponibilite:write'])]
    #[SerializedName('dateFin')]
    private ?\DateTime $dateFin = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['indisponibilite:list', 'indisponibilite:read', 'indisponibilite:write'])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'indisponibilites')]
    #[Groups(['indisponibilite:read', 'indisponibilite:write'])]
    private ?Annonce $annonce_indisponibilite = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAnnonceIndisponibilite(): ?Annonce
    {
        return $this->annonce_indisponibilite;
    }

    public function setAnnonceIndisponibilite(?Annonce $annonce_indisponibilite): static
    {
        $this->annonce_indisponibilite = $annonce_indisponibilite;

        return $this;
    }
}
