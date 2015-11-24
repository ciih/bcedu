<layout name="index_layout" />
<div id="index-login" class="bg-index">
	<div class="login-section">
		<div class="login-title">用户登录</div>
		<form id="form-login" name="form-login" action="__URL__/login" method="post">
			<div class="input-group">
				<span class="glyphicon glyphicon-user"></span>
				<input type="text" class="form-control input-lg" name="inputUsername" placeholder="用户名">
			</div>
			<div class="input-group">
				<span class="glyphicon glyphicon-lock"></span>
				<input type="password" class="form-control input-lg" name="inputPassword" placeholder="密码">
			</div>
			<div class="input-group">
				<button type="submit" class="btn btn-lg btn-info">登录</button>
			</div>
		</form>
	</div>
</div>