<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	$loadCss = getLoadCssStatic('home');
    	$loadJs = getLoadJsStatic('home');
    	$this->assign('loadCss', $loadCss);
    	$this->assign('loadJs', $loadJs);
    	$this->display();
    }
	public function login(){
		$username = I('post.inputUsername');
		$password = md5(I('post.inputPassword'));
		//假逻辑，只为做演示
		if($username == 'admin' && $password == md5('admin')) {
			$this->redirect('/Admin/');
		} else {
			echo 'error';
		}
	}
}