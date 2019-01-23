<?php

namespace App\Form;

use App\Entity\Expert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ExpertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'constraints' => [
                    new Email([
                        'message' => "L'email '{{ value }}' est invalide",
                    ]),
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer un nom',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Prenom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer un prenom',
                    ]),
                ],
            ])
            ->add('phoneNumber', NumberType::class, [
                'label' => 'N° de téléphone',
                'attr' => ['maxlength' => '8'],
                'required' => true,
            ])
            ->add('registration_tax_number', TextType::class, [
                'label' => 'Matricule fiscale',
                'required' => true,
            ])
            ->add('company_name', TextType::class, [
                'label' => 'Raison sociale',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer le nom de l\'entreprise',
                    ]),
                ],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer une adresse',
                    ]),
                ],
            ])
            ->add('city', ChoiceType::class, [
                'label' => 'Ville',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez choisir un pays',
                    ]),
                ],
                'choices' => [
                    'Tunis' => 'tunis',
                    'Sfax' => 'Sfax',
                    'Sousse' => 'Sousse',
                    'Bizerte' => 'bizerte',
                ]
            ])
            ->add('postal_code', NumberType::class, [
                'label' => 'Code postal',
                'attr' => ['maxlength' => '4'],
                'required' => true,
                'constraints' => [
                    new Regex([
                        'pattern' => '&^\d{4}$&',
                        'message' => 'Vous devez mettre un code postal 4 chiffre'
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Expert::class,
        ]);
    }
}
