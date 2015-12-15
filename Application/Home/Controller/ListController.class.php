<?php
namespace Home\Controller;
use Think\Controller;
class ListController extends Controller {
    public function index(){
    	redirectUrl();
    	$loadCss = getLoadCssStatic('list');
    	$loadJs = getLoadJsStatic('list');
    	$this->assign('loadCss', $loadCss);
    	$this->assign('loadJs', $loadJs);
    	$this->display();
    }
}