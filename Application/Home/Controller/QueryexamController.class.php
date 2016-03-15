<?php
namespace Home\Controller;
use Think\Controller;
class QueryexamController extends Controller {
    public function ajax_get_exam(){
    	$schooltype = I('schooltype');
    	$count = I('count');

    	$examTable = M('exam');
    	if(empty($count)) {
    		$data = $examTable->where("schooltype='$schooltype'")->order('id desc')->select();
    	} else {
    		$data = $examTable->where("schooltype='$schooltype'")->order('id desc')->limit($count)->select();
    	}
        
        $this->ajaxReturn (json_encode($data),'JSON');
    }
    public function ajax_get_school(){
        $schooltype = I('schooltype');
        
        // 获取学校列表
        $schoolInfoObj = new \Admin\Model\SchoolInfoData();
        $schoolInfoData = $schoolInfoObj->getSchoolData($schooltype);
        
        $this->ajaxReturn (json_encode($schoolInfoData['schoolList']),'JSON');
    }

    public function ajax_get_zvalue(){
    	$schooltype = I('schooltype');
    	
        // 获取考试数据目录
        $ZValueObj = new \Admin\Model\ZValueData($date, $foldername, $course);
        $ZValueData = $ZValueObj->getZValueData();
        
        $this->ajaxReturn (json_encode($schoolInfoData['schoolList']),'JSON');
    }
}