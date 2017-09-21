<?php

namespace ConfigBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use ConfigBundle\Entity\Annee;
use Doctrine\ORM\EntityRepository;

class ScolariteType extends AbstractType{
    private $em;

    public function __construct($em){
        $this->em = $em;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('montantContributionExigible')
                ->add('montantApee')
                ->add('anneeEnCour',EntityType::class,
                [
                    'label' => 'Année Académique',
                    'class' => Annee::class,
                    'query_builder' => function(EntityRepository $er){

                         return $er->createQueryBuilder('s')
                            ->where('s.isAnneeEnCour = true');
                        }
                ] )
                ->add('cycle', ChoiceType::class,
            [
                'expanded' => true,
                'multiple' => false,
                'choices'=>
                    [
                        'Premier' => true,
                        'Second' => false
                    ],
                'attr'=>
                    [
                        'checked' => "checked"
                    ],
                'required' => true
            ])
                ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ConfigBundle\Entity\Scolarite'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'configbundle_scolarite';
    }


}
