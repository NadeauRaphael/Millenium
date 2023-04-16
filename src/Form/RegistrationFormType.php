<?php

namespace App\Form;

use Symfony\Component\Form\CallbackTransformer;
use App\Entity\Client;
use Locale;
use phpDocumentor\Reflection\Types\Parent_;
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


class RegistrationFormType extends UserFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        Parent::buildForm($builder, $options);
        $builder->add('email', TextType::class, [
                'required' => true,
                'label' => 'email',
                'attr' => []
        ]);
    }
}
