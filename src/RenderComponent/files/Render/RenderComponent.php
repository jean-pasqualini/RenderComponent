<?php

namespace Render;

use Entity\Component;
use Entity\Profiles;
use interfaces\containerAwaireInterface;
use interfaces\UserInterface;

abstract class RenderComponent implements containerAwaireInterface
{
    const VIEW_HTML = "HTML";
    const VIEW_TEXTE = "TEXTE";
    const VIEW_SMS = "SMS";
    const VIEW_MAIL = "MAIL";

    abstract public function getRender($mode = self::VIEW_HTML, UserInterface $user = null);

    private $container;

    private $component;

    public function __construct(Component $component)
    {
        $this->component = $component;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }


    public function getApiKeyAuth(Profiles $profile = null)
    {
            $apiKeyAuth =  $this->getContainer()
                ->get("app.managers.apikeyauth")
                ->getApiKeyAuth($profile, $this->component->getKey())
            ;

        return $apiKeyAuth;
    }


}