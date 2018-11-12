<?php $view->layout() ?>

<?= $block->css() ?>
<link rel="stylesheet" href="<?= $asset('plugins/survey/css/admin/survey.css') ?>">
<?= $block->end() ?>

<div class="page-header">
  <a class="btn btn-default pull-right"
    href="<?= $url('admin/survey-questions?surveyId=%s', isset($surveyId) ? $surveyId : '') ?>">返回列表</a>

  <h1>
    微官网
    <small>
      <i class="fa fa-angle-double-right"></i>
      问题管理
    </small>
  </h1>
</div>
<!-- /.page-header -->

<div class="row">
  <div class="col-xs-12">
    <!-- PAGE CONTENT BEGINS -->
    <form class="js-survey-form form-horizontal survey-form" method="post" role="form">
      <div class="form-group">
        <label class="col-lg-2 control-label" for="question">
          <span class="text-warning">*</span>
          问题
        </label>

        <div class="col-lg-4">
          <input type="text" class="form-control" name="question" id="question" data-rule-required="true">
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-2 control-label" for="sort">
          顺序
        </label>

        <div class="col-lg-4">
          <input type="text" class="form-control" name="sort" id="sort">
        </div>

        <label class="col-lg-6 help-text" for="no">
          大的显示在前面,按从大到小排列.
        </label>
      </div>

      <div class="form-group">
        <label class="col-lg-2 control-label" for="type">
          <span class="text-warning">*</span>
          类型
        </label>

        <div class="col-lg-4">
          <label class="radio-inline">
            <input type="radio" class="js-type" name="type" id="radio" value="1" checked> 单选
          </label>
          <label class="radio-inline">
            <input type="radio" class="js-type" name="type" id="checkbox" value="2"> 多选
          </label>
          <label class="radio-inline">
            <input type="radio" class="js-type" name="type" id="text" value="3"> 单行文字
          </label>
          <label class="radio-inline">
            <input type="radio" class="js-type" name="type" id="textarea" value="4"> 多行文字
          </label>
        </div>
      </div>

      <div class="form-group radio-form-group type-form-group checkbox-form-group">
        <label class="col-lg-2 control-label" for="radio">
          选项
        </label>

        <div class="col-lg-4">
          <div class="js-options">
          </div>

          <a href="javascript:" class="js-add-option add-option text-muted">
            <i class="fa fa-plus"></i>
            增加选项
          </a>
        </div>

        <div class="col-lg-4 no-padding-left">
          <div class="js-closes">
            <div class="js-close close"><i class="fa fa-close"></i></div>
          </div>
        </div>
      </div>

      <div class="form-group radio-form-group type-form-group checkbox-form-group">
        <label class="col-lg-2 control-label" for="radio">
          选项图片
        </label>

        <div class="col-lg-4">
          <div class="js-images">
          </div>

          <a href="javascript:" class="js-add-image add-option text-muted">
            <i class="fa fa-plus"></i>
            增加图片
          </a>
        </div>

        <div class="col-lg-4 no-padding-left">
          <div class="js-images-closes">
            <div class="js-image-close close image-close"><i class="fa fa-close"></i></div>
          </div>
        </div>
      </div>

      <input type="hidden" name="id" id="id"/>
      <input type="hidden" name="surveyId" id="survey-id"/>

      <div class="clearfix form-actions form-group">
        <div class="col-lg-offset-2">
          <button class="btn btn-primary" type="submit">
            <i class="fa fa-check bigger-110"></i>
            保存
          </button>
          &nbsp; &nbsp; &nbsp;
          <a class="btn btn-white"
            href="<?= $url('admin/survey-questions?surveyId=%s', isset($surveyId) ? $surveyId : '') ?>">
            <i class="fa fa-undo bigger-110"></i>
            返回列表
          </a>
        </div>
      </div>
    </form>
  </div>
  <!-- PAGE CONTENT ENDS -->
