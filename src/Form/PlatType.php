<?php

namespace App\Form;

use App\Entity\Plat;
use App\Entity\CategorieMenu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PlatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomPlat')
            ->add('descriptionPlat')
            ->add('prix')
            ->add('quantite')
            ->add('nomCategorie', EntityType::class, [
                'class' => CategorieMenu::class,
                'choice_label' => 'nomCategorie',
                'placeholder' => 'Select a category', 
                'choice_value' => 'nomCategorie'
            ])
            ->add('image', FileType::class, [
                'label' => 'Plat Image',
                'mapped' => false, 
                'required' => true, 
                'attr' => [
                    'accept' => 'image/*', 
                ],
            ])
            ->add('likePlat')
            ->add('dislikePlat')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plat::class,
        ]);
    }
}
