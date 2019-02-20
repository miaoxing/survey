<?php

use Miaoxing\Survey\Service\SurveyQuestion;

$view->layout();
?>
<?= $block->css() ?>
<link rel="stylesheet" href="<?= $asset('assets/admin/stat.css') ?>"/>
<?= $block->end() ?>

<!-- /.page-header -->
<div class="page-header">
  <a class="btn btn-default pull-right" href="<?= $url('admin/surveys') ?>">返回问卷列表</a>

  <h1>
    问卷管理
    <small>
      <i class="fa fa-angle-double-right"></i>
      问卷统计
    </small>
  </h1>
</div>

<div class="row">
  <div class="col-8 offset-2">
    <div class="stat-container form-group">
      <div class="infobox-container row">

        <div class="col-md-12">
          <div class="col-md-6 stat-box">
            <div class="infobox infobox-green">
              <div class="infobox-icon">
                <i class="ace-icon fa fa-group"></i>
              </div>

              <div class="infobox-data">
                <span class="infobox-data-number"><?= $survey->getUserCount() ?></span>

                <div class="infobox-content">总人数</div>
              </div>
            </div>
          </div>

          <div class="col-md-6 stat-box">
            <div class="infobox infobox-orange2">
              <div class="infobox-icon">
                <i class="ace-icon fa fa-question-circle"></i>
              </div>

              <div class="infobox-data">
                <span class="infobox-data-number"><?= $survey->getQuestions()->length() ?></span>

                <div class="infobox-content">总题目</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div>
      <?php foreach ($survey->getQuestions() as $i => $question) : ?>
        <p><?= $i + 1 ?>. <?= $question['type'] ?></p>
        <?php
          switch ($question['type']) :
            case SurveyQuestion::TYPE_RADIO:
            case SurveyQuestion::TYPE_CHECKBOX:
            ?>
            <table class="survey-table record-table table table-bordered table-hover">
              <thead>
              <tr>
                <th class="text-left">选项</th>
                <th class="text-right t-12">人数</th>
                <th class="text-right t-12">百分比</th>
              </tr>
              </thead>
              <tbody>
              <?php foreach ($question['options'] as $option) : ?>
                <tr>
                  <td class="text-left">选项</td>
                  <td class="text-right">0%</td>
                  <td class="text-right">0%</td>
                </tr>
              <?php endforeach ?>
              </tbody>
            </table>

            <?php
                  break;
            case SurveyQuestion::TYPE_TEXT:
            case SurveyQuestion::TYPE_TEXTAREA:
            ?>
            <table
              class="js-survey-table<?= $question['id'] ?> survey-table record-table table table-bordered table-hover">
              <thead>
              <tr>
                <th class="text-left t-12">用户</th>
                <th class="text-left">文本答案</th>
                <th class="text-right t-12">提交时间</th>
              </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
            <?php
                  break;
          endswitch;
        ?>
      <?php endforeach ?>
    </div>
    <!-- PAGE CONTENT ENDS -->
  </div>
  <!-- /col -->
</div>
<!-- /row -->

<?php require $this->getFile('@user/admin/user/richInfo.php') ?>

<?= $block->js() ?>
<script>
  require(['plugins/admin/js/data-table', 'jquery-unparam', 'form'], function () {
    $('.js-survey-table<?= $question['id'] ?>').dataTable({
      ajax: {
        url: $.url('admin/survey-answers/texts.json', {questionId: <?= $question['id'] ?>})
      },
      columns: [
        {
          data: 'user',
          render: function (data) {
            return template.render('user-info-tpl', data);
          }
        },
        {
          data: 'answerText',
          sClass: 'text-left'
        },
        {
          data: 'createTime',
          render: function (data) {
            return data.substr(0, 10);
          }
        }
      ]
    });
  });
</script>
<?= $block->end() ?>
