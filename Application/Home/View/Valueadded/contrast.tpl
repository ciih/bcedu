<layout name="layout" />
<div class="row detail-section">
	<include file="Public/tab_section/tab_section" />
	<div class="content-section">
		<div class="detail-hd clearfix">
			<div class="title"><span class="glyphicon glyphicon-star"></span><?php echo $schoolname ?></div>
			<div class="school-classify">
				<div id="num-dropdown" class="dropdown">
					<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">对比次数</span><span class="caret"></span></button>
					<ul class="dropdown-menu" aria-labelledby="num-dropdown">
						<li><a href="#">2</a></li>
						<li><a href="#">3</a></li>
					</ul>
				</div>
				<button type="button" class="btn btn-primary btn-submit">确定</button>
			</div>
		</div>
		<div class="detail-bd">
			<div class="school-classify-section" data-schooltype="<?php echo $type ?>" data-schoolname="<?php echo $schoolname ?>">
				<div class="school-classify clearfix" data-itemnum="1">
					<div id="schoolyear1-dropdown" class="dropdown">
						<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">学年</span><span class="caret"></span></button>
						<ul class="dropdown-menu" aria-labelledby="schoolyear-dropdown"></ul>
					</div>
					<div id="schoolterm1-dropdown" class="dropdown">
						<button class="btn btn-default dropdown-toggle disabled" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">学期</span><span class="caret"></span></button>
						<ul class="dropdown-menu" aria-labelledby="schoolterm-dropdown"></ul>
					</div>
					<div id="grade1-dropdown" class="dropdown">
						<button class="btn btn-default dropdown-toggle disabled" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">年级</span><span class="caret"></span></button>
						<ul class="dropdown-menu" aria-labelledby="grade-dropdown"></ul>
					</div>
					<div id="course1-dropdown" class="dropdown">
						<button class="btn btn-default dropdown-toggle disabled" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">考试科目</span><span class="caret"></span></button>
						<ul class="dropdown-menu" aria-labelledby="course-dropdown"></ul>
					</div>
					<button type="button" class="btn btn-primary btn-search disabled">查询</button>
				</div>
				<div class="school-classify clearfix" data-itemnum="2">
					<div id="schoolyear2-dropdown" class="dropdown">
						<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">学年</span><span class="caret"></span></button>
						<ul class="dropdown-menu" aria-labelledby="schoolyear-dropdown"></ul>
					</div>
					<div id="schoolterm2-dropdown" class="dropdown">
						<button class="btn btn-default dropdown-toggle disabled" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">学期</span><span class="caret"></span></button>
						<ul class="dropdown-menu" aria-labelledby="schoolterm-dropdown"></ul>
					</div>
					<div id="grade2-dropdown" class="dropdown">
						<button class="btn btn-default dropdown-toggle disabled" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">年级</span><span class="caret"></span></button>
						<ul class="dropdown-menu" aria-labelledby="grade-dropdown"></ul>
					</div>
					<div id="course2-dropdown" class="dropdown">
						<button class="btn btn-default dropdown-toggle disabled" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">考试科目</span><span class="caret"></span></button>
						<ul class="dropdown-menu" aria-labelledby="course-dropdown"></ul>
					</div>
					<button type="button" class="btn btn-primary btn-search disabled">查询</button>
				</div>
				<div class="school-classify clearfix" data-itemnum="3">
					<div id="schoolyear3-dropdown" class="dropdown">
						<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">学年</span><span class="caret"></span></button>
						<ul class="dropdown-menu" aria-labelledby="schoolyear-dropdown"></ul>
					</div>
					<div id="schoolterm3-dropdown" class="dropdown">
						<button class="btn btn-default dropdown-toggle disabled" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">学期</span><span class="caret"></span></button>
						<ul class="dropdown-menu" aria-labelledby="schoolterm-dropdown"></ul>
					</div>
					<div id="grade3-dropdown" class="dropdown">
						<button class="btn btn-default dropdown-toggle disabled" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">年级</span><span class="caret"></span></button>
						<ul class="dropdown-menu" aria-labelledby="grade-dropdown"></ul>
					</div>
					<div id="course3-dropdown" class="dropdown">
						<button class="btn btn-default dropdown-toggle disabled" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">考试科目</span><span class="caret"></span></button>
						<ul class="dropdown-menu" aria-labelledby="course-dropdown"></ul>
					</div>
					<button type="button" class="btn btn-primary btn-search disabled">查询</button>
				</div>
			</div>
			<div class="highcharts-section-load">
				<div class="highcharts-load"><img src="__ROOT__/Public/static/img/admin/ajax_loading.gif" title="正在分析中..."><span>正在分析中</span></div>
			</div>
			<div id="highcharts-section" class="highcharts-section"></div>
		</div>
	</div>
</div>