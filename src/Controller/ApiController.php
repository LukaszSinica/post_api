<?php

namespace App\Controller;

use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api')]
final class ApiController extends AbstractController
{
    #[Route('/posts', name: 'app_get_posts', methods: ['GET'])]
    public function getPosts(PostsRepository $postRepository): JsonResponse
    {
        $posts = $postRepository->findAll();
        return $this->json($posts);
    }
}
