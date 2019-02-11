<?php $view->layout() ?>

<?= $block('header-actions') ?>
<a class="btn btn-default pull-right" href="<?= $url('admin/surveys') ?>">返回列表</a>
<?= $block->end() ?>

<div class="row">
  <div class="col-12">
    <!-- PAGE CONTENT BEGINS -->
    <form class="js-survey-form form-horizontal" method="post" role="form">
      <div class="form-group">
        <label class="col-lg-2 control-label" for="name">
          <span class="text-warning">*</span>
          问卷标题
        </label>

        <div class="col-lg-4">
          <input type="text" class="form-control" name="name" id="name" data-rule-required="true">
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-2 control-label" for="description">
          <span class="text-warning">*</span>
          简介
        </label>

        <div class="col-lg-4">
          <input type="text" class="form-control" name="description" id="description" data-rule-required="true">
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-2 control-label" for="type">
          类型
        </label>

        <div class="col-lg-4">
          <select id="type" name="type" class="form-control">
            <option value="1" selected>普通</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-2 control-label" for="thumb">
          封面
        </label>

        <div class="col-lg-4">
          <input type="text" class="form-control js-thumb" id="pic" name="pic">
        </div>
        <label class="col-lg-6 help-text" for="no">
          支持JPG、PNG格式，建议大图900像素 * 500像素，小图200像素 * 200像素，小于1M
        </label>
      </div>

      <div class="form-group">
        <label class="col-lg-2 control-label" for="endTime">
          结束时间
        </label>

        <div class="col-lg-4">
          <div>
            <input type="text" class="form-control js-end-time" name="endTime">
          </div>
        </div>
      </div>

      <input type="hidden" name="id" id="id"/>

      <div class="clearfix form-actions form-group">
        <div class="offset-lg-2">
          <button class="btn btn-primary" type="submit">
            <i class="fa fa-check bigger-110"></i>
            保存
          </button>
          &nbsp; &nbsp; &nbsp;
          <a class="btn btn-default" href="<?= $url('admin/surveys') ?>">
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

<?= $block->js() ?>
<script>
  require([
    'form', 'validator', 'jquery-deparam',
    'assets/dateTimePicker',
    'plugins/admin/js/image-upload'
  ], function () {
    var survey = <?= $survey->toJson(); ?>;
    $('.js-survey-form')
      .loadJSON(survey)
      .ajaxForm({
        url: $.url('admin/surveys/' + '<?= $survey['id'] ? 'update' : 'create' ?>'),
        dataType: 'json',
        type: 'post',
        loading: true,
        success: function (result) {
          $.msg(result, function () {
            if (result.code > 0) {
              window.location = $.url('admin/surveys');
            }
          });
        }
      }).validate();

    // 开始结束时间使用日期时间范围选择器
    $('.js-start-time, .js-end-time').rangeDateTimePicker({
      dateFormat: 'yy-mm-dd'
    });

    // 点击选择图片
    $('.js-thumb').imageUpload();
  });
</script>
<?= $block->end() ?>
