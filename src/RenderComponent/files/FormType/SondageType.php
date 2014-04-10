<?php

namespace FormType;

use Entity\Components\QuestionComponent;
use Entity\Question\QuestionUnscribeMail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SondageType extends AbstractType
{
    public function getName()
    {
        return "sondage";
    }

    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $question = new QuestionUnscribeMail();

        $builder
            ->add('test', 'component', array(
                'label' => 'un composant',
                'data' =>  (new QuestionComponent($question))->getRenderComponent(),
                'mapped' => false,
            ))
            ->add("untest", "text")
            ->add("lo", "datetime")
            ->add("sa", "submit")
        ;
    }
}