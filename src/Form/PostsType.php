<?php

namespace App\Form;

use App\Entity\Posts;
use App\Entity\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PostsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('content', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control hidden',
                    'data-quill' => true
                ]
            ])
            ->add('created_at', null, [
                'widget' => 'single_text',
            ])
            ->add('image', EntityType::class, [
                'class' => Image::class,
                'choice_label' => 'originalFilename',
                'required' => false,
                'placeholder' => 'Choose an image',
                'attr' => ['class' => 'form-select']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Posts::class,
        ]);
    }
}
