<?php
// todo 这两个方法可以合并成一个方法
// 加载css样式
function getLoadCssStatic($index) {
	$staticConfig = require "./Application/Common/Conf/load_static.php";
	$cssLink = Array();
	$num = 0;
	foreach ($staticConfig['STATIC_CONFIG'] as $key=>$val) { 
		if ($key == 'global' || $key == $index) {
			foreach ($staticConfig['STATIC_CONFIG'][$key]['css'] as $cont) {
				$cssLink[$num] = $cont;
				$num++;
			}
		}
	}
	return $cssLink;
}
// 加载js
function getLoadJsStatic($index) {
	$staticConfig = require "./Application/Common/Conf/load_static.php";
	$jsSrc = Array();
	$num = 0;
	foreach ($staticConfig['STATIC_CONFIG'] as $key=>$val) { 
		if ($key == 'global' || $key == $index) {
			foreach ($staticConfig['STATIC_CONFIG'][$key]['js_foot'] as $cont) {
				$jsSrc[$num] = $cont;
				$num++;
			}
		}
	}
	return $jsSrc;
}
// 加载html
function getLoadHtmlStatic($index) {
	$staticConfig = require "./Application/Common/Conf/load_static.php";
	$htmlLink = Array();
	$num = 0;
	foreach ($staticConfig['STATIC_CONFIG'] as $key=>$val) { 
		if ($key == $index) {
			foreach ($staticConfig['STATIC_CONFIG'][$key]['html'] as $cont) {
				$htmlLink[$num] = $cont;
				$num++;
			}
		}
	}
	return $htmlLink;
}

// 页面跳转
function redirectUrl($url) {
	if(empty($_SERVER['HTTP_REFERER']) && empty(strstr($_SERVER['HTTP_REFERER'],'edu.com')) ) {
		redirect("/{$url}");
	}
}

?>