<?php

namespace Miaoxing\Survey\Controller\admin;

class SurveyQuestions extends \Miaoxing\Plugin\BaseController
{
    protected $controllerName = '问卷题目管理';

    protected $actionPermissions = [
        'index' => '列表',
        'new,create' => '添加',
        'edit,update' => '编辑',
        'destroy' => '删除',
    ];

    public function indexAction($req)
    {
        switch ($req['_format']) {
            case 'json':
                $surveyQuestions = wei()->surveyQuestion()->curApp();

                if ($req['surveyId']) {
                    $surveyQuestions->andWhere(['surveyId' => $req['surveyId']]);
                }

                // 分页
                $surveyQuestions->limit($req['rows'])->page($req['page']);

                // 排序
                $surveyQuestions->desc('sort');

                // 搜索
                if ($req['search']) {
                    $surveyQuestions->andWhere('id = ? OR question LIKE ?', [
                        (int) $req['search'],
                        '%' . $req['search'] . '%',
                    ]);
                }

                $data = [];
                foreach ($surveyQuestions->findAll() as $surveyQuestion) {
                    $data[] = [
                            'userCount' => $surveyQuestion->getUserCount(),
                            'typeName' => $surveyQuestion->getTypeName(),
                        ] + $surveyQuestion->toArray();
                }

                return $this->suc([
                    'data' => $data,
                    'page' => $req['page'],
                    'rows' => $req['rows'],
                    'records' => $surveyQuestions->count(),
                ]);
            default:
                if ($req['surveyId']) {
                    $surveyId = $this->e($req['surveyId']);
                }

                return get_defined_vars();
        }
    }

    public function newAction($req)
    {
        return $this->editAction($req);
    }

    public function editAction($req)
    {
        $surveyQuestion = wei()->surveyQuestion()->curApp()->findId($req['id']);
        if ($req['surveyId']) {
            $surveyId = $this->e($req['surveyId']);
            $surveyQuestion->fromArray(['surveyId' => $surveyId]);
        }

        return get_defined_vars();
    }

    public function createAction($req)
    {
        return $this->updateAction($req);
    }

    public function updateAction($req)
    {
        $validator = wei()->validate([
            'data' => $req,
            'rules' => [
                'question' => [],
                'type' => [],
            ],
            'names' => [
                'question' => '问题',
                'type' => '类型',
            ],
        ]);
        if (!$validator->isValid()) {
            return $this->err($validator->getFirstMessage());
        }

        $surveyQuestion = wei()->surveyQuestion()->curApp()->findOrInitById($req['id']);

        $optionsData = [];
        if ($req['options']['value']) {
            foreach ($req['options']['value'] as $option) {
                $optionsData[] = [
                    'value' => $option ?: '',
                ];
            }
        }

        if ($req['options']['image']) {
            foreach ($req['options']['image'] as $i => $image) {
                if ($optionsData[$i]) {
                    $optionsData[$i] += [
                        'image' => $image ?: '',
                    ];
                } else {
                    $optionsData[] = [
                        'image' => $image ?: '',
                    ];
                }
            }
        }

        $req['options'] = $optionsData;
        $surveyQuestion->save($req);

        return $this->suc();
    }

    public function destroyAction($req)
    {
        $answers = wei()->surveyAnswer()->curApp()->findAll(['questionId' => $req['id']]);
        if ($answers->count()) {
            return $this->err('已存在用户问卷数据，删除失败！');
        }

        wei()->surveyQuestion()->curApp()->findOneById($req['id'])->destroy();

        return $this->suc();
    }
}
