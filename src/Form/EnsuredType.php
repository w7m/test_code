<?php

namespace App\Form;

use App\Entity\Ensured;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnsuredType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Prénom',
                ],
            ])
            ->add('CIN', NumberType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'CIN',
                ],
            ])
            ->add('city', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Ville',
                ],
            ])
            ->add('zipCode', NumberType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Code postal',
                ],
            ])
            ->add('phone', NumberType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Téléphone',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'required'=>false,
                'attr' => [
                    'placeholder' => 'Email',
                ],
            ]);
        $builder
            ->add('Vehicles', CollectionType::class, [
                'entry_type' => VehicleType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_extra_fields' => true,
                'allow_delete' => true,
                'label' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ensured::class,
        ]);
    }
}
