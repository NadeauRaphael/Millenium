<?php

namespace App\Form;

use Symfony\Component\Form\CallbackTransformer;
use App\Entity\Client;
use Locale;
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
                'label' => 'Last name',
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
            ->add('province', CountryType::class, [
                'required' => true,
                'label' => 'Province',
                'attr' => []
            ])
            ->add('postalCode', TextType::class, [
                'required' => true,
                'label' => 'Postal Code',
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
                'attr' => ['class' => 'btnCreate btn-success']
            ]);
            
            $builder->get('phone')->addModelTransformer(new CallbackTransformer(
                function($phoneFromDatabase) {
                    $newPhone = substr_replace($phoneFromDatabase, "-", 3, 0);
                    return substr_replace($newPhone, "-", 7, 0);
                }, 
                function ($phoneFromView) {
                    return str_replace("-", "", $phoneFromView);
                }
            ));

        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
