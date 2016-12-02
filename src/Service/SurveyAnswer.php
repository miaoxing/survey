<?php

namespace Miaoxing\Survey\Service;

class SurveyAnswer extends \miaoxing\plugin\BaseModel
{
    protected $table = 'surveyAnswers';

    protected $providers = [
        'db' => 'app.db'
    ];

    protected $user;
    protected $question;

    public function getUser()
    {
        $this->user || $this->user = wei()->user()->findById($this['userId']);
        return $this->user;
    }

    /**
     * @return $this|false
     */
    public function getQuestion()
    {
        $this->question || $this->question = wei()->surveyQuestion()->findById($this['questionId']);
        return $this->question;
    }

    public function afterFind()
    {
        parent::afterFind();
        $this['answer'] = (array)json_decode($this['answer'], true);
    }

    public function beforeSave()
    {
        parent::beforeSave();
        $this['answer'] = json_encode($this['answer'], JSON_UNESCAPED_UNICODE);
    }
}
