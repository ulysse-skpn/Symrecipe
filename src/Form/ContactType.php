<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class,
            [
                "required" => false,
                "attr" => [ "class" => "form-control" ],
                "label" => "Nom / Prénom",
                "label_attr" => [ "class" => "form-label" ],
                "constraints" => 
                [
                    new Assert\Length(["min" => 2, "max" => 100])
                ]
            ])
            ->add('email', EmailType::class,
            [
                "attr" => [ "class" => "form-control" ],
                "label" => "Email",
                "label_attr" => [ "class" => "form-label" ],
                "constraints" => 
                [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                    new Assert\Length(["min" => 2, "max" => 180])
                ]
            ])
            ->add('subject', TextType::class,
            [
                "required" => false,
                "attr" => [ "class" => "form-control" ],
                "label" => "Sujet",
                "label_attr" => [ "class" => "form-label" ],
                "constraints" => 
                [
                    new Assert\Length(["min" => 2, "max" => 100])
                ]
            ])
            ->add('message', TextareaType::class,
            [
                "attr" => [ "class" => "form-control" ],
                "label" => "Message",
                "label_attr" => [ "class" => "form-label" ],
                "constraints" => 
                [
                    new Assert\NotBlank()
                ]
            ])
            ->add('submit', SubmitType::class,
            [
                "attr" => 
                [
                    'class' => 'btn btn-secondary mt-4'
                ],
                'label' => "Envoyer"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
