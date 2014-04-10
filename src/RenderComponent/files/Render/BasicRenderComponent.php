<?php

namespace Render;
use interfaces\UserInterface;

/**
 * Class BasicRenderComponent
 * @package Render
 * service | | entity | | object
 * UserInterface => Object conctextual
 */
class BasicRenderComponent extends RenderComponent
{
    private $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function getRender($mode = self::VIEW_HTML, UserInterface $user = null)
    {
        return $this->message;
    }
}