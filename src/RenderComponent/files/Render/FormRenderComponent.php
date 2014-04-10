<?php
namespace Render;

use Entity\Components\FormComponent;
use interfaces\UserInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormTypeInterface;

class FormRenderComponent extends RenderComponent
{
    private $formComponent;

    public function __construct(FormComponent $formComponent)
    {
        $this->formComponent = $formComponent;
    }

    private function getForm()
    {
        $form = $this->formComponent->getForm();

        if($form instanceof FormTypeInterface)
        {
            return $form;
        }
        elseif(is_string($form) && $this->getContainer()->has($form))
        {
            return $this->getContainer()->get($form);
        }
        else
        {
            throw new \Exception("unable init form component");
        }
    }

    public function getRender($mode = self::VIEW_HTML, UserInterface $user = null)
    {
        return $this->getContainer()->get("twig")->render(
        	"component/form/component.html.twig",
        	array(
        		"form" => $this->getContainer()->get("form.factory")->create($this->getForm())->createView(),
        		"mode" => $mode
        	)
        );
    }
}