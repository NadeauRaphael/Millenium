<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
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
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Locales;


class PasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('oldPassword', PasswordType::class, [
            'invalid_message' => "The password must be identical.",
            'constraints' => [new Assert\Length(min: 6, minMessage: "The password must containt at least {{ limit }} characters")],
            'attr' => ['class' => 'password-field form-input-bg'],
            'required' => true,
            'label' => 'Current Password'
        ])
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => "The password must be identical.",
            'constraints' => [new Assert\Length(min: 6, minMessage: "The password must containt at least {{ limit }} characters")],
            'options' => ['attr' => ['class' => 'password-field form-input-bg']],
            'required' => true,
            'first_options' => ['label' => "New Password"],
            'second_options' => ['label' => "Password comfirmation"]
        ])
        ->add('Change', SubmitType::class, [
            'label' => "Change password",
            'row_attr' => ['class' => 'form-button'],
            'attr' => ['class' => 'btnCreateAccount btn-success']
        ]);
    }
}
