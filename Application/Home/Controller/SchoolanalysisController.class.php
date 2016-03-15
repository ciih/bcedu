<?php
namespace Home\Controller;
use Think\Controller;
class SchoolanalysisController extends Controller {
    public function index(){
        // 学校成绩分析
    	$loadCss = getLoadCssStatic('detail');
    	$loadJs  = getLoadJsStatic('detail');

        $page = strtolower(CONTROLLER_NAME);
        $type = I('type');
        $schoolname = I('schoolname');

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

        $this->assign('juniorAreaName', $juniorSchoolData['areaName']);
        $this->assign('juniorSchoolList', $juniorSchoolData['schoolArea']);

        $this->assign('middleAreaName', $middleSchoolData['areaName']);
        $this->assign('middleSchoolList', $middleSchoolData['schoolArea']);

        $this->assign('highSchoolList', $highSchoolData['schoolList']);

        $this->assign('page', $page);
        $this->assign('type', $type);
        $this->assign('schoolname', $schoolname);

    	$this->display();
    }
}