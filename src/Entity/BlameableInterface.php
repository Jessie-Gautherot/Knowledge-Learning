<?php

namespace App\Entity;

use App\Entity\User;

/**
 * Interface BlameableInterface
 *
 * Defines a contract for entities that must track
 * the user responsible for creation and update actions.
 */
interface BlameableInterface
{
    public function setCreatedBy(?User $user): void;
    public function setUpdatedBy(?User $user): void;
}