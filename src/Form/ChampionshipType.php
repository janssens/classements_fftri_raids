<?php

namespace App\Form;

use App\Entity\Championship;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChampionshipType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('registration_due_date')
            ->add('final_registration_due_date')
            ->add('rank_outsider')
            ->add('season')
            ->add('races')
            ->add('final')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Championship::class,
        ]);
    }
}
