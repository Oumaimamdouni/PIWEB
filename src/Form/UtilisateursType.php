<?php

namespace App\Form;

use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType; // Import TextType


class UtilisateursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'required' => false,
        ])
        ->add('prenom', TextType::class, [
            'required' => false,
        ])
        ->add('motDePasse', PasswordType::class, [
            'label' => 'Mot de Passe:',
            'attr' => ['class' => 'form-control'],
            'required' => false,
        ])
        ->add('email', TextType::class, [
            'required' => false,
        ])
        ->add('image', FileType::class, [
            'label' => 'Image (JPG, PNG or GIF file):',
            'mapped' => false, // The image field is not mapped to any entity property
            'required' => false, // The image field is not required
            'attr' => ['class' => 'form-control-file'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateurs::class,
        ]);
    }
}
