<?php

namespace App\Form;

use App\Entity\Season;
use App\Entity\Serie;
use App\Repository\SerieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number')
            ->add('firstAirDate', null, [
                'widget' => 'single_text',
            ])
            ->add('overview')
            ->add('poster')
            ->add('tmdbId')
            ->add('serie', EntityType::class, [
                'placeholder' => '--Veuillez choisir une série--',
                'class' => Serie::class,
                'choice_label' => 'name',
                'query_builder' => function (SerieRepository $serieRepository) {
                    return $serieRepository->createQueryBuilder('s')
                        ->andWhere('s.status = :status')
                        ->setParameter(':status', 'returning')
                        ->addOrderBy('s.name', 'ASC');
                }
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Season::class,
        ]);
    }
}
