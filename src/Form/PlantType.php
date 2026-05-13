<?php

namespace App\Form;

use App\Entity\Plant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dutchName')
            ->add('latinName')
            ->add('description')
            ->add('plantType', ChoiceType::class, [
                'choices' => array_flip(Plant::PLANT_TYPES),
                // 'choices' => [
                //     'Boom' => 'boom',
                //     'Boompje' => 'boompje',
                //     'Struik' => 'struik',
                //     'Vaste plant' => 'vaste plant',
                //     'Bloem' => 'bloem',
                //     'Gras' => 'gras',
                //     'Siergras' => 'siergras',
                // ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plant::class,
        ]);
    }
}
