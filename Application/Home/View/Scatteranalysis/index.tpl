<layout name="layout" />
<div class="row detail-section">
	<include file="Public/tab_section/tab_section" />
	<div class="content-section">
		<div class="detail-hd clearfix">
			<div class="title"><span class="glyphicon glyphicon-star"></span><?php echo $schoolname ?></div>
			<div class="school-classify">
				<div class="dropdown">
					<button class="btn btn-default dropdown-toggle" type="button" id="schoolyear-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">学年</span><span class="caret"></span></button>
					<ul class="dropdown-menu" aria-labelledby="schoolyear-dropdown">
						<li><a href="#"><?php echo $schoolyear ?></a></li>
					</ul>
				</div>
				<div class="dropdown">
					<button class="btn btn-default dropdown-toggle" type="button" id="schoolterm-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">学期</span><span class="caret"></span></button>
					<ul class="dropdown-menu" aria-labelledby="schoolterm-dropdown">
						<?php
						foreach ($schoolterm as $value) {
						?>
						<li><a href="#"><?php echo $value ?></a></li>
						<?php
						}
						?>
					</ul>
				</div>
				<div class="dropdown">
					<button class="btn btn-default dropdown-toggle" type="button" id="grade-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">年级</span><span class="caret"></span></button>
					<ul class="dropdown-menu" aria-labelledby="grade-dropdown">
						<?php
						foreach ($grade as $value) {
						?>
						<li><a href="#"><?php echo $value ?></a></li>
						<?php
						}
						?>
					</ul>
				</div>
				<div class="dropdown">
					<button class="btn btn-default dropdown-toggle" type="button" id="examname-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="name">考试名称</span><span class="caret"></span></button>
					<ul class="dropdown-menu" aria-labelledby="examname-dropdown">
						<?php
						foreach ($examname as $value) {
						?>
						<li><a href="#"><?php echo $value ?></a></li>
						<?php
						}
						?>
					</ul>
				</div>
				<button type="button" class="btn btn-primary btn-search">查询</button>
			</div>
		</div>
		<div class="detail-bd">
		</div>
	</div>
</div>