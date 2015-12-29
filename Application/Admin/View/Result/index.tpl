<layout name="layout" />

<div class="col-xs-12">

	<div class="portlet box blue">
		<div class="portlet-title">
			<div class="caption"><i class="icon-comments"></i><span>提交成绩单</span></div>
		</div>
		<div class="portlet-body">
			<div class="alert alert-block alert-info fade in alert-result info-section">
				<h4 class="alert-heading">重要提示：</h4>
				<p>请认真核对基本信息与双向明细表信息，这将直接影响最后报表生成的数据情况！</p>
				<p>
					<button class="btn blue">生成分析报告</button>
				</p>
			</div>
			<div class="loading">
				<img src="../Public/static/img/admin/ajax_loading.gif" title="正在生成分析报告">
			</div>
		</div>
	</div>

</div>

<div class="col-xs-12">
	<h3 class="page-header">基本信息</h3>
	<div class="table-responsive">
		<table class="table table-bordered table-striped table-info">
			<thead>
				<tr>
					<th class="item-title"><%$schoolType['title']%></th>
					<th><%$schoolType['type']%></th>
				</tr>
				<tr>
					<th class="item-title"><%$exam['title']%></th>
					<th><%$exam['name']%></th>
				</tr>
			</thead>
		</table>
	</div>
	<h3 class="page-header">学校</h3>
	<div class="table-responsive">
		<table class="table table-bordered table-striped table-info">
			<tbody>
				<foreach name="school" item="vo" key="title">
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
	<h3 class="page-header">优秀水平</h3>
	<div class="table-responsive">
		<table class="table table-bordered table-striped table-info">
			<thead>
				<tr>
					<foreach name="course" item="co">
					<th scope="row"><span><%$co%></span></th>
				    </foreach>
				</tr>
			</thead>
			<tbody>
				<tr>
					<foreach name="score" item="sc">
					<td>
				    	<span><%$sc%></span>
					</td>
				    </foreach>
			    </tr>
			</tbody>
		</table>
	</div>
</div>