<?php

namespace App\Form;

use App\Entity\Championship;
use App\Entity\Race;
use App\Repository\RaceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChampionshipType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('short_name')
            ->add('registration_due_date')
            ->add('final_registration_due_date', DateType::class, ['widget' => 'single_text',])
            ->add('rank_outsider')
            ->add('is_unisex', CheckboxType::class, ['required'=>false])
            ->add('season')
            ->add('races', EntityType::class, [
                'class' => Race::class,
                'query_builder' => function (RaceRepository $er) {
                    return $er->createQueryBuilder('r')
                        ->orderBy('r.date', 'ASC');
                },
                'multiple' => true,
                'choice_label' => 'getDateAndName',])
            ->add('final', EntityType::class, [
                'class' => Race::class,
                'query_builder' => function (RaceRepository $er) {
                    return $er->createQueryBuilder('r')
                        ->orderBy('r.date', 'ASC');
                },
                'choice_label' => 'getDateAndName',
                'required' => false,])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Championship::class,
        ]);
    }
}
