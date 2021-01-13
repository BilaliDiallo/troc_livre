<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('Nom')
        ->add('prenom')
        ->add('Pseudo')
        ->add('telephone')
        ->add('pays' , TextType::class,[
            
            'label'=>'Renseignez votre pays',
            'mapped' => false,
            'required' => true
        ])
        ->add('region' , TextType::class,[
            
            'label'=>'Renseignez votre région',
            'mapped' => false,
            'required' => true
        ])
        ->add('departement' , TextType::class,[
            
            'label'=>'Renseignez votre département',
            'mapped' => false,
            'required' => true
        ])
        ->add('commune' , TextType::class,[
            
            'label'=>'Renseignez votre commune',
            'mapped' => false,
            'required' => true
        ])
        ->add('civilite', ChoiceType::class, array(
            'choices' => array(
               'Monsieur' => 'Monsieur',
               'Madame' => 'Madame',
               'Mademoiselle' => 'Mademoiselle',
           ),
               //'choices_as_values' => true,
         ))
            ->add('email')
            
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au minimum{{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label'=>"J'accepte les conditions d'utilisation.",
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => "Veillez-cocher cette case avant de continuer.",
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
