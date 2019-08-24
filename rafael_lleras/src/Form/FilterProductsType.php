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

class FilterProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$p = new ProductRepository();
        $builder
            ->add('code', TextType::class, [
                'required' => false, 
                'constraints' => array(
                    new Regex(['pattern' => '/^[a-zA-Z0-9]+$/', 'message' => 'El valor del campo "Código" puede contener unicamente minúsculas, mayúsculas, numeros dígitos.'])
                )
            ])
            ->add('name', TextType::class, [
                'required' => false, 
                'constraints' => array(
                )
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('brand', TextType::class, [
                'required' => false
            ])
            ->add('price', NumberType::class, [
                'required' => false, 
                'constraints' => array(
                )
            ])
            ->add('category', EntityType::class, [
                'required' => false, 
                'class' => Category::class,
                'query_builder' => function (CategoryRepository $cr) {
                    return $cr->createQueryBuilder('c')
                       ->where('c.active = 1')
                       ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name', 
                'placeholder' => 'Seleccione una opción'
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
