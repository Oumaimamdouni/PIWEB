<?php

namespace App\Form;

use App\Entity\CategorieMenu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieMenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nomCategorie', TextType::class, [
            'label' => 'Nom de la Catégorie',
            'required' => false, // Make this field required
            'attr' => ['class' => 'form-control'],
        ])
        ->add('descriptionCategorie', TextareaType::class, [
            'label' => 'Description de la Catégorie',
            'required' => false, // Make this field required
            'attr' => ['class' => 'form-control'],
        ])
        ->add('imageCategorie', FileType::class, [
            'label' => 'Image',
            'required' => false, // Make this field required
            'mapped' => false, // This field is not mapped to any property in the entity
            'attr' => ['class' => 'form-control'],
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CategorieMenu::class,
        ]);
    }
}
