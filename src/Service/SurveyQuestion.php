<?php

namespace Miaoxing\Survey\Service;

class SurveyQuestion extends \miaoxing\plugin\BaseModel
{
    protected $table = 'surveyQuestions';

    protected $providers = [
        'db' => 'app.db'
    ];

    protected $data = [
        'sort' => 50
    ];

    const TYPE_RADIO = 1;

    const TYPE_CHECKBOX = 2;

    const TYPE_TEXT = 3;

    const TYPE_TEXTAREA = 4;

    protected $types = [
        1 => '单选',
        2 => '多选',
        3 => '单行文字',
        4 => '多行文字',
    ];

    protected $typesEn = [
        1 => 'radio',
        2 => 'checkbox',
        3 => 'text',
        4 => 'textarea',
    ];

    protected $userCount;

    public function afterFind()
    {
        parent::afterFind();
        $this['options'] = json_decode($this['options'], true);
    }

    public function beforeSave()
    {
        parent::beforeSave();
        $this['options'] = json_encode($this['options'], JSON_UNESCAPED_UNICODE);
    }

    public function getTypeName()
    {
        return $this->types[$this['type']];
    }

    public function getTypeNameEn()
    {
        return $this->typesEn[$this['type']];
    }

    public function getUserCount()
    {
        $questionId = $this['id'];
        $this->userCount = wei()->cache->get('surveyQuestions'.$this['id'].'UserCount', 30, function () use ($questionId){
            return wei()->surveyAnswer()->curApp()
                ->select('COUNT(DISTINCT(userId))')
                ->andWhere(['questionId' => $questionId])
                ->fetchColumn();
        });
        return $this->userCount;
    }

    /**
     * 是否有选项
     * @return bool
     */
    public function hasNotOptions()
    {
        return $this['type'] > 2;
    }
}
