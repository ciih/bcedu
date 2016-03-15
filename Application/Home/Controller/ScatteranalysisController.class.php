<?php
namespace Home\Controller;
use Think\Controller;
class ScatteranalysisController extends Controller {
    public function index(){
        // 散点图分析
    	$loadCss = getLoadCssStatic('detail');
    	$loadJs  = getLoadJsStatic('detail');

        $page = strtolower(CONTROLLER_NAME);
        $type = I('type');
        $schoolname = I('schoolname');

        $examInfo = getExamInfo($type);

        $examTable = M('exam');
        $data = $examTable->where("schooltype='$type'")->order('id desc')->select();

        if(!empty($data)) {
        	foreach ($data as $key => $value) {
	            $schoolyear[] = $data[$key]['schoolyear'];
	            $schoolterm[] = $data[$key]['schoolterm'];
	            $grade[] = $data[$key]['grade'];
	            $examname[] = $data[$key]['examname'];
        	}

        } else {
            $schoolyear = (date('Y') - 1).'-'.date('Y');
            $schoolterm = $examInfo['schoolterm'];
            $grade = $examInfo['grade'];
            $examname = $examInfo['examname'];
        }

        $schoolyear = array_unique($schoolyear);
        $schoolterm = array_unique($schoolterm);
        $grade = array_unique($grade);
        $examname = array_unique($examname);

    	$this->assign('loadCss', $loadCss);
    	$this->assign('loadJs', $loadJs);

        $this->assign('page', $page);
        $this->assign('type', $type);
        $this->assign('schoolname', $schoolname);

        $this->assign('schoolyear', $schoolyear);
        $this->assign('schoolterm', $schoolterm);
        $this->assign('grade', $grade);
        $this->assign('examname', $examname);

    	$this->display();
    }
}