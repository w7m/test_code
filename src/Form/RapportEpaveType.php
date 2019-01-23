<?php

namespace App\Form;

use App\Entity\WreckageReport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RapportEpaveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('estimatedCarPrice', NumberType::class, [
                'label' => 'Prix estimée de la voiture'
            ])
            ->add('repairAmount', NumberType::class, [
                'label' => 'Estimation des travaux'
            ])
            ->add('comments', TextareaType::class, [
                'label' => 'Déroulement de l\'accident'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WreckageReport::class,
        ]);
    }
}
