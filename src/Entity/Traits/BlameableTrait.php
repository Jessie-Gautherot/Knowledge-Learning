<?php

namespace App\Entity\Traits;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait BlameableTrait
 *
 * Automatically adds createdBy and updatedBy fields
 * to track which user created or updated an entity.
 */
trait BlameableTrait
{
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $updatedBy = null;

    public function setCreatedBy(?User $user): void
    {
        $this->createdBy = $user;
    }

    public function setUpdatedBy(?User $user): void
    {
        $this->updatedBy = $user;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }
}