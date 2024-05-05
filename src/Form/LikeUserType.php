<?php

namespace App\Form;

use App\Entity\LikeUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class LikeUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idUser')
            ->add('plat')
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Like' => 'like',
                    'Dislike' => 'dislike',
                ],
                'placeholder' => 'Choose status',
                
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LikeUser::class,
        ]);
    }
}
