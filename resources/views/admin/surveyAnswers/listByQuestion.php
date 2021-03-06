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
      <div class="well">
        <form class="form-horizontal filter-form" id="search-form" role="form">
          <div>
            问题：<?= $question['question'] ?>
          </div>
        </form>
      </div>

      <table class="js-survey-answer-table record-table table table-bordered table-hover">
        <thead>
        <tr>
          <th class="t-4">ID</th>
          <th class="t-12">用户</th>
          <th>内容</th>
          <th class="t-12">答题时间</th>
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

<?php require $this->getFile('@user/admin/user/richInfo.php') ?>

<?= $block->js() ?>
<script>
  require(['plugins/admin/js/data-table', 'plugins/admin/js/form', 'plugins/app/js/bootbox', 'plugins/admin/js/date-range-picker'], function () {
    $('#search-form').update(function () {
      recordTable.reload($(this).serialize());
    });

    var recordTable = $('.js-survey-answer-table').dataTable({
      ajax: {
        url: $.url('admin/survey-answers/list-by-question.json', {questionId: <?= $this->e($req['questionId']); ?>})
      },
      columns: [
        {
          data: 'id'
        },
        {
          data: 'user',
          render: function (data, type, full) {
            return template.render('user-info-tpl', data);
          }
        },
        {
          data: 'answer',
          render: function (data, type, full) {
            var html = '';
            for(var i in full.values){
              if(full.images[i] != '') {
                html += '<img style="width:50px;height:50px;" src="'+full.images[i]+'">';
              }

              if(full.values[i] != '') {
                html += full.values[i]+' ';
              }

              if(i != full.values.length-1) {
                html += '， ';
              }
            }
            return html;
          }
        },
        {
          data: 'createTime',
          render: function (data, type, full) {
            return data.replace(/-/g, '.').substr(0, 10);
          }
        }
      ]
    });

    recordTable.deletable();

  });
</script>
<?= $block->end() ?>
