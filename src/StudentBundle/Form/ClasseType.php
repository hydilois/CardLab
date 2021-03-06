<?php

namespace StudentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class ClasseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nom')
        ->add('abreviation')
        ->add('classePere',EntityType::class, array('class'=>'StudentBundle\Entity\Classe',
            'query_builder' => function (\Doctrine\ORM\EntityRepository $repository){
                return $repository->createQueryBuilder('c')->where('c.classePere is NULL');
            },
                'required' => false
            ))
        ->add('classeNext',EntityType::class, array('class'=>'StudentBundle\Entity\Classe',
            'query_builder' => function (\Doctrine\ORM\EntityRepository $repository)
            {return $repository->createQueryBuilder('c')->where('c.classePere is NULL');}))
        ->add('cycle');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'StudentBundle\Entity\Classe'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'studentbundle_classe';
    }


}
