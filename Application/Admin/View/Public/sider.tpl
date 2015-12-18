<div class="page-sidebar nav-collapse collapse">
	<ul class="page-sidebar-menu">

		<li>
			<div class="sidebar-toggler hidden-phone"></div>
		</li>

		<if condition="strtolower(CONTROLLER_NAME) eq 'school'">

		<li class="start active open">
			<a href="javascript:;">
				<i class="icon-th"></i>
				<span class="title">录入<%$info[0]%>成绩</span>
				<span class="selected"></span>
				<span class="arrow open"></span>
			</a>
			<ul class="sub-menu">
				<li <eq name="grade" value="$info[2]"> class="active" </eq>>
					<a href="/admin/school?type=<%$type%>&school=<%$school%>&grade=<%$info[2]%>"><%$info[1]%></a>
				</li>
				<li <eq name="grade" value="$info[4]"> class="active" </eq>>
					<a href="/admin/school?type=<%$type%>&school=<%$school%>&grade=<%$info[4]%>"><%$info[3]%></a>
				</li>
				<li <eq name="grade" value="$info[6]"> class="active" </eq>>
					<a href="/admin/school?type=<%$type%>&school=<%$school%>&grade=<%$info[6]%>"><%$info[5]%></a>
				</li>
			</ul>
		</li>

		</if>

		<if condition="strtolower(CONTROLLER_NAME) eq 'list'">

		<li>
			<a href="javascript:;">
				<i class="icon-user"></i> 
				<span class="title">用户设置</span>
				<span class="selected"></span>
				<span class="arrow"></span>
			</a>
			<ul class="sub-menu">
				<li>
					<a href="/admin/createuser">创建用户</a>
				</li>
				<li>
					<a href="/admin/finduser">查询用户</a>
				</li>
			</ul>
		</li>

		<li class="start active">
			<a href="/admin/list">
				<i class="icon-bookmark-empty"></i> 
				<span class="title">选择学校</span>
				<span class="selected"></span>
			</a>
		</li>

		</if>

		<if condition="strtolower(CONTROLLER_NAME) neq 'list'">

		<li>
			<a href="/admin/list">
				<i class="icon-reply"></i> 
				<span class="title">返回列表页</span>
			</a>
		</li>

		</if>

	</ul>
</div>