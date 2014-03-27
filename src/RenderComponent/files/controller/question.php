<?php

namespace  controllers;
use Exception\HumanException;
use Symfony\Component\HttpFoundation\Response;

use \app;



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
            "uri" => $this->uri
        ));
    }

    public function answer($questioncomponentid, $value, $apikeyauth)
    {
        $applicationManager = $this->getApplicationManager();

        $apiKeyAuthManager = $applicationManager->getApiKeyAuthManager();

        $em = $this->getEntityManager();

        $request = $this->getRequest();

        $questionComponent = $em->getRepository("Entity\\Components\\QuestionComponent")->find($questioncomponentid);

        $apiKeyAuth = $apiKeyAuthManager->getApiKeyAuth(null, $questionComponent->getKey(), $apikeyauth);

        if(null === $apiKeyAuth) throw new HumanException("api key auth invalid");

        $userFind = $apiKeyAuth->getProfile()->getUser();

        $question = $questionComponent->getQuestion();

        if($question->isUserAnswerValue($userFind, $value))
        {
            if($request->isXmlHttpRequest())
            {
                $dataJson = array(
                    "question" => $question->getQuestion()
                );

                return new Response(json_encode($dataJson, true));
            }
            else
            {
                return $this->render("question/answer.html.twig", array("renderComponent" => $questionComponent->getRenderComponent()));
            }
        }

        $question->addAnswer(new \Entity\AnswerQuestion($value, $userFind));

        $eventManager = $this->getContainer()->get("eventmanager");

        $eventManager->notify("question.answer.change", $question, $userFind);

        $em->persist($question);

        $em->flush();

        $recepters = ($questionComponent->getVisibilityByUsers()->isEmpty()) ? array() : $questionComponent->getVisibilityByUsers()->toArray();

        $recepters = $this->get("render.eventclient")->getSessionsByUsers($recepters);

        $this->get("render.ui")->updateComponent($questionComponent, $recepters);

        if($request->isXmlHttpRequest())
        {
            $dataJson = array(
                "question" => $question->getQuestion()
            );

            return new Response(json_encode($dataJson, true));
        }
        else
        {
            return $this->render("question/answer.html.twig", array("renderComponent" => $questionComponent->getRenderComponent()));
        }
    }

}

?>