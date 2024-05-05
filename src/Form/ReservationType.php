<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Zones;
use App\Entity\Tables; 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType; 
use Symfony\Component\Form\Extension\Core\Type\DateType;
class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('zone', EntityType::class, [
                'class' => Zones::class,
                'choice_label' => 'nom',
            ])
            ->add('tableId', EntityType::class, [ 
                'class' => Tables::class,
                'choice_label' => 'tableId', 
            ])        
            ->add('dater', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'datepicker'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
