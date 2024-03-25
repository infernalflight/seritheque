<?php

namespace App\Form;

use App\Entity\Serie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'special-class',
                ],
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
                    'Returning' => 'returning',
                    'Ended'     => 'ended',
                    'Canceled'  => 'Canceled'
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
            ->add('poster_file', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'maxSizeMessage' => 'Ton image est trop lourde',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => "Ce Format n'est pas pris en charge"
                    ])
                ]
            ])
            ->add('tmdbId', null, [
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $formEvent) {
                $serie = $formEvent->getData();
                if ($serie && $serie->getPoster()) {
                    $form = $formEvent->getForm();
                    $form->add('delete_image', CheckboxType::class, [
                        'mapped' => false,
                        'required' => false,
                    ]);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Serie::class,
        ]);
    }
}
