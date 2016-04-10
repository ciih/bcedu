<?php
namespace Admin\Controller;
use Think\Controller;

class ResultController extends Controller {

    const KEY = "__gen_data__";

    private function getSign($json, $case){
        return substr(md5($json."--".$case."--".self::KEY), 16, 8);
    }

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

        $examInfoObj = new \Admin\Model\ExamInfoData($date, $foldername);
        $examInfoData = $examInfoObj->getExamInfoData();

        // 获取双向明细表数据(考核范畴、考核层级)
        $detailTableObj = new \Admin\Model\DetailTableData($examInfoData, $course);
        $detailTableData = $detailTableObj->getDetailTableData();

        $examScopeData = $detailTableData['examScopeTotalScore'];
        $examMoldData = $detailTableData['examMoldTotalScore'];

        // 生成图表
        $phantomPath = 'D:/webserver/phantomjs/bin/phantomjs.exe ';

        $examScopePieStr = base64_encode(json_encode($examScopeData));

        $examMoldPieStr = base64_encode(json_encode($examMoldData));

        $subDir = iconv("utf-8", "gb2312", $foldername);

        $case = "pie1";

        $sign = $this->getSign($examScopePieStr, $case);

        $phantomBaseDir = dirname(dirname(dirname(dirname(__FILE__))))."/Phantom/";

        $workDir = $phantomBaseDir.$subDir."/";
        $dataJsDir = $phantomBaseDir.$foldername."/";

        if(file_exists($workDir)) {
            deldir($workDir);
        }

        $old = umask(0);
        mkdir($workDir, 0777);
        umask($old);

        $baseUrl = "http://chenhong.bcedu.com/index.php";
        $workDir2 = str_replace("\\", '/', $dataJsDir);


        $jsPieTplFile = $phantomBaseDir."/exampie.tpl.js";
        $examscopePieJsFile = $workDir."/examscopepie.js";
        $title = 'examscope';

        $examscopePiePicFile = "examscopepie.png";

        $examscopePieJsFilejs = file_get_contents($jsPieTplFile);
        $examscopePieJsFilejs = str_replace(array(
            "{baseurl}", 
            "{workdir}", 
            "{pic}", 
            "{data}",
            "{case}",
            "{title}",
            "{sign}"
        ), array(
            $baseUrl,
            $workDir2, 
            $examscopePiePicFile,
            rawurlencode($examScopePieStr),
            $case,
            $title,
            $sign   
        ), $examscopePieJsFilejs);

        file_put_contents($examscopePieJsFile, $examscopePieJsFilejs);
        exec($phantomPath.$examscopePieJsFile); 

        // 等待截图完成
        sleep(2);

        $sign = $this->getSign($examMoldPieStr, $case);

        $exammoldPieJsFile = $workDir."/exammoldpie.js";
        $title = 'exammold';

        $exammoldPiePicFile = "exammoldpie.png";

        $exammoldPieJsFilejs = file_get_contents($jsPieTplFile);

        $exammoldPieJsFilejs = str_replace(array(
            "{baseurl}", 
            "{workdir}", 
            "{pic}", 
            "{data}",
            "{case}",
            "{title}",
            "{sign}"
        ), array(
            $baseUrl,
            $workDir2, 
            $exammoldPiePicFile,
            rawurlencode($examMoldPieStr),
            $case,
            $title,
            $sign   
        ), $exammoldPieJsFilejs);

        file_put_contents($exammoldPieJsFile, $exammoldPieJsFilejs);
        exec($phantomPath.$exammoldPieJsFile); 

        // 等待截图完成
        sleep(2);

        // 获取学校列表
        $schoolInfoObj = new \Admin\Model\SchoolInfoData();
        $schoolInfoData = $schoolInfoObj->getSchoolData($examInfoData['schoolType']);

        // 获取当前科目得分率
        $baseScoreRateObj = new \Admin\Model\BaseScoreRateData();
        $baseScoreRateData = $baseScoreRateObj->getBaseScoreRateData($course);

        // 获取所有科目列表
        $courseObj = new \Admin\Model\CourseData($examInfoData);
        $courseListData = $courseObj->getCourseData();

        // 获取成绩数据
        $examDataObj = new \Admin\Model\ExcelData($examInfoData, $schoolInfoData, $baseScoreRateData, $courseListData, $detailTableData, $course);

        // 获取课程分析数据(难度、区分度、信度)
        $courseAnalysisData = $examDataObj->getCourseAnalysisData();

        // 获取综合指标数据(学校人数、平均分、最高分、最低分)
        $comprehensiveIndicatorsData = $examDataObj->getComprehensiveIndicatorsData();

        // 获取小题分数据(考核范畴、考核层级各学生分数；学生分数列表；学生所属学校列表；全区、各学校人数统计；人数百分比、累计百分比)
        $studentScoreData = $examDataObj->getStudentScoreData();
        // 获取课程分数统计数据(全区、各学校考核范畴各项目分数；全区、各学校考核层级各项目分数)
        $scoreStatisticsData = $examDataObj->getScoreStatisticsData();
        // 获取客观题统计
        $choiceQuestionsAnalysisData = $examDataObj->getChoiceQuestionsAnalysisData();

