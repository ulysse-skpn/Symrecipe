<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RecipeType extends AbstractType
{
    private $token;

    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token;
    }

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
                'label' => "Appellation",
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
                "required" => false,
                "attr" =>
                [
                    "class => 'form-control"
                ],
                "label" => "Temps de préparation",
                "label_attr" => [ "class" => 'form-label' ],
                "constraints" => 
                [
                    new Assert\LessThan(1),
                    new Assert\GreaterThan(60*24),
                ]
            ])
            ->add('nbPerson', IntegerType::class,
            [
                "required" => false,
                "attr" => 
                [
                    "class" => "form-control"
                ],
                "label" => "Nombre de personnes",
                "label_attr" => 
                [
                    "class" => "form-label"
                ],
                "constraints" => 
                [
                    new Assert\PositiveOrZero()
                ]
            ])
            ->add('difficulty', RangeType::class,
            [
                "required" => false,
                "attr" => 
                [
                    "class" => "form-control",
                    "min" => 1,
                    "max" => 5,
                    "value" => 1
                ],
                "label" => "Difficulté",
                "label_attr" => 
                [
                    "class" => "form-label"
                ]
            ])
            ->add('instructions', TextareaType::class,
            [
                "attr" => [ "class" => "form-control" ],
                "label" => "Instructions",
                "label_attr" => [ "class" => 'form-label' ]
            ])
            ->add('price', MoneyType::class, 
            [
                "required" => false,
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
            ->add('bookmark' , CheckboxType::class,
            [
                "required" => false,
                "attr" => [ "class" => "form-check-input ml-4" ],
                "label" => "Favoris"
            ])
            ->add('submit', SubmitType::class,
            [
                "attr" => 
                [
                    'class' => 'btn btn-secondary'
                ],
                'label' => "Enregister"
            ])
            ->add("ingredientsList", EntityType::class,
            [
                "class" => Ingredient::class,
                "query_builder" => function (IngredientRepository $repository)
                                    {
                                        return $repository->createQueryBuilder('i')
                                                            ->where('i.user = :user')
                                                            ->orderBy('i.name','ASC')
                                                            ->setParameter('user' , $this->token->getToken()->getUser());
                                    },
                "label" => "Mes ingrédients",
                "label_attr" => [ "class" => "form-label mt-4" ],
                "choice_label" => "name",
                "multiple" => true,
                "expanded" => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
