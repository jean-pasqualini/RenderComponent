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
 * @DiscriminatorMap({
 *      "questioncaseconcluded" = "Entity\Question\QuestionCaseConcluded",
 *      "questionvisite" = "Entity\Question\QuestionVisite"
 * })
 */
abstract class Question {

    /**
     * @Id
     * @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(name="question", type="string", length=500)
     */
    private $question;

    /**
     * @OneToMany(targetEntity="AnswerQuestion", mappedBy="question", cascade={"persist"}, orphanRemoval=true)
     */
    private $answers;

    /**
     * @Column(name="answeravailable", type="array")
     */
    private $answerAvailable = array();

    /**
     * @var ArrayCollection $usersCanAnswer
     * @ManyToMany(targetEntity="Users")
     * @JoinTable(name="question_usersCanAnswer")
     */
    private $usersCanAnswer;

    /**
     * Non utiliser pour le moment
     * @var ArrayCollection $usersCanViewAnswer
     * @ManyToMany(targetEntity="Users")
     * @JoinTable(name="question_usersCanViewAnswer")
     */
    private $usersCanViewAnswer;


    public function __construct()
    {
        $this->answerAvailable = array();
        $this->answers = new ArrayCollection();
        $this->usersCanAnswer = new ArrayCollection();
        $this->usersCanViewAnswer = new ArrayCollection();
    }

    /**
     * @return array
     */
    public function getAnswerAvaiable()
    {
        return $this->answerAvailable;
    }

    // Réponse par défaut si aucune réponse 'ex: en attente'

    /**
     * @return bool
     */
    public function isMultiple()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isAnswerRequireForViewResponse()
    {
        return true;
    }

    /**
     * @return ArrayCollection $usersCanAnswer
     */
    public function getUsersCanAnswer()
    {
        return $this->usersCanAnswer;
    }

    /**
     * @param array $users
     */
    public function setUsersCanAnswer(array $users)
    {
        $this->usersCanAnswer = new ArrayCollection(array_unique($users));
    }

    /**
     * @param Users $user
     */
    public function addUserCanAnswer(Users $user)
    {
        $this->usersCanAnswer->add($user);
    }

    /**
     * @param Users $user
     */
    public function removeUserCanAnswer(Users $user)
    {
        $this->usersCanAnswer->removeElement($user);
    }

    /**
     * @return ArrayCollection $usersCanViewAnswer
     */
    public function getUsersCanViewAnswer()
    {
        return $this->usersCanViewAnswer;
    }

    /**
     * @param array $users
     */
    public function setUsersCanViewAnswer(array $users)
    {
        $this->usersCanViewAnswer = new ArrayCollection(array_unique($users));
    }

    /**
     * @param Users $user
     */
    public function addUserCanViewAnswer(Users $user)
    {
        $this->usersCanViewAnswer->add($user);
    }

    /**
     * @param Users $user
     */
    public function removeUserCanViewAnswer(Users $user)
    {
        $this->usersCanViewAnswer->removeElement($user);
    }

    // Les utilisateurs qui peuvent répondre

    // Les utilisateur qui peuvent voir les réponses

    /**
     * @param $answerAvailable
     */
    public function setAnswerAvailable($answerAvailable)
    {
        $this->answerAvailable = $answerAvailable;
    }

    /**
     * @param $answerAvaialble
     */
    public function addAnswerAvailable($answerAvaialble)
    {
        $this->answerAvailable[] = $answerAvaialble;
    }

    /**
     * @return bool
     */
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
    public function getQuestion($context = null)
    {
        return $this->question;
    }

    /**
     * @param AnswerQuestion $answer
     */
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

    /**
     * @param array $answers
     */
    public function setAnswer(array $answers)
    {
        $this->answers = new ArrayCollection($answers);
    }

    public function getNoAnswerMessage()
    {
        return "unknow.answer";
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    public function isUserAnswerValue(Users $user, $value)
    {
        $answer = $this->getAnswerByUserAndValue($value, $user);

        return !empty($answer);
    }

    public function isUserAnswer(Users $user)
    {
        return !$this->getAnswersByUser($user)->isEmpty();
    }

    /**
     * @param Users $user
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAnswersByUser(Users $user)
    {
        return $this->getAnswers()->filter(function(AnswerQuestion $element) use ($user)
        {
            return ($element->getUser()->getId() == $user->getId());
        });
    }

    /**
     * @param $answer
     * @param $user
     * @return mixed
     */
    public function getAnswerByUserAndValue($answer, $user)
    {
        return current($this->getAnswersByUser($user)->filter(function($element) use ($answer)
        {
            return $element->getAnswer() == $answer;
        }));
    }

    /**
     * @return boolean
     */
    public function hasAnswer()
    {
        return !$this->answers->isEmpty();
    }


    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getAfterChoiceMessage()
    {
        return "Votre réponse à bien été prise en compte";
    }


}