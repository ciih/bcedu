<layout name="layout" />
<div class="row detail-section">
	<include file="Public/tab_section/tab_section" />
	<div class="content-section">
		<div class="detail-hd clearfix">
			<div class="title"><span class="glyphicon glyphicon-star"></span><?php echo $schoolname ?></div>
			<div class="school-classify" data-schooltype="<?php echo $type ?>">
				<div id="schoolyear-dropdown" class="dropdown">
					<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">学年</span><span class="caret"></span></button>
					<ul class="dropdown-menu" aria-labelledby="schoolyear-dropdown"></ul>
				</div>
				<div id="schoolterm-dropdown" class="dropdown">
					<button class="btn btn-default dropdown-toggle disabled" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">学期</span><span class="caret"></span></button>
					<ul class="dropdown-menu" aria-labelledby="schoolterm-dropdown"></ul>
				</div>
				<div id="grade-dropdown" class="dropdown">
					<button class="btn btn-default dropdown-toggle disabled" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">年级</span><span class="caret"></span></button>
					<ul class="dropdown-menu" aria-labelledby="grade-dropdown"></ul>
				</div>
				<button type="button" class="btn btn-primary btn-search disabled">查询</button>
			</div>
		</div>
		<div class="detail-bd">
			<div class="table-section">
				<div class="table-content">
					<table class="table-hover">
						<thead>
							<tr>
								<th>考试名称</th>
								<th>考试科目</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>