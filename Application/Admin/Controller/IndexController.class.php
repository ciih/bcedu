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
    public function login(){

		$username = $_POST['inputUsername'];
		$pwd = md5($_POST['inputPassword']);

		$userTable = M('user');
		$data = $userTable->where("username='$username' AND password='$pwd' AND role=0")->select();
		if(!empty($data)) {
			echo '欢迎光临';
		}
    }
}