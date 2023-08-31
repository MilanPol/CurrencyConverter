<?php

namespace App\Service;

use App\Entity\User\User;
use App\Factory\UserFactory;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class UserService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createUser(
        string $email,
        string $plainPassword,
        string $role
    ): void {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            [
                'email' => $email
            ]
        );

        if ($user) {
            throw new Exception(
                'User already exists'
            );
        }

        UserFactory::new()
            ->withAttributes([
                'email' => $email,
                'plainPassword' => $plainPassword,
            ])
            ->promoteRole($role)
            ->create();
    }
}
