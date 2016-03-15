<?php
namespace Home\Controller;
use Think\Controller;
class TeachertalkController extends Controller {
    public function index(){
        // 教师交流室
    	$loadCss = getLoadCssStatic('detail');
    	$loadJs  = getLoadJsStatic('detail');

        $page = strtolower(CONTROLLER_NAME);
        $type = I('type');
        $schoolname = I('schoolname');

    	$this->assign('loadCss', $loadCss);
    	$this->assign('loadJs', $loadJs);

        $this->assign('page', $page);
        $this->assign('type', $type);
        $this->assign('schoolname', $schoolname);

    	$this->display();
    }
}