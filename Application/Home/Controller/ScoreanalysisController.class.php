<?php
namespace Home\Controller;
use Think\Controller;
class ScoreanalysisController extends Controller {
    public function index(){
        if (!session('?username')) {
            redirectUrl('index');
        }
        
        $username = session('username');
        $schoolgroup = session('schoolgroup');
        $role = session('role');
        
        // 成绩深度分析
    	$loadCss     = getLoadCssStatic('detail');
        $loadJs      = getLoadJsStatic('detail');
    	$loadPageJs  = getLoadPageJsStatic('scoreanalysis');

        $page = strtolower(CONTROLLER_NAME);
        $type = I('type');
        $schoolname = I('schoolname');

    	$this->assign('loadCss', $loadCss);
        $this->assign('loadJs', $loadJs);
    	$this->assign('loadPageJs', $loadPageJs);

        $this->assign('username', $username);
        $this->assign('schoolgroup', $schoolgroup);
        $this->assign('role', $role);

        $this->assign('page', $page);
        $this->assign('type', $type);
        $this->assign('schoolname', $schoolname);

    	$this->display();
    }
}