        $examScopeBarData = $scoreStatisticsData['examScopeTotalRate'];
        $examMoldBarData = $scoreStatisticsData['examMoldTotalRate'];
        $choiceNum = 0;
        foreach ($choiceQuestionsAnalysisData as $item) {
            foreach ($item as $key => $value) {
                if($key == '难度') {
                    $choiceQuestionsAnalysisBarData[$choiceNum][] = $value;
                } elseif($key == '区分度') {
                    $choiceQuestionsAnalysisBarData[$choiceNum][] = $value;
                }
            }
            $choiceNum++;
        }

        $examScopeBarStr = base64_encode(json_encode($examScopeBarData));
        $examMoldBarStr = base64_encode(json_encode($examMoldBarData));
        $choiceQuestionsAnalysisBarStr = base64_encode(json_encode($choiceQuestionsAnalysisBarData));

        $case = "bar1";

        $sign = $this->getSign($examScopeBarStr, $case);

        $title = 'examscope';

        $examscopeBarPicFile = "examscopebar.png";

        $jsBarTplFile = $phantomBaseDir."/exambar.tpl.js";
        $examscopeBarJsFile = $workDir."/examscopebar.js";
        $examscopeBarJsFilejs = file_get_contents($jsBarTplFile);

        $examscopeBarJsFilejs = str_replace(array(
            "{baseurl}", 
            "{workdir}", 
            "{pic}", 
            "{data}",
            "{case}",
            "{title}",
            "{sign}"
        ), array(
            $baseUrl,
            $workDir2, 
            $examscopeBarPicFile,
            rawurlencode($examScopeBarStr),
            $case,
            $title,
            $sign   
        ), $examscopeBarJsFilejs);

        file_put_contents($examscopeBarJsFile, $examscopeBarJsFilejs);
        exec($phantomPath.$examscopeBarJsFile); 

        // 等待截图完成
        sleep(2);


        $sign = $this->getSign($examMoldBarStr, $case);

        $title = 'exammold';

        $exammoldBarPicFile = "exammoldbar.png";

        $exammoldBarJsFile = $workDir."/exammoldbar.js";
        $exammoldBarJsFilejs = file_get_contents($jsBarTplFile);

        $exammoldBarJsFilejs = str_replace(array(
            "{baseurl}", 
            "{workdir}", 
            "{pic}", 
            "{data}",
            "{case}",
            "{title}",
            "{sign}"
        ), array(
            $baseUrl,
            $workDir2, 
            $exammoldBarPicFile,
            rawurlencode($examMoldBarStr),
            $case,
            $title,
            $sign   
        ), $exammoldBarJsFilejs);

        file_put_contents($exammoldBarJsFile, $exammoldBarJsFilejs);
        exec($phantomPath.$exammoldBarJsFile); 

        // 等待截图完成
        sleep(2);







        $sign = $this->getSign($choiceQuestionsAnalysisBarStr, $case);

        $title = 'choice';

        $choiceBarPicFile = "choicebar.png";

        $jsBarTplFile = $phantomBaseDir."/choicebar.tpl.js";
        $choiceBarJsFile = $workDir."/choicebar.js";
        $choiceBarJsFilejs = file_get_contents($jsBarTplFile);

        $choiceBarJsFilejs = str_replace(array(
            "{baseurl}", 
            "{workdir}", 
            "{pic}", 
            "{data}",
            "{case}",
            "{title}",
            "{sign}"
        ), array(
            $baseUrl,
            $workDir2, 
            $choiceBarPicFile,
            rawurlencode($choiceQuestionsAnalysisBarStr),
            $case,
            $title,
            $sign   
        ), $choiceBarJsFilejs);

        file_put_contents($choiceBarJsFile, $choiceBarJsFilejs);
        exec($phantomPath.$choiceBarJsFile); 

        // 等待截图完成
        sleep(2);

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

    public function exampie(){
        $data = rawurldecode(I("data")); 
        $sign = I("sign");
        $case = I("case");
        $title = I("title");

        switch ($title) {
            case 'examscope':
                $this->assign("title", '各知识范畴分值分布');
                break;
            case 'exammold':
                $this->assign("title", '各能力层级分值分布');
                break;
        }

        if ($this->getSign($data, $case) == $sign){  
            switch ($case){
                case "pie1":
                    $data = json_decode(base64_decode($data), true);

                    $this->assign("data", $data);
                    $this->display();
                    break;
            }
        }           
    }

    public function exambar(){
        $data = rawurldecode(I("data")); 
        $sign = I("sign");
        $case = I("case");
        $title = I("title");

        switch ($title) {
            case 'examscope':
                $this->assign("title", '各知识范畴全区及不同水平组得分率比较图');
                $this->assign("chartstyleheight", '800px');
                break;
            case 'exammold':
                $this->assign("title", '各能力层级全区及不同水平组得分率比较图');
                $this->assign("chartstyleheight", '400px');
                break;
        }

        if ($this->getSign($data, $case) == $sign){  
            switch ($case){
                case "bar1":
                    $data = json_decode(base64_decode($data), true);

                    $this->assign("data", $data);
                    $this->display();
                    break;
            }
        }           
    }

    public function choicebar(){
        $data = rawurldecode(I("data")); 
        $sign = I("sign");
        $case = I("case");
        $title = I("title");

        switch ($title) {
            case 'choice':
                $this->assign("title", '客观题分析图');
                break;
        }

        if ($this->getSign($data, $case) == $sign){  
            switch ($case){
                case "bar1":
                    $data = json_decode(base64_decode($data), true);

                    $this->assign("data", $data);
                    $this->display();
                    break;
            }
        }           
    }

}