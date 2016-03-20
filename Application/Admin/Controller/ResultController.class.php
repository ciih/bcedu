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
        $foldername = $_GET['foldername'];

        $examInfoObj = new \Admin\Model\ExamInfoData($date, $foldername);
        $examInfoData = $examInfoObj->getExamInfoData();

        $courseObj = new \Admin\Model\CourseData($examInfoData);
        $courseData = $courseObj->getCourseData();

        $examInfoObj->writeExamInfo($courseData);

        $adminCss = getLoadCssStatic('admin_other');
        $adminJs = getLoadJsStatic('admin_other');
        
        $this->assign('adminCss', $adminCss);
        $this->assign('adminJs', $adminJs);

        $this->assign('username', $username);
        $this->assign('date', $date);
        $this->assign('foldername', $foldername);
        $this->assign('course', $courseData);

        $this->display();
    }

    public function createword(){
        if (!session('?username')) {
            redirectUrl('admin');
        }
        
        $username = session('username');

        $date = $_GET['date'];
        $foldername = $_GET['foldername'];
        $course = $_GET['course'];

        $wordObj = new \Admin\Logic\CreateWord($date, $foldername, $course);
        $wordObj->creatWordFile();

        $adminCss = getLoadCssStatic('admin_other');
        $adminJs = getLoadJsStatic('admin_other');
        $this->assign('adminCss', $adminCss);
        $this->assign('adminJs', $adminJs);

        $this->assign('date', $date);
        $this->assign('course', $course);
        $this->assign('examname', $foldername);

        $this->assign('username', $username);

        $this->display();

    }

    public function linkword(){
        if (!session('?username')) {
            redirectUrl('admin');
        }

        $examname = $_GET['examname'];
        $course = $_GET['course'];

        header('Location: /Data/Word/'.$examname.'/'.$course.'.docx');
    }

}