<?php

/**
 * 获取学校列表
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class StudentData {
   
    /**
     * Excel表目录
     * @var string
     */
    const EXCEL_DIR = '/Data';

    /**
     * 学科表名
     * @var string
     */
    const COURSE_NAME = '学科分析';

    /**
     * 平均分对比表名
     * @var string
     */
    const AVERAGE_NAME = '平均分对比';

    /**
     * 学生成绩表名
     * @var string
     */
    const STUDENT_SCORE_NAME = '学生题型分析';

    /**
     * 单科成绩分数表名(未加科目)
     * @var string
     */
    const COURSE_SCORE_NAME = '_小题分';

    /**
     * 日期目录
     * @var string
     */
    protected static $dateDir = '';

    /**
     * 主目录
     * @var string
     */
    protected static $mainDir = '';

    /**
     * 全区目录
     * @var string
     */
    protected static $totalDir = '全区报表';

    /**
     * 课程
     * @var string
     */
    protected static $queryCourse = '';

    /**
     * 学校类型
     * @var string
     */
    protected static $schoolType = '';

    /**
     * 学科总数
     * @var string
     */
    protected static $courseAmount = 0;

    /**
     * 打开excel表
     * @return string $objWorksheet 返回相应excel文件的工作薄
     */
    private function openExcel($filename)
    {
        vendor("PHPExcel.PHPExcel.IOFactory");

        $excelRoot = dirname(dirname(dirname(dirname(__FILE__))));

        $dateDir = self::$dateDir;
        $mainDir = iconv("utf-8", "gb2312", self::$mainDir);
        $totalDir = iconv("utf-8", "gb2312", self::$totalDir);
        $filename = iconv("utf-8", "gb2312", $filename);

        $filePath = $excelRoot.self::EXCEL_DIR.'/'.$dateDir.'/'.$mainDir.'/'.$totalDir.'/'.$filename.'.xls';

        $objPHPExcel = \PHPExcel_IOFactory::load($filePath);
        $objWorksheet = $objPHPExcel->getSheet(0);

        return $objWorksheet;
    }

    /**
     * 获取学科分析
     */
    private function getCourseAnalysisData()
    {
        $data = self::openExcel(self::COURSE_NAME);

        $courseAnalysisData = array(); // 学科分析数据

        $course = array(); // 学科项目
        $amount = array(); // 参考人数

        $difficulty = array(); // 全卷难度
        $distinguish = array(); // 全卷区分度
        $reliability = array(); // 全卷信度

        $difficultyTxt = array(); // 全卷难度评级
        $distinguishTxt = array(); // 全卷区分度评级
        $reliabilityTxt = array(); // 全卷信度评级

        foreach($data->getRowIterator() as $kr => $row){

            $cellIterator = $row->getCellIterator();
            // to do
            // 可以先获得字段所在的位置，以备出现3卷或更多
            if ($kr > 1) {
                foreach($cellIterator as $kc => $cell){
                    if ($kc == 0) {
                        $course[] = $cell->getValue();
                    }
                    if (($kc == 1)) {
                        $amount[] = $cell->getValue();
                    }
                    if (($kc == 4)) {
                        $difficulty[] = $cell->getValue();
                        if ($cell->getValue() >= 0.9) {
                            $difficultyTxt[] = '容易';
                        } elseif ($cell->getValue() >= 0.7 && $cell->getValue() < 0.9) {
                            $difficultyTxt[] = '较易';
                        } elseif ($cell->getValue() >= 0.4 && $cell->getValue() < 0.7) {
                            $difficultyTxt[] = '中等';
                        } elseif ($cell->getValue() < 0.4) {
                            $difficultyTxt[] = '偏难';
                        }
                    }
                    if (($kc == 7)) {
                        $distinguish[] = $cell->getValue();
                        if ($cell->getValue() >= 0.4) {
                            $distinguishTxt[] = '较高';
                        } elseif ($cell->getValue() >= 0.3 && $cell->getValue() < 0.4) {
                            $distinguishTxt[] = '中等';
                        } elseif ($cell->getValue() >= 0.2 && $cell->getValue() < 0.3) {
                            $distinguishTxt[] = '一般';
                        } elseif ($cell->getValue() < 0.2) {
                            $distinguishTxt[] = '较低';
                        }
                    }
                    if (($kc == 10)) {
                        $reliability[] = $cell->getValue();
                        if ($cell->getValue() >= 0.9) {
                            $reliabilityTxt[] = '优秀';
                        } elseif ($cell->getValue() >= 0.7 && $cell->getValue() < 0.9) {
                            $reliabilityTxt[] = '较好';
                        } elseif ($cell->getValue() < 0.7) {
                            $reliabilityTxt[] = '一般';
                        }
                    }
                }
            }
        }

        for ($i = 0; $i < count($course); $i++) {
            if ($course[$i] == self::$queryCourse) {
                $courseAnalysisData = array(
                    'course'         => $course[$i],
                    'amount'         => $amount[$i],
                    'difficulty'     => $difficulty[$i],
                    'difficultyTxt'  => $difficultyTxt[$i],
                    'distinguish'    => $distinguish[$i],
                    'distinguishTxt' => $distinguishTxt[$i],
                    'reliability'    => $reliability[$i],
                    'reliabilityTxt' => $reliabilityTxt[$i]
                );

                break;
            }
        }

        return $courseAnalysisData;

    }

    /**
     * 获取平均分
     */
    private function getAverageData($course)
    {
        $data = self::openExcel(self::AVERAGE_NAME);

        $averageData = array(); // 平均分对比数据
        $scoreData = array(); // 平均分
        $keys = array(); // 平均分字段名

        $schoolName = array(); // 学校名称
        $amountScore = 0; // 全区平均分
        $schoolScore = array(); // 学校平均分

        $courseName = $course . '均分';

        foreach($data->getRowIterator() as $kr => $row){

            $cellIterator = $row->getCellIterator();

            if($kr == 1) {
                foreach($cellIterator as $kc => $cell){
                    $keys[] = $cell->getValue();
                }
            }

            if ($kr > 1) {
                foreach($cellIterator as $kd => $cell){
                    if ($kd == 0) {
                        $schoolName[] = $cell->getValue();
                    }
                    if ($kd > 0) {
                        $scoreData[$kd-1][] =  $cell->getValue();
                    }
                }
            }
        }

        $scoreData = array_slice($scoreData, 0, -2);
        $keys = array_slice($keys, 0, -3);

        for ($i = 0; $i < self::$courseAmount; $i++) { 
            array_splice($scoreData, $i + 1, 1);
            array_splice($keys, $i, 1);
        }

        for ($i = 0; $i < self::$courseAmount; $i++) {
            if ($courseName == $keys[$i]) {
                foreach ($schoolName as $key => $value) {
                    if ($key == count($scoreData[$i]) - 1) {
                        $amountScore = floatval($scoreData[$i][$key]);
                    } else {
                        $schoolScore[$schoolName[$key]] = floatval($scoreData[$i][$key]);
                    }
                }
            }
        }

        array_splice($schoolName, count($schoolName) - 1, 1);

        $averageData = array(
            'course'      => $course,
            'schoolName'  => $schoolName,
            'amountScore' => $amountScore,
            'schoolScore' => $schoolScore
        );

        return $averageData;
    }

    /**
     * 获取学生分
     */
    private function getStudentScoreData($courseAnalysis, $scoreRate, $courseBaseData)
    {
        $data = self::openExcel(self::STUDENT_SCORE_NAME);

        $courseAnalysis = $courseAnalysis;
        $averageScore = $averageScore;

        $scoreData = array(); // 学生分数

        $baseScore = array(); // 考试基准分数线

        $studentScore = array(); // 返回学生分数信息

        $num = 0;
        $scoreRow = 0;
        $courseName = self::$queryCourse . '分数';

        $keys = array();

        $count = array(); // 统计人数
        $cumulativeCount = array(); // 统计累积人数
        $rate = array(); // 所占百分比
        $cumulativeRate = array(); // 累计所占百分比

        foreach($data->getRowIterator() as $kr => $row){

            $cellIterator = $row->getCellIterator();

            if($kr == 1) {
                foreach($cellIterator as $kc => $cell){
                    if($courseName == $cell->getValue()) {
                        $scoreRow = $kc;
                    }
                }
            }

            if($kr > 1) {

                foreach($cellIterator as $kc => $cell){
                    if($kc == 2 || $kc == $scoreRow) {
                        $scoreData[$num][] = $cell->getValue();
                    }
                }

                $num++;
            }
        }

        array_splice($scoreData, -3, 3);

        $totalScore = $courseBaseData['totalScore'];

        $baseScore['excellentScore'] = $totalScore * $scoreRate[0];
        $baseScore['passScore'] = $totalScore * $scoreRate[1];

        $count['excellentCount'] = 0;
        $count['passCount'] = 0;
        $count['failCount'] = 0;

        for ($i = 0; $i < count($scoreData); $i++) { 
            if($scoreData[$i][1] >= $baseScore['excellentScore']) {
                $count['excellentCount']++;
            } elseif($scoreData[$i][1] >= $baseScore['passScore'] && $scoreData[$i][1] < $baseScore['excellentScore']) {
                $count['passCount']++;
            } else {
                $count['failCount']++;
            }
        }

        $cumulativeCount['excellentCount'] = $count['excellentCount'];
        $cumulativeCount['passCount'] = $count['excellentCount'] + $count['passCount'];
        $cumulativeCount['failCount'] = $count['excellentCount'] + $count['passCount'] + $count['failCount'];

        $rate['excellentRate'] = number_format($count['excellentCount'] / $courseAnalysis['amount'] * 100, 2, '.', '');
        $rate['passRate'] = number_format($count['passCount'] / $courseAnalysis['amount'] * 100, 2, '.', '');
        $rate['failRate'] = number_format($count['failCount'] / $courseAnalysis['amount'] * 100, 2, '.', '');

        $cumulativeRate['excellentRate'] = number_format($cumulativeCount['excellentCount'] / $courseAnalysis['amount'] * 100, 2, '.', '');
        $cumulativeRate['passRate'] = number_format($cumulativeCount['passCount'] / $courseAnalysis['amount'] * 100, 2, '.', '');
        $cumulativeRate['failRate'] = number_format($cumulativeCount['failCount'] / $courseAnalysis['amount'] * 100, 2, '.', '');

        $studentScore = array(
            'baseScore'       => $baseScore,
            'count'           => $count,
            'rate'            => $rate,
            'cumulativeCount' => $cumulativeCount,
            'cumulativeRate'  => $cumulativeRate,
        );

        return $studentScore;
    }

    /**
     * 获取知识分析
     */
    private function getKnowledgeAnalysis($schoolData, $scoreRate, $courseBaseData)
    {

        $scoreData = array(); // 分数数据

        $num = 0;

        $filename = self::$queryCourse.'/'.self::$queryCourse.self::COURSE_SCORE_NAME;

        $data = self::openExcel($filename);

        $keys = array();

        $totalScore = $courseBaseData['totalScore'];

        $baseScore['excellentScore'] = $totalScore * $scoreRate[0];
        $baseScore['passScore'] = $totalScore * $scoreRate[1];

        $examScoreData = array(); // 考试类型分数

        foreach($data->getRowIterator() as $kr => $row){

            $cellIterator = $row->getCellIterator();

            if ($kr == 1){
                foreach($cellIterator as $kc => $cell){
                    $keys[] = $cell->getValue();
                }
            }
            else {
                foreach($cellIterator as $kc => $cell){
                    $scoreData[$keys[$kc]][] = $cell->getValue();
                }       
            }
        }

        switch (self::$schoolType) {
            case 'junior' :
                
                break;
            case 'middle' :

                $examScoreData['totalCount'] = 0;

                $examScoreData['excellentCount'] = 0;
                $examScoreData['passCount'] = 0;
                $examScoreData['failCount'] = 0;

                foreach($schoolData['全区学校'] as $schoolName){

                    $examScoreData[$schoolName]['totalCount'] = 0;

                    $examScoreData[$schoolName]['excellentCount'] = 0;
                    $examScoreData[$schoolName]['passCount'] = 0;
                    $examScoreData[$schoolName]['failCount'] = 0;
                }

                foreach($scoreData['全卷'] as $key => $score){

                    $examScoreData['totalCount']++;
                    $examScoreData[$scoreData['学校'][$key]]['totalCount']++;

                    if($scoreData['全卷'][$key] >= $baseScore['excellentScore']) {

                        $examScoreData['excellentCount']++;
                        $examScoreData[$scoreData['学校'][$key]]['excellentCount']++;
                    
                    } elseif($scoreData['全卷'][$key] >= $baseScore['passScore'] && $scoreData['全卷'][$key] < $baseScore['excellentScore']) {

                        $examScoreData['passCount']++;
                        $examScoreData[$scoreData['学校'][$key]]['passCount']++;

                    } else {

                        $examScoreData['failCount']++;
                        $examScoreData[$scoreData['学校'][$key]]['failCount']++;

                    }

                }

                foreach($courseBaseData['examName'] as $itemName){

                    $examScoreData[$itemName]['totalScore'] = 0;

                    $examScoreData[$itemName]['excellentScore'] = 0;
                    $examScoreData[$itemName]['passScore'] = 0;
                    $examScoreData[$itemName]['failScore'] = 0;

                    foreach($schoolData['全区学校'] as $schoolName){

                        $examScoreData[$itemName][$schoolName]['totalScore'] = 0;

                        $examScoreData[$itemName][$schoolName]['excellentScore'] = 0;
                        $examScoreData[$itemName][$schoolName]['passScore'] = 0;
                        $examScoreData[$itemName][$schoolName]['failScore'] = 0;
                    }

                    foreach($courseBaseData['examNumber'][$itemName] as $itemNum){

                        foreach($scoreData[$itemNum] as $key => $value){

                            $examScoreData[$itemName]['totalScore'] = $examScoreData[$itemName]['totalScore'] + $value;
                            $examScoreData[$itemName][$scoreData['学校'][$key]]['totalScore'] = $examScoreData[$itemName][$scoreData['学校'][$key]]['totalScore'] + $value;

                            if($scoreData['全卷'][$key] >= $baseScore['excellentScore']) {

                                $examScoreData[$itemName]['excellentScore'] = $examScoreData[$itemName]['excellentScore'] + $value;
                                $examScoreData[$itemName][$scoreData['学校'][$key]]['excellentScore'] = $examScoreData[$itemName][$scoreData['学校'][$key]]['excellentScore'] + $value;
                            
                            } elseif($scoreData['全卷'][$key] >= $baseScore['passScore'] && $scoreData['全卷'][$key] < $baseScore['excellentScore']) {

                                $examScoreData[$itemName]['passScore'] = $examScoreData[$itemName]['passScore'] + $value;
                                $examScoreData[$itemName][$scoreData['学校'][$key]]['passScore'] = $examScoreData[$itemName][$scoreData['学校'][$key]]['passScore'] + $value;

                            } else {

                                $examScoreData[$itemName]['failScore'] = $examScoreData[$itemName]['failScore'] + $value;
                                $examScoreData[$itemName][$scoreData['学校'][$key]]['failScore'] = $examScoreData[$itemName][$scoreData['学校'][$key]]['failScore'] + $value;

                            }

                        }
                    }

                }

                foreach($courseBaseData['typeName'] as $itemName){

                    $examScoreData[$itemName]['totalScore'] = 0;

                    $examScoreData[$itemName]['excellentScore'] = 0;
                    $examScoreData[$itemName]['passScore'] = 0;
                    $examScoreData[$itemName]['failScore'] = 0;

                    foreach($schoolData['全区学校'] as $schoolName){

                        $examScoreData[$itemName][$schoolName]['totalScore'] = 0;

                        $examScoreData[$itemName][$schoolName]['excellentScore'] = 0;
                        $examScoreData[$itemName][$schoolName]['passScore'] = 0;
                        $examScoreData[$itemName][$schoolName]['failScore'] = 0;
                    }

                    foreach($courseBaseData['typeNumber'][$itemName] as $itemNum){

                        foreach($scoreData[$itemNum] as $key => $value){

                            $examScoreData[$itemName]['totalScore'] = $examScoreData[$itemName]['totalScore'] + $value;
                            $examScoreData[$itemName][$scoreData['学校'][$key]]['totalScore'] = $examScoreData[$itemName][$scoreData['学校'][$key]]['totalScore'] + $value;

                            if($scoreData['全卷'][$key] >= $baseScore['excellentScore']) {

                                $examScoreData[$itemName]['excellentScore'] = $examScoreData[$itemName]['excellentScore'] + $value;
                                $examScoreData[$itemName][$scoreData['学校'][$key]]['excellentScore'] = $examScoreData[$itemName][$scoreData['学校'][$key]]['excellentScore'] + $value;
                            
                            } elseif($scoreData['全卷'][$key] >= $baseScore['passScore'] && $scoreData['全卷'][$key] < $baseScore['excellentScore']) {

                                $examScoreData[$itemName]['passScore'] = $examScoreData[$itemName]['passScore'] + $value;
                                $examScoreData[$itemName][$scoreData['学校'][$key]]['passScore'] = $examScoreData[$itemName][$scoreData['学校'][$key]]['passScore'] + $value;

                            } else {

                                $examScoreData[$itemName]['failScore'] = $examScoreData[$itemName]['failScore'] + $value;
                                $examScoreData[$itemName][$scoreData['学校'][$key]]['failScore'] = $examScoreData[$itemName][$scoreData['学校'][$key]]['failScore'] + $value;

                            }

                        }
                    }

                }
                
                break;
            case 'high' :
                
                break;
        }

        var_dump($examScoreData);
    }

    /**
     * 获取学校列表
     * @param $data 分数
     */
    public function getStudentData($date, $foldername, $course)
    {

        self::$dateDir     = $date; // 得到日期
        self::$mainDir     = $foldername; // 得到主目录
        self::$queryCourse = $course; // 查询课程
        self::$schoolType  = 'middle'; // 学校类型

        $schoolObj = new \Admin\Model\SchoolData();
        $schoolData = $schoolObj->getSchoolData(self::$schoolType);

        $courseObj = new \Admin\Model\CourseData();
        $courseData = $courseObj->getCourseData($date, $foldername);

        self::$courseAmount = count($courseData);

        $rateObj = new \Admin\Model\ScoreRateData();
        $scoreRate = $rateObj->getScoreRateData(self::$queryCourse);

        $courseBaseObj = new \Admin\Model\CourseBaseData();
        $courseBaseData = $courseBaseObj->getCourseBaseData($date, $foldername, $course);

        $courseAnalysis = self::getCourseAnalysisData();

        $averageScore = self::getAverageData();

        // $studentScore = self::getStudentScoreData($courseAnalysis, $scoreRate, $courseBaseData);

        $knowledgeAnalysis = self::getKnowledgeAnalysis($schoolData, $scoreRate, $courseBaseData);

        // var_dump($courseBaseData);

        /*


        $data = array(
            'courseAnalysis' => $courseAnalysis,
            'averageScore' => $averageScore,
        );*/

        // var_dump($data['averageScore']);

        return $data;

    }

}

?>