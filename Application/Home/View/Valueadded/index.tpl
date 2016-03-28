<layout name="layout" />
<div class="row detail-section">
	<include file="Public/tab_section/tab_section" />
	<div class="content-section">
		<div class="detail-hd clearfix">
			<div class="title"><span class="glyphicon glyphicon-star"></span><?php echo $schoolname ?></div>
		</div>
		<div class="detail-bd">
			<ul class="selection-section">
				<li><a href="valueadded/single?type=<?php echo $type ?>&schoolname=<?php echo $schoolname ?>" class="btn btn-primary">单次-增值性评价</a></li>
				<li><a href="valueadded/multi?type=<?php echo $type ?>&schoolname=<?php echo $schoolname ?>" class="btn btn-primary">历次-增值性评价</a></li>
				<li><a href="" class="btn btn-primary disabled">对比-增值性评价</a></li>
			</ul>
		</div>
	</div>
</div>