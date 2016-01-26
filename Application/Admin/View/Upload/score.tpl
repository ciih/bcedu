<layout name="layout" />

<div class="col-xs-12">
	<form id="form-score" class="form-horizontal" name="form-score" action="/admin/upload/update" method="post" enctype="multipart/form-data">
		<div class="col-md-6 col-md-offset-3">
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption"><i class="icon-edit"></i><span>设置各科分数线</span></div>
				</div>
				<div class="portlet-body clearfix">
					<foreach name="course" item="vo" key="key">
					<div class="form-group">
						<label class="col-sm-3 control-label"><%$vo%>优秀水平</label>
						<div class="col-sm-2">
							<input name="score_1_<%$key%>" type="text" class="form-control" placeholder="85" value="85">
						</div>
						<label class="col-sm-3 control-label"><%$vo%>及格水平</label>
						<div class="col-sm-2">
							<input name="score_2_<%$key%>" type="text" class="form-control" placeholder="60" value="60">
						</div>
					</div>
					</foreach>
					<input type="hidden" name="date" value="<%$date%>">
					<input type="hidden" name="foldername" value="<%$foldername%>">
					<input type="hidden" name="courseCount" value="<%$courseCount%>">
					<input type="hidden" name="grade" value="<%$grade%>">
					<div class="col-md-12 btn-upload">
						<button type="submit" class="btn blue btn-score-update">上传分数</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>