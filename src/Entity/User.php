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
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    /**
     * Hashed password
     *
     * Plain password is hashed.
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
     * Indicates if the user has activated is account
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

        $this->roles = ['ROLE_CLIENT'];
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * Removes sensitive temporary data
     *
     * @return void
     */
    public function eraseCredentials(): void
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = strtolower($email);
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

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