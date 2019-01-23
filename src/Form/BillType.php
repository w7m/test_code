<?php

namespace App\Form;

use App\Entity\Bill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bill_ref', TextType::class, [
                'label' => 'Référence de la facture'
            ])
            ->add('bill_date', DateType::class, [
                'label' => 'Date de facturation',
                'widget' => 'single_text'
            ])
            ->add('type', TextType::class, [
                'label' => 'Type des travaux'
            ])
            ->add('works', TextType::class, [
                'label' => 'Travaux facturés'
            ])
            ->add('realAmount', NumberType::class, [
                'label' => 'Montant du devis'
            ])
            ->add('estimaedAmount', NumberType::class, [
                'label' => 'Estimation du montant'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bill::class,
        ]);
    }
}
