<?php

namespace ConfigBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use ConfigBundle\Entity\Annee;

class ExamenType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nom', ChoiceType::class,
            [
                'label' => 'Nom de l\'examen',
                'choices'=> [
                    'BEPC' => 'BEPC',
                    'PROBATOIRE' => 'PROBATOIRE',
                    'BACCALAUREAT' => 'BACCALAUREAT',
                ]
            ])
        ->add('montant')
        ->add('anneeEnCour', EntityType::class,
            [
                'class' => Annee::class,
                'query_builder' => function(EntityRepository $er){
                            return $er->createQueryBuilder('c')
                                ->where('c.isAnneeEnCour = true');

                        },
                'label' => 'Année Académique en cours',
            ])
        ->add('classe', EntityType::class,
                    [
                        'class' => 'IntendanceBundle:Classe',
                        'query_builder' => function(EntityRepository $er){
                            return $er->createQueryBuilder('c')
                                ->where('c.classeMere is NULL');

                        }, 
                        'required' => false, 
                        'attr' => 
                            [
                                'data-type' => 'text'
                            ] 
                    ]
                )
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ConfigBundle\Entity\Examen'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'configbundle_examen';
    }


}
