<?php

namespace Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Component
 * @Entity
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({
 *    "questioncomponent" = "Entity\Components\QuestionComponent",
 *    "moviecomponent" = "Entity\Components\MovieComponent",
 *    "formcomponent" = "Entity\Components\FormComponent",
 *    "containercomponent" = "Entity\Components\ContainerComponent"
 * })
 */
abstract class Component {

    /**
     * @Id
     * @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToMany(targetEntity="Profiles", cascade={"persist"})
     */
    protected $visibilityByProfiles;

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
        $this->visibilityByProfiles = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    abstract public function getTitle();

    public function setVisibilityByUsers(array $users = array())
    {
        $this->visibilityByUsers = new ArrayCollection($users);

        $this->setVisibilityByProfiles(array());

        foreach($users as $user)
        {
            $this->getVisibiblityByProfiles()->add($user->getProfile());
        }
    }

    public function setVisibilityByProfiles(array $profiles = array())
    {
        $this->visibilityByProfiles = new ArrayCollection($profiles);
    }

    public function getVisibiblityByProfiles()
    {
        return $this->visibilityByProfiles;
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