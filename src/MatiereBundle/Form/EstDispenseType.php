<?php

namespace MatiereBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class EstDispenseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('coefficient')
        ->add('nombreHeuresAnnuel')
        ->add('nombreLeconsAnnuel')
        ->add('titulaire', null,
            [
                'required' => false
            ])
        ->add('matiere')
        ->add('enseignant')
        ->add('classe',EntityType::class, array('class'=>'StudentBundle\Entity\Classe',
            'query_builder' => function (\Doctrine\ORM\EntityRepository $repository)
            {return $repository->createQueryBuilder('c')->where('c.classePere is NOT NULL');}))
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
