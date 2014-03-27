<?php
namespace Entity\Components;

use Entity\Component;
use Render\FormRenderComponent;

/**
 * Class FormComponent
 * @package Entity\Components
 * @Entity
 */
class FormComponent extends Component
{
    /**
     * @Id
     * @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {

    }


    /**
     * @return mixed
     */
    public function getForm()
    {

    }

    public function getTitleComponent()
    {
        return $this->getTitle();
    }

    public function getTitle()
    {
        return "form";
    }

    public function setId($id)
    {
        $this->id=$id;
    }

    public function getRenderComponent()
    {
        return new FormRenderComponent($this);
    }


}