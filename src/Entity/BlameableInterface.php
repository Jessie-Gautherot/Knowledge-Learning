<?php

namespace App\Entity;

use App\Entity\User;

/**
 * Interface BlameableInterface
 *
 * Defines methods required for
 * createdBy and updatedBy fields.
 */
interface BlameableInterface
{
    public function setCreatedBy(?User $user): void;
    public function setUpdatedBy(?User $user): void;
}