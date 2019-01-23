<?php

namespace App\Form;

use App\Entity\Insurer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class InsurerType extends AbstractType
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Insurer::class,
        ]);
    }
}
