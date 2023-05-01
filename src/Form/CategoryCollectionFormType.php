<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryCollectionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categories', CollectionType::class, [
                'entry_type' => CategoryType::class,
                'allow_add' => true
            ])
            ->add('btnSave', SubmitType::class, [
                'label' => 'Update',
                'row_attr' => ['class' => 'form-button'],
                'attr' => ['class' => 'btnSave col-6 btn btn-success']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'category_collection_form'
        ]);
    }
}
