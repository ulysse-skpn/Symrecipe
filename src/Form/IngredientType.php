<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class IngredientType extends AbstractType
{
    // private $token;

    // public function __construct(TokenStorageInterface $token)
    // {
    //     $this->token = $token;
    // }
    
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
                    new Assert\LessThan(200)
                ]
            ])
            ->add('submit', SubmitType::class,
            [
                "attr" => 
                [
                    'class' => 'btn btn-secondary'
                ],
                'label' => "Enregister",
            ]
            )
            // ->add("ingredients", EntityType::class,
            // [
            //     "class" => Ingredient::class,
            //     "query_builder" => function (IngredientRepository $repository)
            //                         {
            //                             return $repository->createQueryBuilder('i')
            //                                                 ->where('i.user = :user')
            //                                                 ->orderBy('i.name','ASC')
            //                                                 ->setParameter('user' , $this->token->getToken()->getUser());
            //                         },
            //     "label" => "<legend>Mes ingrédients</legend>", //?
            //     "label_attr" => [ "class" => "form-label" ],
            //     "choice_label" => "name",
            //     "multiple" => true,
            //     "expanded" => true
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
        ]);
    }
}
