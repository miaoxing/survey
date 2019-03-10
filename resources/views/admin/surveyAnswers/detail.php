<script id="answer-detail-tpl" type="text/html">
  <div class="modal fade detail-modal" tabindex="-1" role="dialog" aria-labelledby="showModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="showModalLabel">答案详情</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" role="form">

            <% for(var i in data) {%>
              <div class="form-group">
                <label class="col-sm-2 control-label"><%= parseInt(i)+1 %>.</label>

                <div class="col-sm-10">
                  <p class="form-control-plaintext">
                    <%= data[i].question.question %>
                  </p>
                  <p class="form-control-plaintext">
                    <% for(var j in data[i].values) { %>
                      <% if(data[i].values[j] != '') { %>
                        <%= data[i].values[j] %>
                      <% }%>
                      <% if(data[i].images[j] != '') { %>
                        <div><img style="width: 50px;height: 50px;" src="<%= data[i].images[j] %>"></div>
                      <% }%>
                  <% } %>
                  </p>
                </div>
              </div>
            <% } %>

            <hr>

            <div class="form-group">
              <label for="createTime" class="col-sm-2 control-label">答题时间</label>

              <div class="col-sm-10">
                <p class="form-control-plaintext">
                  <%= data[0].createTime %>
                </p>
              </div>
            </div>

          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">关闭</button>
        </div>
      </div>
    </div>
  </div>
</script>

<?= $block->js() ?>
<script>
  require(['plugins/admin/js/data-table', 'plugins/admin/js/form', 'plugins/app/libs/artTemplate/template.min'], function () {
    template.helper('parseInt', parseInt);
    var surveyId = '<?= $this->e($req['surveyId']); ?>';
    var recordTable = $('.js-survey-answer-table');
    recordTable.on('click', '.js-detail-record', function () {
      $.getJSON($.url('admin/survey-answers/show', {userId: $(this).data('id'), surveyId: surveyId}), function (ret) {
        if (ret.code > 0) {
          var modal = template.render('answer-detail-tpl', ret);
          $(modal).modal('show');
        } else {
          $.msg(ret);
        }
      });
    });
  });
</script>
<?= $block->end() ?>
