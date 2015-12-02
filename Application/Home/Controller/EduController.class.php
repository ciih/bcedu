<?php
namespace Home\Controller;
use Think\Controller;
class EduController extends Controller {
    public function index(){
    	$loadCss   = getLoadCssStatic('edu');
    	$loadJs    = getLoadJsStatic('edu');
    	$loadHtml  = getLoadHtmlStatic('edu');
        $page = I('page');
        $type = I('type');
        $school = I('school');
        $grade = I('grade');
        $name = strtolower(CONTROLLER_NAME);
    	$tpl = 'Application/Home/View/Edu/' . $page . '_section.tpl';
    	$this->assign('loadCss', $loadCss);
    	$this->assign('loadJs', $loadJs);
        $this->assign('loadHtml', $loadHtml);
    	$this->assign('name', $name);
        $this->assign('page', $page);
        $this->assign('type', $type);
        $this->assign('school', $school);
        $this->assign('grade', $grade);
    	$this->assign('tpl', $tpl);
    	$this->display();
    }
}