<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'required' => true, 
                'constraints' => array(
                    new Regex(['pattern' => '/^[a-zA-Z0-9]+$/', 'message' => 'El valor del campo "Código" puede contener unicamente minúsculas, mayúsculas, numeros dígitos.'])
                )
            ])
            ->add('name', TextType::class, [
                'required' => true, 
                'constraints' => array(
                    new Length(['min' => 2, 'minMessage' => 'El valor del campo "Nombre" debe tener por lo menos {{ limit }} caracteres.'])
                )
            ])
            ->add('description', TextareaType::class, [
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
