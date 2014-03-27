<?php

namespace Entity\Question;

use \Entity\Question;

class QuestionUnscribeMail extends Question
{
    public function getQuestion()
    {
        return "Etes vous sur de ne plus vouloir recevoir d'alerte mail ?";
    }
}