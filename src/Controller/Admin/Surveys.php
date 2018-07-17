<?php

namespace Miaoxing\Survey\Controller\admin;

class Surveys extends \Miaoxing\Plugin\BaseController
{
    protected $controllerName = '问卷管理';

    protected $actionPermissions = [
        'index' => '列表',
        'new,create' => '添加',
        'edit,update' => '编辑',
        'destroy' => '删除',
        'audit' => '审核',
    ];

    protected $displayPageHeader = true;

    public function indexAction($req)
    {
        switch ($req['_format']) {
            case 'json':
                $surveys = wei()->survey()->curApp();

                // 分页
                $surveys->limit($req['rows'])->page($req['page']);

                // 排序
                $surveys->desc('sort');

                // 搜索
                if ($req['search']) {
                    $surveys->andWhere('name LIKE ?', ['%' . $req['search'] . '%']);
                }

                // 按创建时间时间筛选
                if ($req['createTimeRange']) {
                    $ranges = explode('~', strtr($req['createTimeRange'], '.', '-'));
                    $ranges[0] = date('Y-m-d', strtotime($ranges[0]));
                    $ranges[1] = date('Y-m-d', strtotime($ranges[1])) . ' 23:59:59';
                    $surveys->andWhere('createTime BETWEEN ? AND ?', [$ranges[0], $ranges[1]]);
                }

                wei()->event->trigger('beforeSurveyFind', [$surveys, $req]);

                $data = [];
                foreach ($surveys->findAll() as $survey) {
                    $data[] = [
                            'userCount' => $survey->getUserCount(),
                            'typeName' => $survey->getTypeName(),
                        ] + $survey->toArray();
                }

                return $this->suc([
                    'data' => $data,
                    'page' => $req['page'],
                    'rows' => $req['rows'],
                    'records' => $surveys->count(),
                ]);
            default:
                return get_defined_vars();
        }
    }

    public function newAction($req)
    {
        return $this->editAction($req);
    }

    public function editAction($req)
    {
        $survey = wei()->survey()->curApp()->findId($req['id']);

        return get_defined_vars();
    }

    public function updateAction($req)
    {
        $validator = wei()->validate([
            'data' => $req,
            'rules' => [
                'name' => [],
                'description' => [],
            ],
            'names' => [
                'name' => '标题',
                'description' => '简介',
            ],
        ]);
        if (!$validator->isValid()) {
            return $this->err($validator->getFirstMessage());
        }

        $survey = wei()->survey()->curApp()->findOrInitById($req['id']);
        $survey->save($req);

        return $this->suc();
    }

    public function createAction($req)
    {
        return $this->updateAction($req);
    }

    public function destroyAction($req)
    {
        $answers = wei()->surveyAnswer()->curApp()->findAll(['surveyId' => $req['id']]);
        if ($answers->count()) {
            return $this->err('已存在用户问卷数据，删除失败！');
        }

        wei()->survey()->curApp()->findOneById($req['id'])->destroy();
        wei()->surveyQuestion()->curApp()->findAll(['surveyId' => $req['id']])->destroy();

        return $this->suc();
    }

    public function auditAction($req)
    {
        $survey = wei()->survey()->curApp()->findOneById($req['id']);
        $ret = wei()->audit->audit($survey, $req['pass'], $req['description']);

        return $this->ret($ret);
    }

    public function updateDefaultAction($req)
    {
        $survey = wei()->survey()->curApp()->findOneById($req['id']);
        wei()->survey()->curApp()->andWhere('isDefault = 1')->update('isDefault = 0');
        $survey->save(['isDefault' => 1]);

        return $this->suc();
    }
}
