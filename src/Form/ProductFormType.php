<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use phpDocumentor\Reflection\PseudoTypes\False_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name',TextType::class,[
            'required' => true,
            'label' => 'Name',
            'attr' => ['class' => "form-input-bg"]
        ])
        ->add('price',MoneyType::class,[
            'required'=>true,
            'currency' => 'CAD',
            'attr' => ['class' => 'form-input-bg']
        ])
        ->add('stockQuantity',NumberType::class,[
            'required'=>true,
            'label'=>'Quantity in stock',
            'attr' => ['class' => 'form-input-bg']
        ])
        ->add('description',TextareaType::class,[
            'required' => true,
            'label' => 'Description',
            'attr' => ['class' => 'form-input-bg']
        ])
        ->add('image',FileType::class,[
            'required' => false,
            'label' => 'Product Image',
            'mapped' => false,
            'attr' => ['class' => 'form-input-bg'],
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/png',
                        'image/jpeg'
                    ],
                    'mimeTypesMessage' => 'Upload a valid image'
                ])
            ]
        ])
        ->add('category',EntityType::class,[
            'required' => true,
            'class' => Category::class,
            'choice_label' => 'category'
        ])
        ->add('create', SubmitType::class, [
            'label' => "Update",
            'row_attr' => ['class' => 'form-button'],
            'attr' => ['class' => 'btnCreateProduct btn-success']
        ]);
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
