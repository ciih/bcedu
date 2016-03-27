<layout name="layout" />
<div class="row detail-section">
	<include file="Public/tab_section/tab_section" />
	<div class="content-section">
		<div class="detail-hd clearfix">
			<div class="title"><span class="glyphicon glyphicon-star"></span><?php echo $schoolname ?></div>
			<div class="school-classify" data-schooltype="<?php echo $type ?>" data-schoolname="<?php echo $schoolname ?>">
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
				<div id="course-dropdown" class="dropdown">
					<button class="btn btn-default dropdown-toggle disabled" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">考试科目</span><span class="caret"></span></button>
					<ul class="dropdown-menu" aria-labelledby="course-dropdown"></ul>
				</div>
				<button type="button" class="btn btn-primary btn-search disabled">查询</button>
			</div>
		</div>
		<div class="detail-bd">
			<div id="highcharts-section" class="highcharts-section">
				<div class="highcharts-load"><img src="../Public/static/img/admin/ajax_loading.gif" title="正在分析中..."><span>正在分析中</span></div>
			</div>
		</div>
	</div>
</div>