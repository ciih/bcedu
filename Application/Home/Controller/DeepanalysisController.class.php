<?php
namespace Home\Controller;
use Think\Controller;
class DeepanalysisController extends Controller {
    public function index(){
        if (!session('?username')) {
            redirectUrl('index');
        }
        
        $username = session('username');
        $schoolgroup = session('schoolgroup');
        $role = session('role');
        
        // 深度分析档案
        $loadCss     = getLoadCssStatic('detail');
        $loadJs      = getLoadJsStatic('detail');
        $loadPageJs  = getLoadPageJsStatic('deepanalysis');

        $page = strtolower(CONTROLLER_NAME);
        $type = I('type');
        $schoolname = I('schoolname');

        $examTable = M('exam');
        $data = $examTable->where("schooltype='$type'")->order('id desc')->getField('schoolyear', true);
        
        $schoolyear = array_unique($data);

        $this->assign('loadCss', $loadCss);
        $this->assign('loadJs', $loadJs);
        $this->assign('loadPageJs', $loadPageJs);

        $this->assign('username', $username);
        $this->assign('schoolgroup', $schoolgroup);
        $this->assign('role', $role);

        $this->assign('page', $page);
        $this->assign('type', $type);
        $this->assign('schoolname', $schoolname);
        
        $this->assign('schoolyear', $schoolyear);

        $this->display();
    }
}