<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){

    	$path = I('server.REMOTE_PORT');
    	var_dump($path);
    	// $this->assign('path', $path);
    	// $this->display();

    }
    public function say(){
        //$this->show('chenhong');
        $name = '我可是跳转过来的哦！<br />';
        $this->assign('name', $name);
        $this->show();
    	$this->urlService();
    }

    private function urlService() {
    	echo '您当前的URL模式为' . C('URL_MODEL');
    	echo '<br />';
    	echo '当前的控制器模式' . U('Home/Index/say');
    }
}