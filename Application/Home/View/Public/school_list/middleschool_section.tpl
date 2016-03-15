<?php
	foreach($middleAreaName as $number => $area){
?>
<div <?php if($number == 0) { ?>id="middleschool-section"<?php } ?> class="row section">
	<div class="classify-hd">
		<div class="title"><span class="glyphicon glyphicon-star"></span>初中 - <?php echo $area ?></div>
	</div>
	<div class="classify-bd">
		<ul class="pic-list">
			<?php
				foreach($middleSchoolList as $name => $schoolarea){
				if($area == $schoolarea){
			?>
			<li>
				<a href="/home/scoreanalysis?type=middle&schoolname=<?php echo urlencode($name) ?>" target="_blank" title="<?php echo $name ?>">
					<div class="pic"><img src="../Public/static/img/default_school.png" /></div>
					<div class="txt"><?php echo $name ?></div>
				</a>
			</li>
			<?php
				}
				}
			?>
		</ul>
	</div>
</div>
<?php
	}
?>