<?php

namespace App\Form;

use App\Entity\Livre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('titre')
           
        ->add('langue')
        ->add('nombrePages') 
   
        ->add('dateDePublication')
        ->add('auteur' , TextType::class,[
            
           
            'mapped' => false,
            'required' => true
        ])
        ->add('isbn' , TextType::class,[
            
            'label'=>'Code isbn',
            'mapped' => true,
            'required' => true
        ])

        ->add('categorie' , TextType::class,[
            
           
            'mapped' => false,
            'required' => true
        ])
        ->add('image', FileType::class,[
            
            'multiple' => false,
            'mapped' => false,
            'required' => true
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}
