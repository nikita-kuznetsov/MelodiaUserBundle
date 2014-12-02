<?php

namespace Melodia\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Melodia\UserBundle\Form\DataTransformer\StringToBooleanTransformer;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('plainPassword', 'password')
            ->add('fullName', 'text', array('required' => false))
            ->add($builder->create('isActive', 'text')->addViewTransformer(new StringToBooleanTransformer()))

            ->add('add', 'submit')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Melodia\UserBundle\Entity\User',
        ));
    }

    public function getName()
    {
        return '';
    }
}
