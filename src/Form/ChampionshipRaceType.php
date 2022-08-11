<?php

namespace App\Form;

use App\Entity\Race;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ChampionshipRaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,['label'=>'nom de la course','required' => true])
            ->add('date', DateType::class)
            ->add('athletes_per_team', IntegerType::class,['label'=>'nombre de coureurs par équipe','required' => true])
            ->add('code',PasswordType::class,['label'=> 'code secret','required' => true])
            ->add('rankingFile',VichFileType::class,['label'=> 'fichier classement (csv)','required' => true]);
        ;
    }

}
