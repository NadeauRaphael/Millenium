<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('email', TextType::class, [
            'required' => true,
            'label' => 'Email',
            'attr' => []
        ])
            ->add('firstName', TextType::class, [
                'required' => true,
                'label' => 'First name',
                'attr' => []
            ])
            ->add('lastName', TextType::class, [
                'required' => true,
                'label' => 'Name',
                'attr' => []
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'label' => 'Phone',
                'attr' => []
            ])
            ->add('adress', TextType::class, [
                'required' => true,
                'label' => 'Adress',
                'attr' => []
            ])
            ->add('city', TextType::class, [
                'required' => true,
                'label' => 'City',
                'attr' => []
            ])
            ->add('postalCode', TextType::class, [
                'required' => true,
                'label' => 'postal Code',
                'attr' => []
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => "Les mots de passe doivent etre identiques",
                'constraints' => [new Assert\Length(min: 6, minMessage: "The password must containt at least {{ limit }} characters")],
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => "Password"],
                'second_options' => ['label' => "Password comfirmation"]
            ])
            ->add('create', SubmitType::class, [
                'label' => "Créer votre compte",
                'row_attr' => ['class' => 'form-button'],
                'attr' => ['class' => 'btnCreate btn-primary']
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
