<?php

namespace AppBundle\Form;

use AppBundle\Entity\Category;
use AppBundle\Entity\Taille;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('fournisseur')
            ->add('prix')
            ->add('role')
            ->add('category', EntityType::class, [
                'class'=>Category::class,
                'choice_label'=>'name'
            ])
            ->add('tailles', EntityType::class, [
                'class'=>Taille::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
                'choice_label'=>'name',
                'expanded'=>true,
                'required'=>false,
                'multiple'=>true,
                'attr'=> ['class'=>'selectpicker']
            ])
            ->add('image');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Product'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_product';
    }


}
