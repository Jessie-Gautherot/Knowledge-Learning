<?php

namespace App\EventSubscriber;

use App\Entity\BlameableInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Automatically sets createdBy and updatedBy fields
 * on entities implementing BlameableInterface.
 *
 * This subscriber listens to Doctrine lifecycle events
 * in order to track the user responsible for creating
 * or updating an entity.
 */
class BlameableSubscriber implements EventSubscriber
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    /**
     * Handles entity creation.
     *
     * Sets createdBy and updatedBy fields
     * when a new entity is persisted.
     *
     * @param LifecycleEventArgs $args Doctrine event arguments
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof BlameableInterface) {
        return;
    }

        $user = $this->security->getUser();

        if ($user) {
            $entity->setCreatedBy($user);
            $entity->setUpdatedBy($user);
        }
    }

    /**
     * Handles entity update.
     *
     * Updates the updatedBy field when an entity is modified.
     *
     * @param LifecycleEventArgs $args Doctrine event arguments
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof BlameableInterface) {
        return;
    }

        $user = $this->security->getUser();

        if ($user) {
            $entity->setUpdatedBy($user);
        }
    }
}