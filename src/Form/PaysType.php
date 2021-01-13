<?php

namespace App\Form;

use App\Entity\Pays;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaysType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           
            ->add('id', TextType::class,[
            
                'label' => 'Entrer le code ISBN du livre',
                'mapped' => false,
                'required' => true
            ])
            ->add('nomPays', TextType::class,[
            
                'label' => 'Categorie du livre (Roman, Théatre, Poésie, etc ...)',
                'mapped' => true,
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pays::class,
        ]);
    }
}
