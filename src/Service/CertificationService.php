<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Certification;
use App\Repository\CertificationRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CertificationService
 *
 * Handle certification creation when a user completes all lessons of a theme.
 */
class CertificationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private CertificationRepository $repo
    ) {}

    /**
     * Create certification if it does not already exist
     *
     * @param User $user
     * @param Theme $theme
     *
     * @return void
     */
    public function createIfNotExists(User $user, Theme $theme): void
    {
        $existing = $this->repo->findOneBy([
            'user' => $user,
            'theme' => $theme
        ]);

        if ($existing) {
            return;
        }

        $certification = new Certification();
        $certification->setUser($user);
        $certification->setTheme($theme);

        $this->em->persist($certification);
        $this->em->flush();
    }
}