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
				<if condition="$type eq 'junior'">
				<li class="icon-tag icon-juniorschool">
					<a href="/home/edu?page=<%$page%>&type=<%$type%>&school=<%$school%>&grade=j4" title="四年级" <eq name="grade" value="j4"> class="active" </eq>>四年级</a>
					<span>四年级</span>
				</li>
				<li class="icon-tag icon-juniorschool">
					<a href="/home/edu?page=<%$page%>&type=<%$type%>&school=<%$school%>&grade=j5" title="五年级" <eq name="grade" value="j5"> class="active" </eq>>五年级</a>
					<span>五年级</span>
				</li>
				<li class="icon-tag icon-juniorschool">
					<a href="/home/edu?page=<%$page%>&type=<%$type%>&school=<%$school%>&grade=j6" title="六年级" <eq name="grade" value="j6"> class="active" </eq>>六年级</a>
					<span>六年级</span>
				</li>
				</if>
				<if condition="$type eq 'middle'">
				<li class="icon-tag icon-middleschool">
					<a href="/home/edu?page=<%$page%>&type=<%$type%>&school=<%$school%>&grade=m7" title="七年级" <eq name="grade" value="m7"> class="active" </eq>>七年级</a>
					<span>七年级</span>
				</li>
				<li class="icon-tag icon-middleschool">
					<a href="/home/edu?page=<%$page%>&type=<%$type%>&school=<%$school%>&grade=m8" title="八年级" <eq name="grade" value="m8"> class="active" </eq>>八年级</a>
					<span>八年级</span>
				</li>
				<li class="icon-tag icon-middleschool">
					<a href="/home/edu?page=<%$page%>&type=<%$type%>&school=<%$school%>&grade=m9" title="九年级" <eq name="grade" value="m9"> class="active" </eq>>九年级</a>
					<span>九年级</span>
				</li>
				</if>
				<if condition="$type eq 'high'">
				<li class="icon-tag icon-highschool">
					<a href="/home/edu?page=<%$page%>&type=<%$type%>&school=<%$school%>&grade=h1" title="高一" <eq name="grade" value="h1"> class="active" </eq>>高一</a>
					<span>高一</span>
				</li>
				<li class="icon-tag icon-highschool">
					<a href="/home/edu?page=<%$page%>&type=<%$type%>&school=<%$school%>&grade=h2" title="高二" <eq name="grade" value="h2"> class="active" </eq>>高二</a>
					<span>高二</span>
				</li>
				<li class="icon-tag icon-highschool">
					<a href="/home/edu?page=<%$page%>&type=<%$type%>&school=<%$school%>&grade=h3" title="高三" <eq name="grade" value="h3"> class="active" </eq>>高三</a>
					<span>高三</span>
				</li>
				</if>
			</if>
			</ul>
		</div>
	</div>
</div>