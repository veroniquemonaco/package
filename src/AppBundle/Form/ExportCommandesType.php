<?php

namespace AppBundle\Form;

use AppBundle\Entity\Agence;
use AppBundle\Entity\Qualification;
use AppBundle\Entity\Commande;
use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportCommandesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('agence', EntityType::class, [
                'class'=>Agence::class,
                'choice_label'=>'name',
                'required' => false
            ])
            ->add('qualification',EntityType::class, [
                'class'=>Qualification::class,
                'choice_label'=>'name',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'button buttonAdmin'
                ],
                'label' => 'Rechercher'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'=>null
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_export_commandes_type';
    }
}
