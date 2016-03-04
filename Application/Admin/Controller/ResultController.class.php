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


        $excelObj = new \Admin\Model\ExcelData($date, $foldername);
        $foldernameData = $excelObj->writeExamInfo();

        $courseObj = new \Admin\Model\CourseData();
        $courseData = $courseObj->getCourseData($date, $foldername);

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

        $wordObj = new \Admin\Logic\CreateWord();
        $wordObj->creatWordFile($date, $foldername, $course);

        $adminCss = getLoadCssStatic('admin_other');
        $adminJs = getLoadJsStatic('admin_other');
        $this->assign('adminCss', $adminCss);
        $this->assign('adminJs', $adminJs);

        $this->assign('date', $date);
        $this->assign('course', $course);

        $this->assign('username', $username);

        $this->display();

    }

    public function linkword(){
        if (!session('?username')) {
            redirectUrl('admin');
        }

        $date = $_GET['date'];
        $course = $_GET['course'];

        header('Location: /Word/'.$date.'/'.$course.'.docx');
    }

}