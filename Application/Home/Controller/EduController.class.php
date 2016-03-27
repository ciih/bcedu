<?php
namespace Home\Controller;
use Think\Controller;
class EduController extends Controller {
    public function index(){
        if (!session('?username')) {
            redirectUrl('index');
        }
        
        $username = session('username');
        
    	$loadCss   = getLoadCssStatic('edu');
    	$loadJs    = getLoadJsStatic('edu');
    	$loadHtml  = getLoadHtmlStatic('edu');
        $page = I('page');
        $type = I('type');
        $school = I('school');

        $examTable = M('exam');
        
        if($type == 'high') {
            $data = $examTable->where("schoolType='high'")->select();
            $schoolType = '高中';
            $grade = array('高一年级','高二年级','高三年级');
        } elseif($type == 'middle') {
            $data = $examTable->where("schoolType='middle'")->select();
            $schoolType = '初中';
            $grade = array('七年级','八年级','九年级');
        } elseif($type == 'junior') {
            $data = $examTable->where("schoolType='junior'")->select();
            $schoolType = '小学';
            $grade = array('四年级','五年级','六年级');
        }

        $schoolterm = array('第一学期','第二学期');
        $examName = array('期中考试','期末考试','一模考试','二模考试','三模考试');

        var_dump($data);

        $name = strtolower(CONTROLLER_NAME);
    	$tpl = 'Application/Home/View/Edu/' . $page . '_section.tpl';
    	$this->assign('loadCss', $loadCss);
    	$this->assign('loadJs', $loadJs);
        $this->assign('loadHtml', $loadHtml);

        $this->assign('username', $username);

        $this->assign('schoolType', $schoolType);
        $this->assign('schoolYear', $schoolYear);
        $this->assign('schoolterm', $schoolterm);
        $this->assign('grade', $grade);
        $this->assign('examName', $examName);

    	$this->assign('name', $name);
        $this->assign('page', $page);
        $this->assign('type', $type);
        $this->assign('school', $school);
    	$this->assign('tpl', $tpl);

    	$this->display();
    }

    public function search(){
        $data = I('post.');
        $arr = array(
'name'=>$data['name'],
'size'=>$data['time']
);
$this->ajaxReturn (json_encode($arr),'JSON');
    }
}