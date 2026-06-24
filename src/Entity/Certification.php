<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use App\Entity\Traits\BlameableTrait;

/**
 * Class Certification
 *
 * Represents a certification obtained by a user for a theme.
 */
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Certification implements BlameableInterface
{
    use TimestampableTrait;
    use BlameableTrait;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    // User who owns the certification
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    // Theme associated to the certification
    #[ORM\ManyToOne(targetEntity: Theme::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Theme $theme;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getTheme(): Theme
    {
        return $this->theme;
    }

    public function setTheme(Theme $theme): self
    {
        $this->theme = $theme;
        return $this;
    }
}