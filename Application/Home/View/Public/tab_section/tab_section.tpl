<div class="tab-section">
	<ul class="tab-nav">
		<li <eq name="page" value="scoreanalysis"> class="tab-active" </eq>>
			<a href="/home/scoreanalysis?type=<%$type%>&schoolname=<%$schoolname%>" class="title cjfx">成绩深度分析</a>
		</li>
		<li <eq name="page" value="baseanalysis"> class="tab-active" </eq>>
			<a href="/home/baseanalysis?type=<%$type%>&schoolname=<%$schoolname%>" class="title jcbb">基础报表分析</a>
		</li>
		<li <eq name="page" value="deepanalysis"> class="tab-active" </eq>>
			<a href="/home/deepanalysis?type=<%$type%>&schoolname=<%$schoolname%>" class="title sdfx">深度分析档案</a>
		</li>
		<li <eq name="page" value="teachertalk"> class="tab-active" </eq>>
			<a href="http://bbs.bcedu.com/" class="title jsjl" target="_blank">典型题目库</a>
		</li>
		<if condition="($schoolname eq 小学) OR ($schoolname eq 初中) OR ($schoolname eq 高中) ">
		<?php // 暂时不计算各学校数据 ?>
		<!-- <li <eq name="page" value="schoolanalysis"> class="tab-active" </eq>>
			<a href="/home/schoolanalysis?type=<%$type%>&schoolname=<%$schoolname%>" class="title xxfx">学校成绩分析</a>
		</li> -->
		</if>
		<li <eq name="page" value="valueadded"> class="tab-active" </eq>>
			<a href="/home/valueadded?type=<%$type%>&schoolname=<%$schoolname%>" class="title zzpj">增值性评价</a>
		</li>
		<li <eq name="page" value="scatteranalysis"> class="tab-active" </eq>>
			<a href="/home/scatteranalysis?type=<%$type%>&schoolname=<%$schoolname%>" class="title sdtfx">散点图分析</a>
		</li>
	</ul>
</div>