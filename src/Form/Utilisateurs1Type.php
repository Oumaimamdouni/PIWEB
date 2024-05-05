<?php

namespace App\Form;

use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class Utilisateurs1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', null, [
            'required' => false,
        ])
        ->add('prenom', null, [
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
        ->add('role', ChoiceType::class, [
            'choices' => [
                'Admin' => 'admin', 
                'Client' => 'client',
                'Livreur' => 'mivreur',
            ],
            'required' => false,
            'placeholder' => 'Select a role',  
        ])
        ->add('image', FileType::class, [
            'label' => 'Image (JPG, PNG or GIF file):',
            'mapped' => false, 
            'required' => false, 
            'attr' => ['class' => 'form-control-file'],
        ])
        ->add('blocked', null, [
            'required' => false,
        ]);    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateurs::class,
        ]);
    }
}
