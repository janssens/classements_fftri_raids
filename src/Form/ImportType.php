<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('delimiter',TextType::class,['label'=>'séparateur','attr'=>['readonly'=>'readonly']])
            ->add('map',TextType::class,['label'=>'colonnes utilisées','attr'=>['readonly'=>'readonly']])
        ;
    }

}
