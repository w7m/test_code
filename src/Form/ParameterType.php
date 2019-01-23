<?php

namespace App\Form;

use App\Entity\Parameter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ParameterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('photoPrice', NumberType::class,[
                'label' => 'Prix d\'une photo: ',
                'attr' => [
                    'placeholder' => 'Prix d\'une photo',
                    'min'=>1,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer un prix de photo',
                    ]),
                ],
            ])
            ->add('openingFileExpense', NumberType::class,[
                'label' => 'Frais d\'ouverture de dossier: ',
                'attr' => [
                    'placeholder' => 'Frais d\'ouverture de dossier',
                    'min'=>1,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer un frais d\'ouverture de dossier',
                    ]),
                ],
            ])
            ->add('expertiseFees', NumberType::class,[
                'label' => 'Frais d\'expertise: ',
                'attr' => [
                    'placeholder' => 'Frais d\'expertise',
                    'min'=>1,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer un frais d\'expertise',
                    ]),
                ],
            ])
            ->add('billPercentage', NumberType::class,[
                'label' => 'Pourcentage de factures: ',
                'attr' => [
                    'placeholder' => 'Pourcentage de factures',
                    'min'=>1,
                    'max'=>50,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer un Pourcentage de factures',
                    ]),
                ],
            ])
            ->add('insuranceCompanyName', TextType::class,[
                'label' => 'Nom de la société',
                'attr' => [
                    'placeholder' => 'Nom de la société',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer un nom de société',
                    ]),
                ],
            ])
            ->add('insurerAddress', TextType::class,[
                'label' => 'Adresse de l\'assurance: ',
                'attr' => [
                    'placeholder' =>  'Adresse de l\'assurance: ',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer une adresse',
                    ]),
                ],
            ])
            ->add('insurerCity', TextType::class,[
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Ville',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer un nom de ville',
                    ]),
                ],
            ])
            ->add('postcodeInsurer', NumberType::class,[
                'label' => 'Code postal',
                'attr' => [
                    'placeholder' => 'Code postal',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer un code postal',
                    ]),
                    new Regex([
                        'pattern' => '&^\d\d\d\d$&',
                        'message' => 'le code doit etre composé de 4 chiffres'
                    ]),
                ],
            ])
            ->add('insurancePhone', TelType::class,[
                'label' => 'N° tel: ',
                'attr' => [
                    'placeholder' => 'n° tel',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer un numero de téléphone',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Parameter::class,
        ]);
    }
}
