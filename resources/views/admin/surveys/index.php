<?php $view->layout() ?>

<?= $block('css') ?>
<link rel="stylesheet" href="<?= $asset('plugins/admin/css/filter.css') ?>"/>
<?= $block->end() ?>

<!-- /.page-header -->
<div class="page-header">
  <div class="pull-right">
    <a class="btn btn-success" href="<?= $url('admin/surveys/new') ?>">添加问卷</a>
  </div>

  <h1>
    微官网
    <small>
      <i class="fa fa-angle-double-right"></i>
      问卷管理
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

            <label class="col-md-1 control-label" for="search">关键字：</label>

            <div class="col-md-3">
              <input type="text" class="form-control" name="search" placeholder="请输入名称搜索">
            </div>

            <?php wei()->event->trigger('searchForm', ['survey']); ?>

          </div>
        </form>
      </div>

      <table class="js-record-table record-table table table-bordered table-hover">
        <thead>
        <tr>
          <th class="t-8">评测类型</th>
          <th>名称</th>
          <th class="t-12">结束时间</th>
          <th class="t-12">创建时间</th>
          <th class="t-4">默认</th>
          <th class="t-4">答题人数</th>
          <th class="t-4">统计</th>
          <?php wei()->event->trigger('tableCol', ['survey']); ?>
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
  require(['dataTable', 'jquery-deparam', 'form', 'bootbox', 'daterangepicker'], function () {
    $('#search-form').update(function () {
      recordTable.reload($(this).serialize());
    });

    var recordTable = $('.js-record-table').dataTable({
      ajax: {
        url: $.queryUrl('admin/surveys.json')
      },
      columns: [
        {
          data: 'typeName'
        },
        {
          data: 'name',
          sClass: 'text-left'
        },
        {
          data: 'endTime'
        },
        {
          data: 'createTime'
        },
        {
          data: 'isDefault',
          render: function (data, type, full) {
            return template.render('isDefaultTpl', full);
          }
        },
        {
          data: 'userCount',
          render: function (data, type, full) {
            return template.render('answer-links', full);
          }
        },
        {
          data: 'userCount',
          render: function (data, type, full) {
            return template.render('statTpl', full);
          }
        },
        <?php wei()->event->trigger('tableData', ['survey']); ?>
        {
          data: 'id',
          render: function (data, type, full) {
            return template.render('table-actions', full);
          }
        }
      ]
    });

    // 设为默认
    recordTable.on('click', '.js-set-default', function () {
      $.post($.url('admin/surveys/%s/update-default', $(this).data('id'), {isDefault: 1}), function (ret) {
        $.msg(ret);
        recordTable.reload();
      }, 'json');
    });

    recordTable.deletable();
  });
</script>
<?= $block->end() ?>

<script id="answer-links" type="text/html">
  <a class="text-danger" href="<%= $.url('admin/survey-answers/list-by-survey', {surveyId : id}) %>" title="查看答题详情">
    <%= userCount %>
  </a>
</script>

<script id="table-actions" type="text/html">
  <div class="action-buttons">
    <?php wei()->event->trigger('mediaAction', ['survey']); ?>

    <a href="<%= $.url('surveys/%s', id) %>" title="查看">
      <i class="fa fa-search-plus bigger-130"></i>
    </a>

    <a href="<%= $.url('admin/survey-questions', {surveyId : id}) %>" title="添加问题">
      <i class="fa fa-plus bigger-130"></i>
    </a>

    <a href="<%= $.url('admin/surveys/edit', {id: id}) %>" title="编辑">
      <i class="fa fa-edit bigger-130"></i>
    </a>

    <a class="text-danger delete-record"
      data-href="<%= $.url('admin/surveys/destroy', {id: id}) %>"
      href="javascript:" title="删除">
      <i class="fa fa-trash-o bigger-130"></i>
    </a>
  </div>
</script>

<script id="isDefaultTpl" type="text/html">
  <% if (isDefault == '1') { %>
  默认问卷
  <% } else { %>
  <a href="javascript:" class="js-set-default text-danger" data-id="<%= id %>">设为默认</a>
  <% } %>
</script>

<script id="statTpl" type="text/html">
  <a class="text-danger" href="<%= $.url('admin/survey-answers/stat', {surveyId: id}) %>">统计</a>
</script>
