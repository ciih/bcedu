<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
		$adminCss = getLoadCssStatic('admin');
		$adminJs = getLoadJsStatic('admin');
		$this->assign('adminCss', $adminCss);
		$this->assign('adminJs', $adminJs);
		$this->display();
    }
}