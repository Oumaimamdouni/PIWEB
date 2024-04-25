<?php

namespace App\Form;

use App\Entity\Tables;
use App\Entity\Zones;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class Tables1Type extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nomZone', EntityType::class, [
            'class' => Zones::class,
            'choice_label' => 'nom',
            'placeholder' => 'Select a zone',
            'required' => true,
        ])
            ->add('capaciteT')
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tables::class,
        ]);
    }
}
