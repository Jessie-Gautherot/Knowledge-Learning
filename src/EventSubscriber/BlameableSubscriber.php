<?php

namespace App\EventSubscriber;

use App\Entity\BlameableInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Automatically sets createdBy and updatedBy
 * when an entity is created or updated.
 * 
 * Only entities implementing BlameableInterface
 * are handled by this subscriber.
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
     * Called when a new entity is created.
     *
     * Sets createdBy and updatedBy
     * with the current user.
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
     * Called when an entity is updated.
     *
     * Updates the updatedBy field
     * with the current user.
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