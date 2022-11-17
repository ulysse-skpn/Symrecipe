<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('fullName', TextType::class,
        [
            "attr" =>
            [
                "class" => "form-control",
                "minlength" => "2",
                "maxlength" => "100",
            ],
            "label" => "Nom / Prénom",
            "label_attr" => 
            [
                "class" => "form-label" 
            ],
            "constraints" => 
            [
                new Assert\NotBlank(),
                new Assert\Length(["min" => 2, "max" => 100])
            ]
        ])
        ->add('pseudo', TextType::class,
        [
            "attr" =>
            [
                "class" => "form-control",
                "minlength" => "2",
                "maxlength" => "100",
            ],
            "required" => false,
            "label" => "Pseudonyme (facultatif)",
            "label_attr" => 
            [
                "class" => "form-label" 
            ],
            "constraints" => 
            [
                new Assert\Length(["min" => 2, "max" => 50])
            ]
        ])
        ->add('submit', SubmitType::class, 
        [
            "attr" =>
            [
                "class" => "btn btn-primary mt-4"
            ],
            "label" => "Valider"
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
