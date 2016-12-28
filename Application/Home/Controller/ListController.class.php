<?php
namespace Home\Controller;
use Think\Controller;
class ListController extends Controller {
    public function index(){
        if (!session('?username')) {
            redirectUrl('index');
        }
        
        $username = session('username');
        $schoolgroup = session('schoolgroup');
        $role = session('role');

    	$loadCss = getLoadCssStatic('list');
    	$loadJs = getLoadJsStatic('list');

    	$schoolType = array('junior','middle','high');

        // 获取学校列表
        $juniorSchoolObj = new \Admin\Model\SchoolInfoData();
        $juniorSchoolData = $juniorSchoolObj->getSchoolData($schoolType[0]);

        $middleSchoolObj = new \Admin\Model\SchoolInfoData();
        $middleSchoolData = $juniorSchoolObj->getSchoolData($schoolType[1]);

        $highSchoolObj = new \Admin\Model\SchoolInfoData();
        $highSchoolData = $juniorSchoolObj->getSchoolData($schoolType[2]);

    	$this->assign('loadCss', $loadCss);
    	$this->assign('loadJs', $loadJs);

        $this->assign('username', $username);
        $this->assign('schoolgroup', $schoolgroup);
        $this->assign('role', $role);
        
    	$this->assign('juniorAreaName', $juniorSchoolData['areaName']);
    	$this->assign('juniorSchoolList', $juniorSchoolData['schoolArea']);

    	$this->assign('middleAreaName', $middleSchoolData['areaName']);
    	$this->assign('middleSchoolList', $middleSchoolData['schoolArea']);

    	$this->assign('highSchoolList', $highSchoolData['schoolList']);

    	$this->display();
    }
}