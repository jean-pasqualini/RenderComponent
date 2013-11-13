<?php
namespace Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Intl\Exception\InvalidArgumentException;

/**
 * Class Question
 * @package Entity
 * @Entity
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({"questioncaseconcluded" = "Entity\Question\QuestionCaseConcluded"})
 */
abstract class Question {

    /**
     * @Id
     * @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(name="question", type="string")
     */
    private $question;

    /**
     * @OneToMany(targetEntity="AnswerQuestion", mappedBy="question", cascade={"persist"}, orphanRemoval=true)
     */
    private $answers;

    /**
     * @Column(name="answeravailable", type="array")
     */
    private $answerAvailable;

    public function getAnswerAvaiable()
    {
        return $this->answerAvailable;
    }

    public function isMultiple()
    {
        return false;
    }

    public function setAnswerAvailable($answerAvailable)
    {
        $this->answerAvailable = $answerAvailable;
    }

    public function addAnswerAvailable($answerAvaialble)
    {
        $this->answerAvailable[] = $answerAvaialble;
    }

    public function __construct()
    {
        $this->answerAvailable = array();
        $this->answers = new ArrayCollection();
    }

    public function isAnswerChangeable()
    {
        return false;
    }

    /**
     * @param mixed $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @return mixed
     */
    public function getQuestion()
    {
        return $this->question;
    }

    public function addAnswer(AnswerQuestion $answer)
    {
        $answersUser = $this->getAnswersByUser($answer->getUser());

        if(!$this->isMultiple() && $answersUser->count() > 0)
        {
            foreach($answersUser as $answerItemUser)
            {
                $this->answers->removeElement($answerItemUser);
            }
        }

        $answer->setQuestion($this);

        if(!$this->answers->contains($answer))
        {
            $this->answers->add($answer);
        }
    }

    public function setAnswer(array $answers)
    {
        $this->answers = new ArrayCollection($answers);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    public function getAnswersByUser(Users $user)
    {
        return $this->getAnswers()->filter(function(AnswerQuestion $element) use ($user)
        {
            return ($element->getUser()->getId() == $user->getId());
        });
    }

    public function getAnswerByUserAndValue($answer, $user)
    {
        return current($this->getAnswersByUser($user)->filter(function($element) use ($answer)
        {
            return $element->getAnswer() == $answer;
        }));
    }

    public function hasAnswer()
    {
        return $this->answers;
    }


    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


}