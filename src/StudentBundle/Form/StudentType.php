<?php

namespace StudentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class StudentType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('matricule')
                ->add('nom')
                // ->add('dateNaissance')
                        ->add('dateNaissance', DateType::class, 
                [
                    'widget' => 'single_text',
                    'label' => "Date de naissance",
                    'required' => false,
                    'attr' => [
                        'data-type' => 'date'
                    ]
                ]
            )
                ->add('lieuNaissance')
                ->add('nomPere')
                ->add('nomMere')
                ->add('adressePere')
                ->add('adresseMere')
                ->add('personneAcontacter')
                ->add('dernierEtablissementFreq')
                ->add('sexe')
                ->add('photo', Component\Form\Extension\Core\Type\FileType::class, ['required' => false])
                ->add('classe',EntityType::class, array('class'=>'StudentBundle\Entity\Classe',
            'query_builder' => function (\Doctrine\ORM\EntityRepository $repository)
            {return $repository->createQueryBuilder('c')->where('c.classePere is NOT NULL');}))
                ->add('redoublant')
                ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'StudentBundle\Entity\Student'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'studentbundle_student';
    }

}
