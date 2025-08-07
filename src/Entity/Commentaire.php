<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Get;
use App\Repository\CommentaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['commentaire:read']],
    denormalizationContext: ['groups' => ['commentaire:write']],
    operations: [
        new Get(
            normalizationContext: ['groups' => ['commentaire:read']],
            security: "is_granted('ROLE_USER')"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['commentaire:list']],
            security: "is_granted('ROLE_USER')"
        ),
        new Post(
            denormalizationContext: ['groups' => ['commentaire:write']],
            security: "is_granted('ROLE_USER')"
        ),
        new Put(
            denormalizationContext: ['groups' => ['commentaire:write']],
            security: "is_granted('ROLE_USER')"
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN') or object.getCommentaireUtilisateur() == user"
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'note' => 'exact',
    'commentaire_utilisateur.id' => 'exact',
    'commentaire_reservation.id' => 'exact',
])]
#[ApiFilter(DateFilter::class, properties: ['datePublication'])]
#[ApiFilter(OrderFilter::class, properties: ['note', 'datePublication'], arguments: ['orderParameterName' => 'order'])]

class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['commentaire:list', 'commentaire:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['commentaire:list', 'commentaire:read', 'commentaire:write'])]
    private ?int $note = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['commentaire:read', 'commentaire:write'])]
    private ?string $commentaire = null;

    #[ORM\Column]
    #[Groups(['commentaire:read', 'commentaire:write'])]
    #[SerializedName('datePublication')]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd/m/Y'])]
    private ?\DateTime $datePublication = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    #[Groups(['commentaire:read', 'commentaire:write'])]
    private ?Utilisateur $commentaire_utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    #[Groups(['commentaire:read', 'commentaire:write'])]
    private ?Reservation $commentaire_reservation = null;

    // Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getDatePublication(): ?\DateTime
    {
        return $this->datePublication;
    }

    public function setDatePublication(\DateTime $datePublication): static
    {
        $this->datePublication = $datePublication;
        return $this;
    }

    public function getCommentaireUtilisateur(): ?Utilisateur
    {
        return $this->commentaire_utilisateur;
    }

    public function setCommentaireUtilisateur(?Utilisateur $commentaire_utilisateur): static
    {
        $this->commentaire_utilisateur = $commentaire_utilisateur;
        return $this;
    }

    public function getCommentaireReservation(): ?Reservation
    {
        return $this->commentaire_reservation;
    }

    public function setCommentaireReservation(?Reservation $commentaire_reservation): static
    {
        $this->commentaire_reservation = $commentaire_reservation;
        return $this;
    }
}
