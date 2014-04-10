<?php

namespace Entity\Form;
use Entity\Form;

/**
 * @Entity
 */
class FormSondage extends Form
{
    /**
     * @Id
     * @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    public function __construct()
    {

    }

    public function getForm()
    {
        return new FormSondage();
    }

    public function getData()
    {
        return array();
    }

    public function setData()
    {

    }
}