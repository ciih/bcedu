<div class="page-sidebar nav-collapse collapse">
	<ul class="page-sidebar-menu">

		<li>
			<div class="sidebar-toggler hidden-phone"></div>
		</li>

		<if condition="strtolower(CONTROLLER_NAME) neq 'result'">

		<li <eq name="pagename" value="uploadexam"> class="active open" </eq>>
			<a href="/admin/uploadexam">
				<i class="icon-th"></i>
				<span class="title">上传考试成绩</span>
				<span class="selected"></span>
			</a>
		</li>

		<li <eq name="pagename" value="uploadpdf"> class="active open" </eq>>
			<a href="/admin/uploadpdf">
				<i class="icon-edit"></i>
				<span class="title">上传基础数据分析</span>
				<span class="selected"></span>
			</a>
		</li>

		<li <eq name="pagename" value="school"> class="active open" </eq>>
			<a href="/admin/school">
				<i class="icon-sitemap"></i>
				<span class="title">学校列表</span>
				<span class="selected"></span>
			</a>
		</li>

		<li <eq name="pagename" value="user"> class="active open" </eq>>
			<a href="javascript:;">
				<i class="icon-user"></i> 
				<span class="title">用户设置</span>
				<span class="selected"></span>
				<span class="arrow <eq name="pagename" value="user">open</eq>"></span>
			</a>
			<ul class="sub-menu">
				<li <eq name="actionename" value="createuser"> class="active" </eq>>
					<a href="/admin/user/createuser">创建用户</a>
				</li>
				<li <eq name="actionename" value="finduser"> class="active" </eq>>
					<a href="/admin/user/finduser">查询用户</a>
				</li>
			</ul>
		</li>

		</if>

		<if condition="strtolower(CONTROLLER_NAME) eq 'result'">

		<li>
			<a href="/admin/uploadexam">
				<i class="icon-reply"></i> 
				<span class="title">返回上传成绩页</span>
			</a>
		</li>

		</if>

	</ul>
</div>