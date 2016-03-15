<?php
namespace Home\Controller;
use Think\Controller;
class ValueaddedController extends Controller {
    public function index(){
        // 增值性评价
        $loadCss     = getLoadCssStatic('detail');
        $loadJs      = getLoadJsStatic('detail');
        $loadPageJs  = getLoadPageJsStatic('valueadded');

        $page = strtolower(CONTROLLER_NAME);
        $type = I('type');
        $schoolname = I('schoolname');

        $examTable = M('exam');
        $data = $examTable->where("schooltype='$type'")->order('id desc')->getField('schoolyear', true);
        
        $schoolyear = array_unique($data);

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