<?php
namespace Admin\Controller;
use Think\Controller;
class ResultController extends Controller {
    public function index(){

    	if (!session('?username')) {
    		redirectUrl('admin');
    	}
    	
    	$username = session('username');

		$adminCss = getLoadCssStatic('admin_other');
		$adminJs = getLoadJsStatic('admin_other');
		$this->assign('adminCss', $adminCss);
		$this->assign('adminJs', $adminJs);
		$this->assign('username', $username);
		$this->display();
    }
}