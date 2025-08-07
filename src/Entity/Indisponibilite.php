<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\IndisponibiliteRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: IndisponibiliteRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['indisponibilite:read']],
    denormalizationContext: ['groups' => ['indisponibilite:write']],
    operations: [
        new Get(
            normalizationContext: ['groups' => ['indisponibilite:read']],
            security: "is_granted('ROLE_USER')"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['indisponibilite:list']],
            security: "is_granted('ROLE_USER')"
        ),
        new Post(
            denormalizationContext: ['groups' => ['indisponibilite:write']],
            security: "is_granted('ROLE_USER')"
        ),
        new Put(
            denormalizationContext: ['groups' => ['indisponibilite:write']],
            security: "is_granted('ROLE_USER')"
        ),
        new Delete(
            security: "is_granted('ROLE_USER')"
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'annonce_indisponibilite.id' => 'exact',
])]
#[ApiFilter(DateFilter::class, properties: ['dateDebut', 'dateFin'])]
#[ApiFilter(OrderFilter::class, properties: ['dateDebut'], arguments: ['orderParameterName' => 'order'])]
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
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    private ?\DateTime $dateDebut = null;

    #[ORM\Column]
    #[Groups(['indisponibilite:list', 'indisponibilite:read', 'indisponibilite:write'])]
    #[SerializedName('dateFin')]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
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
