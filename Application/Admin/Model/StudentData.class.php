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
    protected static $courseAll = array();

    /**
     * 课程
     * @var string
     */
    protected static $queryCourse = '';

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

        self::$courseAll = $course;

        for ($i = 0; $i < count($course); $i++) { 
            $courseAnalysisData[$course[$i]] = array(
                'amount'         => $amount[$i],
                'difficulty'     => $difficulty[$i],
                'difficultyTxt'  => $difficultyTxt[$i],
                'distinguish'    => $distinguish[$i],
                'distinguishTxt' => $distinguishTxt[$i],
                'reliability'    => $reliability[$i],
                'reliabilityTxt' => $reliabilityTxt[$i]
            );
        }

        return $courseAnalysisData;

    }

    /**
     * 获取平均分
     */
    private function getAverageData()
    {
        $data = self::openExcel(self::AVERAGE_NAME);

        $averageData = array(); // 平均分对比数据
        $scoreData = array(); // 平均分

        $schoolName = array(); // 学校名称
        $amountScore = array(); // 全区平均分
        $schoolScore = array(); // 学校平均分

        foreach($data->getRowIterator() as $kr => $row){

            $cellIterator = $row->getCellIterator();

            if ($kr > 1) {
                foreach($cellIterator as $kc => $cell){
                    if ($kc == 0) {
                        $schoolName[] = $cell->getValue();
                    }
                    if ($kc > 0) {
                        $scoreData[$kc-1][] =  $cell->getValue();
                    }
                }
            }
        }

        $scoreData = array_slice($scoreData, 0, -2);

        for ($i = 0; $i < count(self::$courseAll); $i++) { 
            array_splice($scoreData, $i + 1, 1);
        }

        for ($i = 0; $i < count(self::$courseAll); $i++) { 
            foreach ($schoolName as $key => $value) {
                if ($key == count($scoreData[$i]) - 1) {
                    $amountScore[self::$courseAll[$i]] = floatval($scoreData[$i][$key]);
                } else {
                    $schoolScore[self::$courseAll[$i]][$schoolName[$key]] = floatval($scoreData[$i][$key]);
                }
            }
        }

        array_splice($schoolName, count($schoolName) - 1, 1);

        $courseAnalysisData = array(
            'schoolName'  => $schoolName,
            'amountScore' => $amountScore,
            'schoolScore' => $schoolScore
        );

        return $averageData;
    }

    /**
     * 获取学生分
     */
    private function getStudentScoreData($courseAnalysis, $averageScore)
    {
        $data = self::openExcel(self::STUDENT_SCORE_NAME);

        $courseAnalysis = $courseAnalysis;
        $averageScore = $averageScore;

        $scoreData = array(); // 学生分数

        $baseScore = array(); // 考试基准分数线

        $studentScore = array(); // 返回学生分数信息

        $num = 0;

        $keys = array();

        $count = array(); // 统计人数
        $cumulativeCount = array(); // 统计累积人数
        $rate = array(); // 所占百分比
        $cumulativeRate = array(); // 累计所占百分比

        foreach($data->getRowIterator() as $kr => $row){

            $cellIterator = $row->getCellIterator();

            foreach($cellIterator as $kc => $cell){
                $scoreData[$num][] = $cell->getValue();
            }

            array_splice($scoreData[$num], 0, 2);
            array_splice($scoreData[$num], -2, 2);

            for ($i = 0; $i < count(self::$courseAll); $i++) { 
                array_splice($scoreData[$num], $i + 2, 1);
            }

            $num++;

        }

        $keys = $scoreData[0];
        
        array_splice($scoreData, 0, 1);
        array_splice($scoreData, -3, 3);

        $rateData = new \Admin\Model\ScoreRateData();
        $scoreRate = $rateData->getScoreRateData();

        $courseBaseData = new \Admin\Model\CourseBaseData();
        $courseBase = $courseBaseData->getCourseBaseData(self::$dateDir, self::$mainDir);
        $totalScore = $courseBase['totalScore'];

        foreach ($totalScore as $key => $value) {
            $baseScore[$key]['优秀'] = $totalScore[$key] * $scoreRate[$key][0];
            $baseScore[$key]['及格'] = $totalScore[$key] * $scoreRate[$key][1];
        }

        foreach (self::$courseAll as $key => $value) {
            $count[self::$courseAll[$key]]['excellentCount'] = 0;
            $count[self::$courseAll[$key]]['passCount'] = 0;
            $count[self::$courseAll[$key]]['failCount'] = 0;
        }

        foreach ($scoreData as $value) {
            for ($i = 1; $i < count($value); $i++) { 
                if($value[$i] >= $baseScore[self::$courseAll[$i-1]]['优秀']) {
                    $count[self::$courseAll[$i-1]]['excellentCount']++;
                } elseif($value[$i] >= $baseScore[self::$courseAll[$i-1]]['及格'] && $value[$i] < $baseScore[self::$courseAll[$i-1]]['优秀']) {
                    $count[self::$courseAll[$i-1]]['passCount']++;
                } else {
                    $count[self::$courseAll[$i-1]]['failCount']++;
                }
            }
        }

        foreach ($count as $key => $value) {
            $cumulativeCount[$key]['excellentCount'] = $count[$key]['excellentCount'];
            $cumulativeCount[$key]['passCount'] = $count[$key]['excellentCount'] + $count[$key]['passCount'];
            $cumulativeCount[$key]['failCount'] = $count[$key]['excellentCount'] + $count[$key]['passCount'] + $count[$key]['failCount'];

            $rate[$key]['excellentCount'] = number_format($count[$key]['excellentCount'] / $courseAnalysis[$key]['amount'] * 100, 2, '.', '');
            $rate[$key]['passCount'] = number_format($count[$key]['passCount'] / $courseAnalysis[$key]['amount'] * 100, 2, '.', '');
            $rate[$key]['failCount'] = number_format($count[$key]['failCount'] / $courseAnalysis[$key]['amount'] * 100, 2, '.', '');

            $cumulativeRate[$key]['excellentCount'] = number_format($cumulativeCount[$key]['excellentCount'] / $courseAnalysis[$key]['amount'] * 100, 2, '.', '');
            $cumulativeRate[$key]['passCount'] = number_format($cumulativeCount[$key]['passCount'] / $courseAnalysis[$key]['amount'] * 100, 2, '.', '');
            $cumulativeRate[$key]['failCount'] = number_format($cumulativeCount[$key]['failCount'] / $courseAnalysis[$key]['amount'] * 100, 2, '.', '');
        }

        foreach (self::$courseAll as $key => $value) {
            $studentScore[$value] = array(
                'baseScore'        => $baseScore[$value],
                'count'             => $count[$value],
                'rate'              => $rate[$value],
                'cumulativeCount'   => $cumulativeCount[$value],
                'cumulativeRate'    => $cumulativeRate[$value],
            );
        }
    }

    /**
     * 获取知识分析
     */
    private function getKnowledgeAnalysis()
    {

        $scoreData = array(); // 分数数据

        $num = 0;

        $filename = $value.'/'.$value.self::COURSE_SCORE_NAME;

        $data = self::openExcel($filename);

        foreach($data->getRowIterator() as $kr => $row){

            $cellIterator = $row->getCellIterator();

            foreach($cellIterator as $kc => $cell){
                $scoreData[$num][] = $cell->getValue();
            }

            array_splice($scoreData[$num], 0, 2);

            for ($i = 0; $i < count(self::$courseAll); $i++) { 
                array_splice($scoreData[$num], $i + 2, 1);
            }

            $num++;
        }

        var_dump($scoreData);
    }

    /*private function getKnowledgeAnalysis()
    {

        set_time_limit(0);

        $scoreData = array(); // 分数数据

        foreach (self::$courseAll as $key => $value) {
            $num = 0;
            $filename = $value.'/'.$value.self::COURSE_SCORE_NAME;
            $data[$key] = self::openExcel($filename);

            foreach($data[$key]->getRowIterator() as $kr => $row){

                $cellIterator = $row->getCellIterator();

                foreach($cellIterator as $kc => $cell){
                    $scoreData[$num][] = $cell->getValue();
                }

                array_splice($scoreData[$num], 0, 2);

                for ($i = 0; $i < count(self::$courseAll); $i++) { 
                    array_splice($scoreData[$num], $i + 2, 1);
                }

                $num++;

            }
        }

        var_dump($scoreData);
    }*/

    /**
     * 获取学校列表
     * @param $data 分数
     */
    public function getStudentData($date, $foldername, $course)
    {

        self::$dateDir     = $date; // 得到日期
        self::$mainDir     = $foldername; // 得到主目录
        self::$queryCourse = $course; // 查询课程

        var_dump(self::$queryCourse);

        $courseAnalysis = self::getCourseAnalysisData();

        /*$courseAnalysis = self::getCourseAnalysisData();
        $averageScore = self::getAverageData();
        $studentScore = self::getStudentScoreData($courseAnalysis, $averageScore);
        $knowledgeAnalysis = self::getKnowledgeAnalysis();


        $data = array(
            'courseAnalysis' => $courseAnalysis,
            'averageScore' => $averageScore,
        );*/

        // var_dump($data['averageScore']);

        return $data;

    }

}

?>