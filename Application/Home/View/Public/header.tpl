<div class="header-section">
	<div class="container">
		<div class="logo"><img src="../Public/static/img/logo_banner.png" alt="北辰区考试数据分析平台" /></div>
		<div class="nav-bar">
			<ul>
			<if condition="(strtolower(CONTROLLER_NAME) eq 'list') OR ($page eq 'school_analysis')">
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
					<a href="/home/edu?page=<%$page%>&type=<%$type%>&school=<%$school%>&course=chinese" title="语文" <eq name="course" value="chinese"> class="active" </eq>>语文</a>
					<span>语文</span>
				</li>
				<li class="icon-tag icon-math">
					<a href="/home/edu?page=<%$page%>&type=<%$type%>&school=<%$school%>&course=math" title="数学" <eq name="course" value="math"> class="active" </eq>>数学</a>
					<span>数学</span>
				</li>
				<li class="icon-tag icon-english">
					<a href="/home/edu?page=<%$page%>&type=<%$type%>&school=<%$school%>&course=english" title="英语" <eq name="course" value="english"> class="active" </eq>>英语</a>
					<span>英语</span>
				</li>

				<if condition="$type neq 'junior'">
				<li class="icon-tag icon-physics">
					<a href="/home/edu?page=<%$page%>&type=<%$type%>&school=<%$school%>&course=physics" title="物理" <eq name="course" value="physics"> class="active" </eq>>物理</a>
					<span>物理</span>
				</li>
				<li class="icon-tag icon-chemistry">
					<a href="/home/edu?page=<%$page%>&type=<%$type%>&school=<%$school%>&course=chemistry" title="化学" <eq name="course" value="chemistry"> class="active" </eq>>化学</a>
					<span>化学</span>
				</li>
				</if>
			</if>
			</ul>
		</div>
	</div>
</div>