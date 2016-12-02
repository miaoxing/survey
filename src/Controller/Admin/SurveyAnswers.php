<?php

namespace Miaoxing\Survey\Controller\admin;

class SurveyAnswers extends \miaoxing\plugin\BaseController
{
    protected $controllerName = '问卷答案管理';

    protected $actionPermissions = [
        'index,listByQuestion,listBySurvey' => '列表'
    ];

    public function indexAction($req)
    {
        $survey = wei()->survey()->curApp()->findOneById($req['surveyId']);
        $questions = $survey->getQuestions();

        $answers = wei()->surveyAnswer()->curApp()
            ->select('questionId, id as answerId, COUNT(1) AS count')
            ->andWhere(['surveyId' => $survey['id']])
            ->groupBy('questionId, answerId')
            ->fetchAll();

        // 转换为题目,答案,值的树形结构
        $data = [];
        foreach ($answers as $answer) {
            $data[$answer['questionId']][$answer['answerId']] = $answer['count'];
        }

        // 计算出百分比
        $chart = wei()->chart;
        foreach ($data as $i => $answers) {
            $count = array_sum($answers);
            foreach ($answers as $j => $answer) {
                $data[$i][$j] = [
                    'value' => $answer,
                    'percentage' => $chart->div($answer * 100, $count, 2) . '%',
                ];
            }
        }

        return get_defined_vars();
    }

    public function listByQuestionAction($req)
    {
        switch ($req['_format']) {
            case 'json' :
                $answers = wei()->surveyAnswer()->curApp()->andWhere(['questionId' => $req['questionId']]);

                // 分页
                $answers->limit($req['rows'])->page($req['page']);

                // 排序
                $answers->desc('id');

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
                            'question' => $answer->getQuestion()->toArray()
                        ];
                }

                return $this->suc([
                    'data' => $data,
                    'page' => $req['page'],
                    'rows' => $req['rows'],
                    'records' => $answers->count(),
                ]);
            default:
                $question = wei()->surveyQuestion()->curApp()->findOneById($req['questionId']);
                return get_defined_vars();
        }
    }

    public function listBySurveyAction($req)
    {
        switch ($req['_format']) {
            case 'json' :
                $answers = wei()->surveyAnswer()->curApp()
                    ->select('distinct(userId)')
                    ->andWhere(['surveyId' => $req['surveyId']]);

                // 分页
                $answers->limit($req['rows'])->page($req['page']);

                $data = [];
                foreach ($answers->fetchAll() as $answer) {
                    $data[] = [
                        'user' => wei()->user()->findOneById($answer['userId'])->toArray()
                    ];
                }

                return $this->suc([
                    'data' => $data,
                    'page' => $req['page'],
                    'rows' => $req['rows'],
                    'records' => wei()->surveyAnswer()->curApp()
                        ->select('count(distinct(userId))')
                        ->andWhere(['surveyId' => $req['surveyId']])
                        ->fetchColumn()
                ]);
            default:
                return get_defined_vars();
        }
    }

    public function showAction($req)
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

    public function textsAction($req)
    {
        $answers = wei()->surveyAnswer()->where(['questionId' => $req['questionId']]);

        // 分页
        $answers->limit($req['rows'])->page($req['page']);

        $answers->desc('id');

        $data = [];
        foreach ($answers->findAll() as $answer) {
            $data[] = $answer->toArray() + [
                    'user' => wei()->user()->findOrInitById($answer['userId'])->toArray(),
                ];
        }

        return $this->suc([
            'data' => $data,
            'page' => $req['page'],
            'rows' => $req['rows'],
            'records' => $answers->count(),
        ]);
    }

    public function statAction($req)
    {
        $survey = wei()->survey()->findOneById($req['surveyId']);
        $questions = $survey->getQuestions();
        $answers = wei()->surveyAnswer()->curApp()->andWhere(['surveyId' => $req['surveyId']])->findAll();

        // 转换为题目,答案,值的树形结构
        $data = [];
        foreach ($answers as $answer) {
            foreach ($answer['answer'] as $ans) {
                if ($ans >= 1) {
                    if (!$data[$answer['questionId']][$ans - 1]) {
                        $data[$answer['questionId']][$ans - 1] = 0;
                    }
                    $data[$answer['questionId']][$ans - 1]++;
                } else {
                    $data[$answer['questionId']][$ans] = 1;
                }
            }
        }
        // 计算出百分比
        $chart = wei()->chart;
        foreach ($data as $i => $answers) {
            $count = array_sum($answers);
            foreach ($answers as $j => $answer) {
                $data[$i][$j] = [
                    'value' => $answer,
                    'percentage' => $chart->div($answer * 100, $count, 2) . '%',
                ];
            }
        }

        return get_defined_vars();
    }
}
