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
     * 选择题分析表名(未加科目)
     * @var string
     */
    const CHOICE_QUESTIONS_NAME = '_小题分析';

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
    private function getAverageData()
    {
        $data = self::openExcel(self::AVERAGE_NAME);

        $averageData = array(); // 平均分对比数据
        $scoreData = array(); // 平均分
        $keys = array(); // 平均分字段名

        $schoolName = array(); // 学校名称
        $amountAverageScore = 0; // 全区平均分
        $schoolAverageScore = array(); // 学校平均分

        $courseName = self::$queryCourse . '均分';

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
                        $amountAverageScore = floatval($scoreData[$i][$key]);
                    } else {
                        $schoolAverageScore[$schoolName[$key]] = floatval($scoreData[$i][$key]);
                    }
                }
            }
        }

        array_splice($schoolName, count($schoolName) - 1, 1);

        $averageData = array(
            'course'      => self::$queryCourse,
            'schoolName'  => $schoolName, // 学校列表
            'amountAverageScore' => $amountAverageScore, // 全区平均分
            'schoolAverageScore' => $schoolAverageScore // 各学校平均分
        );

        return $averageData;
    }

    /**
     * 获取学生人数百分比
     */
    private function getStudentCountRateData($courseAnalysis, $scoreRate, $detailTableData)
    {
        $data = self::openExcel(self::STUDENT_SCORE_NAME);

        $scoreData = array(); // 学生分数

        $baseScore = array(); // 考试基准分数线

        $studentCountRateData = array(); // 返回学生分数信息

        $num = 0; // 数组下标
        $scoreRow = 0; // 分数所在列
        $courseName = self::$queryCourse . '分数'; // 分数名称

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

        $totalScore = $detailTableData['totalScore'];

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

        $studentCountRateData = array(
            'baseScore'       => $baseScore, // 优秀分数、及格分数
            'count'           => $count, // 全区优秀人数、及格人数、未及格人数
            'rate'            => $rate, // 全区优秀率、及格率、未及格率
            'cumulativeCount' => $cumulativeCount, // 全区累计优秀人数、累计及格人数、未及格人数
            'cumulativeRate'  => $cumulativeRate, // 全区累计优秀率、累计及格率、未及格率
        );

        return $studentCountRateData;
    }

    /**
     * 获取分数统计
     */
    private function getScoreStatisticsData($schoolData, $scoreRate, $detailTableData)
    {

        $scoreData = array(); // 分数数据

        $num = 0;

        $filename = self::$queryCourse.'/'.self::$queryCourse.self::COURSE_SCORE_NAME;

        $data = self::openExcel($filename);

        $keys = array();

        $totalScore = $detailTableData['totalScore'];

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

                break;
            case 'high' :
                
                $examScoreData['totalCount'] = 0;

                $examScoreData['excellentCount'] = 0;
                $examScoreData['passCount'] = 0;
                $examScoreData['failCount'] = 0;

                foreach($schoolData['全区学校'] as $schoolName){

                    $examScoreData['schoolCount'][$schoolName]['totalCount'] = 0;

                    $examScoreData['schoolCount'][$schoolName]['excellentCount'] = 0;
                    $examScoreData['schoolCount'][$schoolName]['passCount'] = 0;
                    $examScoreData['schoolCount'][$schoolName]['failCount'] = 0;
                }

                foreach($scoreData['全卷'] as $key => $score){

                    $examScoreData['totalCount']++;
                    $examScoreData['schoolCount'][$scoreData['学校'][$key]]['totalCount']++;

                    if($scoreData['全卷'][$key] >= $baseScore['excellentScore']) {

                        $examScoreData['excellentCount']++;
                        $examScoreData['schoolCount'][$scoreData['学校'][$key]]['excellentCount']++;
                    
                    } elseif($scoreData['全卷'][$key] >= $baseScore['passScore'] && $scoreData['全卷'][$key] < $baseScore['excellentScore']) {

                        $examScoreData['passCount']++;
                        $examScoreData['schoolCount'][$scoreData['学校'][$key]]['passCount']++;

                    } else {

                        $examScoreData['failCount']++;
                        $examScoreData['schoolCount'][$scoreData['学校'][$key]]['failCount']++;

                    }

                }

                foreach($detailTableData['examName'] as $itemName){

                    $examScoreData['exam'][$itemName]['total']['totalScore'] = 0;

                    $examScoreData['exam'][$itemName]['total']['excellentScore'] = 0;
                    $examScoreData['exam'][$itemName]['total']['passScore'] = 0;
                    $examScoreData['exam'][$itemName]['total']['failScore'] = 0;

                    foreach($schoolData['全区学校'] as $schoolName){

                        $examScoreData['exam'][$itemName]['schoolScore'][$schoolName]['totalScore'] = 0;

                        $examScoreData['exam'][$itemName]['schoolScore'][$schoolName]['excellentScore'] = 0;
                        $examScoreData['exam'][$itemName]['schoolScore'][$schoolName]['passScore'] = 0;
                        $examScoreData['exam'][$itemName]['schoolScore'][$schoolName]['failScore'] = 0;
                    }

                    foreach($detailTableData['examNumber'][$itemName] as $itemNum){

                        foreach($scoreData[$itemNum] as $key => $value){

                            $examScoreData['exam'][$itemName]['total']['totalScore'] = $examScoreData['exam'][$itemName]['total']['totalScore'] + $value;
                            $examScoreData['exam'][$itemName]['schoolScore'][$scoreData['学校'][$key]]['totalScore'] = $examScoreData['exam'][$itemName]['schoolScore'][$scoreData['学校'][$key]]['totalScore'] + $value;

                            if($scoreData['全卷'][$key] >= $baseScore['excellentScore']) {

                                $examScoreData['exam'][$itemName]['total']['excellentScore'] = $examScoreData['exam'][$itemName]['total']['excellentScore'] + $value;
                                $examScoreData['exam'][$itemName]['schoolScore'][$scoreData['学校'][$key]]['excellentScore'] = $examScoreData['exam'][$itemName]['schoolScore'][$scoreData['学校'][$key]]['excellentScore'] + $value;
                            
                            } elseif($scoreData['全卷'][$key] >= $baseScore['passScore'] && $scoreData['全卷'][$key] < $baseScore['excellentScore']) {

                                $examScoreData['exam'][$itemName]['total']['passScore'] = $examScoreData['exam'][$itemName]['total']['passScore'] + $value;
                                $examScoreData['exam'][$itemName]['schoolScore'][$scoreData['学校'][$key]]['passScore'] = $examScoreData['exam'][$itemName]['schoolScore'][$scoreData['学校'][$key]]['passScore'] + $value;

                            } else {

                                $examScoreData['exam'][$itemName]['total']['failScore'] = $examScoreData['exam'][$itemName]['total']['failScore'] + $value;
                                $examScoreData['exam'][$itemName]['schoolScore'][$scoreData['学校'][$key]]['failScore'] = $examScoreData['exam'][$itemName]['schoolScore'][$scoreData['学校'][$key]]['failScore'] + $value;

                            }

                        }
                    }

                }

                foreach($detailTableData['typeName'] as $itemName){

                    $examScoreData['type'][$itemName]['total']['totalScore'] = 0;

                    $examScoreData['type'][$itemName]['total']['excellentScore'] = 0;
                    $examScoreData['type'][$itemName]['total']['passScore'] = 0;
                    $examScoreData['type'][$itemName]['total']['failScore'] = 0;

                    foreach($schoolData['全区学校'] as $schoolName){

                        $examScoreData['type'][$itemName]['schoolScore'][$schoolName]['totalScore'] = 0;

                        $examScoreData['type'][$itemName]['schoolScore'][$schoolName]['excellentScore'] = 0;
                        $examScoreData['type'][$itemName]['schoolScore'][$schoolName]['passScore'] = 0;
                        $examScoreData['type'][$itemName]['schoolScore'][$schoolName]['failScore'] = 0;
                    }

                    foreach($detailTableData['typeNumber'][$itemName] as $itemNum){

                        foreach($scoreData[$itemNum] as $key => $value){

                            $examScoreData['type'][$itemName]['total']['totalScore'] = $examScoreData['type'][$itemName]['total']['totalScore'] + $value;
                            $examScoreData['type'][$itemName]['schoolScore'][$scoreData['学校'][$key]]['totalScore'] = $examScoreData['type'][$itemName]['schoolScore'][$scoreData['学校'][$key]]['totalScore'] + $value;

                            if($scoreData['全卷'][$key] >= $baseScore['excellentScore']) {

                                $examScoreData['type'][$itemName]['total']['excellentScore'] = $examScoreData['type'][$itemName]['total']['excellentScore'] + $value;
                                $examScoreData['type'][$itemName]['schoolScore'][$scoreData['学校'][$key]]['excellentScore'] = $examScoreData['type'][$itemName]['schoolScore'][$scoreData['学校'][$key]]['excellentScore'] + $value;
                            
                            } elseif($scoreData['全卷'][$key] >= $baseScore['passScore'] && $scoreData['全卷'][$key] < $baseScore['excellentScore']) {

                                $examScoreData['type'][$itemName]['total']['passScore'] = $examScoreData['type'][$itemName]['total']['passScore'] + $value;
                                $examScoreData['type'][$itemName]['schoolScore'][$scoreData['学校'][$key]]['passScore'] = $examScoreData['type'][$itemName]['schoolScore'][$scoreData['学校'][$key]]['passScore'] + $value;

                            } else {

                                $examScoreData['type'][$itemName]['total']['failScore'] = $examScoreData['type'][$itemName]['total']['failScore'] + $value;
                                $examScoreData['type'][$itemName]['schoolScore'][$scoreData['学校'][$key]]['failScore'] = $examScoreData['type'][$itemName]['schoolScore'][$scoreData['学校'][$key]]['failScore'] + $value;

                            }

                        }
                    }

                }

                break;
        }

        return $examScoreData;

    }

    /**
     * 获取分数率统计
     */
    private function getScoreStatisticsRateData($detailTableData, $scoreStatistics)
    {

        $scoreStatisticsRateData = array(); // 分数率数据
        $rate = array('totalRate','excellentRate','passRate','failRate');
        $count = array('totalCount','excellentCount','passCount','failCount');
        $score = array('totalScore','excellentScore','passScore','failScore');

        switch (self::$schoolType) {
            case 'junior' :
                
                break;
            case 'middle' :

                break;
            case 'high' :
                

                foreach($detailTableData['examName'] as $name){

                    for($i = 0; $i < count($scoreStatistics['exam'][$name]['total']); $i++) {
                        $scoreStatisticsRateData['exam'][$name]['total'][$rate[$i]] = number_format($scoreStatistics['exam'][$name]['total'][$score[$i]] / $scoreStatistics[$count[$i]] / $detailTableData['examScore'][$name], 2, '.', '');
                    }

                    foreach($scoreStatistics['exam'][$name]['schoolScore'] as $key => $schoolScore){

                        for($j = 0; $j < count($schoolScore); $j++) {
                            $scoreStatisticsRateData['exam'][$name]['schoolScore'][$key][$rate[$j]] = number_format($schoolScore[$score[$j]] / $scoreStatistics['schoolCount'][$key][$count[$j]] / $detailTableData['examScore'][$name], 2, '.', '');
                        }

                    }
                    
                }

                foreach($detailTableData['typeName'] as $name){

                    for($i = 0; $i < count($scoreStatistics['type'][$name]['total']); $i++) {
                        $scoreStatisticsRateData['type'][$name]['total'][$rate[$i]] = number_format($scoreStatistics['type'][$name]['total'][$score[$i]] / $scoreStatistics[$count[$i]] / $detailTableData['typeScore'][$name], 2, '.', '');
                    }

                    foreach($scoreStatistics['type'][$name]['schoolScore'] as $key => $schoolScore){

                        for($j = 0; $j < count($schoolScore); $j++) {
                            $scoreStatisticsRateData['type'][$name]['schoolScore'][$key][$rate[$j]] = number_format($schoolScore[$score[$j]] / $scoreStatistics['schoolCount'][$key][$count[$j]] / $detailTableData['typeScore'][$name], 2, '.', '');
                        }

                    }
                    
                }

                break;
        }

        return $scoreStatisticsRateData;
    }

    /**
     * 获取分数率统计
     */
    private function getChoiceQuestionsAnalysisData($detailTableData, $scoreStatistics)
    {

        $choiceQuestionsAnalysisData = array(); // 分数数据

        $filename = self::$queryCourse.'/'.self::$queryCourse.self::CHOICE_QUESTIONS_NAME;

        $data = self::openExcel($filename);

        $keys = array();
        $rets = array();

        $num = 0;

        foreach($data->getRowIterator() as $kr => $row){

            $cellIterator = $row->getCellIterator();

            if ($kr == 1){
                foreach($cellIterator as $kc => $cell){
                    $keys[] = $cell->getValue();
                }
            }
            else {
                foreach($cellIterator as $kc => $cell){
                    $rets[$keys[$kc]][] = $cell->getValue();
                }       
            }
        }

        for ($i = 0; $i < count($rets['题号']); $i++) { 
            if($rets['答案'][$i] == 'A' || $rets['答案'][$i] == 'B' || $rets['答案'][$i] == 'C' || $rets['答案'][$i] == 'D'){
                $choiceQuestionsAnalysisData[$num][$keys[0]] = $rets['题号'][$i];
                $choiceQuestionsAnalysisData[$num][$keys[2]] = $rets['答案'][$i];
                $choiceQuestionsAnalysisData[$num][$keys[3]] = $rets['人数'][$i];
                $choiceQuestionsAnalysisData[$num][$keys[6]] = $rets['平均分'][$i];
                $choiceQuestionsAnalysisData[$num][$keys[7]] = $rets['标准差'][$i];
                $choiceQuestionsAnalysisData[$num][$keys[8]] = $rets['得分率'][$i];
                $choiceQuestionsAnalysisData[$num][$keys[11]] = $rets['难度'][$i];
                if($rets['难度'][$i] > 0.9){
                    $choiceQuestionsAnalysisData[$num]['难度评价标准'] = '容易';
                }
                elseif($rets['难度'][$i] > 0.7 && $rets['难度'][$i] <= 0.9){
                    $choiceQuestionsAnalysisData[$num]['难度评价标准'] = '较易';
                }
                elseif($rets['难度'][$i] > 0.4 && $rets['难度'][$i] <= 0.7){
                    $choiceQuestionsAnalysisData[$num]['难度评价标准'] = '中等';
                }
                elseif($rets['难度'][$i] <= 0.4){
                    $choiceQuestionsAnalysisData[$num]['难度评价标准'] = '偏难';
                }
                $choiceQuestionsAnalysisData[$num][$keys[12]] = $rets['区分度'][$i];
                $choiceQuestionsAnalysisData[$num]['区分度评价标准'] = $rets['区分度'][$i];
                if($rets['区分度'][$i] >= 0.4){
                    $choiceQuestionsAnalysisData[$num]['区分度评价标准'] = '区分度较高';
                }
                elseif($rets['区分度'][$i] >= 0.3 && $rets['区分度'][$i] <= 0.39){
                    $choiceQuestionsAnalysisData[$num]['区分度评价标准'] = '区分度中等';
                }
                elseif($rets['区分度'][$i] >= 0.2 && $rets['区分度'][$i] <= 0.29){
                    $choiceQuestionsAnalysisData[$num]['区分度评价标准'] = '区分度一般';
                }
                elseif($rets['区分度'][$i] < 0.2){
                    $choiceQuestionsAnalysisData[$num]['区分度评价标准'] = '区分度较低';
                }
                $choiceQuestionsAnalysisData[$num][$keys[14]] = $rets['选A率%'][$i];
                $choiceQuestionsAnalysisData[$num][$keys[16]] = $rets['选B率%'][$i];
                $choiceQuestionsAnalysisData[$num][$keys[18]] = $rets['选C率%'][$i];
                $choiceQuestionsAnalysisData[$num][$keys[20]] = $rets['选D率%'][$i];
                $num++;
            }
        }

        return $choiceQuestionsAnalysisData;
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
        self::$schoolType  = 'high'; // 学校类型

        $schoolObj = new \Admin\Model\SchoolData();
        $schoolData = $schoolObj->getSchoolData(self::$schoolType);

        $courseObj = new \Admin\Model\CourseData();
        $courseData = $courseObj->getCourseData($date, $foldername);

        self::$courseAmount = count($courseData);

        $rateObj = new \Admin\Model\ScoreRateData();
        $scoreRate = $rateObj->getScoreRateData(self::$queryCourse);

        $detailTableObj = new \Admin\Model\DetailTableData();
        $detailTableData = $detailTableObj->getDetailTableData($date, $foldername, $course);

        /*$courseAnalysis = self::getCourseAnalysisData();

        $averageScore = self::getAverageData();

        $studentCountRate = self::getStudentCountRateData($courseAnalysis, $scoreRate, $detailTableData);

        $scoreStatistics = self::getScoreStatisticsData($schoolData, $scoreRate, $detailTableData);
        $scoreStatisticsRate = self::getScoreStatisticsRateData($detailTableData, $scoreStatistics);*/


        // $choiceQuestionsAnalysis = self::getChoiceQuestionsAnalysisData();

        // var_dump($choiceQuestionsAnalysis);


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