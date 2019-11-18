<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserGetter
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(TokenStorageInterface $tokenStorage, UserRepository $userRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->userRepository = $userRepository;
    }

    public function getUsername(): string
    {
        $token = $this->tokenStorage->getToken();
        $username = $token->getUsername();

        if ($username === null || empty($username)) {
            throw new \InvalidArgumentException('User is not logged in');
        }

        return $username;
    }

    public function get(): User
    {
        $token = $this->tokenStorage->getToken();
        /** @var User $user */
        $user = $token->getUser();

        if ($user instanceof User) {
            return $user;
        }

        if (is_string($user) || is_object($user)) {
            $username = $token->getUsername();
            $user = $this->loadFromDb($username);
        }

        if ($user === null) {
            throw new \InvalidArgumentException('User is not logged in');
        }

        return $user;
    }

    private function loadFromDb(string $username): User
    {
        $user = $this->userRepository->findOneBy(['username' => $username]);

        return $user;
    }
}