<?php

namespace AppBundle\Form;

use AppBundle\Entity\Agence;
use AppBundle\Entity\Direction;
use AppBundle\Entity\Qualification;
use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class, [
            'attr' => [
                'class' => 'input-login'
            ]
        ])
            ->add('firstname', TextType::class , [
                'attr' => [
                    'class' => 'input-login'
                ]
            ])
            ->add('lastname', TextType::class , [
                'attr' => [
                    'class' => 'input-login'
                ]
            ])
            ->add('email', TextType::class , [
                'attr' => [
                    'class' => 'input-login'
                ]
            ])
            ->add('password', RepeatedType::class, ['type' => PasswordType::class], [
                'attr' => [
                    'class' => 'input-login'
                ]
            ])
            ->add('qualification', EntityType::class, [
                'class'=>Qualification::class,
                'choice_label'=>'name',
            ])
            ->add('agence', EntityType::class, [
                'class'=>Agence::class,
                'choice_label'=>'name'
            ])
            ->add('direction', EntityType::class, [
                'class'=>Direction::class,
                'choice_label'=>'name'
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'button buttonBlue'
                ],
                'label' => 'Valider'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class'=>User::class]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_registration_type';
    }

}