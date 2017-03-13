<?php $view->layout() ?>

<?= $block('css') ?>
<link rel="stylesheet" href="<?= $asset('plugins/admin/css/filter.css') ?>"/>
<?= $block->end() ?>

<!-- /.page-header -->
<div class="page-header">
  <div class="pull-right">
    <a class="btn btn-success"
      href="<?= $url('admin/survey-questions/new?surveyId=%s', isset($surveyId) ? $surveyId : '') ?>">添加问题</a>
  </div>

  <h1>
    微官网
    <small>
      <i class="fa fa-angle-double-right"></i>
      问题管理
    </small>
  </h1>
</div>

<div class="row">
  <div class="col-xs-12">
    <!-- PAGE CONTENT BEGINS -->
    <div class="table-responsive">

      <div class="well form-well m-b">
        <form class="form-horizontal filter-form" id="search-form" role="form">
          <div class="form-group form-group-sm">
            <label class="col-md-1 control-label" for="createTimeRange">关键字：</label>

            <div class="col-md-3">
              <input type="text" class="form-control" name="search" placeholder="请输入名称或ID搜索">
            </div>
          </div>
        </form>
      </div>

      <table class="js-survey-question-table record-table table table-bordered table-hover">
        <thead>
        <tr>
          <th class="t-4">问卷ID</th>
          <th>问题</th>
          <th class="t-10">评测类型</th>
          <th class="t-6">答题人数</th>
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

<?= $block('js') ?>
<script>
  require(['dataTable', 'jquery-deparam', 'form', 'daterangepicker'], function () {
    $('#search-form').update(function () {
      recordTable.reload($(this).serialize());
    });

    var recordTable = $('.js-survey-question-table').dataTable({
      ajax: {
        url: $.queryUrl('admin/survey-questions.json')
      },
      columns: [
        {
          data: 'id'
        },
        {
          data: 'question'
        },
        {
          data: 'typeName'
        },
        {
          data: 'userCount',
          render: function (data, type, full) {
            return template.render('answer-links', full);
          }
        },
        {
          data: 'id',
          render: function (data, type, full) {
            return template.render('table-actions', full);
          }
        }
      ]
    });

    recordTable.deletable();
  });
</script>
<?= $block->end() ?>

<script id="answer-links" type="text/html">
  <a class="text-danger" href="<%= $.url('admin/survey-answers/list-by-question', {questionId : id}) %>" title="查看答题详情">
    <%= userCount %>
  </a>
</script>

<script id="table-actions" type="text/html">
  <div class="action-buttons">
    <a href="<%= $.url('admin/survey-questions/edit', {id: id, surveyId: surveyId}) %>" title="编辑">
      <i class="fa fa-edit bigger-130"></i>
    </a>

    <a class="text-danger delete-record"
      data-href="<%= $.url('admin/survey-questions/destroy', {id: id}) %>"
      href="javascript:" title="删除">
      <i class="fa fa-trash-o bigger-130"></i>
    </a>
  </div>
</script>

