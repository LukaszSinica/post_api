<?php

namespace App\Controller;

use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/images')]
final class ImageController extends AbstractController
{
    private const ALLOWED_MIMETYPES = [
        'image/jpeg',
        'image/png',
        'image/gif'
    ];
    
    private const MAX_SIZE = '5M';

    #[Route('/', name: 'app_images_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $images = $entityManager->getRepository(Image::class)->findAll();
        return $this->render('images/index.html.twig', [
            'images' => $images,
        ]);
    }

    #[Route('/upload', name: 'app_images_upload', methods: ['GET', 'POST'])]
    public function upload(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        if ($request->isMethod('POST')) {
            $uploadedFile = $request->files->get('image');
            
            if ($uploadedFile) {
                // Validate file
                $constraints = new File([
                    'maxSize' => self::MAX_SIZE,
                    'mimeTypes' => self::ALLOWED_MIMETYPES,
                    'mimeTypesMessage' => 'Please upload a valid image file (JPG, PNG, GIF)',
                ]);

                $violations = $validator->validate(
                    $uploadedFile,
                    $constraints
                );

                if (count($violations) > 0) {
                    $this->addFlash('error', $violations[0]->getMessage());
                    return $this->redirectToRoute('app_images_upload');
                }

                try {
                    $originalFilename = $uploadedFile->getClientOriginalName();
                    $newFilename = uniqid().'.'.$uploadedFile->guessExtension();

                    $uploadedFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );

                    $image = new Image();
                    $image->setFilename($newFilename);
                    $image->setOriginalFilename($originalFilename);

                    $entityManager->persist($image);
                    $entityManager->flush();

                    $this->addFlash('success', 'Image uploaded successfully');
                    return $this->redirectToRoute('app_images_index');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Failed to upload image: ' . $e->getMessage());
                    return $this->redirectToRoute('app_images_upload');
                }
            } else {
                $this->addFlash('error', 'No image file selected');
            }
        }

        return $this->render('images/upload.html.twig');
    }
}