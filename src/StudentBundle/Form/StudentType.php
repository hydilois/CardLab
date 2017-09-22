<?php

namespace StudentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('matricule')->add('nom')->add('dateNaissance')->add('lieuNaissance')->add('nomPere')->add('nomMere')->add('adressePere')->add('adresseMere')->add('personneAcontacter')->add('dernierEtablissementFreq')->add('sexe')->add('photo')->add('classe');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'StudentBundle\Entity\Student'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'studentbundle_student';
    }


}
