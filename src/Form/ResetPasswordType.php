<?php
/**
 * Created by PhpStorm.
 * User: sarfaoui
 * Date: 21/01/2019
 * Time: 13:54
 */

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class,[
                'type' => PasswordType::class,
                'invalid_message' => 'les mots de passe doivent correspondre',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['attr' => ['placeholder' => 'mot de passe'], 'label' => false],
                'second_options' => ['attr' => ['placeholder' => 'répéter mot de passe'], 'label' => false],
                'constraints' => [
                    new Regex([
                        'pattern' => '/\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[\d])\S*$/',
                        'message' => 'mot de passe doit contenir au moins un chiffre et une lettre et au minimum 8 caractère'
                    ])
                ]
            ]);
    }

}