<?php

use Miaoxing\Survey\Service\SurveyQuestion;

$view->layout();

// phpcs:disable Generic.Files.LineLength.TooLong
?>

<?= $block->css() ?>
<link rel="stylesheet" href="<?= $asset('plugins/survey/css/survey.css') ?>">
<?= $block->end() ?>

<div class="g_wrapper page_survey g_survey">
  <!-- 问卷主体 -->
  <div id="container" class="g_container">
    <div class="g_content">
      <div class="survey_wrap">

        <!-- 问卷标题 -->
        <div class="survey_title">
          <div class="inner">
            <div class="title_content"><?= $survey['name'] ?></div>
            <div class="title_description">
              <?= $survey['description'] ?>

            </div>
            <?php if ($isEnded) : ?>
              <div class="text-warning">该问卷已结束</div>
            <?php endif ?>
          </div>
        </div>

        <!-- 问卷内容 -->
        <div class="survey_container">
          <form class="js-survey-form">

            <?php foreach ($questions as $index => $question) : ?>
              <div class="question question_<?= $question->getTypeNameEn() ?> required"
                id="question_<?= $question['id'] ?>" data-type="radio" data-id="<?= $question['id'] ?>">
                <div class="inner">
                  <div class="title">
                    <div class="title_text"><p><?= $index + 1 ?>. <?= $question['question'] ?></p></div>
                    <span class="required" title="必答" style="display: none;">*</span>
                  </div>

                  <div class="description">
                    <div class="description_text"></div>
                  </div>

                  <!-- 是否已回答问卷 -->
                  <?php if ($isAnswered) : ?>
                    <?php $answers = $questionToAnswers[$question['id']]['answer']; ?>
                    <div class="answer">
                      <div class="answer_text js-image">
                        <span>
                          <?php foreach ($answers as $answer) : ?>
                            <?= $question->hasNotOptions() ? $answer : (($value = $question['options'][$answer - 1]['value']) ? '<div>' . $value . '</div>' : ''); ?>
                            <?= $question->hasNotOptions() ? '' : (($image = $question['options'][$answer - 1]['image']) ? '<img src="' . $image . '">' : ''); ?>
                          <?php endforeach; ?>
                        </span>
                      </div>
                    </div>

                  <?php else : ?>
                    <div class="inputs">
                      <?php if ($question['type'] == SurveyQuestion::TYPE_RADIO) : ?>
                        <?php foreach ($question['options'] as $i => $option) : ?>
                          <div class="option_item" style="width: 100%;">
                            <input class="survey_form_checkbox" id="option_<?= $question['id'] . '_' . $i ?>"
                              type="radio" name="answers[<?= $question['id'] ?>][]" value="<?= $i + 1 ?>">

                            <label for="option_<?= $question['id'] . '_' . $i ?>">
                              <i class="survey_form_ui"></i>
                              <?= $option['value'] ? '<span class="option_text">' . $option['value'] . '</span><div></div>' : '<div></div>' ?>
                              <?= $option['image'] ? '<span class="option_text js-image"><img src="' . $option['image'] . '"></span>' : '' ?>
                            </label>
                          </div>
                        <?php endforeach ?>

                      <?php elseif ($question['type'] == SurveyQuestion::TYPE_CHECKBOX) : ?>
                        <?php foreach ($question['options'] as $i => $option) : ?>
                          <div class="option_item" style="width: 100%;">
                            <input class="survey_form_checkbox" id="option_<?= $question['id'] . '_' . $i ?>"
                              type="checkbox" name="answers[<?= $question['id'] ?>][]" value="<?= $i + 1 ?>">

                            <label for="option_<?= $question['id'] . '_' . $i ?>">
                              <i class="survey_form_ui"></i>
                              <?= $option['value'] ? '<div class="option_text">' . $option['value'] . '</div>' : '' ?>
                              <?= $option['image'] ? '<div class="option_text js-image"><img src="' . $option['image'] . '"></div>' : '' ?>
                            </label>
                          </div>
                        <?php endforeach ?>

                      <?php elseif ($question['type'] == SurveyQuestion::TYPE_TEXT) : ?>
                        <input class="survey_form_input" type="text" name="answers[<?= $question['id'] ?>]"
                          placeholder="请输入">

                      <?php elseif ($question['type'] == SurveyQuestion::TYPE_TEXTAREA) : ?>
                        <textarea class="survey_form_input" name="answers[<?= $question['id'] ?>]" rows="5" cols="60"
                          placeholder="请输入"></textarea>
                      <?php endif ?>
                    </div>

                  <?php endif ?>

                </div>
              </div>
            <?php endforeach ?>

            <input type="hidden" name="id" id="id" value="<?= $survey['id'] ?>">

          </form>
        </div>

        <!-- 问卷操作区域 -->
        <?php if (isset($isAnswered) && !$isAnswered && !$isEnded) : ?>
          <div class="survey_footer m-a-sm">
            <a href="javascript:" class="js-survey-submit btn btn-block btn-primary">提交</a>
          </div>
        <?php endif ?>

      </div>
    </div>
  </div>
</div>

<?= $block->js() ?>
<script>
  var getSurvey = function () {
    var arr = $('.js-survey-form').serializeArray();
    var data = {};
    for (var i in arr) {
      data[arr[i].name] = arr[i].value;
    }
    return data;
  };

  // 如果填写了答案,移除错误提示
  $('.js-survey-form :input').change(function () {
    var obj = getSurvey();
    var $question = $(this).closest('.question');
    var id = $question.data('id');
    var name = 'answers[' + id + ']';
    if (typeof obj[name] != 'undefined' || typeof obj[name + '[]']) {
      $question.find('.title_text p').removeClass('text-danger');
    }
  });

  // 微信端点击放大预览
  require([
    <?php if (wei()->plugin->isInstalled('wechat-corp')) :?>
    'plugins/wechat-corp/js/wx-corp'
    <?php else : ?>
    'plugins/wechat/js/wx'
    <?php  endif; ?>
  ], function (wx) {
    wx.load(function () {
      $('.js-image').on('click', 'img', function () {
        var urls = $(this).map(function () {
          return this.src;
        }).get();
        wx.previewImage({
          current: $(this).attr('src'),
          urls: urls
        });

      });
    });
  });

  require(['jquery-form'], function () {
    // 检查必填项是否填写
    $('.js-survey-submit').click(function () {
      var obj = getSurvey();
      var err = false;
      var first = true;
      $('.question').each(function () {
        var name = 'answers[' + $(this).data('id') + ']';
        if ((typeof obj[name] == 'undefined' || $.trim(obj[name]) == '')
          && typeof obj[name + '[]'] == 'undefined'
        ) {
          err = true;
          $(this).find('.title_text p').addClass('text-danger');
          if (first == true) {
            $('html, body').animate({
              scrollTop: $(this).offset().top
            }, 200);
            first = false;
          }
        } else {
          $(this).find('.title_text p').removeClass('text-danger');
        }
      });

      if (!err) {
        $('.js-survey-form').ajaxForm({
          url: $.url('surveys/submit'),
          loading: true,
          method: 'post',
          dataType: 'json',
          success: function (ret) {
            $.msg(ret, function () {
              if (ret.code === 1) {
                window.location.reload();
              }
            });
          }
        }).submit();
      }
    });
  });
</script>
<?= $block->end() ?>
