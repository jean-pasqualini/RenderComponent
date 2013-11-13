<?php

namespace Render;

use Entity\Components\QuestionComponent;
use interfaces\UserInterface;

class QuestionRenderComponent extends RenderComponent {

    private $questionComponent;

    public function __construct(QuestionComponent $questionComponent)
    {
        $this->questionComponent = $questionComponent;
    }

    public function getQuestionComponent()
    {
        return $this->questionComponent;
    }

    public function getDataForRender(UserInterface $user = null)
    {
        $user = (null === $user) ? $this->getContainer()->get("user") : $user;

        $question = $this->questionComponent->getQuestion();

        $answersAvailable = array();

        $answer = (!$question->getAnswersByUser($user)->isEmpty()) ? $question->getAnswersByUser($user)->first()->getAnswer() : array();

        foreach($question->getAnswerAvaiable() as $value => $label)
        {
            $selected = ($value == $answer) ? true : false;

            $enabled = ($selected) ? "active" : "";

            $disabled = (!empty($answers) && !$selected && !$question->isAnswerChangeable()) ? "disabled" : "";

            $url = (null === $this->getContainer()) ? "/question/answer/".$question->getId()."/$value" : $this->getContainer()->get("router")->generate("/question/answer/".$this->questionComponent->getId()."/$value", array(), true);

            $answersAvailable[] = array(
                "selected" => $selected,
                "enabled" => $enabled,
                "disabled" => $disabled,
                "url" => $url,
                "label" => $label
            );
        }

        return array(
            "question" => $question->getQuestion(),
            "answersAvailable" => $answersAvailable
        );
    }

    public function getRender($mode = self::VIEW_HTML, UserInterface $user = null)
    {
        return $this
            ->getContainer()
            ->get("twig")
            ->render("question/component.html.twig", $this->getDataForRender($user));
    }

}