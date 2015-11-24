<?php
// todo 这两个方法可以合并成一个方法
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

?>