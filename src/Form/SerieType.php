<?php

namespace App\Form;

use App\Entity\Serie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'row_attr' => [
                    'class' => 'input-group mb-3'
                ]
            ])
            ->add('overview', TextareaType::class, [
                'label' => 'Résumé',
                'required' => false,
                'row_attr' => [
                    'class' => 'input-group mb-3'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'placeholder' => ' -- Choisissez le statut --',
                'choices' => [
                    'Returning' => 'RETURNING',
                    'Ended'     => 'ENDED',
                    'Canceled'  => 'CANCELED'
                ],
                'row_attr' => [
                    'class' => 'input-group mb-3'
                ]
            ])
            ->add('vote', null, [
                'row_attr' => [
                    'class' => 'input-group mb-3'
                ]
            ])
            ->add('popularity', null, [
                'row_attr' => [
                    'class' => 'input-group mb-3'
                ]
            ])
            ->add('genres')
            ->add('firstAirDate', null, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('lastAirDate', null, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('backdrop', null, [
                'required' => false,
            ])
            ->add('poster', null, [
                'required' => false,
            ])
            ->add('tmdbId', null, [
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Serie::class,
        ]);
    }
}
