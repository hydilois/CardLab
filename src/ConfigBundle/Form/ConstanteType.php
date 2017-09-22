<?php

namespace ConfigBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component;
use StudentBundle\Form\ImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ConstanteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('ville')->add('nomFrancais')->add('nomAnglais')->add('deviseFrancais')->add('deviseAnglais')
        ->add('boitePostal')
        ->add('logo',ImageType::class, 
            [
                // 'class' => Image::class,
                'required' => false
            ]
            );
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ConfigBundle\Entity\Constante'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'configbundle_constante';
    }


}
