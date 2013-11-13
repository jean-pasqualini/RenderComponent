<?php

namespace Render;

use interfaces\containerAwaireInterface;
use interfaces\UserInterface;

abstract class RenderComponent implements containerAwaireInterface
{
    const VIEW_HTML = 1;
    const VIEW_TEXTE = 2;
    const VIEW_SMS = 3;

    abstract public function getRender($mode = self::VIEW_HTML, UserInterface $user = null);

    private $container;

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }


}