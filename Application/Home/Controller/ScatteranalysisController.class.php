<?php
namespace Home\Controller;
use Think\Controller;
class ScatteranalysisController extends Controller {
    public function index(){
        if (!session('?username')) {
            redirectUrl('index');
        }
        
        $username = session('username');
        
        // 散点图
        $loadCss     = getLoadCssStatic('detail');
        $loadJs      = getLoadJsStatic('detail');
        $loadPageJs  = getLoadPageJsStatic('scatteranalysis');

        $this->assign('username', $username);

        $page = strtolower(CONTROLLER_NAME);
        $type = I('type');
        $schoolname = I('schoolname');

        $this->assign('loadCss', $loadCss);
        $this->assign('loadJs', $loadJs);
        $this->assign('loadPageJs', $loadPageJs);

        $this->assign('page', $page);
        $this->assign('type', $type);
        $this->assign('schoolname', $schoolname);
        
        $this->assign('schoolyear', $schoolyear);

        $this->display();
    }
}