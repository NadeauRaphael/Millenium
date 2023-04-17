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


class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('firstName', TextType::class, [
            'required' => true,
            'label' => 'First name',
            'attr' => ['class' => 'form-input-bg']
        ])
            ->add('lastName', TextType::class, [
                'required' => true,
                'label' => 'Last name',
                'attr' => ['class' => 'form-input-bg']
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'label' => 'Phone',
                'attr' => ['class' => 'form-input-bg']
            ])
            ->add('adress', TextType::class, [
                'required' => true,
                'label' => 'Adress',
                'attr' => ['class' => 'form-input-bg']
            ])
            ->add('city', TextType::class, [
                'required' => true,
                'label' => 'City',
                'attr' => ['class' => 'form-input-bg']
            ])
            ->add('province', ChoiceType::class, [
                'required' => true,
                'label' => 'Province',
                'choices' => [
                    'Alberta' => 'AB',
                    'Colombie-Britannique' => 'BC',
                    'Manitoba' => 'MB',
                    'Nouveau-Brunswick' => 'NB',
                    'Terre-Neuve-et-Labrador' => 'NL',
                    'Nouvelle-Écosse' => 'NS',
                    'Territoires du Nord-Ouest' => 'NT',
                    'Nunavut' => 'NU',
                    'Ontario' => 'ON',
                    'Île-du-Prince-Édouard' => 'PE',
                    'Québec' => 'QC',
                    'Saskatchewan' => 'SK',
                    'Yukon' => 'YT',],
                'attr' => []
            ])
            ->add('postalCode', TextType::class, [
                'required' => true,
                'label' => 'Postal Code',
                'attr' => ['class' => 'form-input-bg']
            ])
            ->add('create', SubmitType::class, [
                'label' => "Update",
                'row_attr' => ['class' => 'form-button'],
                'attr' => ['class' => 'btnCreateAccount btn-success']
            ]);

        $builder->get('phone')->addModelTransformer(new CallbackTransformer(
            function ($phoneFromDatabase) {
                $newPhone = substr_replace($phoneFromDatabase, "-", 3, 0);
                return substr_replace($newPhone, "-", 7, 0);
            },
            function ($phoneFromView) {
                return str_replace("-", "", $phoneFromView);
            }
        ));
        $builder->get('postalCode')->addModelTransformer(new CallbackTransformer(
            function ($postalCodeFromDatabase) {
                return substr_replace($postalCodeFromDatabase, " ", 3, 0);
            },
            function ($postalCodeFromView) {
                return str_replace(" ", "", $postalCodeFromView);
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
