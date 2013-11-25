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

        if($question->getUsersCanAnswer()->contains($user))
        {
            $answer = (!$question->getAnswersByUser($user)->isEmpty()) ? $question->getAnswersByUser($user)->first()->getAnswer() : array();
        }
        else
        {
            $answer = (!$question->getAnswers()->isEmpty()) ? $question->getAnswers()->first()->getAnswer() : array();
        }

        foreach($question->getAnswerAvaiable() as $value => $config)
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
                "label" => $config["label"],
                "color" => $config["color"],
            );
        }

        return array(
            "question" => $question->getQuestion(),
            "answersAvailable" => $answersAvailable,
            "isAnswerMultiple" => $question->isMultiple(),
            "isCanAnswer" => $question->getUsersCanAnswer()->contains($user),
            "isCanViewAnswer" => $question->getUsersCanViewAnswer()->contains($user),
            "isAnswerChangeable" => $question->isAnswerChangeable(),
            "noAnswerMessage" => $question->getNoAnswerMessage(),
            "isAnswer" => !empty($answer)
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