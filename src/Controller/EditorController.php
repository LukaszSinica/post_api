<?php

namespace App\Controller;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\File;

#[Route('/editor')]
class EditorController extends AbstractController
{
    private const ALLOWED_MIMETYPES = [
        'image/jpeg',
        'image/png',
        'image/gif'
    ];
    
    private const MAX_SIZE = '5M';

    #[Route('/images', name: 'app_editor_images', methods: ['GET'])]
    public function getImages(EntityManagerInterface $entityManager): Response
    {
        $images = $entityManager->getRepository(Image::class)->findAll();
        
        $imagesArray = array_map(function($image) {
            return [
                'id' => $image->getId(),
                'filename' => $image->getFilename(),
                'originalFilename' => $image->getOriginalFilename(),
                'url' => '/uploads/images/' . $image->getFilename()
            ];
        }, $images);
        
        return $this->json([
            'images' => $imagesArray
        ]);
    }

    #[Route('/images/upload', name: 'app_editor_upload_image', methods: ['POST'])]
    public function upload(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $uploadedFile = $request->files->get('imageFile');
        
        if (!$uploadedFile) {
            return $this->json(['error' => 'No file uploaded'], 400);
        }

        $constraints = new File([
            'maxSize' => self::MAX_SIZE,
            'mimeTypes' => self::ALLOWED_MIMETYPES,
            'mimeTypesMessage' => 'Please upload a valid image file (JPG, PNG, GIF)',
        ]);

        $violations = $validator->validate($uploadedFile, $constraints);

        if (count($violations) > 0) {
            return $this->json(['error' => $violations[0]->getMessage()], 400);
        }

        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();

        try {
            $uploadedFile->move(
                $this->getParameter('images_directory'),
                $newFilename
            );

            $image = new Image();
            $image->setFilename($newFilename);
            $image->setOriginalFilename($originalFilename);

            $entityManager->persist($image);
            $entityManager->flush();

            return $this->json([
                'filename' => $newFilename,
                'originalFilename' => $originalFilename,
                'url' => '/uploads/images/' . $newFilename
            ]);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Failed to upload file'], 500);
        }
        
    }

    #[Route('/images/list', name: 'app_editor_images_list', methods: ['GET'])]
    public function listImages(): JsonResponse
    {
        $uploadDir = $this->getParameter('images_directory');
        $files = array_diff(scandir($uploadDir), ['.', '..']);
        
        return new JsonResponse(array_values($files));
    }
}