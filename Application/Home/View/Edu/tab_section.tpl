<div class="tab-section">
	<ul class="tab-nav">
		<li <eq name="page" value="score_analysis"> class="tab-active" </eq>>
			<a href="/home/edu?page=score_analysis&type=<%$type%>&school=<%$school%>" class="title cjfx">成绩深度分析</a>
		</li>
		<li>
			<a href="###" class="title jcbb">基础报表分析</a>
		</li>
		<li <eq name="page" value="deep_analysis"> class="tab-active" </eq>>
			<a href="/home/edu?page=deep_analysis&type=<%$type%>&school=<%$school%>" class="title sdfx">深度分析档案</a>
		</li>
		<li <eq name="page" value="teacher_talk"> class="tab-active" </eq>>
			<a href="http://bbs.bcedu.com/" class="title jsjl" target="_blank">典型题目库</a>
		</li>
		<if condition="($school eq junior) OR ($school eq middle) OR ($school eq high) ">
		<li <eq name="page" value="school_analysis"> class="tab-active" </eq>>
			<a href="/home/edu?page=school_analysis&type=<%$type%>&school=<%$school%>" class="title xxfx">学校成绩分析</a>
		</li>
		</if>
		<li <eq name="page" value="value_added"> class="tab-active" </eq>>
			<a href="/home/edu?page=value_added&type=<%$type%>&school=<%$school%>" class="title zzpj">增值性评价</a>
		</li>
		<li <eq name="page" value="scatter_analysis"> class="tab-active" </eq>>
			<a href="/home/edu?page=scatter_analysis&type=<%$type%>&school=<%$school%>" class="title sdtfx">散点图分析</a>
		</li>
	</ul>
</div>