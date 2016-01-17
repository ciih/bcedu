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

        $courseData = new \Admin\Model\CourseData();
        $course = $courseData->getCourseData($date, $foldername);


        $adminCss = getLoadCssStatic('admin_other');
        $adminJs = getLoadJsStatic('admin_other');
        $this->assign('adminCss', $adminCss);
        $this->assign('adminJs', $adminJs);

        $this->assign('username', $username);
        $this->assign('date', $date);
        $this->assign('foldername', $foldername);
        $this->assign('course', $course);

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

        $studentData = new \Admin\Model\StudentData();
        $courseBase = $studentData->getStudentData($date, $foldername, $course);


        // var_dump(session('baseinfo'));







        $adminCss = getLoadCssStatic('admin_other');
        $adminJs = getLoadJsStatic('admin_other');
        $this->assign('adminCss', $adminCss);
        $this->assign('adminJs', $adminJs);


        $this->assign('username', $username);

        $this->display();

    }

    public function linkword(){
        if (!session('?username')) {
            redirectUrl('admin');
        }

        // var_dump(session('baseinfo'));
        
        vendor("PHPWord.PHPWord");
       
        $PHPWord = new \PHPWord();
        $wordBaseDir = dirname(dirname(dirname(dirname(__FILE__))))."/Word/";
        $wordTemplateDir = $wordBaseDir."/Template/";
        $document = $PHPWord->loadTemplate($wordBaseDir.'high.docx');



        $document->setValue('valuetitle', $baseinfo['keys'][0]);


        $document->save($wordBaseDir.'chenhong.docx');
        header("Content-Disposition: attachment; filename='chenhong.docx'");
        echo file_get_contents($wordBaseDir.'chenhong.docx');
        unlink($wordBaseDir.'chenhong.docx');  // remove temp file      
        @rmdir($workDir);

        $this->display();

    }

    const KEY = "__gen_data__";

    private function getSign($json, $case){
        return substr(md5($json."--".$case."--".self::KEY), 16, 8);
    }

}