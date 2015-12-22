<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
		$adminCss = getLoadCssStatic('admin_index');
		$adminJs = getLoadJsStatic('admin_index');
		$this->assign('adminCss', $adminCss);
		$this->assign('adminJs', $adminJs);
		$this->display();
    }
    public function login(){
    	if(!isset($_POST['inputSubmit'])){
		    $this->redirect('/admin');
		}

		$username = trim($_POST['inputUsername']);
		$pwd = md5($_POST['inputPassword']);

		$userTable = M('user');
		$data = $userTable->where("username='$username' AND password='$pwd' AND role=0")->select();

		if(!empty($data)) {
			session('username',$data[0]['username']);
			$this->redirect('/admin/upload');
		} else {
			$this->redirect('/admin');
		}
    }
}