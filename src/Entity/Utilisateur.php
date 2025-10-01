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
// Indique que cette classe est une entité Doctrine (mappée à une table en BDD)
// et précise le repository associé (ici UtilisateurRepository).

#[UniqueEntity('email', message: 'Cet email est déjà utilisé')]
// Validation : impose que l’email soit unique dans la base.
// Si doublon → message d’erreur personnalisé.

#[ApiResource(
    normalizationContext: ['groups' => ['user:read']], // Détermine les groupes de sérialisation pour lecture
    denormalizationContext: ['groups' => ['user:write']], // Groupes pour désérialisation (écriture)
    operations: [
        // -------------------- GET COLLECTION --------------------
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')", // Seuls les admins peuvent voir la liste complète
            normalizationContext: ['groups' => ['user:read', 'user:list']], // Sérialisation spéciale pour liste
            paginationEnabled: true, // Active la pagination
            paginationItemsPerPage: 10 // Limite 10 utilisateurs par page
        ),
        // -------------------- GET ITEM --------------------
        new Get(
            security: "is_granted('ROLE_ADMIN') or object == user",
            // Autorisation : admin OU le propriétaire de l’objet
            normalizationContext: ['groups' => ['user:read', 'user:item']] // Sérialisation détaillée pour un utilisateur
        ),
        // -------------------- POST (CREATION) --------------------
        new Post(
            name: 'create_user', // Nom symbolique de l’opération
            security: "is_granted('PUBLIC_ACCESS')", // Accessible publiquement (ex: inscription)
            denormalizationContext: ['groups' => ['user:create']], // Champs utilisables à la création
            processor: UtilisateurPasswordHasherProcessor::class
            // Classe qui hache le mot de passe avant l’insertion
        ),
        // -------------------- PATCH --------------------
        new Patch(
            security: "is_granted('ROLE_ADMIN') or object == user", // Admin ou utilisateur lui-même
            denormalizationContext: ['groups' => ['user:update']] // Champs modifiables
        ),
        // -------------------- PUT --------------------
        new Put(
            security: "is_granted('ROLE_ADMIN') or object == user",
            denormalizationContext: ['groups' => ['user:update']]
        ),
        // -------------------- DELETE --------------------
        new Delete(
            security: "is_granted('ROLE_ADMIN')" // Seul un admin peut supprimer un compte
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'email' => 'partial',
    'nom' => 'partial',
    'prenom' => 'partial',
    'roles' => 'partial',
])]
// Ajoute des filtres de recherche partielle (LIKE %motcle%) pour certains champs.

#[ApiFilter(OrderFilter::class, properties: ['email', 'nom'], arguments: ['orderParameterName' => 'order'])]
// Permet d’ordonner les résultats par email ou nom via un paramètre "order".

class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    // ---------------------------
    // PROPRIÉTÉS DE BASE
    // ---------------------------

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;
    // Identifiant auto-incrémenté unique de l’utilisateur.

    #[ORM\Column]
    #[Groups(['user:read'])]
    private array $roles = [];
    // Tableau des rôles (par défaut ROLE_USER sera ajouté automatiquement).

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['user:read', 'user:create', 'user:write'])]
    #[Assert\NotBlank]
    private ?string $username = null;
    // Pseudonyme unique, obligatoire.

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:create', 'user:write'])]
    private ?string $genre = null;
    // Genre de l’utilisateur (H/F/Autre).

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:create', 'user:write'])]
    #[Assert\NotBlank]
    private ?string $nom = null;
    // Nom de famille, obligatoire.

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:create', 'user:write'])]
    #[Assert\NotBlank]
    private ?string $prenom = null;
    // Prénom, obligatoire.

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['user:read', 'user:create', 'user:write'])]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;
    // Email unique, obligatoire, doit être un email valide.

    #[ORM\Column(length: 255)]
    #[Groups(['user:create', 'user:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    private ?string $password = null;
    // Mot de passe (haché par le processor), obligatoire et ≥ 8 caractères.

    #[ORM\Column(type: 'datetime')]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd/m/Y'])]
    #[Groups(['user:read', 'user:create', 'user:write'])]
    #[Assert\NotNull]
    #[Assert\LessThan('today')]
    private ?\DateTime $dateNaissance = null;
    // Date de naissance (format JJ/MM/AAAA), obligatoire et doit être passée.

    #[ORM\Column(type: 'datetime_immutable')]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd/m/Y'])]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $createdAt = null;
    // Date de création automatique de l’utilisateur.

    #[ORM\Column]
    #[Groups(['user:read'])]
    private bool $isVerified = false;
    // Indique si le compte est vérifié (ex: par mail).

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:create', 'user:write'])]
    private ?string $profilPicture = null;
    // URL ou chemin vers l’image de profil.

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:create', 'user:write'])]
    private ?string $billingAddress = null;
    // Adresse de facturation éventuelle.

    // ---------------------------
    // RELATIONS DOCTRINE
    // ---------------------------

    #[ORM\OneToMany(targetEntity: Annonce::class, mappedBy: 'annonceUtilisateur')]
    private Collection $annonces; // Liste des annonces créées par l’utilisateur.

    #[ORM\OneToMany(targetEntity: Logement::class, mappedBy: 'logementUtilisateur')]
    private Collection $logements; // Liste des logements liés.

    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'commentaireUtilisateur')]
    private Collection $commentaires; // Commentaires laissés par l’utilisateur.

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'messageReceiver')]
    private Collection $messagesReceived; // Messages reçus par l’utilisateur.

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'messageSender')]
    private Collection $messagesSent; // Messages envoyés.

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'utilisateur')]
    private Collection $reservations; // Réservations associées.

    // ---------------------------
    // CONSTRUCTEUR
    // ---------------------------
    public function __construct()
    {
        // Initialise toutes les collections Doctrine comme des ArrayCollection
        $this->annonces = new ArrayCollection();
        $this->logements = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->messagesReceived = new ArrayCollection();
        $this->messagesSent = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable(); // Date de création définie automatiquement
    }

    // ---------------------------
    // GETTERS / SETTERS
    // ---------------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // Ajoute par défaut "ROLE_USER" à chaque compte
        return array_unique($roles); // Supprime les doublons éventuels
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
    // Méthode imposée par Symfony pour identifier l’utilisateur.

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

    // Méthode imposée par l’interface UserInterface.
    // Utilisée pour supprimer des données sensibles temporaires.
    public function eraseCredentials(): void {}
}
