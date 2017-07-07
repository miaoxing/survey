<?php

namespace Miaoxing\Survey\Service;

class Survey extends \miaoxing\plugin\BaseModel
{
    protected $table = 'surveys';

    protected $providers = [
        'db' => 'app.db',
    ];

    protected $data = [
        'sort' => 50,
    ];

    protected $types = [
        1 => '普通',
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
        $this->userCount || $this->userCount = wei()->surveyAnswer()->curApp()->select('COUNT(DISTINCT(userId))')
            ->andWhere(['surveyId' => $surveyId])
            ->fetchColumn();

        return $this->userCount;
    }

    public function isEnded()
    {
        return $this['endTime'] !== '0000-00-00 00:00:00'
            && date('Y-m-d H:i:s') > $this['endTime'];
    }
}
