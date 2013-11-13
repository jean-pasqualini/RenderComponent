<?php

namespace Render;

class BasicRenderComponent extends RenderComponent
{
    private $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function getRender()
    {
        return $this->message;
    }
}