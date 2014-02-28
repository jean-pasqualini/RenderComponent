<?php
namespace Entity\Components;

use \Entity\Component;
use \Entity\Question;
use Render\QuestionRenderComponent;

/**
 * Class QuestionComponent
 * @package Entity\Components
 * @Entity
 */
class QuestionComponent extends Component {

    /**
     * @Id
     * @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @OneToOne(targetEntity="Entity\Question", cascade={"persist"})
     */
    private $question;

    public function __construct(Question $question)
    {
        $this->setQuestion($question);

        parent::__construct();
    }

    /**
     * @param mixed $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @return \Entity\Question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    public function getTitleComponent()
    {
        return $this->getQuestion()->getQuestion();
    }

    public function getRenderComponent()
    {
        return new QuestionRenderComponent($this);
    }

    public function getTitle()
    {
        return "Question"; //$this->getTitleComponent();
    }


    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


}