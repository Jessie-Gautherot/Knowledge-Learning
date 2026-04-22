<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Traits\TimestampableTrait;


/**
 * Class User
 *
 * This entity is responsible for:
 * - Authentication (email + password)
 * - Authorization (roles)
 * - Account verification (email validation)
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[UniqueEntity(fields: ['email'], message: 'cet Email est déja utilisé')]

#[ORM\HasLifecycleCallbacks] 

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableTrait;
    
    /**
     * Unique identifier of the user (Primary Key)
     *
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Name of the user
     *
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    /**
     * User email (used as login identifier)
     *
     * Must be unique in database.
     *
     * @var string|null
     */
    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    /**
     * Hashed password
     *
     * Plain password is validated before being hashed.
     *
     * @var string
     */
    #[ORM\Column]
    private string $password;

    /**
     * User roles 
     *
     * Example: ['ROLE_USER', 'ROLE_ADMIN']
     *
     * @var array
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * Indicates if the user has actived their email
     *
     * @var bool
     */
    #[ORM\Column]
    private bool $isActive = false;

    /**
     * Token used for email account activation
     *
     * @var string|null
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $activationToken = null;


     /**
     * User constructor
     *
     * Initializes default values.
     */
    public function __construct()
    {

        // Définit le rôle par défaut demandé dans le TP
        $this->roles = [];
    }

    /**
     * Returns the unique identifier of the user 
     *
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    

    /**
     * Removes sensitive temporary data
     *
     * Not used here but required by interface.
     *
     * @return void
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * Get user ID
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

     /**
     * Get user name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

     /**
     * Set user name
     *
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get user email
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set user email 
     *
     * @param string $email
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = strtolower($email);
        return $this;
    }

    /**
     * Get hashed password
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

     /**
     * Set hashed password
     *
     * @param string $password
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get user roles
     *
     * Ensures ROLE_USER is always present.
     *
     * @return array
     */
    public function getRoles(): array
    {
    $roles = $this->roles;
    $roles[] = 'ROLE_USER';

    return array_unique($roles);
    }

    /**
     * Set user roles
     *
     * @param array $roles
     * @return self
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * Check if user account is Actived
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

     /**
     * Set verification status
     *
     * @param bool $isActive
     * @return self
     */
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * Get activation token
     *
     * @return string|null
     */
    public function getActivationToken(): ?string
    {
        return $this->activationToken;
    }

    /**
     * Set activation token
     *
     * @param string|null $token
     * @return self
     */
    public function setActivationToken(?string $token): self
    {
        $this->activationToken = $token;
        return $this;
    }
    
}