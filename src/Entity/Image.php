<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['image:list']],
    denormalizationContext: ['groups' => ['image:write']],
    operations: [
        new Get(normalizationContext: ['groups' => ['image:read']]),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete()
    ]
)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['image:list', 'image:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['image:list', 'image:read', 'image:write'])]
    private ?string $path = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[Groups(['image:read', 'image:write'])]
    private ?Logement $logement_image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;
        return $this;
    }

    public function getLogementImage(): ?Logement
    {
        return $this->logement_image;
    }

    public function setLogementImage(?Logement $logement_image): static
    {
        $this->logement_image = $logement_image;
        return $this;
    }
}
