<?php
namespace Admin\Controller;
use Think\Controller;
class SchoolController extends Controller {
    public function index(){

    	if (!session('?username')) {
    		redirectUrl('admin');
    	}
    	
    	$username = session('username');

        $junior = array('小学','四年级','j4','五年级','j5','六年级','j6');
        $middle = array('中学','七年级','m7','八年级','m8','九年级','m9');
        $high = array('高中','高一','h1','高二','h2','高三','h3');

        $type = I('type');
        $grade = I('grade');
        $school = I('school');

        if ($type == 'junior') {
            $info = $junior;
        } elseif ($type == 'middle') {
            $info = $middle;
        } elseif ($type == 'high') {
            $info = $high;
        } else {
            $info = '';
        }

		$adminCss = getLoadCssStatic('admin_other');
		$adminJs = getLoadJsStatic('admin_other');
		$this->assign('adminCss', $adminCss);
		$this->assign('adminJs', $adminJs);
        $this->assign('username', $username);
        $this->assign('info', $info);
        $this->assign('type', $type);
		$this->assign('grade', $grade);
        $this->assign('school', $school);
		$this->display();
    }
}