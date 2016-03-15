<layout name="layout" />
<div class="row detail-section">
	<include file="Public/tab_section/tab_section" />
	<div class="content-section">
		<div class="detail-bd">
		<?php
			include "Application/Home/View/Public/school_list/highschool_section.tpl";
			include "Application/Home/View/Public/school_list/middleschool_section.tpl";
			include "Application/Home/View/Public/school_list/juniorschool_section.tpl";
		?>
	</div>
</div>