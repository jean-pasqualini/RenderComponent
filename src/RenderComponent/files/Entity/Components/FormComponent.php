<?php
namespace Entity\Components;

use Entity\Component;
use FormType\SondageType;
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

    private $form;

    public function __construct()
    {
        $this->form = new SondageType();
    }

    /**
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
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