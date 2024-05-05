<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Zones;
use App\Entity\Tables; 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType; 
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class Reservation1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('idC', TextType::class, [
            'required' => false,  // Make field not required
        ])
        ->add('zone', EntityType::class, [
            'class' => Zones::class,
            'choice_label' => 'nom',
            'required' => false,  // Make field not required
        ])
        ->add('tableId', EntityType::class, [
            'class' => Tables::class,
            'choice_label' => 'tableId',
            'required' => false,  // Make field not required
        ])
        ->add('dater', DateType::class, [
            'required' => false,
            'widget' => 'single_text',
            'attr' => ['class' => 'datepicker'],
        ])
        ->add('status', ChoiceType::class, [
            'choices' => [
                'On Hold' => 'on_hold',
                'In Progress' => 'in_progress',
                'Completed' => 'completed',
                'Cancelled' => 'cancelled',
            ],
            'data' => 'in_progress',  // Default selected value
            'required' => false,  // Make field not required
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
