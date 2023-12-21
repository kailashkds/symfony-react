<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Uuid;

class UserService
{
    private $entityManager;
    private $passwordEncoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function createNewUser(FormInterface $formData){
        $user = new User();
        $user->setUsername($formData['username']);
        $user->setEmail($formData['email']);
        $this->passwordEncoderAndGenerateConfirmationToken($user,$formData['password']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->emailService->sendEmail($user);
        return $user;
    }

    public function getUserByEmail(string $email) {
        return $this->entityManager->getRepository(User::class)->count(['email' => $email]);
    }

    public function passwordEncoderAndGenerateConfirmationToken($user,$plainPassword){
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);

        // Set the user's new password and confirmation token
        $user->setPassword($encodedPassword);
        $confirmationToken = $this->generateConfirmationToken();
        $user->setConfirmationToken($confirmationToken);
    }

    public function generateConfirmationToken():string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}