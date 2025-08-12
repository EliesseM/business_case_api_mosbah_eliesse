<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Put;
use App\Repository\UtilisateurRepository;
use App\State\UtilisateurPasswordHasherProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity('email', message: 'Cet email est déjà utilisé')]
#[ApiResource(
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']],
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['user:read', 'user:list']],
            paginationEnabled: true,
            paginationItemsPerPage: 10
        ),
        new Get(
            security: "is_granted('ROLE_ADMIN') or object == user",
            normalizationContext: ['groups' => ['user:read', 'user:item']]
        ),
        new Post(
            name: 'create_user',
            security: "is_granted('PUBLIC_ACCESS')",
            denormalizationContext: ['groups' => ['user:create']],
            processor: UtilisateurPasswordHasherProcessor::class
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN') or object == user",
            denormalizationContext: ['groups' => ['user:update']]
        ),
        new Put(
            security: "is_granted('ROLE_ADMIN') or object == user",
            denormalizationContext: ['groups' => ['user:update']]
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')"
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'email' => 'partial',
    'nom' => 'partial',
    'prenom' => 'partial',
    'roles' => 'partial',
])]
#[ApiFilter(OrderFilter::class, properties: ['email', 'nom'], arguments: ['orderParameterName' => 'order'])]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private array $roles = [];

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['user:read', 'user:create', 'user:write'])]
    #[Assert\NotBlank]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:create', 'user:write'])]
    private ?string $genre = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:create', 'user:write'])]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:create', 'user:write'])]
    #[Assert\NotBlank]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['user:read', 'user:create', 'user:write'])]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:create', 'user:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    private ?string $password = null;

    #[ORM\Column(type: 'datetime')]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd/m/Y'])]
    #[Groups(['user:read', 'user:create', 'user:write'])]
    #[Assert\NotNull]
    #[Assert\LessThan('today')]
    private ?\DateTime $dateNaissance = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd/m/Y'])]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private bool $isVerified = false;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:create', 'user:write'])]
    private ?string $profilPicture = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:create', 'user:write'])]
    private ?string $billingAddress = null;

    #[ORM\OneToMany(targetEntity: Annonce::class, mappedBy: 'annonceUtilisateur')]
    private Collection $annonces;

    #[ORM\OneToMany(targetEntity: Logement::class, mappedBy: 'logementUtilisateur')]
    private Collection $logements;

    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'commentaireUtilisateur')]
    private Collection $commentaires;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'messageReceiver')]
    private Collection $messagesReceived;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'messageSender')]
    private Collection $messagesSent;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'utilisateur')]
    private Collection $reservations;

    public function __construct()
    {
        $this->annonces = new ArrayCollection();
        $this->logements = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->messagesReceived = new ArrayCollection();
        $this->messagesSent = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getDateNaissance(): ?\DateTime
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTime $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getProfilPicture(): ?string
    {
        return $this->profilPicture;
    }

    public function setProfilPicture(?string $profilPicture): self
    {
        $this->profilPicture = $profilPicture;
        return $this;
    }

    public function getBillingAddress(): ?string
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?string $billingAddress): self
    {
        $this->billingAddress = $billingAddress;
        return $this;
    }

    /** @return Collection<int, Annonce> */
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    /** @return Collection<int, Logement> */
    public function getLogements(): Collection
    {
        return $this->logements;
    }

    /** @return Collection<int, Commentaire> */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    /** @return Collection<int, Message> */
    public function getMessagesReceived(): Collection
    {
        return $this->messagesReceived;
    }

    /** @return Collection<int, Message> */
    public function getMessagesSent(): Collection
    {
        return $this->messagesSent;
    }

    /** @return Collection<int, Reservation> */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function eraseCredentials(): void {}
}
