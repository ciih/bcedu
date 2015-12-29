<layout name="layout" />

<div class="col-xs-12">
	<form id="form-xls" name="form-xls" action="/admin/upload/file" method="post" enctype="multipart/form-data">
	<div class="col-md-5 col-md-offset-4">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption"><i class="icon-edit"></i><span>上传成绩单</span></div>
			</div>
			<div class="portlet-body clearfix">
				<div class="info-section">
					<div class="clearfix">
						<div class="col-md-7 col-md-offset-1">
							<div class="form-group">
							    <input type="file" name="inputFile" />
							    <p class="help-block">只能上传后缀为zip或者rar文件</p>
							</div>
						</div>
					</div>
					<div class="clearfix">
						<div class="col-md-7 col-md-offset-1">
							<button type="submit" class="btn blue btn-ok">开始上传</button>
						</div>
					</div>
				</div>
				<div class="loading">
					<img src="../Public/static/img/admin/ajax_loading.gif" title="正在上传中">
				</div>
			</div>
		</div>
	</div>
	</form>
</div>