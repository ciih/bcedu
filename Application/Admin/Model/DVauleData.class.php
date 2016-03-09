<?php

/**
 * 获取C/E/D值
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class DVauleData {

    /**
     * 获取学校列表
     * @var array
     */
    protected static $schoolInfo;

    /**
     * 获取双向明细表数据
     * @var array
     */
    protected static $detailTable;

    /**
     * 获取学生分数(小题分)
     * @var array
     */
    protected static $studentScore;

    /**
     * 获取分数统计
     * @var array
     */
    protected static $scoreStatistics;

    /**
     * 获取学生所在学校列表
     * @var array
     */
    protected static $studentSchoolList;

    /**
     * 构造
     */
    function __construct($schoolInfoData, $detailTableData, $studentScoreData, $scoreStatisticsData)
    {
        self::$detailTable     = $detailTableData;
        self::$studentScore    = $studentScoreData;
        self::$scoreStatistics = $scoreStatisticsData;

        self::$schoolInfo = $schoolInfoData;
    }

    /**
     * 获取标准差及D值统计
     */
    public function getDVauleData()
    {
        $baseScore          = self::$studentScore['baseScore']; // 分数线
        $examScopeScoreList = self::$studentScore['examScopeScoreList']; // 考核范畴各项目分数列表
        $examMoldScoreList  = self::$studentScore['examMoldScoreList']; // 考核层级各项目分数列表
        $studentScoreList   = self::$studentScore['studentScoreList']; // 学生分数列表
        $studentSchoolList  = self::$studentScore['studentSchoolList']; // 学生所属学校列表
        $studentTotalCount  = self::$studentScore['totalCount']; // 全区学生人数统计
        $studentSchoolCount  = self::$studentScore['totalSchoolCount']; // 各学校学生人数统计

        $detailTable = self::$detailTable; // 双向明细表数据
        $schoolList = self::$schoolInfo['schoolList']; // 学校列表

        $examScopeTotalAverageScore  = self::$scoreStatistics['examScopeTotalAverageScore']; // 全区考核范畴各项目平均分
        $examScopeSchoolAverageScore = self::$scoreStatistics['examScopeSchoolAverageScore']; // 各学校考核范畴各项目平均分

        $examMoldTotalAverageScore  = self::$scoreStatistics['examMoldTotalAverageScore']; // 全区考核层级各项目平均分
        $examMoldSchoolAverageScore = self::$scoreStatistics['examMoldSchoolAverageScore']; // 各学校考核层级各项目平均分
        

        $examScopeTotalNumerator = array(); // 全区考核范畴对照组标准差分子
        $examScopeSchoolNumerator = array(); // 全校考核范畴对比组标准差分子

        $examMoldTotalNumerator = array(); // 全区考核层级对照组标准差分子
        $examMoldSchoolNumerator = array(); // 全校考核层级对比组标准差分子

        $examScopeTotalEValue = array(); // 全区考核范畴对照组标准差E值
        $examScopeSchoolCValue = array(); // 全校考核范畴对比组标准差C值

        $examMoldTotalEValue = array(); // 全区考核层级对照组标准差E值
        $examMoldSchoolCValue = array(); // 全校考核层级对比组标准差C值

        $examScopeTotalSValue = array(); // 全区考核范畴D值分母
        $examMoldTotalSValue = array(); // 全校考核层级D值分母

        $examScopeTotalDValue = array(); // 全区考核范畴D值
        $examMoldTotalDValue = array(); // 全校考核层级D值

        $dVauleData = array(); // D值数据

        foreach ($detailTable['examScopeName'] as $name) {
            $examScopeTotalNumerator[$name]['totalScore'] = 0;
            $examScopeTotalNumerator[$name]['excellentScore'] = 0;
            $examScopeTotalNumerator[$name]['passScore'] = 0;
            $examScopeTotalNumerator[$name]['failScore'] = 0;

            $examScopeTotalEValue[$name]['totalScore'] = 0;
            $examScopeTotalEValue[$name]['excellentScore'] = 0;
            $examScopeTotalEValue[$name]['passScore'] = 0;
            $examScopeTotalEValue[$name]['failScore'] = 0;

            foreach ($schoolList as $schoolName) {
                $examScopeSchoolNumerator[$name][$schoolName]['totalScore'] = 0;
                $examScopeSchoolNumerator[$name][$schoolName]['excellentScore'] = 0;
                $examScopeSchoolNumerator[$name][$schoolName]['passScore'] = 0;
                $examScopeSchoolNumerator[$name][$schoolName]['failScore'] = 0;

                $examScopeSchoolCValue[$name][$schoolName]['totalScore'] = 0;
                $examScopeSchoolCValue[$name][$schoolName]['excellentScore'] = 0;
                $examScopeSchoolCValue[$name][$schoolName]['passScore'] = 0;
                $examScopeSchoolCValue[$name][$schoolName]['failScore'] = 0;
            }
        }

        foreach ($detailTable['examMoldName'] as $name) {
            $examMoldTotalNumerator[$name]['totalScore'] = 0;
            $examMoldTotalNumerator[$name]['excellentScore'] = 0;
            $examMoldTotalNumerator[$name]['passScore'] = 0;
            $examMoldTotalNumerator[$name]['failScore'] = 0;

            $examMoldTotalEValue[$name]['totalScore'] = 0;
            $examMoldTotalEValue[$name]['excellentScore'] = 0;
            $examMoldTotalEValue[$name]['passScore'] = 0;
            $examMoldTotalEValue[$name]['failScore'] = 0;

            foreach ($schoolList as $schoolName) {
                $examMoldSchoolNumerator[$name][$schoolName]['totalScore'] = 0;
                $examMoldSchoolNumerator[$name][$schoolName]['excellentScore'] = 0;
                $examMoldSchoolNumerator[$name][$schoolName]['passScore'] = 0;
                $examMoldSchoolNumerator[$name][$schoolName]['failScore'] = 0;

                $examMoldSchoolCValue[$name][$schoolName]['totalScore'] = 0;
                $examMoldSchoolCValue[$name][$schoolName]['excellentScore'] = 0;
                $examMoldSchoolCValue[$name][$schoolName]['passScore'] = 0;
                $examMoldSchoolCValue[$name][$schoolName]['failScore'] = 0;
            }
        }

        foreach ($studentScoreList as $num => $studentScore) {
            foreach ($detailTable['examScopeName'] as $examScopeName) {
                $currSchoolName = $studentSchoolList[$num];
                $currScore = $examScopeScoreList[$num][$examScopeName];
                $currTotalAverageScore = $examScopeTotalAverageScore[$examScopeName];
                $currSchoolAverageScore = $examScopeSchoolAverageScore[$examScopeName][$currSchoolName];

                $culScore = number_format(pow(number_format($currScore - $currTotalAverageScore['totalScore'], 2, '.', '') , 2), 2, '.', '');
                $examScopeTotalNumerator[$examScopeName]['totalScore'] = number_format($examScopeTotalNumerator[$examScopeName]['totalScore'] + $culScore, 2, '.', '');

                $culScore = number_format(pow(number_format($currScore - $currSchoolAverageScore['totalScore'], 2, '.', '') , 2), 2, '.', '');
                $examScopeSchoolNumerator[$examScopeName][$currSchoolName]['totalScore'] = number_format($examScopeSchoolNumerator[$examScopeName][$currSchoolName]['totalScore'] + $culScore, 2, '.', '');
                
                if($studentScore >= $baseScore['excellentScore']) {
                    $culScore = number_format(pow(number_format($currScore - $currTotalAverageScore['excellentScore'], 2, '.', '') , 2), 2, '.', '');
                    $examScopeTotalNumerator[$examScopeName]['excellentScore'] = number_format($examScopeTotalNumerator[$examScopeName]['excellentScore'] + $culScore, 2, '.', '');

                    $culScore = number_format(pow(number_format($currScore - $currSchoolAverageScore['excellentScore'], 2, '.', '') , 2), 2, '.', '');
                    $examScopeSchoolNumerator[$examScopeName][$currSchoolName]['excellentScore'] = number_format($examScopeSchoolNumerator[$examScopeName][$currSchoolName]['excellentScore'] + $culScore, 2, '.', '');
                } elseif($studentScore >= $baseScore['passScore'] && $studentScore < $baseScore['excellentScore']) {
                    $culScore = number_format(pow(number_format($currScore - $currTotalAverageScore['passScore'], 2, '.', '') , 2), 2, '.', '');
                    $examScopeTotalNumerator[$examScopeName]['passScore'] = number_format($examScopeTotalNumerator[$examScopeName]['passScore'] + $culScore, 2, '.', '');

                    $culScore = number_format(pow(number_format($currScore - $currSchoolAverageScore['passScore'], 2, '.', '') , 2), 2, '.', '');
                    $examScopeSchoolNumerator[$examScopeName][$currSchoolName]['passScore'] = number_format($examScopeSchoolNumerator[$examScopeName][$currSchoolName]['passScore'] + $culScore, 2, '.', '');
                } else {
                    $culScore = number_format(pow(number_format($currScore - $currTotalAverageScore['failScore'], 2, '.', '') , 2), 2, '.', '');
                    $examScopeTotalNumerator[$examScopeName]['failScore'] = number_format($examScopeTotalNumerator[$examScopeName]['failScore'] + $culScore, 2, '.', '');

                    $culScore = number_format(pow(number_format($currScore - $currSchoolAverageScore['failScore'], 2, '.', '') , 2), 2, '.', '');
                    $examScopeSchoolNumerator[$examScopeName][$currSchoolName]['failScore'] = number_format($examScopeSchoolNumerator[$examScopeName][$currSchoolName]['failScore'] + $culScore, 2, '.', '');
                }
            }

            foreach ($detailTable['examMoldName'] as $examMoldName) {
                $currSchoolName = $studentSchoolList[$num];
                $currScore = $examMoldScoreList[$num][$examMoldName];
                $currTotalAverageScore = $examMoldTotalAverageScore[$examMoldName];
                $currSchoolAverageScore = $examMoldSchoolAverageScore[$examMoldName][$currSchoolName];

                $culScore = number_format(pow(number_format($currScore - $currTotalAverageScore['totalScore'], 2, '.', '') , 2), 2, '.', '');
                $examMoldTotalNumerator[$examMoldName]['totalScore'] = number_format($examMoldTotalNumerator[$examMoldName]['totalScore'] + $culScore, 2, '.', '');

                $culScore = number_format(pow(number_format($currScore - $currSchoolAverageScore['totalScore'], 2, '.', '') , 2), 2, '.', '');
                $examMoldSchoolNumerator[$examMoldName][$currSchoolName]['totalScore'] = number_format($examMoldSchoolNumerator[$examMoldName][$currSchoolName]['totalScore'] + $culScore, 2, '.', '');
                
                if($studentScore >= $baseScore['excellentScore']) {
                    $culScore = number_format(pow(number_format($currScore - $currTotalAverageScore['excellentScore'], 2, '.', '') , 2), 2, '.', '');
                    $examMoldTotalNumerator[$examMoldName]['excellentScore'] = number_format($examMoldTotalNumerator[$examMoldName]['excellentScore'] + $culScore, 2, '.', '');

                    $culScore = number_format(pow(number_format($currScore - $currSchoolAverageScore['excellentScore'], 2, '.', '') , 2), 2, '.', '');
                    $examMoldSchoolNumerator[$examMoldName][$currSchoolName]['excellentScore'] = number_format($examMoldSchoolNumerator[$examMoldName][$currSchoolName]['excellentScore'] + $culScore, 2, '.', '');
                } elseif($studentScore >= $baseScore['passScore'] && $studentScore < $baseScore['excellentScore']) {
                    $culScore = number_format(pow(number_format($currScore - $currTotalAverageScore['passScore'], 2, '.', '') , 2), 2, '.', '');
                    $examMoldTotalNumerator[$examMoldName]['passScore'] = number_format($examMoldTotalNumerator[$examMoldName]['passScore'] + $culScore, 2, '.', '');

                    $culScore = number_format(pow(number_format($currScore - $currSchoolAverageScore['passScore'], 2, '.', '') , 2), 2, '.', '');
                    $examMoldSchoolNumerator[$examMoldName][$currSchoolName]['passScore'] = number_format($examMoldSchoolNumerator[$examMoldName][$currSchoolName]['passScore'] + $culScore, 2, '.', '');
                } else {
                    $culScore = number_format(pow(number_format($currScore - $currTotalAverageScore['failScore'], 2, '.', '') , 2), 2, '.', '');
                    $examMoldTotalNumerator[$examMoldName]['failScore'] = number_format($examMoldTotalNumerator[$examMoldName]['failScore'] + $culScore, 2, '.', '');

                    $culScore = number_format(pow(number_format($currScore - $currSchoolAverageScore['failScore'], 2, '.', '') , 2), 2, '.', '');
                    $examMoldSchoolNumerator[$examMoldName][$currSchoolName]['failScore'] = number_format($examMoldSchoolNumerator[$examMoldName][$currSchoolName]['failScore'] + $culScore, 2, '.', '');
                }
            }
        }

        foreach ($detailTable['examScopeName'] as $name) {
            $examScopeTotalEValue[$name]['totalScore'] = number_format(sqrt(number_format($examScopeTotalNumerator[$name]['totalScore'] / $studentTotalCount['totalCount'], 2, '.', '')), 2, '.', '');
            $examScopeTotalEValue[$name]['excellentScore'] = number_format(sqrt(number_format($examScopeTotalNumerator[$name]['excellentScore'] / $studentTotalCount['excellentCount'], 2, '.', '')), 2, '.', '');
            $examScopeTotalEValue[$name]['passScore'] = number_format(sqrt(number_format($examScopeTotalNumerator[$name]['passScore'] / $studentTotalCount['passCount'], 2, '.', '')), 2, '.', '');
            $examScopeTotalEValue[$name]['failScore'] = number_format(sqrt(number_format($examScopeTotalNumerator[$name]['failScore'] / $studentTotalCount['failCount'], 2, '.', '')), 2, '.', '');

            foreach ($schoolList as $schoolName) {
                $examScopeSchoolCValue[$name][$schoolName]['totalScore'] = number_format(sqrt(number_format($examScopeSchoolNumerator[$name][$schoolName]['totalScore'] / $studentSchoolCount[$schoolName]['totalCount'], 2, '.', '')), 2, '.', '');
                $examScopeSchoolCValue[$name][$schoolName]['excellentScore'] = number_format(sqrt(number_format($examScopeSchoolNumerator[$name][$schoolName]['excellentScore'] / $studentSchoolCount[$schoolName]['excellentCount'], 2, '.', '')), 2, '.', '');
                $examScopeSchoolCValue[$name][$schoolName]['passScore'] = number_format(sqrt(number_format($examScopeSchoolNumerator[$name][$schoolName]['passScore'] / $studentSchoolCount[$schoolName]['passCount'], 2, '.', '')), 2, '.', '');
                $examScopeSchoolCValue[$name][$schoolName]['failScore'] = number_format(sqrt(number_format($examScopeSchoolNumerator[$name][$schoolName]['failScore'] / $studentSchoolCount[$schoolName]['failCount'], 2, '.', '')), 2, '.', '');

                $examScopeTotalSValue[$name][$schoolName]['totalScore'] = number_format(sqrt(number_format((($studentSchoolCount[$schoolName]['totalCount'] - 1) * number_format(pow($examScopeSchoolCValue[$name][$schoolName]['totalScore'], 2), 2, '.', '') + ($studentTotalCount['totalCount'] - 1) * number_format(pow($examScopeTotalEValue[$name]['totalScore'], 2), 2, '.', '')) / ($studentSchoolCount[$schoolName]['totalCount'] + $studentTotalCount['totalCount'] - 2), 2, '.', '')), 2, '.', '');

                $examScopeTotalSValue[$name][$schoolName]['excellentScore'] = number_format(sqrt(number_format((($studentSchoolCount[$schoolName]['excellentCount'] - 1) * number_format(pow($examScopeSchoolCValue[$name][$schoolName]['excellentScore'], 2), 2, '.', '') + ($studentTotalCount['excellentCount'] - 1) * number_format(pow($examScopeTotalEValue[$name]['excellentScore'], 2), 2, '.', '')) / ($studentSchoolCount[$schoolName]['excellentCount'] + $studentTotalCount['excellentCount'] - 2), 2, '.', '')), 2, '.', '');

                $examScopeTotalSValue[$name][$schoolName]['passScore'] = number_format(sqrt(number_format((($studentSchoolCount[$schoolName]['passCount'] - 1) * number_format(pow($examScopeSchoolCValue[$name][$schoolName]['passScore'], 2), 2, '.', '') + ($studentTotalCount['passCount'] - 1) * number_format(pow($examScopeTotalEValue[$name]['passScore'], 2), 2, '.', '')) / ($studentSchoolCount[$schoolName]['passCount'] + $studentTotalCount['passCount'] - 2), 2, '.', '')), 2, '.', '');

                $examScopeTotalSValue[$name][$schoolName]['failScore'] = number_format(sqrt(number_format((($studentSchoolCount[$schoolName]['failCount'] - 1) * number_format(pow($examScopeSchoolCValue[$name][$schoolName]['failScore'], 2), 2, '.', '') + ($studentTotalCount['failCount'] - 1) * number_format(pow($examScopeTotalEValue[$name]['failScore'], 2), 2, '.', '')) / ($studentSchoolCount[$schoolName]['failCount'] + $studentTotalCount['failCount'] - 2), 2, '.', '')), 2, '.', '');

                $examScopeTotalDValue[$name][$schoolName]['totalScore'] = number_format(($examScopeSchoolAverageScore[$name][$schoolName]['totalScore'] - $examScopeTotalAverageScore[$name]['totalScore']) / $examScopeTotalSValue[$name][$schoolName]['totalScore'], 2, '.', '');
                $examScopeTotalDValue[$name][$schoolName]['excellentScore'] = number_format(($examScopeSchoolAverageScore[$name][$schoolName]['excellentScore'] - $examScopeTotalAverageScore[$name]['excellentScore']) / $examScopeTotalSValue[$name][$schoolName]['excellentScore'], 2, '.', '');
                $examScopeTotalDValue[$name][$schoolName]['passScore'] = number_format(($examScopeSchoolAverageScore[$name][$schoolName]['passScore'] - $examScopeTotalAverageScore[$name]['passScore']) / $examScopeTotalSValue[$name][$schoolName]['passScore'], 2, '.', '');
                $examScopeTotalDValue[$name][$schoolName]['failScore'] = number_format(($examScopeSchoolAverageScore[$name][$schoolName]['failScore'] - $examScopeTotalAverageScore[$name]['failScore']) / $examScopeTotalSValue[$name][$schoolName]['failScore'], 2, '.', '');
            }
        }

        foreach ($detailTable['examMoldName'] as $name) {
            $examMoldTotalEValue[$name]['totalScore'] = number_format(sqrt(number_format($examMoldTotalNumerator[$name]['totalScore'] / $studentTotalCount['totalCount'], 2, '.', '')), 2, '.', '');
            $examMoldTotalEValue[$name]['excellentScore'] = number_format(sqrt(number_format($examMoldTotalNumerator[$name]['excellentScore'] / $studentTotalCount['excellentCount'], 2, '.', '')), 2, '.', '');
            $examMoldTotalEValue[$name]['passScore'] = number_format(sqrt(number_format($examMoldTotalNumerator[$name]['passScore'] / $studentTotalCount['passCount'], 2, '.', '')), 2, '.', '');
            $examMoldTotalEValue[$name]['failScore'] = number_format(sqrt(number_format($examMoldTotalNumerator[$name]['failScore'] / $studentTotalCount['failCount'], 2, '.', '')), 2, '.', '');

            foreach ($schoolList as $schoolName) {
                $examMoldSchoolCValue[$name][$schoolName]['totalScore'] = number_format(sqrt(number_format($examMoldSchoolNumerator[$name][$schoolName]['totalScore'] / $studentSchoolCount[$schoolName]['totalCount'], 2, '.', '')), 2, '.', '');
                $examMoldSchoolCValue[$name][$schoolName]['excellentScore'] = number_format(sqrt(number_format($examMoldSchoolNumerator[$name][$schoolName]['excellentScore'] / $studentSchoolCount[$schoolName]['excellentCount'], 2, '.', '')), 2, '.', '');
                $examMoldSchoolCValue[$name][$schoolName]['passScore'] = number_format(sqrt(number_format($examMoldSchoolNumerator[$name][$schoolName]['passScore'] / $studentSchoolCount[$schoolName]['passCount'], 2, '.', '')), 2, '.', '');
                $examMoldSchoolCValue[$name][$schoolName]['failScore'] = number_format(sqrt(number_format($examMoldSchoolNumerator[$name][$schoolName]['failScore'] / $studentSchoolCount[$schoolName]['failCount'], 2, '.', '')), 2, '.', '');

                $examMoldTotalSValue[$name][$schoolName]['totalScore'] = number_format(sqrt(number_format((($studentSchoolCount[$schoolName]['totalCount'] - 1) * number_format(pow($examMoldSchoolCValue[$name][$schoolName]['totalScore'], 2), 2, '.', '') + ($studentTotalCount['totalCount'] - 1) * number_format(pow($examMoldTotalEValue[$name]['totalScore'], 2), 2, '.', '')) / ($studentSchoolCount[$schoolName]['totalCount'] + $studentTotalCount['totalCount'] - 2), 2, '.', '')), 2, '.', '');

                $examMoldTotalSValue[$name][$schoolName]['excellentScore'] = number_format(sqrt(number_format((($studentSchoolCount[$schoolName]['excellentCount'] - 1) * number_format(pow($examMoldSchoolCValue[$name][$schoolName]['excellentScore'], 2), 2, '.', '') + ($studentTotalCount['excellentCount'] - 1) * number_format(pow($examMoldTotalEValue[$name]['excellentScore'], 2), 2, '.', '')) / ($studentSchoolCount[$schoolName]['excellentCount'] + $studentTotalCount['excellentCount'] - 2), 2, '.', '')), 2, '.', '');

                $examMoldTotalSValue[$name][$schoolName]['passScore'] = number_format(sqrt(number_format((($studentSchoolCount[$schoolName]['passCount'] - 1) * number_format(pow($examMoldSchoolCValue[$name][$schoolName]['passScore'], 2), 2, '.', '') + ($studentTotalCount['passCount'] - 1) * number_format(pow($examMoldTotalEValue[$name]['passScore'], 2), 2, '.', '')) / ($studentSchoolCount[$schoolName]['passCount'] + $studentTotalCount['passCount'] - 2), 2, '.', '')), 2, '.', '');

                $examMoldTotalSValue[$name][$schoolName]['failScore'] = number_format(sqrt(number_format((($studentSchoolCount[$schoolName]['failCount'] - 1) * number_format(pow($examMoldSchoolCValue[$name][$schoolName]['failScore'], 2), 2, '.', '') + ($studentTotalCount['failCount'] - 1) * number_format(pow($examMoldTotalEValue[$name]['failScore'], 2), 2, '.', '')) / ($studentSchoolCount[$schoolName]['failCount'] + $studentTotalCount['failCount'] - 2), 2, '.', '')), 2, '.', '');

                $examMoldTotalDValue[$name][$schoolName]['totalScore'] = number_format(($examMoldSchoolAverageScore[$name][$schoolName]['totalScore'] - $examMoldTotalAverageScore[$name]['totalScore']) / $examMoldTotalSValue[$name][$schoolName]['totalScore'], 2, '.', '');
                $examMoldTotalDValue[$name][$schoolName]['excellentScore'] = number_format(($examMoldSchoolAverageScore[$name][$schoolName]['excellentScore'] - $examMoldTotalAverageScore[$name]['excellentScore']) / $examMoldTotalSValue[$name][$schoolName]['excellentScore'], 2, '.', '');
                $examMoldTotalDValue[$name][$schoolName]['passScore'] = number_format(($examMoldSchoolAverageScore[$name][$schoolName]['passScore'] - $examMoldTotalAverageScore[$name]['passScore']) / $examMoldTotalSValue[$name][$schoolName]['passScore'], 2, '.', '');
                $examMoldTotalDValue[$name][$schoolName]['failScore'] = number_format(($examMoldSchoolAverageScore[$name][$schoolName]['failScore'] - $examScopeTotalAverageScore[$name]['failScore']) / $examScopeTotalSValue[$name][$schoolName]['failScore'], 2, '.', '');
            }
        }

        $dVauleData = array(
            'examScopeTotalDValue'   => $examScopeTotalDValue, // 全区考核范畴D值
            'examMoldTotalDValue'    => $examMoldTotalDValue, // 全校考核层级D值

            /* 为了测试数据用，调试时用
            'examScopeTotalSValue'   => $examScopeTotalSValue, // 全区考核范畴D值分母
            'examMoldTotalSValue'    => $examMoldTotalSValue, // 全校考核层级D值分母

            'examScopeTotalEValue'   => $examScopeTotalEValue, // 全区考核范畴对照组标准差E值
            'examScopeSchoolCValue'  => $examScopeSchoolCValue, // 全校考核范畴对比组标准差C值
            'examMoldTotalEValue'    => $examMoldTotalEValue, // 全区考核层级对照组标准差E值
            'examMoldSchoolCValue'   => $examMoldSchoolCValue, // 全校考核层级对比组标准差C值

            'examScopeTotalNumerator'   => $examScopeTotalNumerator, // 全区考核范畴对照组标准差分子
            'examScopeSchoolNumerator'  => $examScopeSchoolNumerator, // 全校考核范畴对比组标准差分子
            'examMoldTotalNumerator'    => $examMoldTotalNumerator, // 全区考核层级对照组标准差分子
            'examMoldSchoolNumerator'   => $examMoldSchoolNumerator, // 全校考核层级对比组标准差分子
            */
        );

        return $dVauleData;
    }
}

?>