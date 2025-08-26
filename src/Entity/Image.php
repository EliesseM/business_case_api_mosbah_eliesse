<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\ImageRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['image:read']],
    denormalizationContext: ['groups' => ['image:write']],
    operations: [
        new Get(
            normalizationContext: ['groups' => ['image:read']],
            security: "is_granted('ROLE_USER')"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['image:list']],
            security: "is_granted('ROLE_USER')"
        ),
        new Post(
            denormalizationContext: ['groups' => ['image:write']],
            security: "is_granted('ROLE_USER') and object.getLogementImage().getLogementUtilisateur() == user"
        ),

        new Patch(
            security: "is_granted('ROLE_USER') and object.getLogementImage().getLogementUtilisateur() == user",
        ),
        new Put(
            security: "is_granted('ROLE_USER') and object.getLogementImage().getLogementUtilisateur() == user",
        ),
        new Delete(
            security: "is_granted('ROLE_USER') and object.getLogementImage().getLogementUtilisateur() == user",
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'annonceImage.id' => 'exact',
    'logementImage.id' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['id'], arguments: ['orderParameterName' => 'order'])]

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
    private ?Logement $logementImage = null;

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
        return $this->logementImage;
    }

    public function setLogementImage(?Logement $logementImage): static
    {
        $this->logementImage = $logementImage;
        return $this;
    }
}
