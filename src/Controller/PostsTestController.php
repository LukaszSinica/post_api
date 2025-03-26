<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final class PostsTestController extends AbstractController
{
    // #[Route('/posts', name: 'create_post', methods: ['PUT'])]
    // public function createPost(EntityManagerInterface $entityManager, ValidatorInterface $validator, LoggerInterface $logger): Response
    // {
    //     $post = new Posts();
    //     $post->setTitle('test post');
    //     $post->setContent('This is the test post');
    //     $post->setCreatedAt(new \DateTimeImmutable());
    //     $errors = $validator->validate($post);
    //     if (count($errors) > 0) {
    //         return new Response((string) $errors, 400);
    //     }

    //     $entityManager->persist($post);
    //     $entityManager->flush();
    //     $logger->info('We are logging!');
    //     return new Response('Saved new post with id '. $post->getId());
    // }

    // #[Route('/posts', name: 'show_posts', methods: ['GET'])]
    // public function showPosts(PostsRepository $postRepository): JsonResponse
    // {
    //     $posts = $postRepository->findAll();
    //     return $this->json($posts);
    // }
}
