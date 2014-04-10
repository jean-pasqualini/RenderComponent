<?php

namespace  controllers;
use Symfony\Component\HttpFoundation\Response;

use \app;

class component extends \controllers\AbstractController {
    //Constructor
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('cookie');

        $this->load->library('Form_validation');

        $this->load->model('Users_model');
        $this->load->model('Email_model');
        $this->load->model('Message_model');
        $this->load->model('Contacts_model');

        app::getInstance()->getEventManager()->notify("codeigniter.loadlibrary", array(
            "dx_auth" => $this->dx_auth,
            "session" => $this->session,
            "gallery" => $this->Gallery,
            "commonmodel" => $this->Common_model,
            "db" => $this->db,
            "uri" => $this->uri
        ));
    }

    public function render($componentId)
    {
        $em = $this->getEntityManager();

        $componentRepository = $em->getRepository("Entity\\Component");

        $component = $componentRepository->find($componentId);

        if(null === $component) return;

        $renderComponent = $component->getRenderComponent();

        if($renderComponent instanceof \interfaces\containerAwaireInterface) $renderComponent->setContainer($this->getContainer());

        echo $renderComponent->getRender();
    }

    public function form($componentId)
    {
            $em = $this->getEntityManager();

        $request = $this->getRequest();

            $componentRepository = $em->getRepository("Entity\\Component");

            /**
             * @var $component \Entity\Components\FormComponent
             */
            $component = $componentRepository->find($componentId);

            if(null === $component) return;

            $form = $this->createForm($component->getForm());

        if($request->getMethod() == "POST")
        {
            $form->bindRequest($request);

            if($form->isValid())
            {
                $this->get("eventmanager")->notify("component.form.submitted", $form);
            }
        }

        if($request->isXmlHttpRequest())
        {
            return new Response(json_encode(array(), true));
        }
        else
        {
            return $this->render("component/form/submitted.html.twig", array(
                "component" => $component
            ));
        }
    }

}