<?php

namespace ConfigBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnneeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('anneeDebut', null,
            [
                'label' => 'Année de départ',
            ]);
        $builder->add('anneeFin', null,
            [
                'label' => 'Année de fin',
            ]);
        $builder->add('isAnneeEnCour', ChoiceType::class,
            [
                'expanded' => true,
                'label' => 'Année en cours ?',
                'multiple' => false,
                'choices'=>
                    [
                        'Oui' => true,
                        'Non' => false
                    ],
                'attr'=>
                    [
                        'checked' => "checked"
                    ],
                'required' => true
            ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ConfigBundle\Entity\Annee'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'configbundle_annee';
    }


}
