<layout name="layout" />

<div class="col-xs-12">
	<div class="alert alert-block alert-info fade in alert-result info-section">
		<h4 class="alert-heading">重要提示：</h4>
		<p>请认真核对基本信息与双向明细表信息，这将直接影响最后报表生成的数据情况！</p>
	</div>
</div>

<div class="col-xs-12">
	<h3 class="page-header">生成分析报告</h3>
	<div class="table-responsive">
		<table class="table table-bordered table-striped table-info">
			<thead>
				<tr>
				<foreach name="course" item="vo">
					<th class="item-title"><%$vo%></th>
			    </foreach>
				</tr>
			</thead>
			<tbody>
				<tr>
				<foreach name="course" item="vo">
					<td><a href="/admin/result/createword?date=<%$date%>&foldername=<%$foldername%>&course=<%$vo%>" class="btn blue" target="_blank">生成报告</a></td>
			    </foreach>
				</tr>
			</tbody>
		</table>
	</div>
</div>