<?php
/**
 * Created by PhpStorm.
 * User: veronique
 * Date: 07/10/18
 * Time: 18:23
 */

namespace AppBundle\Form;

use AppBundle\Entity\Agence;
use AppBundle\Entity\Commande;
use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportUserCommandesType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('Recherche', SearchType::class, [
                'required' => false,
                'attr'     => ['placeholder'  => 'Entrez un nom...',
                    'autocomplete' => 'off',
                ],
            ])
            ->add('rechercher',SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'=>null
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_export_user_commandes_type';
    }

}