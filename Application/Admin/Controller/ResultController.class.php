<?php
namespace Admin\Controller;
use Think\Controller;

class ResultController extends Controller {
    public function index(){

        if (!session('?username')) {
            redirectUrl('admin');
        }
        
        $username = session('username');

        $date = $_GET['date'];
        $filename = $_GET['filename'];

        $data = new \Admin\Model\ExcelData();

        $baseinfo = $data->getBaseInfo($date, $filename);
        $schoolType = array(
            'title' => $baseinfo['data_field'][0],
            'type' => $baseinfo['type']
        );
        
        $exam = array(
            'title' => $baseinfo['data_field'][1],
            'name' => $baseinfo['exam_name']
        );
        $school = $baseinfo['school'];
        $course = $baseinfo['course'];
        $score = $baseinfo['score'];

        $adminCss = getLoadCssStatic('admin_other');
        $adminJs = getLoadJsStatic('admin_other');
        $this->assign('adminCss', $adminCss);
        $this->assign('adminJs', $adminJs);

        $this->assign('username', $username);
        $this->assign('schoolType', $schoolType);
        $this->assign('exam', $exam);
        $this->assign('school', $school);
        $this->assign('course', $course);
        $this->assign('score', $score);

        $this->display();
    }
}