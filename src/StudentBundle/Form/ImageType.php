<?php

namespace StudentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\Extension\Core\Type\FileType;

class ImageType extends AbstractType{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('file', FileType::class,
                [
                    'required' => false
                ]);
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver){
        $resolver->setDefaults(array(
            'data_class' => 'StudentBundle\Entity\Image'
        ));
    }

    /**
     * @return string
     */
    public function getName(){
        return 'studentbundle_image';
    }
}
