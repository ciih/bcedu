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
    	if(!isset($_POST['inputSubmit'])){
		    $this->redirect('/');
		}

		$username = trim($_POST['inputUsername']);
		$pwd = md5($_POST['inputPassword']);

		$userTable = M('user');
		$data = $userTable->where("username='$username' AND password='$pwd'")->select();

		if(!empty($data)) {
			if($data[0]['role'] < 100) {
        session('username',$data[0]['username']);
        session('schoolgroup',$data[0]['schoolgroup']);
				session('role',$data[0]['role']);
				$this->redirect('/home/list/');
			} elseif($data[0]['role'] == 100) { // 对特殊组进行单独处理
				session('username',$data[0]['username']);
				$this->redirect('/home/detail/');
			} else {
				$this->redirect('/');
			}
		} else {
			$this->redirect('/');
		}
	}
}