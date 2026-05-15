<?php

namespace App\Form;

use App\Entity\Observation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class ObservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('observedAt', DateTimeType::class, [
                'constraints' => [
                    new NotBlank(
                        message: 'Vul een datum in'
                    ),
                ],
            ])
            ->add('notes', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(
                        message: 'Voeg een beschrijving toe van wat je hebt gezien.'
                    )
                ],
            ])
            ->add('locationName')
            ->add('imagePath', FileType::class, [
                'label' => 'Foto',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(
                        maxSize: '5M',
                        mimeTypes: [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        mimeTypesMessage: 'Upload een geldige afbeelding (JPEG, PNG of WebP).',
                    ),
                ],
            ])
            ->add('suspectedName')
            // ->add('user', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Observation::class,
        ]);
    }
}
