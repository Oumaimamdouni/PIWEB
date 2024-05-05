<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class Commande1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('adressec', TextType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
        ->add('panier', TextType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
        ->add('price', IntegerType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
        ->add('status', ChoiceType::class, [
            'choices' => [
                'On Hold' => 'On Hold',
                'Processing' => 'Processing',
                'Completed' => 'Completed',
                'Cancelled' => 'Cancelled',
            ],
            'data' => 'On Hold',
            'required' => false,
            'attr' => ['class' => 'form-control'],
        ])
            ->add('idresto')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
