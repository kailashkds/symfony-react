<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTUserManager
{
    private $jwtTokenManager;

    public function __construct(JWTTokenManagerInterface $jwtTokenManager)
    {
        $this->jwtTokenManager = $jwtTokenManager;
    }

    public function createJWTToken(UserInterface $user): string
    {
        // Create and return the JWT token
        return $this->jwtTokenManager->create($user);
    }
}
