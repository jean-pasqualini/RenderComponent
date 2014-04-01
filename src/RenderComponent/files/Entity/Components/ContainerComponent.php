<?php
namespace Entity\Components;

use Doctrine\Common\Collections\ArrayCollection;
use Entity\Component;
use Render\ContainerRenderComponent;

/**
 * Class FormComponent
 * @package Entity\Components
 * @Entity
 */
class ContainerComponent extends Component
{
    /**
     * @Id
     * @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToMany(targetEntity="Entity\Component", cascade={"persist"})
     */
    protected $components;

    public function __construct(array $components = array())
    {
        $this->setComponents($components);
    }


    /**
     * @return \Entity\Component[]
     */
    public function getComponents()
    {
        return $this->components;
    }

    public function setComponents(array $components)
    {
        $this->components = new ArrayCollection($components);
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
        return new ContainerRenderComponent($this);
    }

    public function setVisibilityByUsers(array $users = array(), $recursive = true)
    {
        foreach($this->getComponents() as $component)
        {
            $component->setVisibilityByUsers($users);
        }

        parent::setVisibilityByUsers($users);
    }


}