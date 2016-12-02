<?php

namespace Miaoxing\Survey\Migration;

use Miaoxing\Plugin\BaseMigration;

class V20161202104212CreateSurveyTables extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->scheme->table('surveys')
            ->id()
            ->int('appId')
            ->string('name', 32)
            ->text('description')
            ->text('pic')
            ->tinyInt('type', 1)->comment('问卷类型')
            ->int('sort', 4)
            ->bool('isDefault')
            ->datetime('endTime')
            ->bool('passed')
            ->bool('audit')
            ->timestamps()
            ->userstamps()
            ->exec();

        $this->scheme->table('surveyQuestions')
            ->id()
            ->int('appId')
            ->int('surveyId')
            ->string('question')
            ->text('options')
            ->tinyInt('type', 1)->comment('问题类型，1是单选，2是多选，3是单行文字，4是多行文字')
            ->int('sort', 4)
            ->timestamps()
            ->userstamps()
            ->exec();

        $this->scheme->table('surveyAnswers')
            ->id()
            ->int('appId')
            ->int('surveyId')
            ->int('questionId')
            ->int('userId')
            ->text('answer')
            ->timestamp('createTime')
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->scheme->dropIfExists('surveys');
        $this->scheme->dropIfExists('surveyQuestions');
        $this->scheme->dropIfExists('surveyAnswers');
    }
}
