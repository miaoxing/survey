<?php

namespace Miaoxing\Survey\Controller;

class Surveys extends \miaoxing\plugin\BaseController
{
    public function showAction($req)
    {
        $survey = wei()->survey()->curApp()->findOneById($req['id']);
        $questions = wei()->surveyQuestion()->curApp()->findAll(['surveyId' => $req['id']]);
        if ($questions->count() == 0) {
            return get_defined_vars();
        }

        $answers = wei()->surveyAnswer()
            ->curApp()
            ->andWhere(['surveyId' => $req['id']])
            ->andWhere(['userId' => wei()->curUser['id']])
            ->andWhere(['questionId' => wei()->coll->column($questions->toArray(), 'id')])
            ->findAll();

        $isAnswered = $answers->count() > 0;

        $questionToAnswers = [];
        foreach ($answers as $answer) {
            $questionToAnswers[$answer['questionId']] = $answer->toArray();
        }

        return get_defined_vars();
    }

    public function submitAction($req)
    {
        if (!$req['answers']) {
            return $this->err('缺少参数');
        }

        foreach ($req['answers'] as $questionId => $answer) {
            $tempAnswer = wei()->surveyAnswer()->curApp()->findOrInit([
                'questionId' => $questionId,
                'surveyId' => $req['id'],
                'userId' => wei()->curUser['id'],
            ]);

            if ($tempAnswer->isNew()) {
                $tempAnswer->save([
                    'answer' => is_array($answer) ? $answer : trim($answer),
                ]);
            }
        }

        return $this->suc();
    }

    /**
     * 获取默认问卷页面
     */
    public function defaultAction($req)
    {
        $default = wei()->survey()->curApp()->find(['isDefault' => 1]);
        if (!$default) {
            return $this->err('暂无默认问卷');
        }

        $req['id'] = $default['id'];
        $this->app->forward('surveys', 'show');
    }

    /**
     * 获取答案
     * @param $req
     * @return array
     */
    public function getAnswersAction($req)
    {
        $answers = wei()->surveyAnswer()
            ->select('surveyAnswers.*')
            ->leftJoin('surveyQuestions', 'questionId = surveyQuestions.id')
            ->andWhere(['userId' => $req['userId']])
            ->andWhere(['surveyAnswers.appId' => wei()->app->getId()])
            ->desc('surveyQuestions.sort');

        if ($req['surveyId']) {
            $answers->andWhere(['surveyAnswers.surveyId' => $req['surveyId']]);
        } else {
            $survey = wei()->survey()->curApp()->findOrInit(['isDefault' => 1]);
            if (!$survey->isNew()) {
                $answers->andWhere(['surveyAnswers.surveyId' => $survey['id']]);
            }
        }

        $data = [];
        foreach ($answers->findAll() as $answer) {
            $values = [];
            $images = [];
            $question = $answer->getQuestion();
            foreach ($answer['answer'] as $ans) {
                $values[] = $question->hasNotOptions() ? $ans : ($question['options'][$ans - 1]['value'] ?: '');
                $images[] = $question->hasNotOptions() ? '' : ($question['options'][$ans - 1]['image'] ?: '');
            }

            $data[] = $answer->toArray() + [
                    'values' => $values,
                    'images' => $images,
                    'user' => $answer->getUser()->toArray(),
                    'question' => $question->toArray(),
                ];
        }

        return $this->suc([
            'data' => $data,
            'page' => $req['page'],
            'rows' => $req['rows'],
            'records' => $answers->count(),
        ]);
    }
}
