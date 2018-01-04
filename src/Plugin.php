<?php

namespace Miaoxing\Survey;

class Plugin extends \Miaoxing\Plugin\BasePlugin
{
    protected $name = '问卷';

    protected $description = '问卷调查功能';

    public function onAdminNavGetNavs(&$navs, &$categories, &$subCategories)
    {
        $subCategories['app-feedback'] = [
            'parentId' => 'app',
            'name' => '反馈',
            'icon' => 'fa fa-question-circle',
            'sort' => 1000,
        ];

        $navs[] = [
            'parentId' => 'app-feedback',
            'url' => 'admin/surveys',
            'name' => '问卷管理',
        ];
    }

    public function onLinkToGetLinks(&$links, &$types)
    {
        $links[] = [
            'typeId' => 'site',
            'name' => '默认问卷调查',
            'url' => 'surveys/default',
        ];
    }
}
