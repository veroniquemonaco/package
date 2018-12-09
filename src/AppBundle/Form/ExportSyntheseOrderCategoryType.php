<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ExportSyntheseOrderCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('yearPaquetage2', TextType::class)
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'button buttonAdmin'
                ],
                'label' => 'search'
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
        return 'app_bundle_export_synthese_order_category_type';
    }
}
