<?php

namespace App\Controller;

use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api')]
final class ApiController extends AbstractController
{
    #[Route('/posts', name: 'app_get_posts', methods: ['GET'])]
    public function getPosts(Request $request, PostsRepository $postRepository): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $this->getParameter('pagination_limit');
        
        $total = $postRepository->count([]);
        $posts = $postRepository->findBy(
            [], 
            ['created_at' => 'DESC'],
            $limit,
            ($page - 1) * $limit
        );

        return $this->json([
            'items' => $posts,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ]);
    }

    #[Route('/posts/featured', name: 'app_get_featured_posts', methods: ['GET'])]
    public function getFeaturedPosts(PostsRepository $postRepository): JsonResponse
    {
        $featuredPosts = $postRepository->findBy(
            [], 
            ['created_at' => 'DESC'],
            2
        );

        return $this->json([
            'items' => $featuredPosts
        ]);
    }
}
