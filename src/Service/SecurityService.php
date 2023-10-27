<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Category;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityService
{
    private UserRepository $repository;

    public function __construct(
        UserRepository $repository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        MailerInterface $mailer
    ) {
        $this->repository = $repository;
    }

    //Cette fonction permet d'enregistrer un utilisateur
    public function register(User $user)
    {

        if($user->getPlainPassword()){
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $user->getPlainPassword()
            );
            $user->setPassword($hashedPassword);
        }
        
        return $this->repository->save($user);
    }
}