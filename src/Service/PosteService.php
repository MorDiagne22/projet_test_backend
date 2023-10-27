<?php

namespace App\Service;

use App\Controller\PostController;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Post;
use App\Repository\PosteRepository;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PosteService
{
    private PosteRepository $repository;

    public function __construct(PosteRepository $repository) {
        $this->repository = $repository;
    }
    
    public function save(Post $post)
    {
        return $this->repository->save($post);
    }

    public function getAllPosts()
    {
        return $this->repository->findAll();
    }

    public function getPost($id)
    {
        return $this->repository->find($id);
    }
}