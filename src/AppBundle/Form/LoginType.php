<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\EntityRepository;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

             $builder
                 ->add('matricule', TextType::class, [
                 'attr' => [
                     'class' => 'input-login input-matricule',
                     'placeholder' => ''
                 ]
             ])
                 ->add('username', TextType::class, [
                     'attr' => [
                         'class' => 'input-login input-username',
                         'placeholder' => ''
                     ]
                 ])
                 ->add('password',PasswordType::class, [
                     'attr' => [
                         'class' => 'input-login'
                     ]
                 ])
                 ->add('submit', SubmitType::class, [
                     'attr' => [
                         'class' => 'button buttonBlue'
                     ],
                     'label' => 'Envoyer'
                 ]);

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class'=>User::class]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_login_type';
    }

}