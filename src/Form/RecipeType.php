<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, 
            [
                "attr" => 
                [
                    'class' => 'form-control',
                    'minLength' => "2",
                    'maxLength' => "50"
                ],
                'label' => "Nom",
                'label_attr' => 
                [
                    'class' => 'form-label'
                ],
                'constraints' => 
                [
                    new Assert\Length(['min' => 2 , 'max' => 50]),
                    new Assert\NotBlank()
                ]
            ])
            ->add('time', TimeType::class,
            [
                "attr" =>
                [
                    "class => 'form-control",
                    'max' => new \DateTime()
                ],
                "label" => "Temps de préparation",
                "label_attr" => [ "class" => 'form-label' ],
                "constraints" => 
                [
                    new Assert\LessThan(1),
                    new Assert\GreaterThan(60*24),
                ]
            ])
            ->add('nbPerson', IntegerType::class)
            ->add('difficulty', TextType::class)
            ->add('instructions', TextareaType::class)
            // ->add('ingredientsList', TextareaType::class)
            ->add('price', MoneyType::class, 
            [
                "attr" => 
                [
                    'class' => 'form-control',
                    'min' => '1'
                ],
                'label' => "Prix",
                'label_attr' => 
                [
                    'class' => 'form-label'
                ],
                'constraints' => 
                [
                    new Assert\Positive(),
                    new Assert\LessThan(1000)
                ]
            ])
            ->add('bookmark')
            ->add('submit', SubmitType::class,
            [
                "attr" => 
                [
                    'class' => 'btn btn-secondary'
                ],
                'label' => "Enregister",
            ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
