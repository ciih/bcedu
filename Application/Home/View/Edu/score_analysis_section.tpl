<div class="content-section">
	<div class="detail-hd clearfix">
		<div class="title"><span class="glyphicon glyphicon-star"></span><?php echo $schoolType ?></div>
		<div class="school-classify">
			<div class="dropdown">
				<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">学年<span class="caret"></span></button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<?php
					foreach ($schoolYear as $value) {
					?>
					<li><a href="#"><?php echo $value ?></a></li>
					<?php
					}
					?>
				</ul>
			</div>
			<div class="dropdown">
				<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">学期<span class="caret"></span></button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
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
				<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">年级<span class="caret"></span></button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
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
				<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">考试名称<span class="caret"></span></button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<?php
					foreach ($examName as $value) {
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
		<div class="loading">
			<div class="progress">
				<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%"><span class="sr-only">20% Complete</span></div>
			</div>
		</div>
	</div>
</div>