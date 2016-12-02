<?php

namespace Miaoxing\Survey\Service;

class Survey extends \miaoxing\plugin\BaseModel
{
    protected $table = 'surveys';

    protected $providers = [
        'db' => 'app.db'
    ];

    protected $data = [
        'sort' => 50
    ];

    protected $types = [
        1 => '普通'
    ];

    /**
     * @var SurveyQuestion|SurveyQuestion[]
     */
    protected $questions;

    protected $userCount;

    public function getTypeName()
    {
        return $this->types[$this['type']];
    }

    public function getQuestions()
    {
        $this->questions || $this->questions = wei()->surveyQuestion()->curApp()->findAll(['surveyId' => $this['id']]);
        return $this->questions;
    }

    public function getUserCount()
    {
        $surveyId = $this['id'];
        wei()->cache->clear();
        $this->userCount || $this->userCount = wei()->cache->get('surveys'.$surveyId.'UserCount', 86400, function () use ($surveyId){
            return wei()->surveyAnswer()->curApp()->select('COUNT(DISTINCT(userId))')
                ->andWhere(['surveyId' => $surveyId])
                ->fetchColumn();
        });
        return $this->userCount;
    }
}
