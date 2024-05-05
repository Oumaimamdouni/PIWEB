<?php

namespace App\Form;

use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints as Assert;

class Evenement1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'required' => false,
            ])
            ->add('nbparticipant', null, [
                'required' => false, 
            ])
            ->add('datedebut', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Start date', 
                ],
            ])
            ->add('datefin', DateType::class, [
                'widget' => 'single_text', 
                'required' => false,
                'attr' => [
                    'placeholder' => 'End date',
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control-file',
                ],
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '2M', // Maximum file size (2 megabytes)
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPEG or PNG image.',
                        'maxSizeMessage' => 'The file is too large. Maximum size allowed is 2M.',
                    ]),        
                    new Assert\NotBlank([
                        'message' => 'An image is required. Please upload an image.', // Custom error message for missing file
                    ]),
                ],
            ])
            ->add('description', null, [
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    $resolver->setDefaults([
        'data_class' => Evenement::class,
    ]);
    }
}
