<?php
namespace Admin\Controller;
use Think\Controller;

class UserController extends Controller {

    public function createuser() {

        if (!session('?username')) {
            redirectUrl('admin');
        }
        
        $username = session('username');
        $pagename = strtolower(CONTROLLER_NAME);
        $actionename = strtolower(ACTION_NAME);

        $adminCss = getLoadCssStatic('admin_other');
        $adminJs = getLoadJsStatic('admin_other');
        $this->assign('adminCss', $adminCss);
        $this->assign('adminJs', $adminJs);

        $this->assign('username', $username);
        $this->assign('pagename', $pagename);
        $this->assign('actionename', $actionename);


        $this->display();
    }
    
    public function finduser(){

        if (!session('?username')) {
            redirectUrl('admin');
        }
        
        $username = session('username');
        $pagename = strtolower(CONTROLLER_NAME);
        $actionename = strtolower(ACTION_NAME);

        $adminCss = getLoadCssStatic('admin_other');
        $adminJs = getLoadJsStatic('admin_other');
        $this->assign('adminCss', $adminCss);
        $this->assign('adminJs', $adminJs);

        $this->assign('username', $username);
        $this->assign('pagename', $pagename);
        $this->assign('actionename', $actionename);

        $this->display();
    }
}