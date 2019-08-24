<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;

class ProductType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$p = new ProductRepository();
        $builder
            ->add('code', TextType::class, [
                'required' => true, 
                'constraints' => array(
                    new Regex(['pattern' => '/^[a-zA-Z0-9]+$/', 'message' => 'El valor del campo "Código" puede contener unicamente minúsculas, mayúsculas, numeros dígitos.']), 
                    new Length(['min' => 4, 'minMessage' => 'El valor del campo "Nombre" debe tener por lo menos {{ limit }} caracteres.']),
                    new Length(['max' => 10, 'maxMessage' => 'El valor del campo "Nombre" debe tener como máximo {{ limit }} caracteres.'])
                )
            ])
            ->add('name', TextType::class, [
                'required' => true, 
                'constraints' => array(
                    new Length(['min' => 4, 'minMessage' => 'El valor del campo "Nombre" debe tener por lo menos {{ limit }} caracteres.'])
                )
            ])
            ->add('description', TextareaType::class, [
                'required' => true
            ])
            ->add('brand', TextType::class, [
                'required' => true
            ])
            ->add('price', NumberType::class, [
                'required' => true, 
                'constraints' => array(
                    new Positive(['message' => 'El valor del campo "Precio" no es válido.'])
                )
            ])
            ->add('category', EntityType::class, [
                'required' => true, 
                'class' => Category::class,
                'query_builder' => function (CategoryRepository $cr) {
                    return $cr->createQueryBuilder('c')
                       ->where('c.active = 1')
                       ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }

}
