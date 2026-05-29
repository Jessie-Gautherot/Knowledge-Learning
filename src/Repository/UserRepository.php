<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

/**
 * Class UserRepository
 *
 * Handles all database operations related to User entity.
 *
 * Responsibilities:
 * - CRUD operations
 * - Custom queries (email, token, etc.)
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * Constructor
     *
     * @param ManagerRegistry $registry Doctrine registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }


    /**
     * Find a user by email 
     *
     * @param string $email
     * @return User|null
     */
    public function findOneByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('LOWER(u.email) = :email')
            ->setParameter('email', strtolower($email))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Load user for authentication (Symfony Security)
     *
     * @param string $identifier
     * @return User
     * @throws UserNotFoundException
     */
    public function loadUserByIdentifier(string $identifier): User
    {
        $user = $this->findOneByEmail($identifier);

        if (!$user) {
            throw new UserNotFoundException('Utilisateur non trouvé.');
        }

        return $user;
    }

    /**
     * Find a user by activation token
     *
     * @param string $token
     * @return User|null
     */
    public function findOneByActivationToken(string $token): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.activationToken = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }
}