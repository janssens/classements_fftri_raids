<?php

namespace App\Form;

use App\Form\DataTransformer\RegistrationToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AutocompleteRegistrationType extends AbstractType
{

    private $transformer;

    public function __construct(RegistrationToStringTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function getBlockPrefix()
    {
        return 'autocomplete_registration';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => 'The selected data does not match any registration',
        ]);
    }

    public function getParent()
    {
        return HiddenType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transformer);
    }

}
