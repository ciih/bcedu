<?php
return array(
	// '数据库配置'
	'DB_HOST'   => 'localhost',
	'DB_NAME'   => 'bcedu',
	'DB_USER'   => 'root',
	'DB_PWD'    => 'root',
	'DB_PORT'   => '3306',
	'DB_PREFIX' => 'bc_',

	// '项目参数配置'
	'TMPL_TEMPLATE_SUFFIX' => '.tpl', // 修改模板后缀
	'TMPL_L_DELIM'         => '<%', // 修改左定界符
	'TMPL_R_DELIM'         => '%>', // 修改右定界符
	'URL_MODEL'            => 2, // URL模式为Rewrite模式

	'TMPL_CACHE_ON'   => false,  // 默认开启模板编译缓存 false 的话每次都重新编译模板

	'ACTION_CACHE_ON'  => false,  // 默认关闭Action 缓存

	'HTML_CACHE_ON'   => false,   // 默认关闭静态缓存	
);