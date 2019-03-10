<?php $view->layout() ?>

<!-- /.page-header -->
<div class="page-header">
  <h1>
    微官网
    <small>
      <i class="fa fa-angle-double-right"></i>
      答案管理
    </small>
  </h1>
</div>

<div class="row">
  <div class="col-12">
    <!-- PAGE CONTENT BEGINS -->
    <div class="table-responsive">

      <table class="js-survey-answer-table record-table table table-bordered table-hover">
        <thead>
        <tr>
          <th>用户</th>
          <th class="t-12">操作</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
      </table>

    </div>
    <!-- /.table-responsive -->
    <!-- PAGE CONTENT ENDS -->
  </div>
  <!-- /col -->
</div>
<!-- /row -->

<script id="table-actions" type="text/html">
  <div class="action-buttons">
    <a class="js-detail-record" href="javascript:" title="查看" data-id="<%= id %>">
      <i class="fa fa-search-plus bigger-130"></i>
    </a>
  </div>
</script>

<?php require $this->getFile('@user/admin/user/richInfo.php') ?>
<?php require $this->getFile('@survey/admin/surveyAnswers/detail.php') ?>

<?= $block->js() ?>
<script>
  require(['plugins/admin/js/data-table', 'plugins/admin/js/form', 'plugins/app/js/bootbox', 'plugins/admin/js/date-range-picker'], function () {
    var recordTable = $('.js-survey-answer-table').dataTable({
      ajax: {
        url: $.url('admin/survey-answers/list-by-survey.json', {surveyId: <?= $this->e($req['surveyId']); ?>})
      },
      columns: [
        {
          data: 'user',
          render: function (data, type, full) {
            return template.render('user-info-tpl', data);
          }
        },
        {
          data: 'user',
          render: function (data, type, full) {
            return template.render('table-actions', data);
          }
        }
      ]
    });

    recordTable.deletable();

    $('.js-date-range').daterangepicker({}, function (start, end) {
      this.element.trigger('change');
    });
  });
</script>
<?= $block->end() ?>