</div><!-- /.col -->
<!-- /.row -->

<script type="text/html" class="js-choose-image-tpl">
  <div class="type-option js-input" style="overflow: hidden">
    <input type="text" class="form-control js-thumb" id="options[image][]" name="options[image][]" value="<%= image %>">
  </div>
</script>

<?= $block->js() ?>
<script>
  require(['form', 'validator', 'jquery-deparam', 'ueditor', 'template', 'plugins/admin/js/image-upload'], function () {
    var surveyQuestion = <?= $surveyQuestion->toJson(); ?>;
    $('.js-survey-form')
      .loadJSON(surveyQuestion)
      .ajaxForm({
        url: $.url('admin/survey-questions/' + '<?= $surveyQuestion['id'] ? 'update' : 'create' ?>'),
        dataType: 'json',
        type: 'post',
        loading: true,
        success: function (result) {
          $.msg(result, function () {
            if (result.code > 0) {
              window.location = $.url('admin/survey-questions', {surveyId: <?= $surveyId ?>});
            }
          });
        }
      }).validate();

    // 类型选择
    $('.js-type').change(function () {
      $('.type-form-group').hide();
      $('.' + $(this).attr('id') + '-form-group').show();
    });
    $('.js-type').filter(':checked').change();

    var appendOptionHtml = function (html) {
      $('.js-survey-form').find('.js-options').append(html);
      $('.js-survey-form').find('.js-closes').append('<div class="js-close close"><i class="fa fa-close"></i></div>');
    };

    // options选项单独初始化
    if (surveyQuestion['options'] != '') {
      $('.js-survey-form').find('.js-options').html('');
      $('.js-survey-form').find('.js-closes').html('');
      for (var i in surveyQuestion['options']) {
        if (typeof surveyQuestion['options'][i]['value'] != "undefined") {
          var html = '<input type="text" class="form-control type-option" name="options[value][]" value="'
            + surveyQuestion['options'][i]['value'] + '" placeholder="请输入选项内容">';
          appendOptionHtml(html);
        }
      }
    }

    // 点击添加选项
    $('.js-survey-form').find('.js-add-option').click(function () {
      var html = '<input type="text" class="form-control type-option" name="options[value][]" placeholder="请输入选项内容">';
      appendOptionHtml(html);
    });

    // 删除选项
    $('.js-closes').on('click', '.js-close', function () {
      $('.js-options').find('input').eq($(this).index()).remove();
      $(this).remove();
    });

    var appendImageHtml = function (html) {
      $('.js-survey-form').find('.js-images').append(html);
      $('.js-survey-form')
        .find('.js-images-closes')
        .append('<div class="js-images-close close image-close"><i class="fa fa-close"></i></div>');
    };

    // images选项单独初始化
    if (surveyQuestion['options'] != '') {
      $('.js-survey-form').find('.js-images').html('');
      $('.js-survey-form').find('.js-images-closes').html('');
      for (var i in surveyQuestion['options']) {
        if (typeof surveyQuestion['options'][i]['image'] != "undefined") {
          var tpl = template.compile($('.js-choose-image-tpl').html());
          html = tpl(surveyQuestion['options'][i]);
          appendImageHtml(html);
        }
      }
    }

    // 点击添加选项
    $('.js-survey-form').find('.js-add-image').click(function () {
      var tpl = template.compile($('.js-choose-image-tpl').html());
      var data = {'image': ''};
      var html = tpl(data);
      appendImageHtml(html);
      $('.js-survey-form').find('.js-thumb:last').imageUpload({
        max: 1
      });
    });

    // 删除选项
    $('.js-images-closes').on('click', '.js-images-close', function () {
      $('.js-images').find('.js-input').eq($(this).index()).remove();
      $(this).remove();
    });

    // 点击选择图片
    $('.js-thumb').each(function () {
      $(this).imageUpload({
        max: 1
      });
    })
  });
</script>
<?= $block->end() ?>
