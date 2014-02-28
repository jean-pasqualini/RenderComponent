<?php

namespace Entity\Question;

use Entity\Profiles;
use \Entity\Question;
use \Entity\Visite;

/**
 * Class QuestionCaseConcluded
 * @package Entity\Question
 * @Entity(repositoryClass="Entity\Repository\QuestionCaseConcluedRepository")
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
    const EN_COURS = 3;

    public function __construct(Visite $visite)
    {
        $this->setVisite($visite);

        $contact = $this->getVisite()->getContact();

        $list = $contact->getList();

        $this->setQuestion("avez vous conclue par un marché pour [room id=\"".$list->getId()."\"]".$list->getTitle()."[/room] ?");

        parent::__construct();
    }


    public function getQuestion(Profiles $profile = null)
    {
        $visite = $this->getVisite();

        $contact = $visite->getContact();

        $list = $contact->getList();

        if($profile === $contact->getList()->getProfile())
        {
            return "Suite à la visite de l'appartement [room id=\"".$list->getId()."\"]".$list->getTitle()."[/room], votre dossier a-t-il été accepté ?";
        }
        else
        {
            return "Suite à la visite de l'appartement [room id=\"".$list->getId()."\"]".$list->getTitle()."[/room], avez vous accepté le dossier de xxx ?";
        }
    }

    public function getAnswerAvaiable()
    {
        return array(
            self::OUI => array(
                "label" => "Oui",
                "labelonview" => "Dossier validé",
                "color" => "success",
            ),
            self::NON => array(
                "label" => "Non",
                "labelonview" => "Dossier refusé",
                "color" => "danger",
            ),
            self::EN_COURS => array(
                "label" => "En cours",
                "labelonview" => "Dossier en cours de validation",
                "color" => "warning"
            )
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