<div id="edu-section" class="row section">
	<div class="classify-hd">
		<div class="title"><span class="glyphicon glyphicon-star"></span>教育局</div>
	</div>
	<div class="classify-bd">
		<ul class="pic-list">
			<?php
				if($schoolgroup == 'super' || $schoolgroup == 'all'){
			?>
			<li>
				<a href="/home/scoreanalysis?type=junior&schoolname=小学" target="_blank">
					<div class="pic"><img src="../Public/static/img/default_school.png" /></div>
					<div class="txt">小学</div>
				</a>
			</li>
			<li>
				<a href="/home/scoreanalysis?type=middle&schoolname=初中" target="_blank">
					<div class="pic"><img src="../Public/static/img/default_school.png" /></div>
					<div class="txt">初中</div>
				</a>
			</li>
			<li>
				<a href="/home/scoreanalysis?type=high&schoolname=高中" target="_blank">
					<div class="pic"><img src="../Public/static/img/default_school.png" /></div>
					<div class="txt">高中</div>
				</a>
			</li>
			<?php
				}
			?>
			<?php
				if($schoolgroup == 'high'){
			?>
			<li>
					<div class="pic"><img src="../Public/static/img/default_school.png" /></div>
					<div class="txt">小学</div>
			</li>
			<li>
					<div class="pic"><img src="../Public/static/img/default_school.png" /></div>
					<div class="txt">初中</div>
			</li>
			<li>
				<a href="/home/scoreanalysis?type=high&schoolname=高中" target="_blank">
					<div class="pic"><img src="../Public/static/img/default_school.png" /></div>
					<div class="txt">高中</div>
				</a>
			</li>
			<?php
				}
			?>
			<?php
				if($schoolgroup == 'middle'){
			?>
			<li>
					<div class="pic"><img src="../Public/static/img/default_school.png" /></div>
					<div class="txt">小学</div>
			</li>
			<li>
				<a href="/home/scoreanalysis?type=middle&schoolname=初中" target="_blank">
					<div class="pic"><img src="../Public/static/img/default_school.png" /></div>
					<div class="txt">初中</div>
				</a>
			</li>
			<li>
					<div class="pic"><img src="../Public/static/img/default_school.png" /></div>
					<div class="txt">高中</div>
			</li>
			<?php
				}
			?>
			<?php
				if($schoolgroup == 'junior'){
			?>
			<li>
				<a href="/home/scoreanalysis?type=junior&schoolname=小学" target="_blank">
					<div class="pic"><img src="../Public/static/img/default_school.png" /></div>
					<div class="txt">小学</div>
				</a>
			</li>
			<li>
					<div class="pic"><img src="../Public/static/img/default_school.png" /></div>
					<div class="txt">初中</div>
			</li>
			<li>
					<div class="pic"><img src="../Public/static/img/default_school.png" /></div>
					<div class="txt">高中</div>
			</li>
			<?php
				}
			?>
		</ul>
	</div>
</div>