<?php

namespace App\Form;

use App\Entity\PlannedTeam;
use App\Entity\Team;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlannedTeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,array('label'=>'Nom de l\'équipe'))
        ;
        $builder->add('requests', CollectionType::class, [
            'label'=>'Equipiers',
            'entry_type' => AutocompleteRegistrationType::class,
            'entry_options' => [
                'label' => '&nbsp;',
                'attr' => [],
            ],
        ]);
        $builder->add('registrations', CollectionType::class, [
            'label'=>'Equipiers',
            'entry_type' => AutocompleteRegistrationType::class,

            'entry_options' => [
                'label' => '&nbsp;',
                'attr' => [],
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlannedTeam::class,
        ]);

    }
}
