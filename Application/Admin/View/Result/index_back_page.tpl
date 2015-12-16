<layout name="layout" />

<div class="col-xs-12">

	<div class="portlet box blue">
		<div class="portlet-title">
			<div class="caption"><i class="icon-edit"></i>成绩单</div>
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

			<div id="sample_editable_1_wrapper" class="dataTables_wrapper form-inline" role="grid">
				<table class="table table-striped table-hover table-bordered dataTable" id="sample_editable_1" aria-describedby="sample_editable_1_info">
					<thead>
						<tr role="row">
							<th class="sorting_disabled" role="columnheader" rowspan="1" colspan="1" aria-label="Username" style="width: 148px;">Username</th>
							<th class="sorting" role="columnheader" tabindex="0" aria-controls="sample_editable_1" rowspan="1" colspan="1" aria-label="Full Name: activate to sort column ascending" style="width: 200px;">Full Name</th>
							<th class="sorting" role="columnheader" tabindex="0" aria-controls="sample_editable_1" rowspan="1" colspan="1" aria-label="Points: activate to sort column ascending" style="width: 97px;">Points</th>
							<th class="sorting" role="columnheader" tabindex="0" aria-controls="sample_editable_1" rowspan="1" colspan="1" aria-label="Notes: activate to sort column ascending" style="width: 133px;">Notes</th>
							<th class="sorting" role="columnheader" tabindex="0" aria-controls="sample_editable_1" rowspan="1" colspan="1" aria-label="Edit: activate to sort column ascending" style="width: 66px;">Edit</th>
							<th class="sorting" role="columnheader" tabindex="0" aria-controls="sample_editable_1" rowspan="1" colspan="1" aria-label="Delete: activate to sort column ascending" style="width: 102px;">Delete</th>
						</tr>
					</thead>
					<tbody role="alert" aria-live="polite" aria-relevant="all">
						<tr class="odd">
							<td class=" sorting_1">alex</td>
							<td class=" ">Alex Nilson</td>
							<td class=" ">1234</td>
							<td class="center ">power user</td>
							<td class=" "><a class="edit" href="javascript:;">Edit</a></td>
							<td class=" "><a class="delete" href="javascript:;">Delete</a></td>

						</tr>
						<tr class="even">
							<td class=" sorting_1">gist124</td>
							<td class=" ">Nick Roberts</td>
							<td class=" ">62</td>
							<td class="center ">new user</td>
							<td class=" "><a class="edit" href="javascript:;">Edit</a></td>
							<td class=" "><a class="delete" href="javascript:;">Delete</a></td>

						</tr>
						<tr class="odd">
							<td class=" sorting_1">goldweb</td>
							<td class=" ">Sergio Jackson</td>
							<td class=" ">132</td>
							<td class="center ">elite user</td>
							<td class=" "><a class="edit" href="javascript:;">Edit</a></td>
							<td class=" "><a class="delete" href="javascript:;">Delete</a></td>
						</tr>
						<tr class="even">
							<td class=" sorting_1">lisa</td>
							<td class=" ">Lisa Wong</td>
							<td class=" ">434</td>
							<td class="center ">new user</td>
							<td class=" "><a class="edit" href="javascript:;">Edit</a></td>
							<td class=" "><a class="delete" href="javascript:;">Delete</a></td>

						</tr>
						<tr class="odd">
							<td class=" sorting_1">nick12</td>
							<td class=" ">Nick Roberts</td>
							<td class=" ">232</td>
							<td class="center ">power user</td>
							<td class=" "><a class="edit" href="javascript:;">Edit</a></td>
							<td class=" "><a class="delete" href="javascript:;">Delete</a></td>

						</tr>
					</tbody>
				</table>
			</div>
			<div class="clearfix">
				<ul class="pagination pull-right">
					<li>
						<a href="#" aria-label="Previous">
							<span aria-hidden="true">«</span>
						</a>
					</li>
					<li><a href="#">1</a></li>
					<li><a href="#">2</a></li>
					<li><a href="#">3</a></li>
					<li><a href="#">4</a></li>
					<li><a href="#">5</a></li>
					<li>
						<a href="#" aria-label="Next">
							<span aria-hidden="true">»</span>
						</a>
					</li>
				</ul>
			</div>


			<style>
			.excel-data tr:nth-child(odd){background:#fff;}
			.excel-data tr:nth-child(even){background:#e5e5e5;}
			</style>
			<div id="index-login" class="bg-index">
				<table cellpadding="0" cellspacing="1">
					<thead style="background:#ccc;">
						<foreach name="keys" item="vo" >
						    <th style="min-width:100px;text-align:center;"><%$vo%></th>
						</foreach>
					</thead>
					<tbody class="excel-data">
						<foreach name="rets" item="vo" >
							<tr>
								<foreach name="vo" item="vos">
							    	<td style="min-width:100px;text-align:center;"><%$vos%></td>
							    </foreach>
						    </tr>
						</foreach>			
					</tbody>
				</table>
			</div>


			
		</div>
	</div>
</div>