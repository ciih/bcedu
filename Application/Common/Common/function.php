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

function getLoadPageJsStatic($index) {
	$staticConfig = require "./Application/Common/Conf/load_static.php";
	$jsSrc = Array();
	$num = 0;
	foreach ($staticConfig['STATIC_CONFIG'] as $key=>$val) { 
		if ($key == $index) {
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

// 返回考试信息
function getExamInfo($type) {
    if($type == 'high') {
        $grade = array('高一年级','高二年级','高三年级');
    } elseif($type == 'middle') {
        $grade = array('七年级','八年级','九年级');
    } elseif($type == 'junior') {
        $grade = array('四年级','五年级','六年级');
    }

    $schoolterm = array('第一学期','第二学期');
    $examname = array('期中考试','期末考试','一模考试','二模考试','三模考试');

    return array(
        'schoolterm' => $schoolterm, // 学期
        'grade'      => $grade, // 年级
        'examname'   => $examname, // 考试名称
    );
}

// 删除文件夹及文件
function deldir($dir) {
  //先删除目录下的文件：
  $dh=opendir($dir);
  while ($file=readdir($dh)) {
    if($file!="." && $file!="..") {
      $fullpath=$dir."/".$file;
      if(!is_dir($fullpath)) {
          unlink($fullpath);
      } else {
          deldir($fullpath);
      }
    }
  }
  
  closedir($dh);
  //删除当前文件夹：
  if(rmdir($dir)) {
    return true;
  } else {
    return false;
  }
}

?>