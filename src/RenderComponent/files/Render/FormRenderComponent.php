<?php
namespace Render;

use Entity\Components\FormComponent;
use interfaces\UserInterface;

class FormRenderComponent extends RenderComponent
{
    private $formComponent;

    public function __construct(FormComponent $formComponent)
    {
        $this->formComponent = $formComponent;
    }

    public function getRender($mode = self::VIEW_HTML, UserInterface $user = null)
    {
        return $this->getContainer()->get("twig")->render(
        	"video/component.html.twig",
        	array(
        		"form" => $this->getContainer()->get("form.factory")->createForm($this->movieComponent->getForm()),
        		"mode" => $mode
        	)
        );
    }
}