<?php

namespace StudentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class InscriptionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('avance')
        ->add('dateDerniereAvance')
        ->add('status')
        ->add('redoublant')
        ->add('student')
        ->add('classe',EntityType::class, array('class'=>'StudentBundle\Entity\Classe',
            'query_builder' => function (\Doctrine\ORM\EntityRepository $repository){
                return $repository->createQueryBuilder('c')->where('c.classePere IS NOT NULL');
            }))
        ->add('annee',EntityType::class, array('class'=>'ConfigBundle\Entity\Annee',
            'query_builder' => function (\Doctrine\ORM\EntityRepository $repository){
                return $repository->createQueryBuilder('a')->where('a.isAnneeEnCour = TRUE');
            }))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'StudentBundle\Entity\Inscription'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'studentbundle_inscription';
    }


}
