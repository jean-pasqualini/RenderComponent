<?php

namespace FormType;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class ComponentType extends \Symfony\Component\Form\AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "component";
    }

    public function getParent()
    {
        return "form";
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {

    }


    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(\Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array());

        $resolver->setDefaults(array(
        ));
    }

}