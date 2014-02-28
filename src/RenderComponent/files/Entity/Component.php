<?php

namespace Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Component
 * @Entity
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({"questioncomponent" = "Entity\Components\QuestionComponent"})
 */
abstract class Component {

    /**
     * @Id
     * @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToMany(targetEntity="Users", cascade={"persist"})
     */
    protected  $visibilityByUsers;

    /**
     * @return \Render\RenderComponent
     */
    abstract public function getRenderComponent();

    public function __construct()
    {
        $this->visibilityByUsers = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    abstract public function getTitle();


    public function setVisibilityByUsers(array $users = array())
    {
        $this->visibilityByUsers = new ArrayCollection($users);
    }

    /**
     * @return mixed
     */
    public function getVisibilityByUsers()
    {
        return $this->visibilityByUsers;
    }

    public function getKey()
    {
        return __CLASS__."@".$this->getId();
    }



}