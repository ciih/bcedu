<div id="highschool-section" class="row section">
	<div class="classify-hd">
		<div class="title"><span class="glyphicon glyphicon-star"></span>高中</div>
	</div>
	<div class="classify-bd">
		<ul class="pic-list">
			<?php
				foreach($highSchoolList as $name){
			?>
			<li>
				<a href="/home/scoreanalysis?type=high&schoolname=<?php echo urlencode($name) ?>" target="_blank" title="<?php echo $name ?>">
					<div class="pic"><img src="../Public/static/img/default_school.png" /></div>
					<div class="txt"><?php echo $name ?></div>
				</a>
			</li>
			<?php
				}
			?>
		</ul>
	</div>
</div>