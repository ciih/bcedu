<layout name="layout" />

<div class="col-xs-12">

	<div class="portlet box blue">
		<div class="portlet-title">
			<div class="caption"><i class="icon-comments"></i>提交成绩单</div>
		</div>
		<div class="portlet-body">
			<div class="alert alert-block alert-info fade in">

				<h4 class="alert-heading">重要提示：</h4>
				<p>请认真核对学生成绩及考卷信息，错误的数据将影响到最后报表的各项参数！</p>
				<p>
					<a class="btn blue" href="#" style="margin-right:15px;">确认</a><a class="btn red" href="#">返回</a>
				</p>

			</div>
		</div>
	</div>

	<div class="alert alert-success"><strong>Success!</strong> The page has been added.</div>

</div>

<div class="col-xs-12">

	<div class="portlet box blue">

		<div class="portlet-title">
			<div class="caption"><i class="icon-edit"></i>成绩单</div>
		</div>

		<div class="portlet-body">

			<div class="dataTables_wrapper form-inline" role="grid">
				<table class="table table-striped table-hover table-bordered dataTable" id="sample_editable_1" aria-describedby="sample_editable_1_info">
					<thead>
						<tr role="row">
						<foreach name="keys" item="vo" >
						    <th class="sorting_disabled" role="columnheader" rowspan="1" colspan="1"><%$vo%></th>
						</foreach>
						</tr>
					</thead>
					<tbody role="alert" aria-live="polite" aria-relevant="all">
					<foreach name="rets" item="vo" key="k">
						<if condition="$k%2 == 0">
							<tr class="even">
								<foreach name="vo" item="vos">
							    	<td><%$vos%></td>
							    </foreach>
						    </tr>
						<else />
							<tr class="odd">
								<foreach name="vo" item="vos">
							    	<td><%$vos%></td>
							    </foreach>
						    </tr>
						</if>
					</foreach>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>