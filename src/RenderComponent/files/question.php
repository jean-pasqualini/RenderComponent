<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class question extends \controllers\AbstractController {

    //Constructor
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('url');
        $this->load->model('Users_model');

        app::getInstance()->getEventManager()->notify("codeigniter.loadlibrary", array(
            "dx_auth" => $this->dx_auth,
            "session" => $this->session,
            "gallery" => $this->Gallery,
            "commonmodel" => $this->Common_model,
            "db" => $this->db,
            "twconnect" => $this->twconnect,
            "uri" => $this->uri
        ));
    }

    public function answer($questioncomponentid, $value)
    {
        $em = $this->getEntityManager();

        $request = $this->getRequest();

        $questionComponent = $em->getRepository("Entity\\Components\\QuestionComponent")->find($questioncomponentid);

        $question = $questionComponent->getQuestion();

        $question->addAnswer(new \Entity\AnswerQuestion($value, $this->getUser()));

        $eventManager = $this->getContainer()->get("eventmanager");

        $eventManager->notify("question.answer.change", $question, $this->getUser());

        $em->persist($question);

        $em->flush();

        $recepters = ($questionComponent->getVisibilityByUsers()->isEmpty()) ? array($this->get("user")) : $questionComponent->getVisibilityByUsers()->toArray();

        $recepters = $this->get("render.eventclient")->getSessionsByUsers($recepters);

        $this->get("render.ui")->updateComponent($questionComponent, $recepters);

        if($request->isXmlHttpRequest())
        {
            $dataJson = array(
                "question" => $question->getQuestion()
            );

            echo json_encode($dataJson, true);
        }
        else
        {
            echo $this->render("question/answer.html.twig", array("renderComponent" => $questionComponent->getRenderComponent()))->getContent();
        }
    }

}

?>