<?php

namespace MatiereBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EstDispenseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('coefficient')->add('nombreHeuresAnnuel')->add('nombreLeconsAnnuel')->add('titulaire')->add('matiere')->add('enseignant')->add('classe')->add('annee');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MatiereBundle\Entity\EstDispense'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'matierebundle_estdispense';
    }


}
