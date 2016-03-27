<div class="header-section">
	<div class="container">
		<div class="logo"><img src="__ROOT__/Public/static/img/logo_banner.png" alt="北辰区考试数据分析平台" /></div>
		<div class="nav-bar">
			<ul>
			<if condition="strtolower(CONTROLLER_NAME) eq 'list'">
				<li class="icon-tag icon-edu">
					<a href="#edu-section" title="教育局">教育局</a>
					<span>教育局</span>
				</li>
				<li class="icon-tag icon-juniorschool">
					<a href="#juniorschool-section" title="小学">小学</a>
					<span>小学</span>
				</li>
				<li class="icon-tag icon-middleschool">
					<a href="#middleschool-section" title="初中">初中</a>
					<span>初中</span>
				</li>
				<li class="icon-tag icon-highschool">
					<a href="#highschool-section" title="高中">高中</a>
					<span>高中</span>
				</li>
			<elseif condition="strtolower(CONTROLLER_NAME) eq 'edu'"/>
				<li class="icon-tag icon-chinese">
					<a href="###" class="active" title="语文">语文</a>
					<span>语文</span>
				</li>
				<li class="icon-tag icon-math">
					<a href="###" title="数学">数学</a>
					<span>数学</span>
				</li>
				<li class="icon-tag icon-english">
					<a href="###" title="英语">英语</a>
					<span>英语</span>
				</li>
				<li class="icon-tag icon-physics">
					<a href="###" title="物理">物理</a>
					<span>物理</span>
				</li>
				<li class="icon-tag icon-chemistry">
					<a href="###" title="化学">化学</a>
					<span>化学</span>
				</li>
			</if>
			</ul>
		</div>
	</div>
</div>