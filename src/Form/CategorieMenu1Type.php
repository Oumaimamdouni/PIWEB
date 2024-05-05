<?php

namespace App\Form;

use App\Entity\CategorieMenu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType; // Required for text inputs
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert; // Import constraints

class CategorieMenu1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nomCategorie', TextType::class, [
            'label' => 'Nom de la Catégorie',
            'required' => false, // Make this field required
        ])
        ->add('descriptionCategorie', TextareaType::class, [
            'label' => 'Description de la Catégorie',
            'required' => false, // Make this field required
        ])
        ->add('imageCategorie', FileType::class, [
            'label' => 'Image',
            'required' => false, // Set to true if image upload is mandatory
            'mapped' => false, // This field is not mapped to any property in the entity
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CategorieMenu::class,
        ]);
    }
}
