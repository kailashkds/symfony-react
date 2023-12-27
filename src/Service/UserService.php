<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Uuid;

class UserService
{
    private $entityManager;
    private $passwordEncoder;
    private $router;
    private $parameterBag;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        UrlGeneratorInterface $router,
        ParameterBagInterface  $parameterBag
    )
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->router = $router;
        $this->parameterBag = $parameterBag;
    }

    public function createNewUser($formData){
        $user = new User();
        $user->setUsername($formData['email']);
        $user->setEmail($formData['email']);
        $this->passwordEncoderAndGenerateConfirmationToken($user,$formData['password']);
        $user->setIsActive(0);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

//        $this->emailService->sendEmail($user);
        return $user;
    }

    public function getUserByEmail(string $email) {
        return $this->entityManager->getRepository(User::class)->count(['email' => $email]);
    }

    public function passwordEncoderAndGenerateConfirmationToken($user,$password){
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $password);

        // Set the user's new password and confirmation token
        $user->setPassword($encodedPassword);
        $confirmationToken = $this->generateConfirmationToken();
        $user->setConfirmationToken($confirmationToken);

        $confirmationUrl = $this->router->generate('user_confirm', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        $emailContent = "Click the following link to confirm your account: $confirmationUrl";
        $directoryPath = $this->parameterBag->get('kernel.project_dir') . '/var/email';

        if (!file_exists($directoryPath) && !mkdir($directoryPath, 0777, true)) {
            throw new \RuntimeException('Unable to create directory: ' . $directoryPath);
        }

        $filePath = $directoryPath . '/' . $user->getEmail() . '_confirmation.txt';

        // Write content to the file
        if (file_put_contents($filePath, $emailContent) === false) {
            throw new \RuntimeException('Unable to write content to file: ' . $filePath);
        }
    }

    public function generateConfirmationToken():string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}