<?php

namespace App\Form;

use App\Entity\Vehicle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class VehicleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Type de véhicule',
                ],
                'choices' => array(
                    'voiture ' => 'voiture',
                    'camion' => 'camion',
                    'moto' => 'moto',
                ),
            ])
            ->add('registrationNumber', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Matricule',
                ],
            ])
            ->add('imageFile', VichImageType::class, [
                'attr' => [
                    'placeholder' => 'Carte Grise',
                ],
//                'mapped' => false,
            ])
            ->add('horsePower', NumberType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Puissance Fiscale',
                ],
            ])
            ->add('dateOfRegistration', DateType::class, [
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'placeholder' => 'Date première mise circulation',
                ],
            ])
            ->add('doorsNumber', NumberType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nombre portes',
                ],
            ])
            ->add('color', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Couleur',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vehicle::class,
        ]);
    }
}
