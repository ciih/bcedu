<?php

/**
 * 获取Excel数据
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class ExcelData {

    /**
     * 考试信息
     * @var array
     */
    protected static $examInfo;

    /**
     * Excel表主路径
     * @var string
     */
    protected static $basePath;

    /**
     * 科目
     * @var string
     */
    protected static $course;

    /**
     * 打开excel表对象
     * @var obj
     */
    protected static $excelFile;

    /**
     * 获取分数率
     * @var array
     */
    protected static $baseScoreRate;

    /**
     * 获取分数线
     * @var array
     */
    protected static $baseScore;

    /**
     * 获取学校列表
     * @var array
     */
    protected static $schoolInfo;

    /**
     * 获取考试课程列表
     * @var array
     */
    protected static $courseList;

    /**
     * 获取学生分数列表
     * @var array
     */
    protected static $studentScoreList;

    /**
     * 获取小题分列表
     * @var array
     */
    protected static $itemScoreList;

    /**
     * 获取学生所在学校列表
     * @var array
     */
    protected static $studentSchoolList;

    /**
     * 获取科目双向细目表
     * @var array
     */
    protected static $detailTable;

    /**
     * 获取全区各标准人数
     * @var array
     */
    protected static $totalCount;

    /**
     * 获取全校各标准人数
     * @var array
     */
    protected static $totalSchoolCount;

    /**
     * 学科表名
     * @var string
     */
    protected static $courseAnalysisName = '学科分析';

    /**
     * 综合指标表名
     * @var string
     */
    protected static $comprehensiveIndicators = '综合指标';

    /**
     * 小题分表名
     * @var string
     */
    protected static $itemScore = '小题分';

    /**
     * 选择题分析表名(未加科目)
     * @var string
     */
    protected static $choiceQuestionsName = '小题分析';

    /**
     * 构造
     * @param $date 日期
     * @param $foldername 文件夹名称（包含信息：学年、学期、年级、考试名称）
     * @param $course 查询科目
     */
    function __construct($examInfoData, $schoolInfoData, $baseScoreRateData, $courseListData, $detailTableData, $course)
    {
        self::$examInfo = $examInfoData;
        self::$basePath = self::$examInfo['rootDir'].self::$examInfo['uploadDate'].'/'.self::$examInfo['fullname'].'/';
        self::$course = $course;

        self::$schoolInfo = $schoolInfoData;

        self::$baseScoreRate = $baseScoreRateData;

        self::$courseList = $courseListData;

        self::$detailTable = $detailTableData;

        self::$baseScore['excellentScore'] = self::$detailTable['totalScore'] * (self::$baseScoreRate[0] / 100);
        self::$baseScore['passScore'] = self::$detailTable['totalScore'] * (self::$baseScoreRate[1] / 100);

        self::$excelFile = new \Admin\Model\ExeclFile();
    }

    /**
     * 获取学科分析
     */
    public function getCourseAnalysisData()
    {
        $filePath = self::$basePath.self::$examInfo['mainDir'].'/';
        $filename = self::$courseAnalysisName;

        $excelData = self::$excelFile->openExcel($filePath, $filename);

        $difficulty = array(); // 全卷难度
        $distinguish = array(); // 全卷区分度
        $reliability = array(); // 全卷信度

        $difficultyTxt = array(); // 全卷难度评级
        $distinguishTxt = array(); // 全卷区分度评级
        $reliabilityTxt = array(); // 全卷信度评级

        $queryCourse = self::$course;
        $course = ''; // 科目名称

        $courseAnalysisData = array(); // 学科分析数据

        foreach($excelData->getRowIterator() as $kr => $row)
        {
            $cellIterator = $row->getCellIterator();
            if ($kr > 1) {
                foreach($cellIterator as $kc => $cell){
                    if ($kc == 0) {
                        $course = $cell->getValue();
                    }
                    if (($kc == 4)) {
                        $difficulty[$course][] = number_format($cell->getValue(), 2, '.', '');
                        if ($difficulty[$course] >= 0.9) {
                            $difficultyTxt[$course][] = '容易';
                        } elseif ($difficulty[$course] >= 0.7 && $difficulty[$course] < 0.9) {
                            $difficultyTxt[$course][] = '较易';
                        } elseif ($difficulty[$course] >= 0.4 && $difficulty[$course] < 0.7) {
                            $difficultyTxt[$course][] = '中等';
                        } elseif ($difficulty[$course] < 0.4) {
                            $difficultyTxt[$course][] = '偏难';
                        }
                    }
                    if (($kc == 7)) {
                        $distinguish[$course][] = number_format($cell->getValue(), 2, '.', '');
                        if ($distinguish[$course] >= 0.4) {
                            $distinguishTxt[$course][] = '较高';
                        } elseif ($distinguish[$course] >= 0.3 && $distinguish[$course] < 0.4) {
                            $distinguishTxt[$course][] = '中等';
                        } elseif ($distinguish[$course] >= 0.2 && $distinguish[$course] < 0.3) {
                            $distinguishTxt[$course][] = '一般';
                        } elseif ($distinguish[$course] < 0.2) {
                            $distinguishTxt[$course][] = '较低';
                        }
                    }
                    if (($kc == 10)) {
                        $reliability[$course][] = number_format($cell->getValue(), 2, '.', '');
                        if ($reliability[$course] >= 0.9) {
                            $reliabilityTxt[$course][] = '优秀';
                        } elseif ($reliability[$course] >= 0.7 && $reliability[$course] < 0.9) {
                            $reliabilityTxt[$course][] = '较好';
                        } elseif ($reliability[$course] < 0.7) {
                            $reliabilityTxt[$course][] = '一般';
                        }
                    }
                }
            }
        }

        $courseAnalysisData = array(
            'difficulty'     => $difficulty[$queryCourse][0],
            'difficultyTxt'  => $difficultyTxt[$queryCourse][0],
            'distinguish'    => $distinguish[$queryCourse][0],
            'distinguishTxt' => $distinguishTxt[$queryCourse][0],
            'reliability'    => $reliability[$queryCourse][0],
            'reliabilityTxt' => $reliabilityTxt[$queryCourse][0],
        );

        return $courseAnalysisData;
    }

    /**
     * 获取综合指标
     */
    public function getComprehensiveIndicatorsData()
    {
        $filePath = self::$basePath.self::$examInfo['mainDir'].'/'.self::$course.'/';
        $filename = self::$comprehensiveIndicators;

        $excelData = self::$excelFile->openExcel($filePath, $filename);

        $keys = array(); // 平均分字段名

        $totalStudentCount = 0; // 全区参加考试人数
        $totalSchoolStudentCount = array(); // 全校参加考试人数
        $totalAverageScore = 0; // 全区平均分
        $totalSchoolAverageScore = array(); // 全校平均分
        $totalHighestScore = 0; // 全区最高分
        $totalSchoolHighestScore = array(); // 全校最高分
        $totalLowestScore = 0; // 全区最低分
        $totalSchoolLowestScore = array(); // 全校最低分

        $comprehensiveIndicatorsData = array(); // 平均分对比数据

        foreach($excelData->getRowIterator() as $kr => $row){
            $cellIterator = $row->getCellIterator();

            if ($kr > 1) {
                foreach($cellIterator as $kc => $cell){
                    if ($kc == 0) {
                        $schoolName = $cell->getValue();
                    }
                    if ($kc == 2) {
                        $totalSchoolStudentCount[$schoolName] = $cell->getValue();
                    }
                    if ($kc == 3) {
                        $totalSchoolAverageScore[$schoolName] = number_format($cell->getValue(), 2, '.', '');
                    }
                    if ($kc == 5) {
                        $totalSchoolHighestScore[$schoolName] = $cell->getValue();
                    }
                    if ($kc == 6) {
                        $totalSchoolLowestScore[$schoolName] = $cell->getValue();
                    }
                }
            }
        }

        $totalStudentCount = array_pop($totalSchoolStudentCount);
        $totalAverageScore = array_pop($totalSchoolAverageScore);
        $totalHighestScore = array_pop($totalSchoolHighestScore);
        $totalLowestScore  = array_pop($totalSchoolLowestScore);

        $comprehensiveIndicatorsData = array(
            'totalStudentCount'       => $totalStudentCount, // 全区参加考试人数
            'totalSchoolStudentCount' => $totalSchoolStudentCount, // 全校参加考试人数
            'totalAverageScore'       => $totalAverageScore, // 全区平均分
            'totalSchoolAverageScore' => $totalSchoolAverageScore, // 全校平均分
            'totalHighestScore'       => $totalHighestScore, // 全区最高分
            'totalSchoolHighestScore' => $totalSchoolHighestScore, // 全校最高分
            'totalLowestScore'        => $totalLowestScore, // 全区最低分
            'totalSchoolLowestScore'  => $totalSchoolLowestScore, // 全校最低分
        );

        return $comprehensiveIndicatorsData;
    }

    /**
     * 获取学生分数(小题分)
     */
    public function getStudentScoreData()
    {
        $filePath = self::$basePath.self::$examInfo['mainDir'].'/'.self::$course.'/';
        $filename = self::$itemScore;

        $excelData = self::$excelFile->openExcel($filePath, $filename);

        $totalCount = array(); // 统计全区各标准人数
        $totalSchoolCount = array(); // 统计学校各标准人数

        $cumulativeCount = array(); // 统计累积人数

        $totalRate = array(); // 所占百分比
        $cumulativeRate = array(); // 累计所占百分比

        $keys = array(); // 字段名

        $detailTable = self::$detailTable; // 双向明细表数据
        $baseScore = self::$baseScore; // 分数基准
        $schoolList = self::$schoolInfo['schoolList']; // 学校列表

        $studentSchoolList = array();
        $studentScoreList = array();
        $itemScoreList = array();

        $studentScoreData = array(); // 学生分数

        foreach($excelData->getRowIterator() as $kr => $row){
            $cellIterator = $row->getCellIterator();

            if($kr == 1) {
                foreach($cellIterator as $kc => $cell){
                    if($kc > 5) {
                        $keys[] = $cell->getValue();
                    }
                }

            } elseif($kr > 1) {
                foreach($cellIterator as $kc => $cell){
                    if($kc == 2) {
                        $studentSchoolList[] = $cell->getValue();
                    } elseif($kc == 3) {
                        $studentScoreList[] = $cell->getValue();
                    } elseif($kc > 5) {
                        $itemScoreList[$keys[$kc-6]][] = $cell->getValue();
                    }
                }
            }
        }

        $totalCount['totalCount'] = 0;
        $totalCount['excellentCount'] = 0;
        $totalCount['passCount'] = 0;
        $totalCount['failCount'] = 0;

        foreach($schoolList as $schoolName){
            $totalSchoolCount[$schoolName]['totalCount'] = 0;
            $totalSchoolCount[$schoolName]['excellentCount'] = 0;
            $totalSchoolCount[$schoolName]['passCount'] = 0;
            $totalSchoolCount[$schoolName]['failCount'] = 0;
        }

        for ($i = 0; $i < count($studentScoreList); $i++) {
            $totalCount['totalCount']++;
            $totalSchoolCount[$studentSchoolList[$i]]['totalCount']++;

            if($studentScoreList[$i] >= $baseScore['excellentScore']) {
                $totalCount['excellentCount']++;
                $totalSchoolCount[$studentSchoolList[$i]]['excellentCount']++;
            } elseif($studentScoreList[$i] >= $baseScore['passScore'] && $studentScoreList[$i] < $baseScore['excellentScore']) {
                $totalCount['passCount']++;
                $totalSchoolCount[$studentSchoolList[$i]]['passCount']++;
            } elseif($studentScoreList[$i] < $baseScore['passScore'] && !empty($studentScoreList[$i])) {
                $totalCount['failCount']++;
                $totalSchoolCount[$studentSchoolList[$i]]['failCount']++;
            }

            foreach($detailTable['examScopeNumber'] as $itemName => $item){
                foreach ($item as $itemScoreName) {
                    $examScopeScoreList[$i][$itemName] = $examScopeScoreList[$i][$itemName] + $itemScoreList[$itemScoreName][$i];
                }
            }
            foreach($detailTable['examMoldNumber'] as $itemName => $item){
                foreach ($item as $itemScoreName) {
                    $examMoldScoreList[$i][$itemName] = $examMoldScoreList[$i][$itemName] + $itemScoreList[$itemScoreName][$i];
                }
            }
        }

        $cumulativeCount['excellentCount'] = $totalCount['excellentCount'];
        $cumulativeCount['passCount'] = $totalCount['excellentCount'] + $totalCount['passCount'];
        $cumulativeCount['failCount'] = $totalCount['excellentCount'] + $totalCount['passCount'] + $totalCount['failCount'];

        $totalRate['excellentRate'] = number_format($totalCount['excellentCount'] / $totalCount['totalCount'] * 100, 2, '.', '');
        $totalRate['passRate'] = number_format($totalCount['passCount'] / $totalCount['totalCount'] * 100, 2, '.', '');
        $totalRate['failRate'] = number_format($totalCount['failCount'] / $totalCount['totalCount'] * 100, 2, '.', '');

        $cumulativeRate['excellentRate'] = number_format($cumulativeCount['excellentCount'] / $totalCount['totalCount'] * 100, 2, '.', '');
        $cumulativeRate['passRate'] = number_format($cumulativeCount['passCount'] / $totalCount['totalCount'] * 100, 2, '.', '');
        $cumulativeRate['failRate'] = number_format($cumulativeCount['failCount'] / $totalCount['totalCount'] * 100, 2, '.', '');

        self::$studentScoreList = $studentScoreList;
        self::$studentSchoolList = $studentSchoolList;
        self::$itemScoreList = $itemScoreList;

        self::$totalCount = $totalCount;
        self::$totalSchoolCount = $totalSchoolCount;

        $studentScoreData = array(
            'baseScore'          => $baseScore, // 分数线
            'totalCount'         => $totalCount, // 全区优秀人数、及格人数、未及格人数
            'totalSchoolCount'   => $totalSchoolCount, // 全校优秀人数、及格人数、未及格人数
            'totalRate'          => $totalRate, // 全区优秀率、及格率、未及格率
            'cumulativeCount'    => $cumulativeCount, // 全区累计优秀人数、累计及格人数、未及格人数
            'cumulativeRate'     => $cumulativeRate, // 全区累计优秀率、累计及格率、未及格率
            'examScopeScoreList' => $examScopeScoreList, // 全区学生考试范畴各小项分数
            'examMoldScoreList'  => $examMoldScoreList, // 全区学生考试层级各小项分数
            'studentScoreList'   => $studentScoreList, // 全区学生分数列表
            'studentSchoolList'  => $studentSchoolList, // 全区学生所属学校列表
        );

        return $studentScoreData;
    }

    /**
     * 获取分数统计
     */
    public function getScoreStatisticsData()
    {
        $examScopeTotalScore = array(); // 全区考核范畴各项目总分
        $examScopeTotalAverageScore = array(); // 全区考核范畴各项目平均分
        $examScopeSchoolTotalScore = array(); // 各学校考核范畴各项目总分
        $examScopeSchoolAverageScore = array(); // 各学校考核范畴各项目平均分
        $examScopeTotalRate = array(); // 全区考核范畴各项目得分率
        $examScopeSchoolRate = array(); // 各学校考核范畴各项目得分率

        $examMoldTotalScore = array(); // 全区考核层级各项目总分
        $examMoldTotalAverageScore = array(); // 全区考核层级各项目平均分
        $examMoldSchoolTotalScore = array(); // 各学校考核层级各项目总分
        $examMoldSchoolAverageScore = array(); // 各学校考核层级各项目平均分
        $examMoldTotalRate = array(); // 全区考核层级各项目得分率
        $examMoldSchoolRate = array(); // 各学校考核层级各项目得分率

        $itemScoreList = self::$itemScoreList; // 各小题分数
        $detailTable = self::$detailTable; // 双向明细表数据
        $studentScoreList = self::$studentScoreList; // 学生分数列表
        $studentSchoolList = self::$studentSchoolList; // 学生所属学校列表

        $baseScore = self::$baseScore; // 分数线
        $schoolList = self::$schoolInfo['schoolList']; // 学校列表
        $totalCount = self::$totalCount; // 全区各标准人数
        $totalSchoolCount = self::$totalSchoolCount; // 全校各标准人数

        $scoreStatisticsData = array(); // 考试类型分数

        foreach($detailTable['examScopeName'] as $itemName){

            $examScopeTotalScore[$itemName]['totalScore'] = 0;
            $examScopeTotalScore[$itemName]['excellentScore'] = 0;
            $examScopeTotalScore[$itemName]['passScore'] = 0;
            $examScopeTotalScore[$itemName]['failScore'] = 0;

            $examScopeTotalAverageScore[$itemName]['totalScore'] = 0;
            $examScopeTotalAverageScore[$itemName]['excellentScore'] = 0;
            $examScopeTotalAverageScore[$itemName]['passScore'] = 0;
            $examScopeTotalAverageScore[$itemName]['failScore'] = 0;

            $examScopeTotalRate[$itemName]['totalRate'] = 0;
            $examScopeTotalRate[$itemName]['excellentRate'] = 0;
            $examScopeTotalRate[$itemName]['passRate'] = 0;
            $examScopeTotalRate[$itemName]['failRate'] = 0;

            foreach($schoolList as $schoolName){
                $examScopeSchoolTotalScore[$itemName][$schoolName]['totalScore'] = 0;
                $examScopeSchoolTotalScore[$itemName][$schoolName]['excellentScore'] = 0;
                $examScopeSchoolTotalScore[$itemName][$schoolName]['passScore'] = 0;
                $examScopeSchoolTotalScore[$itemName][$schoolName]['failScore'] = 0;

                $examScopeSchoolAverageScore[$itemName][$schoolName]['totalScore'] = 0;
                $examScopeSchoolAverageScore[$itemName][$schoolName]['excellentScore'] = 0;
                $examScopeSchoolAverageScore[$itemName][$schoolName]['passScore'] = 0;
                $examScopeSchoolAverageScore[$itemName][$schoolName]['failScore'] = 0;

                $examScopeSchoolRate[$itemName][$schoolName]['totalRate'] = 0;
                $examScopeSchoolRate[$itemName][$schoolName]['excellentRate'] = 0;
                $examScopeSchoolRate[$itemName][$schoolName]['passRate'] = 0;
                $examScopeSchoolRate[$itemName][$schoolName]['failRate'] = 0;
            }

            foreach($detailTable['examScopeNumber'][$itemName] as $itemNum){

                foreach($itemScoreList[$itemNum] as $key => $score){

                    $examScopeTotalScore[$itemName]['totalScore'] = $examScopeTotalScore[$itemName]['totalScore'] + $score;
                    $examScopeSchoolTotalScore[$itemName][$studentSchoolList[$key]]['totalScore'] = $examScopeSchoolTotalScore[$itemName][$studentSchoolList[$key]]['totalScore'] + $score;

                    if($studentScoreList[$key] >= $baseScore['excellentScore']) {

                        $examScopeTotalScore[$itemName]['excellentScore'] = $examScopeTotalScore[$itemName]['excellentScore'] + $score;
                        $examScopeSchoolTotalScore[$itemName][$studentSchoolList[$key]]['excellentScore'] = $examScopeSchoolTotalScore[$itemName][$studentSchoolList[$key]]['excellentScore'] + $score;
                    
                    } elseif($studentScoreList[$key] >= $baseScore['passScore'] && $studentScoreList[$key] < $baseScore['excellentScore']) {

                        $examScopeTotalScore[$itemName]['passScore'] = $examScopeTotalScore[$itemName]['passScore'] + $score;
                        $examScopeSchoolTotalScore[$itemName][$studentSchoolList[$key]]['passScore'] = $examScopeSchoolTotalScore[$itemName][$studentSchoolList[$key]]['passScore'] + $score;

                    } else {

                        $examScopeTotalScore[$itemName]['failScore'] = $examScopeTotalScore[$itemName]['failScore'] + $score;
                        $examScopeSchoolTotalScore[$itemName][$studentSchoolList[$key]]['failScore'] = $examScopeSchoolTotalScore[$itemName][$studentSchoolList[$key]]['failScore'] + $score;
                    }
                }

                $examScopeTotalAverageScore[$itemName]['totalScore'] = number_format($examScopeTotalScore[$itemName]['totalScore'] / $totalCount['totalCount'], 2, '.', '');
                $examScopeTotalAverageScore[$itemName]['excellentScore'] = number_format($examScopeTotalScore[$itemName]['excellentScore'] / $totalCount['excellentCount'], 2, '.', '');
                $examScopeTotalAverageScore[$itemName]['passScore'] = number_format($examScopeTotalScore[$itemName]['passScore'] / $totalCount['passCount'], 2, '.', '');
                $examScopeTotalAverageScore[$itemName]['failScore'] = number_format($examScopeTotalScore[$itemName]['failScore'] / $totalCount['failCount'], 2, '.', '');

                $examScopeTotalRate[$itemName]['totalRate'] = number_format($examScopeTotalScore[$itemName]['totalScore'] / $totalCount['totalCount'] / $detailTable['examScopeTotalScore'][$itemName], 2, '.', '');
                $examScopeTotalRate[$itemName]['excellentRate'] = number_format($examScopeTotalScore[$itemName]['excellentScore'] / $totalCount['excellentCount'] / $detailTable['examScopeTotalScore'][$itemName], 2, '.', '');
                $examScopeTotalRate[$itemName]['passRate'] = number_format($examScopeTotalScore[$itemName]['passScore'] / $totalCount['passCount'] / $detailTable['examScopeTotalScore'][$itemName], 2, '.', '');
                $examScopeTotalRate[$itemName]['failRate'] = number_format($examScopeTotalScore[$itemName]['failScore'] / $totalCount['failCount'] / $detailTable['examScopeTotalScore'][$itemName], 2, '.', '');

                foreach ($examScopeSchoolTotalScore[$itemName] as $schoolName => $score) {
                    $examScopeSchoolAverageScore[$itemName][$schoolName]['totalScore'] = number_format($score['totalScore'] / $totalSchoolCount[$schoolName]['totalCount'], 2, '.', '');
                    $examScopeSchoolAverageScore[$itemName][$schoolName]['excellentScore'] = number_format($score['excellentScore'] / $totalSchoolCount[$schoolName]['excellentCount'], 2, '.', '');
                    $examScopeSchoolAverageScore[$itemName][$schoolName]['passScore'] = number_format($score['passScore'] / $totalSchoolCount[$schoolName]['passCount'], 2, '.', '');
                    $examScopeSchoolAverageScore[$itemName][$schoolName]['failScore'] = number_format($score['failScore'] / $totalSchoolCount[$schoolName]['failCount'], 2, '.', '');

                    $examScopeSchoolRate[$itemName][$schoolName]['totalRate'] = number_format($score['totalScore'] / $totalSchoolCount[$schoolName]['totalCount'] / $detailTable['examScopeTotalScore'][$itemName], 2, '.', '');
                    $examScopeSchoolRate[$itemName][$schoolName]['excellentRate'] = number_format($score['excellentScore'] / $totalSchoolCount[$schoolName]['excellentCount'] / $detailTable['examScopeTotalScore'][$itemName], 2, '.', '');
                    $examScopeSchoolRate[$itemName][$schoolName]['passRate'] = number_format($score['passScore'] / $totalSchoolCount[$schoolName]['passCount'] / $detailTable['examScopeTotalScore'][$itemName], 2, '.', '');
                    $examScopeSchoolRate[$itemName][$schoolName]['failRate'] = number_format($score['failScore'] / $totalSchoolCount[$schoolName]['failCount'] / $detailTable['examScopeTotalScore'][$itemName], 2, '.', '');
                }
            }
        }

        foreach($detailTable['examMoldName'] as $itemName){

            $examMoldTotalScore[$itemName]['totalScore'] = 0;
            $examMoldTotalScore[$itemName]['excellentScore'] = 0;
            $examMoldTotalScore[$itemName]['passScore'] = 0;
            $examMoldTotalScore[$itemName]['failScore'] = 0;

            $examMoldTotalAverageScore[$itemName]['totalScore'] = 0;
            $examMoldTotalAverageScore[$itemName]['excellentScore'] = 0;
            $examMoldTotalAverageScore[$itemName]['passScore'] = 0;
            $examMoldTotalAverageScore[$itemName]['failScore'] = 0;

            $examMoldTotalRate[$itemName]['totalRate'] = 0;
            $examMoldTotalRate[$itemName]['excellentRate'] = 0;
            $examMoldTotalRate[$itemName]['passRate'] = 0;
            $examMoldTotalRate[$itemName]['failRate'] = 0;

            foreach($schoolList as $schoolName){
                $examMoldSchoolTotalScore[$itemName][$schoolName]['totalScore'] = 0;
                $examMoldSchoolTotalScore[$itemName][$schoolName]['excellentScore'] = 0;
                $examMoldSchoolTotalScore[$itemName][$schoolName]['passScore'] = 0;
                $examMoldSchoolTotalScore[$itemName][$schoolName]['failScore'] = 0;

                $examMoldSchoolAverageScore[$itemName][$schoolName]['totalScore'] = 0;
                $examMoldSchoolAverageScore[$itemName][$schoolName]['excellentScore'] = 0;
                $examMoldSchoolAverageScore[$itemName][$schoolName]['passScore'] = 0;
                $examMoldSchoolAverageScore[$itemName][$schoolName]['failScore'] = 0;
                
                $examMoldSchoolRate[$itemName][$schoolName]['totalRate'] = 0;
                $examMoldSchoolRate[$itemName][$schoolName]['excellentRate'] = 0;
                $examMoldSchoolRate[$itemName][$schoolName]['passRate'] = 0;
                $examMoldSchoolRate[$itemName][$schoolName]['failRate'] = 0;
            }

            foreach($detailTable['examMoldNumber'][$itemName] as $itemNum){

                foreach($itemScoreList[$itemNum] as $key => $score){

                    $examMoldTotalScore[$itemName]['totalScore'] = $examMoldTotalScore[$itemName]['totalScore'] + $score;
                    $examMoldSchoolTotalScore[$itemName][$studentSchoolList[$key]]['totalScore'] = $examMoldSchoolTotalScore[$itemName][$studentSchoolList[$key]]['totalScore'] + $score;

                    if($studentScoreList[$key] >= $baseScore['excellentScore']) {

                        $examMoldTotalScore[$itemName]['excellentScore'] = $examMoldTotalScore[$itemName]['excellentScore'] + $score;
                        $examMoldSchoolTotalScore[$itemName][$studentSchoolList[$key]]['excellentScore'] = $examMoldSchoolTotalScore[$itemName][$studentSchoolList[$key]]['excellentScore'] + $score;
                    
                    } elseif($studentScoreList[$key] >= $baseScore['passScore'] && $studentScoreList[$key] < $baseScore['excellentScore']) {

                        $examMoldTotalScore[$itemName]['passScore'] = $examMoldTotalScore[$itemName]['passScore'] + $score;
                        $examMoldSchoolTotalScore[$itemName][$studentSchoolList[$key]]['passScore'] = $examMoldSchoolTotalScore[$itemName][$studentSchoolList[$key]]['passScore'] + $score;

                    } else {

                        $examMoldTotalScore[$itemName]['failScore'] = $examMoldTotalScore[$itemName]['failScore'] + $score;
                        $examMoldSchoolTotalScore[$itemName][$studentSchoolList[$key]]['failScore'] = $examMoldSchoolTotalScore[$itemName][$studentSchoolList[$key]]['failScore'] + $score;
                    }
                }

                $examMoldTotalAverageScore[$itemName]['totalScore'] = number_format($examMoldTotalScore[$itemName]['totalScore'] / $totalCount['totalCount'], 2, '.', '');

                $examMoldTotalAverageScore[$itemName]['excellentScore'] = number_format($examMoldTotalScore[$itemName]['excellentScore'] / $totalCount['excellentCount'], 2, '.', '');
                $examMoldTotalAverageScore[$itemName]['passScore'] = number_format($examMoldTotalScore[$itemName]['passScore'] / $totalCount['passCount'], 2, '.', '');
                $examMoldTotalAverageScore[$itemName]['failScore'] = number_format($examMoldTotalScore[$itemName]['failScore'] / $totalCount['failCount'], 2, '.', '');

                $examMoldTotalRate[$itemName]['totalRate'] = number_format($examMoldTotalScore[$itemName]['totalScore'] / $totalCount['totalCount'] / $detailTable['examMoldTotalScore'][$itemName], 2, '.', '');
                $examMoldTotalRate[$itemName]['excellentRate'] = number_format($examMoldTotalScore[$itemName]['excellentScore'] / $totalCount['excellentCount'] / $detailTable['examMoldTotalScore'][$itemName], 2, '.', '');
                $examMoldTotalRate[$itemName]['passRate'] = number_format($examMoldTotalScore[$itemName]['passScore'] / $totalCount['passCount'] / $detailTable['examMoldTotalScore'][$itemName], 2, '.', '');
                $examMoldTotalRate[$itemName]['failRate'] = number_format($examMoldTotalScore[$itemName]['failScore'] / $totalCount['failCount'] / $detailTable['examMoldTotalScore'][$itemName], 2, '.', '');

                foreach ($examMoldSchoolTotalScore[$itemName] as $schoolName => $score) {
                    $examMoldSchoolAverageScore[$itemName][$schoolName]['totalScore'] = number_format($score['totalScore'] / $totalSchoolCount[$schoolName]['totalCount'], 2, '.', '');

                    $examMoldSchoolAverageScore[$itemName][$schoolName]['excellentScore'] = number_format($score['excellentScore'] / $totalSchoolCount[$schoolName]['excellentCount'], 2, '.', '');
                    $examMoldSchoolAverageScore[$itemName][$schoolName]['passScore'] = number_format($score['passScore'] / $totalSchoolCount[$schoolName]['passCount'], 2, '.', '');
                    $examMoldSchoolAverageScore[$itemName][$schoolName]['failScore'] = number_format($score['failScore'] / $totalSchoolCount[$schoolName]['failCount'], 2, '.', '');

                    $examMoldSchoolRate[$itemName][$schoolName]['totalRate'] = number_format($score['totalScore'] / $totalSchoolCount[$schoolName]['totalCount'] / $detailTable['examMoldTotalScore'][$itemName], 2, '.', '');
                    $examMoldSchoolRate[$itemName][$schoolName]['excellentRate'] = number_format($score['excellentScore'] / $totalSchoolCount[$schoolName]['excellentCount'] / $detailTable['examMoldTotalScore'][$itemName], 2, '.', '');
                    $examMoldSchoolRate[$itemName][$schoolName]['passRate'] = number_format($score['passScore'] / $totalSchoolCount[$schoolName]['passCount'] / $detailTable['examMoldTotalScore'][$itemName], 2, '.', '');
                    $examMoldSchoolRate[$itemName][$schoolName]['failRate'] = number_format($score['failScore'] / $totalSchoolCount[$schoolName]['failCount'] / $detailTable['examMoldTotalScore'][$itemName], 2, '.', '');
                }
            }
        }

        $scoreStatisticsData = array(
            'examScopeTotalScore'          => $examScopeTotalScore, // 全区考核范畴各项目总分
            'examScopeTotalAverageScore'   => $examScopeTotalAverageScore, // 全区考核范畴各项目平均分
            'examScopeSchoolTotalScore'    => $examScopeSchoolTotalScore, // 各学校考核范畴各项目总分
            'examScopeSchoolAverageScore'  => $examScopeSchoolAverageScore, // 各学校考核范畴各项目平均分
            'examScopeTotalRate'           => $examScopeTotalRate, // 全区考核范畴各项目得分率
            'examScopeSchoolRate'          => $examScopeSchoolRate, // 各学校考核范畴各项目得分率
            'examMoldTotalScore'           => $examMoldTotalScore, // 全区考核层级各项目总分
            'examMoldTotalAverageScore'    => $examMoldTotalAverageScore, // 全区考核层级各项目平均分
            'examMoldSchoolTotalScore'     => $examMoldSchoolTotalScore, // 各学校考核层级各项目总分
            'examMoldSchoolAverageScore'   => $examMoldSchoolAverageScore, // 各学校考核层级各项目平均分
            'examMoldTotalRate'            => $examMoldTotalRate, // 全区考核层级各项目得分率
            'examMoldSchoolRate'           => $examMoldSchoolRate, // 各学校考核层级各项目得分率
        );

        return $scoreStatisticsData;
    }

    /**
     * 获取客观题统计
     */
    public function getChoiceQuestionsAnalysisData()
    {
        $filePath = self::$basePath.self::$examInfo['mainDir'].'/'.self::$course.'/';
        $filename = self::$choiceQuestionsName;

        $excelData = self::$excelFile->openExcel($filePath, $filename);

        $keys = array();
        $rets = array();

        $num = 0;
        
        $choiceQuestionsAnalysisData = array(); // 分数数据

        foreach($excelData->getRowIterator() as $kr => $row){

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
                $choiceQuestionsAnalysisData[$num][$keys[6]] = number_format($rets['平均分'][$i], 2, '.', '');
                $choiceQuestionsAnalysisData[$num][$keys[7]] = number_format($rets['标准差'][$i], 2, '.', '');
                $choiceQuestionsAnalysisData[$num][$keys[8]] = number_format($rets['得分率'][$i], 2, '.', '');
                $choiceQuestionsAnalysisData[$num][$keys[11]] = number_format($rets['难度'][$i], 2, '.', '');
                if($choiceQuestionsAnalysisData[$num][$keys[11]] > 0.9){
                    $choiceQuestionsAnalysisData[$num]['难度评价标准'] = '容易';
                }
                elseif($choiceQuestionsAnalysisData[$num][$keys[11]] > 0.7 && $choiceQuestionsAnalysisData[$num][$keys[11]] <= 0.9){
                    $choiceQuestionsAnalysisData[$num]['难度评价标准'] = '较易';
                }
                elseif($choiceQuestionsAnalysisData[$num][$keys[11]] > 0.4 && $choiceQuestionsAnalysisData[$num][$keys[11]] <= 0.7){
                    $choiceQuestionsAnalysisData[$num]['难度评价标准'] = '中等';
                }
                elseif($choiceQuestionsAnalysisData[$num][$keys[11]] <= 0.4){
                    $choiceQuestionsAnalysisData[$num]['难度评价标准'] = '偏难';
                }
                $choiceQuestionsAnalysisData[$num][$keys[12]] = number_format($rets['区分度'][$i], 2, '.', '');
                if($choiceQuestionsAnalysisData[$num][$keys[12]] >= 0.4){
                    $choiceQuestionsAnalysisData[$num]['区分度评价标准'] = '区分度较高';
                }
                elseif($choiceQuestionsAnalysisData[$num][$keys[12]] >= 0.3 && $choiceQuestionsAnalysisData[$num][$keys[12]] <= 0.39){
                    $choiceQuestionsAnalysisData[$num]['区分度评价标准'] = '区分度中等';
                }
                elseif($choiceQuestionsAnalysisData[$num][$keys[12]] >= 0.2 && $choiceQuestionsAnalysisData[$num][$keys[12]] <= 0.29){
                    $choiceQuestionsAnalysisData[$num]['区分度评价标准'] = '区分度一般';
                }
                elseif($choiceQuestionsAnalysisData[$num][$keys[12]] < 0.2){
                    $choiceQuestionsAnalysisData[$num]['区分度评价标准'] = '区分度较低';
                }
                $choiceQuestionsAnalysisData[$num][$keys[14]] = number_format($rets['选A率%'][$i], 2, '.', '');
                $choiceQuestionsAnalysisData[$num][$keys[16]] = number_format($rets['选B率%'][$i], 2, '.', '');
                $choiceQuestionsAnalysisData[$num][$keys[18]] = number_format($rets['选C率%'][$i], 2, '.', '');
                $choiceQuestionsAnalysisData[$num][$keys[20]] = number_format($rets['选D率%'][$i], 2, '.', '');
                $num++;
            }
        }

        return $choiceQuestionsAnalysisData;
    }
}

?>