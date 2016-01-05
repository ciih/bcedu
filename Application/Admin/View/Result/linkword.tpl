<layout name="layout" />

<div class="col-xs-12">
	<h3 class="page-header">下载分析报告</h3>
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