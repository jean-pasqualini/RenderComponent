<?php

namespace service;

use interfaces\containerAwaireInterface;
use Render\BasicRenderComponent;
use Render\RenderComponent;

class ApplicationTwigPlus extends \Twig_Extension implements containerAwaireInterface
{
    private $container;

    public function __construct($container)
    {
        $this->setContainer($container);
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter("renderComponent", array($this, "renderComponent"), array('is_safe' => array('html')))
        );
    }


    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function renderComponent(RenderComponent $renderComponent = null)
    {
        if($renderComponent === null) return;

        if($renderComponent instanceof containerAwaireInterface) $renderComponent->setContainer($this->getContainer());

        return $renderComponent->getRender();
    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return "application.twig.plus";
    }
}