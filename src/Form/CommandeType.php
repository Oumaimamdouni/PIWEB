<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Plat;
use App\Entity\Resto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\PlatRepository;

class CommandeType extends AbstractType
{
    private PlatRepository $platRepository;

    public function __construct(PlatRepository $platRepository)
    {
        $this->platRepository = $platRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $plats = $this->platRepository->findAll();

        $builder
            ->add('adressec', TextType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('panier', ChoiceType::class, [
                'choices' => $plats,  // Populate the choices with the list of plats
                'choice_label' => 'nomPlat',  // Use the name of the plat as the label
                'multiple' => true,  // Allow multiple selections
                'expanded' => true,  // Use checkboxes for selection (optional)
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
