<?php

namespace Entity\Question;

use Entity\Question;
use Entity\Visite;

/**
 * Class QuestionCaseConcluded
 * @package Entity\Question
 * @Entity
 */
class QuestionCaseConcluded extends Question {

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

    const OUI = 1;
    const NON = 2;

    public function __construct(Visite $visite)
    {
        $this->setVisite($visite);

        $contact = $this->getVisite()->getContact();

        $list = $contact->getList();

        $this->setQuestion("avez vous conclue par un marché pour [room id=\"".$list->getId()."\"]".$list->getTitle()."[/room] ?");

        parent::__construct();
    }

    public function getAnswerAvaiable()
    {
        return array(
            self::OUI => "oui, c'est exact",
            self::NON => "non, c'est faux"
        );
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