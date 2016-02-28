<?php

/**
 * 获取学校列表
 * @author chenhong
 */

/**
* 此处数据可以单独先把需要的excel表里面的数据事先读取出来，然后根据不同需求将数据加工处理，现在将读取数据与逻辑写在一起实在有些乱，不便于查找
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
    const AVERAGE_NAME = '综合指标';

    /**
     * 学生成绩表名
     * @var string
     */
    const STUDENT_SCORE_NAME = '学生成绩';

    /**
     * 单科成绩分数表名(未加科目)
     * @var string
     */
    const COURSE_SCORE_NAME = '小题分';

    /**
     * 选择题分析表名(未加科目)
     * @var string
     */
    const CHOICE_QUESTIONS_NAME = '小题分析';

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
     * 年级
     * @var string
     */
    protected static $grade = '';

    /**
     * 搜索数据表数量
     * @var string
     */
    protected static $queryExcelCount = 1;

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
                    if (($kc == 4)) {
                        $difficulty[] = number_format($cell->getValue(), 2, '.', '');
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
                        $distinguish[] = number_format($cell->getValue(), 2, '.', '');
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
                        $reliability[] = number_format($cell->getValue(), 2, '.', '');
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
    private function getAverageData($schoolData)
    {
        $filename = self::$queryCourse.'/'.self::AVERAGE_NAME;
        $data = self::openExcel($filename);

        $averageData = array(); // 平均分对比数据

        $keys = array(); // 平均分字段名

        $schoolName = array(); // 学校名称
        $schoolArea = array(); // 学校区域
        $amountStudentCount = 0; // 全区参加考试人数
        $studentCount = array(); // 全学校参加考试人数
        $amountAverageScore = 0; // 全区平均分
        $averageScore = array(); // 各学校平均分

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
                    if ($kd == 2) {
                        $studentCount[] = $cell->getValue();
                    }
                    if ($kd == 3) {
                        $averageScore[] = number_format($cell->getValue(), 2, '.', '');
                    }
                }
            }
        }

        $amountStudentCount = $studentCount[count($studentCount)-1];
        $amountAverageScore = $averageScore[count($averageScore)-1];

        $schoolName = array_splice($schoolName, 0, -1);
        $studentCount = array_splice($studentCount, 0, -1);
        $averageScore = array_splice($averageScore, 0, -1);

        foreach ($studentCount as $key => $value) {
            $studentCount[$schoolName[$key]] = $value;
        }

        foreach ($averageScore as $key => $value) {
            $averageScore[$schoolName[$key]] = $value;
        }

        $studentCount = array_splice($studentCount, 6, 6);
        $averageScore = array_splice($averageScore, 6, 6);

        if($schoolData['schoolType'] != 'high') {
            $areaStudentCount = array(); // 区域参加考试人数
            $schoolArea = array(); // 学校所属区域
            for ($i = 0; $i < count($schoolData['schoolArea']); $i++) { 
                $areaStudentCount[$schoolData['schoolArea'][$i]] = 0;
                foreach ($schoolData['schoolList'][$schoolData['schoolArea'][$i]] as $name) {
                    foreach ($schoolName as $j => $value) {
                        if($value == $name) {
                            $schoolArea[$name] = $schoolData['schoolArea'][$i];
                            $areaStudentCount[$schoolData['schoolArea'][$i]] = $areaStudentCount[$schoolData['schoolArea'][$i]] + $studentCount[$j];
                        }
                    }
                }
            }
        }

        $averageData = array(
            'course'      => self::$queryCourse,
            'schoolName'  => $schoolName, // 学校列表
            'studentCount' => $studentCount, // 各学校参加考试总人数
            'amountStudentCount' => $amountStudentCount, // 全区参加考试总人数
            'averageScore' => $averageScore, // 各学校平均分
            'amountAverageScore' => $amountAverageScore // 全区平均分
        );

        if($schoolData['schoolType'] != 'high') {
            $averageData['schoolArea'] = $schoolArea; // 学校区域列表
            $averageData['areaStudentCount'] = $areaStudentCount; // 区域参加考试人数
        }

        return $averageData;
    }

    /**
     * 获取学生人数百分比
     */
    private function getStudentCountRateData($schoolData, $averageScoreData, $scoreRateData, $detailTableData)
    {
        $filename = self::$queryCourse.'/'.self::COURSE_SCORE_NAME;

        $data = self::openExcel($filename);
        
        $scoreData = array(); // 学生分数

        $baseScore = array(); // 考试基准分数线

        $studentCountRateData = array(); // 返回学生分数信息

        $num = 0; // 数组下标
        $scoreRow = array(); // 分数所在列
        $rowName = array('学校','全卷'); // 分数所在列名称

        $count = array(); // 统计人数
        $schoolCount = array(); // 统计各学校人数
        $cumulativeCount = array(); // 统计累积人数
        $rate = array(); // 所占百分比
        $cumulativeRate = array(); // 累计所占百分比

        foreach($data->getRowIterator() as $kr => $row){

            $cellIterator = $row->getCellIterator();

            if ($kr == 1){
                foreach($cellIterator as $kc => $cell){
                    if($rowName[0] == $cell->getValue() || $rowName[1] == $cell->getValue()) {
                        $scoreRow[] = $kc;
                    }
                }
            }
            else {
                foreach($cellIterator as $kc => $cell){
                    if($kc == $scoreRow[0] || $kc == $scoreRow[1]) {
                        $scoreData[$num][] = $cell->getValue();
                    }
                }
                $num++;
            }
        }

        $totalScore = $detailTableData['totalScore']; // 该科满分值
        $totalScoreList = array(); // 全区分数列表
        $scoreFilter = array(); // 分数过滤（取最大值、最小值）

        $baseScore['excellentScore'] = $totalScore * $scoreRateData[0];
        $baseScore['passScore'] = $totalScore * $scoreRateData[1];

        $count['totalCount'] = 0;
        $count['excellentCount'] = 0;
        $count['passCount'] = 0;
        $count['failCount'] = 0;

        foreach($averageScoreData['schoolName'] as $schoolName){

            $schoolCount[$schoolName]['totalCount'] = 0;

            $schoolCount[$schoolName]['excellentCount'] = 0;
            $schoolCount[$schoolName]['passCount'] = 0;
            $schoolCount[$schoolName]['failCount'] = 0;
        }

        if ($schoolData['schoolType'] != 'high') {
            $areaTotalStudentScore = array(); // 区域学生分数总和
            $areaAverageScore = array(); // 区域平均分
            $areaScoreList = array(); // 区域分数列表
            $areaScoreFilter = array(); // 区域分数过滤（取最大值、最小值）
            foreach ($schoolData['schoolArea'] as $value) {
                $areaTotalStudentScore[$value] = 0;
                $areaAverageScore[$value] = 0;
                $areaScoreList[$value] = array();
            }
        }

        for ($i = 0; $i < count($scoreData); $i++) {
            if ($schoolData['schoolType'] != 'high') {
                $areaTotalStudentScore[$averageScoreData['schoolArea'][$scoreData[$i][0]]] = $areaTotalStudentScore[$averageScoreData['schoolArea'][$scoreData[$i][0]]] + $scoreData[$i][1];
                $areaScoreList[$averageScoreData['schoolArea'][$scoreData[$i][0]]][] = $scoreData[$i][1];
            }
            $totalScoreList[] = $scoreData[$i][1];

            $count['totalCount']++;
            $schoolCount[$scoreData[$i][0]]['totalCount']++;

            if($scoreData[$i][1] >= $baseScore['excellentScore']) {
                $count['excellentCount']++;
                $schoolCount[$scoreData[$i][0]]['excellentCount']++;
            } elseif($scoreData[$i][1] >= $baseScore['passScore'] && $scoreData[$i][1] < $baseScore['excellentScore']) {
                $count['passCount']++;
                $schoolCount[$scoreData[$i][0]]['passCount']++;
            } elseif($scoreData[$i][1] < $baseScore['passScore'] && !empty($scoreData[$i][1])) {
                $count['failCount']++;
                $schoolCount[$scoreData[$i][0]]['failCount']++;
            }
        }

        if($schoolData['schoolType'] != 'high') {
            $areaSchoolCount = array(); // 区域学校人数统计
            $areaRate = array(); // 区域学校优秀率等统计
            for ($i = 0; $i < count($schoolData['schoolArea']); $i++) {
                $areaSchoolCount[$schoolData['schoolArea'][$i]]['excellentCount'] = 0;
                $areaSchoolCount[$schoolData['schoolArea'][$i]]['passCount'] = 0;
                $areaSchoolCount[$schoolData['schoolArea'][$i]]['failCount'] = 0;
                foreach ($schoolData['schoolList'][$schoolData['schoolArea'][$i]] as $name) {
                    foreach ($averageScoreData['schoolName'] as $j => $value) {
                        if($value == $name) {
                            $areaSchoolCount[$schoolData['schoolArea'][$i]]['excellentCount'] = $areaSchoolCount[$schoolData['schoolArea'][$i]]['excellentCount'] + $schoolCount[$value]['excellentCount'];
                            $areaSchoolCount[$schoolData['schoolArea'][$i]]['passCount'] = $areaSchoolCount[$schoolData['schoolArea'][$i]]['passCount'] + $schoolCount[$value]['passCount'];
                            $areaSchoolCount[$schoolData['schoolArea'][$i]]['failCount'] = $areaSchoolCount[$schoolData['schoolArea'][$i]]['failCount'] + $schoolCount[$value]['failCount'];
                        }
                    }
                }
                $areaRate[$schoolData['schoolArea'][$i]]['excellentRate'] = number_format($areaSchoolCount[$schoolData['schoolArea'][$i]]['excellentCount'] / $averageScoreData['areaStudentCount'][$schoolData['schoolArea'][$i]] * 100, 2, '.', '');
                $areaRate[$schoolData['schoolArea'][$i]]['passRate'] = number_format($areaSchoolCount[$schoolData['schoolArea'][$i]]['passCount'] / $averageScoreData['areaStudentCount'][$schoolData['schoolArea'][$i]] * 100, 2, '.', '');
                $areaRate[$schoolData['schoolArea'][$i]]['failRate'] = number_format($areaSchoolCount[$schoolData['schoolArea'][$i]]['failCount'] / $averageScoreData['areaStudentCount'][$schoolData['schoolArea'][$i]] * 100, 2, '.', '');
            }
        }

        $cumulativeCount['excellentCount'] = $count['excellentCount'];
        $cumulativeCount['passCount'] = $count['excellentCount'] + $count['passCount'];
        $cumulativeCount['failCount'] = $count['excellentCount'] + $count['passCount'] + $count['failCount'];

        $rate['excellentRate'] = number_format($count['excellentCount'] / $averageScoreData['amountStudentCount'] * 100, 2, '.', '');
        $rate['passRate'] = number_format($count['passCount'] / $averageScoreData['amountStudentCount'] * 100, 2, '.', '');
        $rate['failRate'] = number_format($count['failCount'] / $averageScoreData['amountStudentCount'] * 100, 2, '.', '');

        $cumulativeRate['excellentRate'] = number_format($cumulativeCount['excellentCount'] / $averageScoreData['amountStudentCount'] * 100, 2, '.', '');
        $cumulativeRate['passRate'] = number_format($cumulativeCount['passCount'] / $averageScoreData['amountStudentCount'] * 100, 2, '.', '');
        $cumulativeRate['failRate'] = number_format($cumulativeCount['failCount'] / $averageScoreData['amountStudentCount'] * 100, 2, '.', '');

        $scoreFilter['highestScore'] = max($totalScoreList);
        $scoreFilter['lowestScore'] = min($totalScoreList);

        $studentCountRateData = array(
            'baseScore'       => $baseScore, // 优秀分数、及格分数
            'count'           => $count, // 全区优秀人数、及格人数、未及格人数
            'schoolCount'     => $schoolCount, // 全区各学校优秀人数、及格人数、未及格人数
            'rate'            => $rate, // 全区优秀率、及格率、未及格率
            'cumulativeCount' => $cumulativeCount, // 全区累计优秀人数、累计及格人数、未及格人数
            'cumulativeRate'  => $cumulativeRate, // 全区累计优秀率、累计及格率、未及格率
            'scoreFilter'     => $scoreFilter, // 过滤全区分数（最大值 、最小值）
        );

        if($schoolData['schoolType'] != 'high') {
            foreach ($areaTotalStudentScore as $key => $value) {
                $areaAverageScore[$key] = number_format($value / $averageScoreData['areaStudentCount'][$key], 2, '.', '');
            }
            foreach ($areaScoreList as $key => $value) {
                $areaScoreFilter[$key]['highestScore'] = max($value);
                $areaScoreFilter[$key]['lowestScore'] = min($value);
            }
            $studentCountRateData['areaTotalStudentScore'] = $areaTotalStudentScore; // 学校区域列表
            $studentCountRateData['areaAverageScore'] = $areaAverageScore; // 区域平均分
            $studentCountRateData['areaScoreFilter'] = $areaScoreFilter; // 过滤区域分数（最大值 、最小值）
            $studentCountRateData['areaSchoolCount'] = $areaSchoolCount; // 区域优秀人数统计
            $studentCountRateData['areaRate'] = $areaRate; // 区域优秀率统计
        }

        return $studentCountRateData;
    }

    /**
     * 获取分数统计
     */
    private function getScoreStatisticsData($schoolData, $scoreRateData, $detailTableData, $averageScoreData)
    {

        $scoreData = array(); // 分数数据

        $num = 0;

        $filename = self::$queryCourse.'/'.self::COURSE_SCORE_NAME;

        $data = self::openExcel($filename);

        $keys = array();

        $totalScore = $detailTableData['totalScore'];

        $baseScore['excellentScore'] = $totalScore * $scoreRateData[0];
        $baseScore['passScore'] = $totalScore * $scoreRateData[1];

        $examScoreData = array(); // 考试类型分数
        $schoolCount = array(); // 各学校人数统计

        $studentScore = array(); // 学生成绩列表

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

        $count['totalCount'] = 0;
        $count['excellentCount'] = 0;
        $count['passCount'] = 0;
        $count['failCount'] = 0;

        $examScore = array(); // 考核范畴统计
        $typeScore = array(); // 考核层级统计

        $examAverageScore = array(); // 考核范畴统计
        $typeAverageScore = array(); // 考核层级统计

        if($schoolData['schoolType'] != 'high') {
            $examAreaScore = array(); // 区域考核范畴统计
            $typeAreaScore = array(); // 区域考核层级统计

            $examAreaAverageScore = array(); // 区域考核范畴统计
            $typeAreaAverageScore = array(); // 区域考核层级统计
        }

        foreach($averageScoreData['schoolName'] as $schoolName){
            $schoolCount[$schoolName]['totalCount'] = 0;

            $schoolCount[$schoolName]['excellentCount'] = 0;
            $schoolCount[$schoolName]['passCount'] = 0;
            $schoolCount[$schoolName]['failCount'] = 0;

            $schoolScore[$schoolName]['totalScore'] = 0;
            $schoolScore[$schoolName]['excellentScore'] = 0;
            $schoolScore[$schoolName]['passScore'] = 0;
            $schoolScore[$schoolName]['failScore'] = 0;

            $schoolAverageScore[$schoolName]['totalScore'] = 0;
            $schoolAverageScore[$schoolName]['excellentScore'] = 0;
            $schoolAverageScore[$schoolName]['passScore'] = 0;
            $schoolAverageScore[$schoolName]['failScore'] = 0;
        }

        $studentScore['score'] = $scoreData['全卷'];
        $studentScore['school'] = $scoreData['学校'];

        foreach($scoreData['全卷'] as $key => $score){

            foreach ($detailTableData['examName'] as $name) {
                $studentScore['exam'][$key][$name] = 0;
            }
            foreach ($detailTableData['typeName'] as $name) {
                $studentScore['type'][$key][$name] = 0;
            }
            foreach($detailTableData['examNumber'] as $itemName => $item){
                foreach ($item as $value) {
                    $studentScore['exam'][$key][$itemName] = $studentScore['exam'][$key][$itemName] + $scoreData[$value][$key];
                }
            }
            foreach($detailTableData['typeNumber'] as $itemName => $item){
                foreach ($item as $value) {
                    $studentScore['type'][$key][$itemName] = $studentScore['type'][$key][$itemName] + $scoreData[$value][$key];
                }
            }

            $count['totalCount']++;
            $schoolCount[$scoreData['学校'][$key]]['totalCount']++;

            $schoolScore[$scoreData['学校'][$key]]['totalScore'] = $schoolScore[$scoreData['学校'][$key]]['totalScore'] + $score;

            if($scoreData['全卷'][$key] >= $baseScore['excellentScore']) {
                $count['excellentCount']++;
                $schoolCount[$scoreData['学校'][$key]]['excellentCount']++;

                $schoolScore[$scoreData['学校'][$key]]['excellentScore'] = $schoolScore[$scoreData['学校'][$key]]['excellentScore'] + $score;
            
            } elseif($scoreData['全卷'][$key] >= $baseScore['passScore'] && $scoreData['全卷'][$key] < $baseScore['excellentScore']) {
                $count['passCount']++;
                $schoolCount[$scoreData['学校'][$key]]['passCount']++;

                $schoolScore[$scoreData['学校'][$key]]['passScore'] = $schoolScore[$scoreData['学校'][$key]]['passScore'] + $score;

            } else {
                $count['failCount']++;
                $schoolCount[$scoreData['学校'][$key]]['failCount']++;

                $schoolScore[$scoreData['学校'][$key]]['failScore'] = $schoolScore[$scoreData['学校'][$key]]['failScore'] + $score;
            }

        }

        foreach ($schoolScore as $schoolName => $item) {
            foreach ($item as $key => $value) {
                $schoolAverageScore[$schoolName]['totalScore'] = number_format($schoolScore[$schoolName]['totalScore'] / $schoolCount[$schoolName]['totalCount'], 2, '.', '');
                $schoolAverageScore[$schoolName]['excellentScore'] = number_format($schoolScore[$schoolName]['excellentScore'] / $schoolCount[$schoolName]['excellentCount'], 2, '.', '');
                $schoolAverageScore[$schoolName]['passScore'] = number_format($schoolScore[$schoolName]['passScore'] / $schoolCount[$schoolName]['passCount'], 2, '.', '');
                $schoolAverageScore[$schoolName]['failScore'] = number_format($schoolScore[$schoolName]['failScore'] / $schoolCount[$schoolName]['failCount'], 2, '.', '');
            }
        }


        foreach($detailTableData['examName'] as $itemName){

            $examScore[$itemName]['total']['totalScore'] = 0;

            $examScore[$itemName]['total']['excellentScore'] = 0;
            $examScore[$itemName]['total']['passScore'] = 0;
            $examScore[$itemName]['total']['failScore'] = 0;

            $examAverageScore[$itemName]['total']['totalScore'] = 0;

            $examAverageScore[$itemName]['total']['excellentScore'] = 0;
            $examAverageScore[$itemName]['total']['passScore'] = 0;
            $examAverageScore[$itemName]['total']['failScore'] = 0;

            foreach($averageScoreData['schoolName'] as $schoolName){

                $examScore[$itemName]['schoolScore'][$schoolName]['totalScore'] = 0;

                $examScore[$itemName]['schoolScore'][$schoolName]['excellentScore'] = 0;
                $examScore[$itemName]['schoolScore'][$schoolName]['passScore'] = 0;
                $examScore[$itemName]['schoolScore'][$schoolName]['failScore'] = 0;

                $examAverageScore[$itemName]['schoolScore'][$schoolName]['totalScore'] = 0;

                $examAverageScore[$itemName]['schoolScore'][$schoolName]['excellentScore'] = 0;
                $examAverageScore[$itemName]['schoolScore'][$schoolName]['passScore'] = 0;
                $examAverageScore[$itemName]['schoolScore'][$schoolName]['failScore'] = 0;
            }

            foreach($detailTableData['examNumber'][$itemName] as $itemNum){

                foreach($scoreData[$itemNum] as $key => $value){

                    $examScore[$itemName]['total']['totalScore'] = $examScore[$itemName]['total']['totalScore'] + $value;
                    $examScore[$itemName]['schoolScore'][$scoreData['学校'][$key]]['totalScore'] = $examScore[$itemName]['schoolScore'][$scoreData['学校'][$key]]['totalScore'] + $value;

                    if($scoreData['全卷'][$key] >= $baseScore['excellentScore']) {

                        $examScore[$itemName]['total']['excellentScore'] = $examScore[$itemName]['total']['excellentScore'] + $value;
                        $examScore[$itemName]['schoolScore'][$scoreData['学校'][$key]]['excellentScore'] = $examScore[$itemName]['schoolScore'][$scoreData['学校'][$key]]['excellentScore'] + $value;
                    
                    } elseif($scoreData['全卷'][$key] >= $baseScore['passScore'] && $scoreData['全卷'][$key] < $baseScore['excellentScore']) {

                        $examScore[$itemName]['total']['passScore'] = $examScore[$itemName]['total']['passScore'] + $value;
                        $examScore[$itemName]['schoolScore'][$scoreData['学校'][$key]]['passScore'] = $examScore[$itemName]['schoolScore'][$scoreData['学校'][$key]]['passScore'] + $value;

                    } else {

                        $examScore[$itemName]['total']['failScore'] = $examScore[$itemName]['total']['failScore'] + $value;
                        $examScore[$itemName]['schoolScore'][$scoreData['学校'][$key]]['failScore'] = $examScore[$itemName]['schoolScore'][$scoreData['学校'][$key]]['failScore'] + $value;
                    }
                }

                $examAverageScore[$itemName]['total']['totalScore'] = number_format($examScore[$itemName]['total']['totalScore'] / $count['totalCount'], 2, '.', '');

                $examAverageScore[$itemName]['total']['excellentScore'] = number_format($examScore[$itemName]['total']['excellentScore'] / $count['excellentCount'], 2, '.', '');
                $examAverageScore[$itemName]['total']['passScore'] = number_format($examScore[$itemName]['total']['passScore'] / $count['passCount'], 2, '.', '');
                $examAverageScore[$itemName]['total']['failScore'] = number_format($examScore[$itemName]['total']['failScore'] / $count['failCount'], 2, '.', '');

                foreach ($examScore[$itemName]['schoolScore'] as $key => $value) {
                    $examAverageScore[$itemName]['schoolScore'][$key]['totalScore'] = number_format($value['totalScore'] / $schoolCount[$key]['totalCount'], 2, '.', '');

                    $examAverageScore[$itemName]['schoolScore'][$key]['excellentScore'] = number_format($value['excellentScore'] / $schoolCount[$key]['excellentCount'], 2, '.', '');
                    $examAverageScore[$itemName]['schoolScore'][$key]['passScore'] = number_format($value['passScore'] / $schoolCount[$key]['passCount'], 2, '.', '');
                    $examAverageScore[$itemName]['schoolScore'][$key]['failScore'] = number_format($value['failScore'] / $schoolCount[$key]['failCount'], 2, '.', '');
                }
            }
        }

        foreach($detailTableData['typeName'] as $itemName){
            $typeScore[$itemName]['total']['totalScore'] = 0;

            $typeScore[$itemName]['total']['excellentScore'] = 0;
            $typeScore[$itemName]['total']['passScore'] = 0;
            $typeScore[$itemName]['total']['failScore'] = 0;

            $typeAverageScore[$itemName]['total']['totalScore'] = 0;

            $typeAverageScore[$itemName]['total']['excellentScore'] = 0;
            $typeAverageScore[$itemName]['total']['passScore'] = 0;
            $typeAverageScore[$itemName]['total']['failScore'] = 0;

            foreach($averageScoreData['schoolName'] as $schoolName){

                $typeScore[$itemName]['schoolScore'][$schoolName]['totalScore'] = 0;

                $typeScore[$itemName]['schoolScore'][$schoolName]['excellentScore'] = 0;
                $typeScore[$itemName]['schoolScore'][$schoolName]['passScore'] = 0;
                $typeScore[$itemName]['schoolScore'][$schoolName]['failScore'] = 0;

                $typeAverageScore[$itemName]['schoolScore'][$schoolName]['totalScore'] = 0;

                $typeAverageScore[$itemName]['schoolScore'][$schoolName]['excellentScore'] = 0;
                $typeAverageScore[$itemName]['schoolScore'][$schoolName]['passScore'] = 0;
                $typeAverageScore[$itemName]['schoolScore'][$schoolName]['failScore'] = 0;
            }

            foreach($detailTableData['typeNumber'][$itemName] as $itemNum){

                foreach($scoreData[$itemNum] as $key => $value){

                    $typeScore[$itemName]['total']['totalScore'] = $typeScore[$itemName]['total']['totalScore'] + $value;
                    $typeScore[$itemName]['schoolScore'][$scoreData['学校'][$key]]['totalScore'] = $typeScore[$itemName]['schoolScore'][$scoreData['学校'][$key]]['totalScore'] + $value;

                    if($scoreData['全卷'][$key] >= $baseScore['excellentScore']) {

                        $typeScore[$itemName]['total']['excellentScore'] = $typeScore[$itemName]['total']['excellentScore'] + $value;
                        $typeScore[$itemName]['schoolScore'][$scoreData['学校'][$key]]['excellentScore'] = $typeScore[$itemName]['schoolScore'][$scoreData['学校'][$key]]['excellentScore'] + $value;
                    
                    } elseif($scoreData['全卷'][$key] >= $baseScore['passScore'] && $scoreData['全卷'][$key] < $baseScore['excellentScore']) {

                        $typeScore[$itemName]['total']['passScore'] = $typeScore[$itemName]['total']['passScore'] + $value;
                        $typeScore[$itemName]['schoolScore'][$scoreData['学校'][$key]]['passScore'] = $typeScore[$itemName]['schoolScore'][$scoreData['学校'][$key]]['passScore'] + $value;

                    } else {

                        $typeScore[$itemName]['total']['failScore'] = $typeScore[$itemName]['total']['failScore'] + $value;
                        $typeScore[$itemName]['schoolScore'][$scoreData['学校'][$key]]['failScore'] = $typeScore[$itemName]['schoolScore'][$scoreData['学校'][$key]]['failScore'] + $value;
                    }
                }

                $typeAverageScore[$itemName]['total']['totalScore'] = number_format($typeScore[$itemName]['total']['totalScore'] / $count['totalCount'], 2, '.', '');

                $typeAverageScore[$itemName]['total']['excellentScore'] = number_format($typeScore[$itemName]['total']['excellentScore'] / $count['excellentCount'], 2, '.', '');
                $typeAverageScore[$itemName]['total']['passScore'] = number_format($typeScore[$itemName]['total']['passScore'] / $count['passCount'], 2, '.', '');
                $typeAverageScore[$itemName]['total']['failScore'] = number_format($typeScore[$itemName]['total']['failScore'] / $count['failCount'], 2, '.', '');

                foreach ($typeScore[$itemName]['schoolScore'] as $key => $value) {
                    $typeAverageScore[$itemName]['schoolScore'][$key]['totalScore'] = number_format($value['totalScore'] / $schoolCount[$key]['totalCount'], 2, '.', '');

                    $typeAverageScore[$itemName]['schoolScore'][$key]['excellentScore'] = number_format($value['excellentScore'] / $schoolCount[$key]['excellentCount'], 2, '.', '');
                    $typeAverageScore[$itemName]['schoolScore'][$key]['passScore'] = number_format($value['passScore'] / $schoolCount[$key]['passCount'], 2, '.', '');
                    $typeAverageScore[$itemName]['schoolScore'][$key]['failScore'] = number_format($value['failScore'] / $schoolCount[$key]['failCount'], 2, '.', '');
                }
            }
        }

        $examScoreData = array(
            'count'              => $count, // 全区人数统计
            'schoolCount'        => $schoolCount, // 各学校人数统计
            'schoolScore'        => $schoolScore, // 各学校分数统计
            'schoolAverageScore' => $schoolAverageScore, // 各学校平均分统计
            'examScore'          => $examScore, // 考核范畴分数统计
            'typeScore'          => $typeScore, // 考核层级分数统计
            'examAverageScore'   => $examAverageScore, // 考核范畴平均分统计
            'typeAverageScore'   => $typeAverageScore, // 考核层级平均分统计
            'studentScore'       => $studentScore, // 学生考核范畴、考核层级分数统计
        );

        return $examScoreData;
    }

    /**
     * 获取分数率统计
     */
    private function getScoreStatisticsRateData($schoolData, $detailTableData, $averageScoreData, $scoreStatisticsData, $studentCountRate)
    {

        $scoreStatisticsRateData = array(); // 分数率数据
        $rate = array('totalRate','excellentRate','passRate','failRate');
        $count = array('totalCount','excellentCount','passCount','failCount');
        $score = array('totalScore','excellentScore','passScore','failScore');

        $examRate = array(); // 考核范畴
        $typeRate = array(); // 考核层级

        foreach($detailTableData['examName'] as $name){
            for($i = 0; $i < count($scoreStatisticsData['examScore'][$name]['total']); $i++) {
                $examRate[$name]['total'][$rate[$i]] = number_format($scoreStatisticsData['examScore'][$name]['total'][$score[$i]] / $scoreStatisticsData['count'][$count[$i]] / $detailTableData['examScore'][$name], 2, '.', '');
            }

            foreach($scoreStatisticsData['examScore'][$name]['schoolScore'] as $key => $schoolScore){

                for($j = 0; $j < count($schoolScore); $j++) {
                    $examRate[$name]['schoolScore'][$key][$rate[$j]] = number_format($schoolScore[$score[$j]] / $scoreStatisticsData['schoolCount'][$key][$count[$j]] / $detailTableData['examScore'][$name], 2, '.', '');
                }
            }
        }

        foreach($detailTableData['typeName'] as $name){
            for($i = 0; $i < count($scoreStatisticsData['typeScore'][$name]['total']); $i++) {
                $typeRate[$name]['total'][$rate[$i]] = number_format($scoreStatisticsData['typeScore'][$name]['total'][$score[$i]] / $scoreStatisticsData['count'][$count[$i]] / $detailTableData['typeScore'][$name], 2, '.', '');
            }

            foreach($scoreStatisticsData['typeScore'][$name]['schoolScore'] as $key => $schoolScore){

                for($j = 0; $j < count($schoolScore); $j++) {
                    $typeRate[$name]['schoolScore'][$key][$rate[$j]] = number_format($schoolScore[$score[$j]] / $scoreStatisticsData['schoolCount'][$key][$count[$j]] / $detailTableData['typeScore'][$name], 2, '.', '');
                }
            }
        }

        if($schoolData['schoolType'] != 'high') {
            $areaScore = array(); // 区域学校分数率统计
            $areaExamRate = array(); // 区域学校考核范畴
            $areaTypeRate = array(); // 区域学校考核层级
            $areaAverageScore = array();

            foreach ($scoreStatisticsData['examScore'] as $key => $item) {
                for ($i = 0; $i < count($schoolData['schoolArea']); $i++) {
                    $areaScore[$schoolData['schoolArea'][$i]]['exam'][$key]['totalScore'] = 0;
                    $areaScore[$schoolData['schoolArea'][$i]]['exam'][$key]['excellentScore'] = 0;
                    $areaScore[$schoolData['schoolArea'][$i]]['exam'][$key]['passScore'] = 0;
                    $areaScore[$schoolData['schoolArea'][$i]]['exam'][$key]['failScore'] = 0;
                    foreach ($schoolData['schoolList'][$schoolData['schoolArea'][$i]] as $name) {
                        foreach ($averageScoreData['schoolName'] as $j => $value) {
                            if($value == $name) {
                                $areaScore[$schoolData['schoolArea'][$i]]['exam'][$key]['totalScore'] = $areaScore[$schoolData['schoolArea'][$i]]['exam'][$key]['totalScore'] + $item['schoolScore'][$name]['totalScore'];
                                $areaScore[$schoolData['schoolArea'][$i]]['exam'][$key]['excellentScore'] = $areaScore[$schoolData['schoolArea'][$i]]['exam'][$key]['excellentScore'] + $item['schoolScore'][$name]['excellentScore'];
                                $areaScore[$schoolData['schoolArea'][$i]]['exam'][$key]['passScore'] = $areaScore[$schoolData['schoolArea'][$i]]['exam'][$key]['passScore'] + $item['schoolScore'][$name]['passScore'];
                                $areaScore[$schoolData['schoolArea'][$i]]['exam'][$key]['failScore'] = $areaScore[$schoolData['schoolArea'][$i]]['exam'][$key]['failScore'] + $item['schoolScore'][$name]['failScore'];
                            }
                        }
                    }
                    $areaExamRate[$schoolData['schoolArea'][$i]]['exam'][$key]['totalRate'] = number_format($areaScore[$schoolData['schoolArea'][$i]]['exam'][$key]['totalScore'] / $averageScoreData['areaStudentCount'][$schoolData['schoolArea'][$i]] / $detailTableData['examScore'][$key], 2, '.', '');
                    $areaExamRate[$schoolData['schoolArea'][$i]]['exam'][$key]['excellentRate'] = number_format($areaScore[$schoolData['schoolArea'][$i]]['exam'][$key]['excellentScore'] / $averageScoreData['areaStudentCount'][$schoolData['schoolArea'][$i]] / $detailTableData['examScore'][$key], 2, '.', '');
                    $areaExamRate[$schoolData['schoolArea'][$i]]['exam'][$key]['passRate'] = number_format($areaScore[$schoolData['schoolArea'][$i]]['exam'][$key]['passScore'] / $averageScoreData['areaStudentCount'][$schoolData['schoolArea'][$i]] / $detailTableData['examScore'][$key], 2, '.', '');
                    $areaExamRate[$schoolData['schoolArea'][$i]]['exam'][$key]['failRate'] = number_format($areaScore[$schoolData['schoolArea'][$i]]['exam'][$key]['failScore'] / $averageScoreData['areaStudentCount'][$schoolData['schoolArea'][$i]] / $detailTableData['examScore'][$key], 2, '.', '');

                    $areaAverageScore[$schoolData['schoolArea'][$i]]['exam'][$key]['totalScore'] = number_format($areaScore[$schoolData['schoolArea'][$i]]['exam'][$key]['totalScore'] / $averageScoreData['areaStudentCount'][$schoolData['schoolArea'][$i]], 2, '.', '');
                    $areaAverageScore[$schoolData['schoolArea'][$i]]['exam'][$key]['excellentScore'] = number_format($areaScore[$schoolData['schoolArea'][$i]]['exam'][$key]['excellentScore'] / $averageScoreData['areaStudentCount'][$schoolData['schoolArea'][$i]], 2, '.', '');
                    $areaAverageScore[$schoolData['schoolArea'][$i]]['exam'][$key]['passScore'] = number_format($areaScore[$schoolData['schoolArea'][$i]]['exam'][$key]['passScore'] / $averageScoreData['areaStudentCount'][$schoolData['schoolArea'][$i]], 2, '.', '');
                    $areaAverageScore[$schoolData['schoolArea'][$i]]['exam'][$key]['failScore'] = number_format($areaScore[$schoolData['schoolArea'][$i]]['exam'][$key]['failScore'] / $averageScoreData['areaStudentCount'][$schoolData['schoolArea'][$i]], 2, '.', '');
                }
            }

            foreach ($scoreStatisticsData['typeScore'] as $key => $item) {
                for ($i = 0; $i < count($schoolData['schoolArea']); $i++) {
                    $areaScore[$schoolData['schoolArea'][$i]]['type'][$key]['totalScore'] = 0;
                    $areaScore[$schoolData['schoolArea'][$i]]['type'][$key]['excellentScore'] = 0;
                    $areaScore[$schoolData['schoolArea'][$i]]['type'][$key]['passScore'] = 0;
                    $areaScore[$schoolData['schoolArea'][$i]]['type'][$key]['failScore'] = 0;
                    foreach ($schoolData['schoolList'][$schoolData['schoolArea'][$i]] as $name) {
                        foreach ($averageScoreData['schoolName'] as $j => $value) {
                            if($value == $name) {
                                $areaScore[$schoolData['schoolArea'][$i]]['type'][$key]['totalScore'] = $areaScore[$schoolData['schoolArea'][$i]]['type'][$key]['totalScore'] + $item['schoolScore'][$name]['totalScore'];
                                $areaScore[$schoolData['schoolArea'][$i]]['type'][$key]['excellentScore'] = $areaScore[$schoolData['schoolArea'][$i]]['type'][$key]['excellentScore'] + $item['schoolScore'][$name]['excellentScore'];
                                $areaScore[$schoolData['schoolArea'][$i]]['type'][$key]['passScore'] = $areaScore[$schoolData['schoolArea'][$i]]['type'][$key]['passScore'] + $item['schoolScore'][$name]['passScore'];
                                $areaScore[$schoolData['schoolArea'][$i]]['type'][$key]['failScore'] = $areaScore[$schoolData['schoolArea'][$i]]['type'][$key]['failScore'] + $item['schoolScore'][$name]['failScore'];
                            }
                        }
                    }
                    $areaTypeRate[$schoolData['schoolArea'][$i]]['type'][$key]['totalRate'] = number_format($areaScore[$schoolData['schoolArea'][$i]]['type'][$key]['totalScore'] / $averageScoreData['areaStudentCount'][$schoolData['schoolArea'][$i]] / $detailTableData['typeScore'][$key], 2, '.', '');
                    $areaTypeRate[$schoolData['schoolArea'][$i]]['type'][$key]['excellentRate'] = number_format($areaScore[$schoolData['schoolArea'][$i]]['type'][$key]['excellentScore'] / $averageScoreData['areaStudentCount'][$schoolData['schoolArea'][$i]] / $detailTableData['typeScore'][$key], 2, '.', '');
                    $areaTypeRate[$schoolData['schoolArea'][$i]]['type'][$key]['passRate'] = number_format($areaScore[$schoolData['schoolArea'][$i]]['type'][$key]['passScore'] / $averageScoreData['areaStudentCount'][$schoolData['schoolArea'][$i]] / $detailTableData['typeScore'][$key], 2, '.', '');
                    $areaTypeRate[$schoolData['schoolArea'][$i]]['type'][$key]['failRate'] = number_format($areaScore[$schoolData['schoolArea'][$i]]['type'][$key]['failScore'] / $averageScoreData['areaStudentCount'][$schoolData['schoolArea'][$i]] / $detailTableData['typeScore'][$key], 2, '.', '');


                    $areaAverageScore[$schoolData['schoolArea'][$i]]['type'][$key]['totalScore'] = number_format($areaScore[$schoolData['schoolArea'][$i]]['type'][$key]['totalScore'] / $averageScoreData['areaStudentCount'][$schoolData['schoolArea'][$i]], 2, '.', '');
                    $areaAverageScore[$schoolData['schoolArea'][$i]]['type'][$key]['excellentScore'] = number_format($areaScore[$schoolData['schoolArea'][$i]]['type'][$key]['excellentScore'] / $averageScoreData['areaStudentCount'][$schoolData['schoolArea'][$i]], 2, '.', '');
                    $areaAverageScore[$schoolData['schoolArea'][$i]]['type'][$key]['passScore'] = number_format($areaScore[$schoolData['schoolArea'][$i]]['type'][$key]['passScore'] / $averageScoreData['areaStudentCount'][$schoolData['schoolArea'][$i]], 2, '.', '');
                    $areaAverageScore[$schoolData['schoolArea'][$i]]['type'][$key]['failScore'] = number_format($areaScore[$schoolData['schoolArea'][$i]]['type'][$key]['failScore'] / $averageScoreData['areaStudentCount'][$schoolData['schoolArea'][$i]], 2, '.', '');
                }
            }
        }

        $scoreStatisticsRateData = array(
            'examRate'        => $examRate, // 考核范畴分数统计
            'typeRate'        => $typeRate, // 考核层级分数统计
        );

        if($schoolData['schoolType'] != 'high') {
            $scoreStatisticsRateData['areaExamRate'] = $areaExamRate;
            $scoreStatisticsRateData['areaTypeRate'] = $areaTypeRate; // 区域考核层级得分率统计
            $scoreStatisticsRateData['areaScore'] = $areaScore; // 区域考考核范畴、考核层级分数统计
            $scoreStatisticsRateData['areaAverageScore'] = $areaAverageScore; // 区域考核范畴、考核层级平均分统计
        }

        return $scoreStatisticsRateData;
    }

    /**
     * 获取客观题统计
     */
    private function getChoiceQuestionsAnalysisData($detailTableData, $scoreStatistics)
    {

        $choiceQuestionsAnalysisData = array(); // 分数数据

        $filename = self::$queryCourse.'/'.self::CHOICE_QUESTIONS_NAME;

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
     * 获取标准差及D值统计
     */
    private function getDVauleData($schoolData, $scoreRateData, $detailTableData, $averageScoreData, $scoreStatisticsData, $scoreStatisticsRate, $studentCountRate)
    {

        $data = array(); // 数据列表

        $exam = array();
        $type = array();

        if($schoolData['schoolType'] != 'high') {
            $areaTotal = array();
            $examArea = array();
            $typeArea = array();
        }

        $cValue = array();
        $eValue = array();

        $dValue = array();

        $totalScore = $detailTableData['totalScore'];

        $baseScore['excellentScore'] = $totalScore * $scoreRateData[0];
        $baseScore['passScore'] = $totalScore * $scoreRateData[1];


        foreach($averageScoreData['schoolName'] as $schoolName){
            foreach ($detailTableData['examName'] as $name) {
                $exam['total'][$name]['totalScore'] = 0;
                $exam['total'][$name]['excellentScore'] = 0;
                $exam['total'][$name]['passScore'] = 0;
                $exam['total'][$name]['failScore'] = 0;

                $exam['schoolScore'][$schoolName][$name]['totalScore'] = 0;
                $exam['schoolScore'][$schoolName][$name]['excellentScore'] = 0;
                $exam['schoolScore'][$schoolName][$name]['passScore'] = 0;
                $exam['schoolScore'][$schoolName][$name]['failScore'] = 0;
            }
            foreach ($detailTableData['typeName'] as $name) {
                $type['total'][$name]['totalScore'] = 0;
                $type['total'][$name]['excellentScore'] = 0;
                $type['total'][$name]['passScore'] = 0;
                $type['total'][$name]['failScore'] = 0;

                $type['schoolScore'][$schoolName][$name]['totalScore'] = 0;
                $type['schoolScore'][$schoolName][$name]['excellentScore'] = 0;
                $type['schoolScore'][$schoolName][$name]['passScore'] = 0;
                $type['schoolScore'][$schoolName][$name]['failScore'] = 0;
            }
        }

        if($schoolData['schoolType'] != 'high') {
            foreach ($schoolData['schoolArea'] as $area) {
                $areaTotal[$area]['total']['totalScore'] = 0;

                $areaTotal[$area]['schoolArea']['totalScore'] = 0;
                $areaTotal[$area]['schoolArea']['excellentScore'] = 0;
                $areaTotal[$area]['schoolArea']['passScore'] = 0;
                $areaTotal[$area]['schoolArea']['failScore'] = 0;
                foreach ($detailTableData['examName'] as $name) {
                    $examArea[$area]['total'][$name]['totalScore'] = 0;
                    $examArea[$area]['total'][$name]['excellentScore'] = 0;
                    $examArea[$area]['total'][$name]['passScore'] = 0;
                    $examArea[$area]['total'][$name]['failScore'] = 0;
                }
                foreach ($detailTableData['typeName'] as $name) {
                    $typeArea[$area]['total'][$name]['totalScore'] = 0;
                    $typeArea[$area]['total'][$name]['excellentScore'] = 0;
                    $typeArea[$area]['total'][$name]['passScore'] = 0;
                    $typeArea[$area]['total'][$name]['failScore'] = 0;
                }
            }
        }

        foreach($scoreStatisticsData['studentScore']['score'] as $key => $score){

            if($schoolData['schoolType'] != 'high') {
                $areaTotal[$averageScoreData['schoolArea'][$scoreStatisticsData['studentScore']['school'][$key]]]['total']['totalScore'] = $areaTotal[$averageScoreData['schoolArea'][$scoreStatisticsData['studentScore']['school'][$key]]]['total']['totalScore'] + pow($score - $averageScoreData['amountAverageScore'] , 2);

                $areaTotal[$averageScoreData['schoolArea'][$scoreStatisticsData['studentScore']['school'][$key]]]['schoolArea']['totalScore'] = $areaTotal[$averageScoreData['schoolArea'][$scoreStatisticsData['studentScore']['school'][$key]]]['schoolArea']['totalScore'] + pow($score - $studentCountRate['areaAverageScore'][$scoreStatisticsData['studentScore']['schoolArea'][$key]] , 2);

                if($score >= $baseScore['excellentScore']) {
                    $areaTotal[$averageScoreData['schoolArea'][$scoreStatisticsData['studentScore']['school'][$key]]]['schoolArea']['excellentScore'] = $areaTotal[$averageScoreData['schoolArea'][$scoreStatisticsData['studentScore']['school'][$key]]]['schoolArea']['excellentScore'] + pow($score - $studentCountRate['areaAverageScore'][$averageScoreData['schoolArea'][$scoreStatisticsData['studentScore']['school'][$key]]] , 2);
                } elseif($score >= $baseScore['passScore'] && $score < $baseScore['excellentScore']) {
                    $areaTotal[$averageScoreData['schoolArea'][$scoreStatisticsData['studentScore']['school'][$key]]]['schoolArea']['passScore'] = $areaTotal[$averageScoreData['schoolArea'][$scoreStatisticsData['studentScore']['school'][$key]]]['schoolArea']['passScore'] + pow($score - $studentCountRate['areaAverageScore'][$averageScoreData['schoolArea'][$scoreStatisticsData['studentScore']['school'][$key]]] , 2);
                } else {
                    $areaTotal[$averageScoreData['schoolArea'][$scoreStatisticsData['studentScore']['school'][$key]]]['schoolArea']['failScore'] = $areaTotal[$averageScoreData['schoolArea'][$scoreStatisticsData['studentScore']['school'][$key]]]['schoolArea']['failScore'] + pow($score - $studentCountRate['areaAverageScore'][$averageScoreData['schoolArea'][$scoreStatisticsData['studentScore']['school'][$key]]] , 2);
                }
            }

            foreach ($scoreStatisticsData['studentScore']['exam'][$key] as $item => $value) {
                $exam['total'][$item]['totalScore'] = $exam['total'][$item]['totalScore'] + pow($value - $scoreStatisticsData['examAverageScore'][$item]['total']['totalScore'] , 2);

                $exam['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]][$item]['totalScore'] = $exam['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]][$item]['totalScore'] + pow($value - $scoreStatisticsData['examAverageScore'][$item]['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]]['totalScore'] , 2);

                if($schoolData['schoolType'] != 'high') {
                    $examArea[$scoreStatisticsData['studentScore']['schoolArea'][$key]]['total'][$item]['totalScore'] = $examArea[$scoreStatisticsData['studentScore']['schoolArea'][$key]]['total'][$item]['totalScore'] + pow($value - $scoreStatisticsData['examAverageScore'][$item]['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]]['totalScore'] , 2);
                }

                if($score >= $baseScore['excellentScore']) {
                    $exam['total'][$item]['excellentScore'] = $exam['total'][$item]['excellentScore'] + pow($value - $scoreStatisticsData['examAverageScore'][$item]['total']['excellentScore'] , 2);

                    $exam['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]][$item]['excellentScore'] = $exam['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]][$item]['excellentScore'] + pow($value - $scoreStatisticsData['examAverageScore'][$item]['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]]['excellentScore'] , 2);

                    if($schoolData['schoolType'] != 'high') {
                        $examArea[$scoreStatisticsData['studentScore']['schoolArea'][$key]]['total'][$item]['excellentScore'] = $examArea[$scoreStatisticsData['studentScore']['schoolArea'][$key]]['total'][$item]['excellentScore'] + pow($value - $scoreStatisticsData['examAverageScore'][$item]['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]]['excellentScore'] , 2);
                    }
                
                } elseif($score >= $baseScore['passScore'] && $score < $baseScore['excellentScore']) {
                    $exam['total'][$item]['passScore'] = $exam['total'][$item]['passScore'] + pow($value - $scoreStatisticsData['examAverageScore'][$item]['total']['passScore'] , 2);

                    $exam['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]][$item]['passScore'] = $exam['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]][$item]['passScore'] + pow($value - $scoreStatisticsData['examAverageScore'][$item]['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]]['passScore'] , 2);

                    if($schoolData['schoolType'] != 'high') {
                        $examArea[$scoreStatisticsData['studentScore']['schoolArea'][$key]]['total'][$item]['passScore'] = $examArea[$scoreStatisticsData['studentScore']['schoolArea'][$key]]['total'][$item]['passScore'] + pow($value - $scoreStatisticsData['examAverageScore'][$item]['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]]['passScore'] , 2);
                    }
                } else {
                    $exam['total'][$item]['failScore'] = $exam['total'][$item]['failScore'] + pow($value - $scoreStatisticsData['examAverageScore'][$item]['total']['failScore'] , 2);

                    $exam['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]][$item]['failScore'] = $exam['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]][$item]['failScore'] + pow($value - $scoreStatisticsData['examAverageScore'][$item]['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]]['failScore'] , 2);

                    if($schoolData['schoolType'] != 'high') {
                        $examArea[$scoreStatisticsData['studentScore']['schoolArea'][$key]]['total'][$item]['failScore'] = $examArea[$scoreStatisticsData['studentScore']['schoolArea'][$key]]['total'][$item]['failScore'] + pow($value - $scoreStatisticsData['examAverageScore'][$item]['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]]['failScore'] , 2);
                    }
                }
            }

            foreach ($scoreStatisticsData['studentScore']['type'][$key] as $item => $value) {
                $type['total'][$item]['totalScore'] = $type['total'][$item]['totalScore'] + pow($value - $scoreStatisticsData['typeAverageScore'][$item]['total']['totalScore'] , 2);

                $type['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]][$item]['totalScore'] = $type['schoolScore'][$scoreStatisticsData['studentScore']['school']][$item]['totalScore'] + pow($value - $scoreStatisticsData['typeAverageScore'][$item]['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]]['totalScore'] , 2);

                if($schoolData['schoolType'] != 'high') {
                    $typeArea[$scoreStatisticsData['studentScore']['schoolArea'][$key]]['total'][$item]['totalScore'] = $typeArea[$scoreStatisticsData['studentScore']['schoolArea'][$key]]['total'][$item]['totalScore'] + pow($value - $scoreStatisticsData['typeAverageScore'][$item]['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]]['totalScore'] , 2);
                }

                if($score >= $baseScore['excellentScore']) {
                    $type['total'][$item]['excellentScore'] = $type['total'][$item]['excellentScore'] + pow($value - $scoreStatisticsData['typeAverageScore'][$item]['total']['excellentScore'] , 2);

                    $type['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]][$item]['excellentScore'] = $type['schoolScore'][$scoreStatisticsData['studentScore']['school']][$item]['excellentScore'] + pow($value - $scoreStatisticsData['typeAverageScore'][$item]['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]]['excellentScore'] , 2);

                    if($schoolData['schoolType'] != 'high') {
                        $typeArea[$scoreStatisticsData['studentScore']['schoolArea'][$key]]['total'][$item]['failScore'] = $typeArea[$scoreStatisticsData['studentScore']['schoolArea'][$key]]['total'][$item]['failScore'] + pow($value - $scoreStatisticsData['typeAverageScore'][$item]['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]]['failScore'] , 2);
                    }
                
                } elseif($score >= $baseScore['passScore'] && $score < $baseScore['excellentScore']) {
                    $type['total'][$item]['passScore'] = $type['total'][$item]['passScore'] + pow($value - $scoreStatisticsData['typeAverageScore'][$item]['total']['passScore'] , 2);

                    $type['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]][$item]['passScore'] = $type['schoolScore'][$scoreStatisticsData['studentScore']['school']][$item]['passScore'] + pow($value - $scoreStatisticsData['typeAverageScore'][$item]['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]]['passScore'] , 2);

                    if($schoolData['schoolType'] != 'high') {
                        $typeArea[$scoreStatisticsData['studentScore']['schoolArea'][$key]]['total'][$item]['failScore'] = $typeArea[$scoreStatisticsData['studentScore']['schoolArea'][$key]]['total'][$item]['failScore'] + pow($value - $scoreStatisticsData['typeAverageScore'][$item]['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]]['failScore'] , 2);
                    }
                } else {
                    $type['total'][$item]['failScore'] = $type['total'][$item]['failScore'] + pow($value - $scoreStatisticsData['typeAverageScore'][$item]['total']['failScore'] , 2);

                    $type['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]][$item]['failScore'] = $type['schoolScore'][$scoreStatisticsData['studentScore']['school']][$item]['failScore'] + pow($value - $scoreStatisticsData['typeAverageScore'][$item]['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]]['failScore'] , 2);

                    if($schoolData['schoolType'] != 'high') {
                        $typeArea[$scoreStatisticsData['studentScore']['schoolArea'][$key]]['total'][$item]['failScore'] = $typeArea[$scoreStatisticsData['studentScore']['schoolArea'][$key]]['total'][$item]['failScore'] + pow($value - $scoreStatisticsData['typeAverageScore'][$item]['schoolScore'][$scoreStatisticsData['studentScore']['school'][$key]]['failScore'] , 2);
                    }
                }
            }
        }

        $total = number_format($total, 2, '.', '');

        foreach ($exam['total'] as $itemName => $item) {
            foreach ($item as $key => $value) {
                $exam['total'][$itemName][$key] = number_format($value, 2, '.', '');
            }
            
        }

        foreach ($exam['schoolScore'] as $schoolName => $schoolItem) {
            foreach ($schoolItem as $itemName => $item) {
                foreach ($item as $key => $value) {
                    $exam['schoolScore'][$schoolName][$itemName][$key] = number_format($value, 2, '.', '');
                }
            }
        }

        foreach ($type['total'] as $itemName => $item) {
            foreach ($item as $key => $value) {
                $type['total'][$itemName][$key] = number_format($value, 2, '.', '');
            }
            
        }

        foreach ($type['schoolScore'] as $schoolName => $schoolItem) {
            foreach ($schoolItem as $itemName => $item) {
                foreach ($item as $key => $value) {
                    $type['schoolScore'][$schoolName][$itemName][$key] = number_format($value, 2, '.', '');
                }
            }
        }

        if($schoolData['schoolType'] != 'high') {
            foreach ($areaTotal as $areaName => $area) {
                foreach ($area as $itemName => $item) {
                    foreach ($item as $key => $value) {
                        $areaTotal[$areaName][$itemName][$key] = number_format($value, 2, '.', '');
                    }
                }
            }
        }

        foreach ($exam as $examItem => $item) {
            if($examItem == 'total') {
                foreach ($item as $itemName => $itemList) {
                    foreach ($itemList as $key => $value) {
                        if($key == 'totalScore') {
                            $eValue['exam'][$examItem][$itemName][$key] = number_format(sqrt($value / $scoreStatisticsData['count']['totalCount']), 2, '.', '');
                        } elseif($key == 'excellentScore') {
                            $eValue['exam'][$examItem][$itemName][$key] = number_format(sqrt($value / $scoreStatisticsData['count']['excellentCount']), 2, '.', '');
                        } elseif($key == 'passScore') {
                            $eValue['exam'][$examItem][$itemName][$key] = number_format(sqrt($value / $scoreStatisticsData['count']['passCount']), 2, '.', '');
                        } elseif($key == 'failScore') {
                            $eValue['exam'][$examItem][$itemName][$key] = number_format(sqrt($value / $scoreStatisticsData['count']['failCount']), 2, '.', '');
                        }
                    }
                }

            } elseif($examItem == 'schoolScore') {
                foreach ($item as $schoolName => $schoolList) {
                    foreach ($schoolList as $itemName => $itemList) {
                        foreach ($itemList as $key => $value) {
                            if($key == 'totalScore') {
                                $cValue['exam'][$examItem][$schoolName][$itemName][$key] = number_format(sqrt($value / $scoreStatisticsData['schoolCount'][$schoolName]['totalCount']), 2, '.', '');
                            } elseif($key == 'excellentScore') {
                                $cValue['exam'][$examItem][$schoolName][$itemName][$key] = number_format(sqrt($value / $scoreStatisticsData['schoolCount'][$schoolName]['excellentCount']), 2, '.', '');
                            } elseif($key == 'passScore') {
                                $cValue['exam'][$examItem][$schoolName][$itemName][$key] = number_format(sqrt($value / $scoreStatisticsData['schoolCount'][$schoolName]['passCount']), 2, '.', '');
                            } elseif($key == 'failScore') {
                                $cValue['exam'][$examItem][$schoolName][$itemName][$key] = number_format(sqrt($value / $scoreStatisticsData['schoolCount'][$schoolName]['failCount']), 2, '.', '');
                            }
                        }
                    }
                }
            }
        }

        foreach ($type as $typeItem => $item) {
            if($typeItem == 'total') {
                foreach ($item as $itemName => $itemList) {
                    foreach ($itemList as $key => $value) {
                        if($key == 'totalScore') {
                            $eValue['type'][$typeItem][$itemName][$key] = number_format(sqrt($value / $scoreStatisticsData['count']['totalCount']), 2, '.', '');
                        } elseif($key == 'excellentScore') {
                            $eValue['type'][$typeItem][$itemName][$key] = number_format(sqrt($value / $scoreStatisticsData['count']['excellentCount']), 2, '.', '');
                        } elseif($key == 'passScore') {
                            $eValue['type'][$typeItem][$itemName][$key] = number_format(sqrt($value / $scoreStatisticsData['count']['passCount']), 2, '.', '');
                        } elseif($key == 'failScore') {
                            $eValue['type'][$typeItem][$itemName][$key] = number_format(sqrt($value / $scoreStatisticsData['count']['failCount']), 2, '.', '');
                        }
                    }
                }

            } elseif($typeItem == 'schoolScore') {
                foreach ($item as $schoolName => $schoolList) {
                    foreach ($schoolList as $itemName => $itemList) {
                        foreach ($itemList as $key => $value) {
                            if($key == 'totalScore') {
                                $cValue['type'][$typeItem][$schoolName][$itemName][$key] = number_format(sqrt($value / $scoreStatisticsData['schoolCount'][$schoolName]['totalCount']), 2, '.', '');
                            } elseif($key == 'excellentScore') {
                                $cValue['type'][$typeItem][$schoolName][$itemName][$key] = number_format(sqrt($value / $scoreStatisticsData['schoolCount'][$schoolName]['excellentCount']), 2, '.', '');
                            } elseif($key == 'passScore') {
                                $cValue['type'][$typeItem][$schoolName][$itemName][$key] = number_format(sqrt($value / $scoreStatisticsData['schoolCount'][$schoolName]['passCount']), 2, '.', '');
                            } elseif($key == 'failScore') {
                                $cValue['type'][$typeItem][$schoolName][$itemName][$key] = number_format(sqrt($value / $scoreStatisticsData['schoolCount'][$schoolName]['failCount']), 2, '.', '');
                            }
                        }
                    }
                }
            }
        }

        if($schoolData['schoolType'] != 'high') {
            foreach ($schoolData['schoolArea'] as $area) {
                $eValue['areaTotal'][$area] = number_format(sqrt($areaTotal[$area]['total']['totalScore'] / $scoreStatisticsData['count']['totalCount']), 2, '.', '');

                $cValue['areaTotal'][$area]['schoolArea']['totalScore'] = number_format(sqrt($areaTotal[$area]['schoolArea']['totalScore'] / $averageScoreData['areaStudentCount'][$area]), 2, '.', '');
                $cValue['areaTotal'][$area]['schoolArea']['excellentScore'] = number_format(sqrt($areaTotal[$area]['schoolArea']['excellentScore'] / $averageScoreData['areaStudentCount'][$area]), 2, '.', '');
                $cValue['areaTotal'][$area]['schoolArea']['passScore'] = number_format(sqrt($areaTotal[$area]['schoolArea']['passScore'] / $averageScoreData['areaStudentCount'][$area]), 2, '.', '');
                $cValue['areaTotal'][$area]['schoolArea']['failScore'] = number_format(sqrt($areaTotal[$area]['schoolArea']['failScore'] / $averageScoreData['areaStudentCount'][$area]), 2, '.', '');
            }
        }

        foreach ($cValue['exam']['schoolScore'] as $schoolName => $schoolList) {
            foreach ($schoolList as $itemName => $itemList) {
                foreach ($itemList as $key => $value) {
                    $chenhong[] = $scoreStatisticsData['schoolAverageScore'][$schoolName][$key] - $averageScoreData['amountAverageScore'];
                    $chenhong[] = ($scoreStatisticsData['schoolCount'][$schoolName]['totalCount']-1) * pow($value ,2);
                    $chenhong[] = ($scoreStatisticsData['count']['totalCount']-1) * pow($eValue['exam']['total'][$itemName][$key] ,2);
                    $chenhong[] = $scoreStatisticsData['schoolCount'][$schoolName]['totalCount'] + $scoreStatisticsData['count']['totalCount'] - 2;
                    $chenhong[] = 'end';
                    if($key == 'totalScore') {
                        $dValue['exam']['schoolScore'][$schoolName][$itemName][$key] = number_format(($scoreStatisticsData['schoolAverageScore'][$schoolName][$key] - $averageScoreData['amountAverageScore']) / sqrt(((($scoreStatisticsData['schoolCount'][$schoolName]['totalCount']-1) * pow($value ,2)) + (($scoreStatisticsData['count']['totalCount']-1) * pow($eValue['exam']['total'][$itemName][$key] ,2))) / ($scoreStatisticsData['schoolCount'][$schoolName]['totalCount'] + $scoreStatisticsData['count']['totalCount'] - 2)), 2, '.', '');
                    } elseif($key == 'excellentScore'){
                        $dValue['exam']['schoolScore'][$schoolName][$itemName][$key] = number_format(($scoreStatisticsData['schoolAverageScore'][$schoolName][$key] - $averageScoreData['amountAverageScore']) / sqrt(((($scoreStatisticsData['schoolCount'][$schoolName]['excellentCount']-1) * pow($value ,2)) + (($scoreStatisticsData['count']['excellentCount']-1) * pow($eValue['total'][$itemName][$key] ,2))) / ($scoreStatisticsData['schoolCount'][$schoolName]['excellentCount'] + $scoreStatisticsData['count']['excellentCount'] - 2)), 2, '.', '');
                    } elseif($key == 'passScore'){
                        $dValue['exam']['schoolScore'][$schoolName][$itemName][$key] = number_format(($scoreStatisticsData['schoolAverageScore'][$schoolName][$key] - $averageScoreData['amountAverageScore']) / sqrt(((($scoreStatisticsData['schoolCount'][$schoolName]['passCount']-1) * pow($value ,2)) + (($scoreStatisticsData['count']['passCount']-1) * pow($eValue['total'][$itemName][$key] ,2))) / ($scoreStatisticsData['schoolCount'][$schoolName]['passCount'] + $scoreStatisticsData['count']['passCount'] - 2)), 2, '.', '');
                    } elseif($key == 'failScore'){
                        $dValue['exam']['schoolScore'][$schoolName][$itemName][$key] = number_format(($scoreStatisticsData['schoolAverageScore'][$schoolName][$key] - $averageScoreData['amountAverageScore']) / sqrt(((($scoreStatisticsData['schoolCount'][$schoolName]['failCount']-1) * pow($value ,2)) + (($scoreStatisticsData['count']['failCount']-1) * pow($eValue['total'][$itemName][$key] ,2))) / ($scoreStatisticsData['schoolCount'][$schoolName]['failCount'] + $scoreStatisticsData['count']['failCount'] - 2)), 2, '.', '');
                    }
                }
            }
        }
        
        /*var_export($chenhong);
        var_export('=================end==============');
        var_export('=================chenhong print==============');*/
        /*foreach ($cValue['type']['schoolScore'] as $schoolName => $schoolList) {
            foreach ($schoolList as $itemName => $itemList) {
                foreach ($itemList as $key => $value) {
                    $dValue['type']['schoolScore'][$schoolName][$itemName][$key] = number_format(($scoreStatisticsData['typeAverageScore'][$itemName]['schoolScore'][$schoolName]['total']['totalScore'] - $value) / sqrt(((pow($scoreStatisticsData['schoolCount'][$schoolName]['totalCount'] * $value ,2)) + (pow($scoreStatisticsData['count']['totalCount'] * $eValue['total'] ,2) ) / ($scoreStatisticsData['schoolCount'][$schoolName]['totalCount'] + $scoreStatisticsData['count']['totalCount'] - 2))), 2, '.', '');
                }
            }
        }*/
 

        $data = array(
            'exam'        => $exam, // 考核范畴分数统计
            'type'        => $type, // 考核层级分数统计
            'cValue'      => $cValue, // 对比组标准差统计
            'eValue'      => $eValue, // 对照组标准差统计
            'dValue'      => $dValue, // D值
        );

        /*if($schoolData['schoolType'] != 'high') {
            $data['areaTotal'] = $areaTotal; // 区域全体分数统计
            $data['examArea'] = $examArea; // 区域考核范畴分数统计
            $data['typeArea'] = $typeArea; // 区域考核层级分数统计
        }*/

        /*var_export('===============data[dValue] start=================');
        var_export($data['dValue']);
        var_export('===============data[dValue] end=================');
        var_export('===============data[cValue] start=================');
        var_export($data['cValue']);
        var_export('===============data[cValue] end=================');
        var_export('===============data[eValue] start=================');
        var_export($data['eValue']);
        var_export('===============data[eValue] end=================');*/
        
        // var_export('===============chen=================');
        // var_export($data['dValue']);
        // var_export($chenhong);
        // var_export('===============hong=================');
        // var_export($studentCountRate);
        // var_export('===============love=================');
        // var_export($scoreStatisticsRate);
        // var_export('================you================');
        // var_export($scoreStatisticsData);
        // var_export($scoreData);

        return $data;
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

        $folderArr = explode("_" , $foldername);

        if($folderArr[2] == '高三年级') {
            self::$grade      = 'h3';
            self::$schoolType = 'high';
        }
        elseif($folderArr[2] == '高二年级') {
            self::$grade      = 'h2';
            self::$schoolType = 'high';
        }
        elseif($folderArr[2] == '高一年级') {
            self::$grade      = 'h1';
            self::$schoolType = 'high';
        }
        elseif($folderArr[2] == '九年级') {
            self::$grade      = 'm9';
            self::$schoolType = 'middle';
        }
        elseif($folderArr[2] == '八年级') {
            self::$grade      = 'm8';
            self::$schoolType = 'middle';
        }
        elseif($folderArr[2] == '七年级') {
            self::$grade      = 'm7';
            self::$schoolType = 'middle';
        }
        elseif($folderArr[2] == '六年级') {
            self::$grade      = 'j6';
            self::$schoolType = 'junior';
        }
        elseif($folderArr[2] == '五年级') {
            self::$grade      = 'j5';
            self::$schoolType = 'junior';
        }
        elseif($folderArr[2] == '四年级') {
            self::$grade      = 'j4';
            self::$schoolType = 'junior';
        }

        $schoolObj = new \Admin\Model\SchoolData();
        $schoolData = $schoolObj->getSchoolData(self::$schoolType);

        $courseObj = new \Admin\Model\CourseData();
        $courseData = $courseObj->getCourseData($date, $foldername);

        self::$courseAmount = count($courseData);

        $rateObj = new \Admin\Model\ScoreRateData();
        $scoreRateData = $rateObj->getScoreRateData(self::$queryCourse);

        $detailTableObj = new \Admin\Model\DetailTableData();
        $detailTableData = $detailTableObj->getDetailTableData($date, $foldername, $course);

        $courseAnalysisData = self::getCourseAnalysisData();

        $averageScoreData = self::getAverageData($schoolData);

        $studentCountRate = self::getStudentCountRateData($schoolData, $averageScoreData, $scoreRateData, $detailTableData);

        $scoreStatisticsData = self::getScoreStatisticsData($schoolData, $scoreRateData, $detailTableData, $averageScoreData);
        $scoreStatisticsRate = self::getScoreStatisticsRateData($schoolData, $detailTableData, $averageScoreData, $scoreStatisticsData, $studentCountRate);

        // $choiceQuestionsAnalysis = self::getChoiceQuestionsAnalysisData();

        $dVauleData = self::getDVauleData($schoolData, $scoreRateData, $detailTableData, $averageScoreData, $scoreStatisticsData, $scoreStatisticsRate, $studentCountRate);

        // var_export($scoreStatisticsData);
        // var_export('===============scoreStatisticsRate=================');
        // var_export($scoreStatisticsRate);
        // var_export('===============chen=================');
        // var_export($data['dValue']);
        // var_dump($scoreRateData);
        // var_export('===============hong=================');
        // var_dump($schoolData);
        // var_export('===============love=================');
        // var_dump($courseData);


        // var_export($studentCountRate);
        // var_export($scoreStatisticsData);
        // var_dump($detailTableData);
        // var_export($scoreStatisticsData);
        // var_export('=============赵========================');
        // var_export($scoreStatisticsRate);
        // var_export('=============赵========================');
        // var_export($detailTableData);
        // var_export('=====================================');
        // var_export($studentCountRate);
        // var_export('=====================================');
        // var_export($averageScoreData);

        /*$data = array(
            'scoreRate' => $scoreRateData, // 分数率
            'detailTable' => $detailTableData, // 双向明细表（包括：总分）
            'courseAnalysis' => $courseAnalysisData, // 学科分析（包括：难度、区分度、信度）
            'averageScore' => $averageScoreData, // 平均分（包括：学校列表、各学校参加考试总人数、全区参加考试总人数、各学校平均分、全区平均分）
            'studentCountRate' => $studentCountRate, // 学生优秀率统计（包括：全区优秀人数、全区优秀率、全区累计优秀人数、全区累计优秀率）
            'scoreStatisticsRate' => $scoreStatisticsRate, // 分数率统计（包括：总体、各学校考核范畴与考核层级各项优秀率统计）
            'choiceQuestionsAnalysis' => $choiceQuestionsAnalysis // 客观题统计（包括：所有单选题各项指数）
        );

        return $data;*/

    }

}

?>