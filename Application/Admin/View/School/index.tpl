<layout name="layout" />

<div class="col-xs-12">
	<form id="form-xls" name="form-xls" action="/admin/school/file" method="post" enctype="multipart/form-data">
	<div class="col-md-5">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption"><i class="icon-edit"></i><span>上传学校列表</span></div>
			</div>
			<div class="portlet-body clearfix">
				<div class="info-section">
					<div class="clearfix">
						<div class="col-md-7 col-md-offset-1">
							<div class="form-group">
							    <input type="file" name="inputFile" />
							    <p class="help-block">只能上传后缀为.zip文件</p>
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

<div class="col-xs-12">
	<h3 class="page-header">小学列表<span>(共涉及<%$juniorCount%>所学校)</span></h3>
	<a href="__ROOT__/Baseinfo/小学列表.xls" class="btn blue btn-download-template">下载小学模板</a>
	<div class="table-responsive">
		<table class="table table-bordered table-striped table-info">
			<tbody>
				<foreach name="juniorData" item="vo" key="title">
					<tr>
						<th class="item-title"><span><%$title%></span></th>
						<td>
						<foreach name="vo" item="vc">
					    	<span><%$vc%></span><span class="line">|</span>
					    </foreach>
						</td>
				    </tr>
				</foreach>
			</tbody>
		</table>
	</div>
</div>
<div class="col-xs-12">
	<h3 class="page-header">初中列表<span>(共涉及<%$middleCount%>所学校)</span></h3>
	<a href="__ROOT__/Baseinfo/初中列表.xls" class="btn blue btn-download-template">下载初中模板</a>
	<div class="table-responsive">
		<table class="table table-bordered table-striped table-info">
			<tbody>
				<foreach name="middleData" item="vo" key="title">
					<tr>
						<th class="item-title"><span><%$title%></span></th>
						<td>
						<foreach name="vo" item="vc">
					    	<span><%$vc%></span><span class="line">|</span>
					    </foreach>
						</td>
				    </tr>
				</foreach>
			</tbody>
		</table>
	</div>
</div>
<div class="col-xs-12">
	<h3 class="page-header">高中列表<span>(共涉及<%$highCount%>所学校)</span></h3>
	<a href="__ROOT__/Baseinfo/高中列表.xls" class="btn blue btn-download-template">下载高中模板</a>
	<div class="table-responsive">
		<table class="table table-bordered table-striped table-info">
			<tbody>
				<foreach name="highData" item="vo" key="title">
					<tr>
						<th class="item-title"><span><%$title%></span></th>
						<td>
						<foreach name="vo" item="vc">
					    	<span><%$vc%></span><span class="line">|</span>
					    </foreach>
						</td>
				    </tr>
				</foreach>
			</tbody>
		</table>
	</div>
</div>