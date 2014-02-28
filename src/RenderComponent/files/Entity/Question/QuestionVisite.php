<?php

namespace Entity\Question;

/**
 * Class QuestionVisite
 * @Entity
 */
class QuestionVisite extends \Entity\Question
{

    /**
     * @Id
     * @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @OneToOne(targetEntity="Entity\Visite")
     */
    private $visite;

    CONST ACCEPTER = 1;
    CONST REFUSER = 2;


    public function __construct(\Entity\Visite $visite)
    {
        $this->setVisite($visite);

        $contact = $this->getVisite()->getContact();

        $list = $contact->getList();

        $this->setQuestion("[user id=\"".$contact->getUserby()->getId()."\"]".$contact->getUserby()->getProfile()->getFname()."[/user] souhaite visiter [room id=\"".$list->getId()."\"]".$list->getTitle()."[/room] le ".$visite->getDate()->format("d/m/Y")." à ".$visite->getHour()."H");

        parent::__construct();
    }

    public function getAnswerAvaiable()
    {
        return array(
            self::ACCEPTER => array(
                "label" => "Accepter",
                "labelonview" => "Visite acceptée",
                "color" => "success",
            ),
            self::REFUSER => array(
                "label" => "Refuser",
                "labelonview" => "Visite refusée",
                "color" => "danger"
            )
        );
    }

    public function getNoAnswerMessage()
    {
        return "question.visite.noanswer";
    }


    /**
     * @param mixed $visite
     */
    public function setVisite($visite)
    {
        $this->visite = $visite;
    }

    /**
     * @return \Entity\Visite
     */
    public function getVisite()
    {
        return $this->visite;
    }


}