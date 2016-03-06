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
    protected static $schoolList;

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
     * 获取考核范畴分数列表
     * @var array
     */
    protected static $examScopeScoreList;

    /**
     * 获取考核层级分数列表
     * @var array
     */
    protected static $examMoldScoreList;

    /**
     * 获取学生所在学校列表
     * @var array
     */
    protected static $studentSchoolList;

    /**
     * 获取科目双向细目表
     * @var array
     */
    protected static $detailTableData;

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
     * 构造
     * @param $date 日期
     * @param $foldername 文件夹名称（包含信息：学年、学期、年级、考试名称）
     * @param $course 查询科目
     */
    function __construct($examInfoData, $course)
    {
        self::$examInfo = $examInfoData;
        self::$basePath = self::$examInfo['rootDir'].self::$examInfo['uploadDate'].'/'.self::$examInfo['fullname'].'/';
        self::$course = $course;

        $schoolListObj = new \Admin\Model\SchoolListData();
        self::$schoolList = $schoolListObj->getSchoolData(self::$examInfo['schoolType']);

        $baseScoreRateData = new \Admin\Model\BaseScoreRateData();
        self::$baseScoreRate = $baseScoreRateData->getBaseScoreRateData(self::$course);

        $courseObj = new \Admin\Model\CourseData(self::$examInfo);
        self::$courseList = $courseObj->getCourseData();

        $detailTableObj = new \Admin\Model\DetailTableData(self::$examInfo, self::$course);
        self::$detailTableData = $detailTableObj->getDetailTableData();

        self::$baseScore['excellentScore'] = self::$detailTableData['totalScore'] * (self::$baseScoreRate[0] / 100);
        self::$baseScore['passScore'] = self::$detailTableData['totalScore'] * (self::$baseScoreRate[1] / 100);

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
            'difficulty'     => $difficulty[self::$course][0],
            'difficultyTxt'  => $difficultyTxt[self::$course][0],
            'distinguish'    => $distinguish[self::$course][0],
            'distinguishTxt' => $distinguishTxt[self::$course][0],
            'reliability'    => $reliability[self::$course][0],
            'reliabilityTxt' => $reliabilityTxt[self::$course][0],
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
                        self::$studentSchoolList[] = $cell->getValue();
                    } elseif($kc == 3) {
                        self::$studentScoreList[] = $cell->getValue();
                    } elseif($kc > 5) {
                        self::$itemScoreList[$keys[$kc-6]][] = $cell->getValue();
                    }
                }
            }
        }

        $totalCount['totalCount'] = 0;
        $totalCount['excellentCount'] = 0;
        $totalCount['passCount'] = 0;
        $totalCount['failCount'] = 0;

        foreach(self::$schoolList as $schoolName){
            $totalSchoolCount[$schoolName]['totalCount'] = 0;

            $totalSchoolCount[$schoolName]['excellentCount'] = 0;
            $totalSchoolCount[$schoolName]['passCount'] = 0;
            $totalSchoolCount[$schoolName]['failCount'] = 0;
        }

        for ($i = 0; $i < count(self::$studentScoreList); $i++) {
            $totalCount['totalCount']++;
            $totalSchoolCount[self::$studentSchoolList[$i]]['totalCount']++;

            if(self::$studentScoreList[$i] >= self::$baseScore['excellentScore']) {
                $totalCount['excellentCount']++;
                $totalSchoolCount[self::$studentSchoolList[$i]]['excellentCount']++;
            } elseif(self::$studentScoreList[$i] >= self::$baseScore['passScore'] && self::$studentScoreList[$i] < self::$baseScore['excellentScore']) {
                $totalCount['passCount']++;
                $totalSchoolCount[self::$studentSchoolList[$i]]['passCount']++;
            } elseif(self::$studentScoreList[$i] < self::$baseScore['passScore'] && !empty(self::$studentScoreList[$i])) {
                $totalCount['failCount']++;
                $totalSchoolCount[self::$studentSchoolList[$i]]['failCount']++;
            }

            foreach(self::$detailTableData['examScopeNumber'] as $itemName => $item){
                self::$examScopeScoreList[$i][$itemName] = 0;
                foreach ($item as $itemScoreName) {
                    self::$examScopeScoreList[$i][$itemName] = self::$examScopeScoreList[$i][$itemName] + self::$itemScoreList[$itemScoreName][$i];
                }
            }
            foreach(self::$detailTableData['examMoldNumber'] as $itemName => $item){
                self::$examMoldScoreList[$i][$itemName] = 0;
                foreach ($item as $itemScoreName) {
                    self::$examMoldScoreList[$i][$itemName] = self::$examMoldScoreList[$i][$itemName] + self::$itemScoreList[$itemScoreName][$i];
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

        self::$totalCount = $totalCount;
        self::$totalSchoolCount = $totalSchoolCount;

        $studentScoreData = array(
            'totalCount'        => $totalCount, // 全区优秀人数、及格人数、未及格人数
            'totalSchoolCount'  => $totalSchoolCount, // 全校优秀人数、及格人数、未及格人数
            'totalRate'         => $totalRate, // 全区优秀率、及格率、未及格率
            'cumulativeCount'   => $cumulativeCount, // 全区累计优秀人数、累计及格人数、未及格人数
            'cumulativeRate'    => $cumulativeRate, // 全区累计优秀率、累计及格率、未及格率
        );

        return $studentScoreData;
    }

    /**
     * 获取分数统计
     */
    public function getScoreStatisticsData()
    {
        $examScore = array(); // 考核范畴统计
        $typeScore = array(); // 考核层级统计

        $examAverageScore = array(); // 考核范畴统计
        $typeAverageScore = array(); // 考核层级统计

        $examScopeAverageScore = array(); // 考核范畴统计
        $examMoldAverageScore = array(); // 考核层级统计

        $scoreStatisticsData = array(); // 考试类型分数

        foreach(self::$detailTableData['examScopeName'] as $itemName){

            $examScopeTotalScore[$itemName]['totalScore'] = 0;
            $examScopeTotalScore[$itemName]['excellentScore'] = 0;
            $examScopeTotalScore[$itemName]['passScore'] = 0;
            $examScopeTotalScore[$itemName]['failScore'] = 0;

            $examScopeTotalAverageScore[$itemName]['totalScore'] = 0;
            $examScopeTotalAverageScore[$itemName]['excellentScore'] = 0;
            $examScopeTotalAverageScore[$itemName]['passScore'] = 0;
            $examScopeTotalAverageScore[$itemName]['failScore'] = 0;

            foreach(self::$schoolList as $schoolName){

                $examScopeSchoolTotalScore[$itemName][$schoolName]['totalScore'] = 0;

                $examScopeSchoolTotalScore[$itemName][$schoolName]['excellentScore'] = 0;
                $examScopeSchoolTotalScore[$itemName][$schoolName]['passScore'] = 0;
                $examScopeSchoolTotalScore[$itemName][$schoolName]['failScore'] = 0;

                $examScopeSchoolAverageScore[$itemName][$schoolName]['totalScore'] = 0;
                $examScopeSchoolAverageScore[$itemName][$schoolName]['excellentScore'] = 0;
                $examScopeSchoolAverageScore[$itemName][$schoolName]['passScore'] = 0;
                $examScopeSchoolAverageScore[$itemName][$schoolName]['failScore'] = 0;
            }

            foreach(self::$detailTableData['examScopeNumber'][$itemName] as $itemNum){

                foreach(self::$itemScoreList[$itemNum] as $key => $score){

                    $examScopeTotalScore[$itemName]['totalScore'] = $examScopeTotalScore[$itemName]['totalScore'] + $score;
                    $examScopeSchoolTotalScore[$itemName][self::$studentSchoolList[$key]]['totalScore'] = $examScopeSchoolTotalScore[$itemName][self::$studentSchoolList[$key]]['totalScore'] + $score;

                    if(self::$studentScoreList[$key] >= self::$baseScore['excellentScore']) {

                        $examScopeTotalScore[$itemName]['excellentScore'] = $examScopeTotalScore[$itemName]['excellentScore'] + $score;
                        $examScopeSchoolTotalScore[$itemName][self::$studentSchoolList[$key]]['excellentScore'] = $examScopeSchoolTotalScore[$itemName][self::$studentSchoolList[$key]]['excellentScore'] + $score;
                    
                    } elseif(self::$studentScoreList[$key] >= self::$baseScore['passScore'] && self::$studentScoreList[$key] < self::$baseScore['excellentScore']) {

                        $examScopeTotalScore[$itemName]['passScore'] = $examScopeTotalScore[$itemName]['passScore'] + $score;
                        $examScopeSchoolTotalScore[$itemName][self::$studentSchoolList[$key]]['passScore'] = $examScopeSchoolTotalScore[$itemName][self::$studentSchoolList[$key]]['passScore'] + $score;

                    } else {

                        $examScopeTotalScore[$itemName]['failScore'] = $examScopeTotalScore[$itemName]['failScore'] + $score;
                        $examScopeSchoolTotalScore[$itemName][self::$studentSchoolList[$key]]['failScore'] = $examScopeSchoolTotalScore[$itemName][self::$studentSchoolList[$key]]['failScore'] + $score;
                    }
                }

                $examScopeTotalAverageScore[$itemName]['totalScore'] = number_format($examScopeTotalScore[$itemName]['totalScore'] / self::$totalCount['totalCount'], 2, '.', '');

                $examScopeTotalAverageScore[$itemName]['excellentScore'] = number_format($examScopeTotalScore[$itemName]['excellentScore'] / self::$totalCount['excellentCount'], 2, '.', '');
                $examScopeTotalAverageScore[$itemName]['passScore'] = number_format($examScopeTotalScore[$itemName]['passScore'] / self::$totalCount['passCount'], 2, '.', '');
                $examScopeTotalAverageScore[$itemName]['failScore'] = number_format($examScopeTotalScore[$itemName]['failScore'] / self::$totalCount['failCount'], 2, '.', '');

                foreach ($examScopeSchoolTotalScore[$itemName] as $schoolName => $score) {
                    $examScopeSchoolAverageScore[$itemName][$schoolName]['totalScore'] = number_format($score['totalScore'] / self::$totalSchoolCount[$schoolName]['totalCount'], 2, '.', '');

                    $examScopeSchoolAverageScore[$itemName][$schoolName]['excellentScore'] = number_format($score['excellentScore'] / self::$totalSchoolCount[$schoolName]['excellentCount'], 2, '.', '');
                    $examScopeSchoolAverageScore[$itemName][$schoolName]['passScore'] = number_format($score['passScore'] / self::$totalSchoolCount[$schoolName]['passCount'], 2, '.', '');
                    $examScopeSchoolAverageScore[$itemName][$schoolName]['failScore'] = number_format($score['failScore'] / self::$totalSchoolCount[$schoolName]['failCount'], 2, '.', '');
                }
            }
        }

        foreach(self::$detailTableData['examMoldName'] as $itemName){

            $examMoldTotalScore[$itemName]['totalScore'] = 0;
            $examMoldTotalScore[$itemName]['excellentScore'] = 0;
            $examMoldTotalScore[$itemName]['passScore'] = 0;
            $examMoldTotalScore[$itemName]['failScore'] = 0;

            $examMoldTotalAverageScore[$itemName]['totalScore'] = 0;
            $examMoldTotalAverageScore[$itemName]['excellentScore'] = 0;
            $examMoldTotalAverageScore[$itemName]['passScore'] = 0;
            $examMoldTotalAverageScore[$itemName]['failScore'] = 0;

            foreach(self::$schoolList as $schoolName){

                $examMoldSchoolTotalScore[$itemName][$schoolName]['totalScore'] = 0;

                $examMoldSchoolTotalScore[$itemName][$schoolName]['excellentScore'] = 0;
                $examMoldSchoolTotalScore[$itemName][$schoolName]['passScore'] = 0;
                $examMoldSchoolTotalScore[$itemName][$schoolName]['failScore'] = 0;

                $examMoldSchoolAverageScore[$itemName][$schoolName]['totalScore'] = 0;
                $examMoldSchoolAverageScore[$itemName][$schoolName]['excellentScore'] = 0;
                $examMoldSchoolAverageScore[$itemName][$schoolName]['passScore'] = 0;
                $examMoldSchoolAverageScore[$itemName][$schoolName]['failScore'] = 0;
            }

            foreach(self::$detailTableData['examMoldNumber'][$itemName] as $itemNum){

                foreach(self::$itemScoreList[$itemNum] as $key => $score){

                    $examMoldTotalScore[$itemName]['totalScore'] = $examMoldTotalScore[$itemName]['totalScore'] + $score;
                    $examMoldSchoolTotalScore[$itemName][self::$studentSchoolList[$key]]['totalScore'] = $examMoldSchoolTotalScore[$itemName][self::$studentSchoolList[$key]]['totalScore'] + $score;

                    if(self::$studentScoreList[$key] >= self::$baseScore['excellentScore']) {

                        $examMoldTotalScore[$itemName]['excellentScore'] = $examMoldTotalScore[$itemName]['excellentScore'] + $score;
                        $examMoldSchoolTotalScore[$itemName][self::$studentSchoolList[$key]]['excellentScore'] = $examMoldSchoolTotalScore[$itemName][self::$studentSchoolList[$key]]['excellentScore'] + $score;
                    
                    } elseif(self::$studentScoreList[$key] >= self::$baseScore['passScore'] && self::$studentScoreList[$key] < self::$baseScore['excellentScore']) {

                        $examMoldTotalScore[$itemName]['passScore'] = $examMoldTotalScore[$itemName]['passScore'] + $score;
                        $examMoldSchoolTotalScore[$itemName][self::$studentSchoolList[$key]]['passScore'] = $examMoldSchoolTotalScore[$itemName][self::$studentSchoolList[$key]]['passScore'] + $score;

                    } else {

                        $examMoldTotalScore[$itemName]['failScore'] = $examMoldTotalScore[$itemName]['failScore'] + $score;
                        $examMoldSchoolTotalScore[$itemName][self::$studentSchoolList[$key]]['failScore'] = $examMoldSchoolTotalScore[$itemName][self::$studentSchoolList[$key]]['failScore'] + $score;
                    }
                }

                $examMoldTotalAverageScore[$itemName]['totalScore'] = number_format($examMoldTotalScore[$itemName]['totalScore'] / self::$totalCount['totalCount'], 2, '.', '');

                $examMoldTotalAverageScore[$itemName]['excellentScore'] = number_format($examMoldTotalScore[$itemName]['excellentScore'] / self::$totalCount['excellentCount'], 2, '.', '');
                $examMoldTotalAverageScore[$itemName]['passScore'] = number_format($examMoldTotalScore[$itemName]['passScore'] / self::$totalCount['passCount'], 2, '.', '');
                $examMoldTotalAverageScore[$itemName]['failScore'] = number_format($examMoldTotalScore[$itemName]['failScore'] / self::$totalCount['failCount'], 2, '.', '');

                foreach ($examMoldSchoolTotalScore[$itemName] as $schoolName => $score) {
                    $examMoldSchoolAverageScore[$itemName][$schoolName]['totalScore'] = number_format($score['totalScore'] / self::$totalSchoolCount[$schoolName]['totalCount'], 2, '.', '');

                    $examMoldSchoolAverageScore[$itemName][$schoolName]['excellentScore'] = number_format($score['excellentScore'] / self::$totalSchoolCount[$schoolName]['excellentCount'], 2, '.', '');
                    $examMoldSchoolAverageScore[$itemName][$schoolName]['passScore'] = number_format($score['passScore'] / self::$totalSchoolCount[$schoolName]['passCount'], 2, '.', '');
                    $examMoldSchoolAverageScore[$itemName][$schoolName]['failScore'] = number_format($score['failScore'] / self::$totalSchoolCount[$schoolName]['failCount'], 2, '.', '');
                }
            }
        }

        $scoreStatisticsData = array(
            'examScopeTotalScore'          => $examScopeTotalScore, // 全区考核范畴各项目总分
            'examScopeTotalAverageScore'   => $examScopeTotalAverageScore, // 全区考核范畴各项目平均分
            'examScopeSchoolTotalScore'    => $examScopeSchoolTotalScore, // 各学校考核范畴各项目总分
            'examScopeSchoolAverageScore'  => $examScopeSchoolAverageScore, // 各学校考核范畴各项目平均分
            'examMoldTotalScore'           => $examMoldTotalScore, // 全区考核层级各项目总分
            'examMoldTotalAverageScore'    => $examMoldTotalAverageScore, // 全区考核层级各项目平均分
            'examMoldSchoolTotalScore'     => $examMoldSchoolTotalScore, // 各学校考核层级各项目总分
            'examMoldSchoolAverageScore'   => $examMoldSchoolAverageScore, // 各学校考核层级各项目平均分
        );

        return $scoreStatisticsData;
    }

    /**
     * 获取分数率统计
     */
    private function getScoreStatisticsRateData()
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
     * 验证数据
     */
    public function getAllData()
    {
        $data1 = self::getStudentScoreData();
        $data2 = self::getScoreStatisticsData();
        // var_export('============$baseScore=================');
        // var_export(self::$baseScore);
        var_export('===========$getStudentScoreData==================');
        var_export($data1);
        var_export('===========$getScoreStatisticsData==================');
        var_export($data2);
        exit();
    }
}

?>