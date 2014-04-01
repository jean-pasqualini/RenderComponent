<?php
namespace Render;

use Entity\Components\ContainerComponent;
use Entity\Components\MovieComponent;
use interfaces\UserInterface;

class ContainerRenderComponent extends RenderComponent
{
    private $containerComponent;

    public function __construct(ContainerComponent $containerComponent)
    {
        $this->containerComponent = $containerComponent;
    }

    public function getRender($mode = self::VIEW_HTML, UserInterface $user = null)
    {
        return $this->getContainer()->get("twig")->render(
            "component/container/component.html.twig",
            array(
                "components" => $this->containerComponent->getComponents(),
                "mode" => $mode
            )
        );
    }
}