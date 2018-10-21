<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Qualification;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ExportAllCommandesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('qualification',EntityType::class, [
//                'class'=>Qualification::class,
//                'choice_label'=>'name'
//            ])
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
        return 'app_bundle_export_all_commandes_type';
    }
}
