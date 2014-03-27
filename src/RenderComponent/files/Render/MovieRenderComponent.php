<?php
namespace Render;

use Entity\Components\MovieComponent;
use interfaces\UserInterface;

class MovieRenderComponent extends RenderComponent
{
    private $movieComponent;

    public function __construct(MovieComponent $movieComponent)
    {
        $this->movieComponent = $movieComponent;
    }

    public function getRender($mode = self::VIEW_HTML, UserInterface $user = null)
    {
        return $this->getContainer()->get("twig")->render(
        	"video/component.html.twig",
        	array_merge(
        		$this->movieComponent->getMovie()->toArray(),
        		array(
        			"mode" => $mode
        		)
        	)
        );
    }
}