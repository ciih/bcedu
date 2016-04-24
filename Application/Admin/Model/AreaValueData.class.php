<?php

/**
 * 获取学科双向明细表
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class AreaValueData {

    /**
     * 获取学校列表
     * @var array
     */
    protected static $schoolInfo;

    /**
     * 获取科目双向细目表
     * @var array
     */
    protected static $detailTableData;

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
     * 获取综合指标
     * @var array
     */
    protected static $comprehensiveIndicatorsData;

    /**
     * 构造
     * @param $examInfoData 文件夹名称（包含信息：学年、学期、年级、考试名称）
     */
    function __construct($schoolInfoData, $detailTableData, $comprehensiveIndicatorsData, $studentScoreData, $scoreStatisticsData)
    {
        self::$schoolInfo = $schoolInfoData;
        self::$detailTableData = $detailTableData;
        self::$comprehensiveIndicatorsData = $comprehensiveIndicatorsData;
        self::$studentScore = $studentScoreData;
        self::$scoreStatistics = $scoreStatisticsData;
    }

    /**
     * 获取双向明细表数据
     */
    public function getAreaValueData()
    {

        $courseTotalScore = self::$detailTableData['totalScore']; // 该科总分
        $areaNameList = self::$schoolInfo['areaName']; // 区域名称列表

        $studentScoreList = self::$studentScore['studentScoreList']; // 学生分数列表
        $studentSchoolList = self::$studentScore['studentSchoolList']; // 学生所属学校列表

        $areaValueData = array(); // 区域分数统计

        $areaStudentCount = array(); // 区域学生人数
        $areaHighestScore = array(); // 区域最高分
        $areaLowestScore = array(); // 区域最低分
        $areaTotalScore = array(); // 区域学生分数总和
        $areaTotalAverageScore = array(); // 区域总平均分
        $areaTotalRate = array(); // 区域总得分率
        $areaTotalCValue = array(); // 区域总标准差

        $areaCValueNumerator = array(); // 区域标准差分子

        $totalStudentCount = self::$studentScore['totalCount']['totalCount']; // 全区总人数
        $totalHighestScore = self::$comprehensiveIndicatorsData['totalHighestScore']; // 全区最高分
        $totalLowestScore = self::$comprehensiveIndicatorsData['totalLowestScore']; // 全区最低分
        $totalAverageScore = self::$comprehensiveIndicatorsData['totalAverageScore']; // 全区总人平均分
        $totalRate = 0; // 全区得分率
        $totalCValueNumerator = 0; // 全区标准差分子
        $totalCValue = 0; // 全区总标准差
        

        $examScopeTotalRate = self::$scoreStatistics['examScopeTotalRate']; // 全区统计范畴得分率
        $examMoldTotalRate = self::$scoreStatistics['examMoldTotalRate']; // 全区考核层级得分率
        
        $areaExamScopeTotalScore = array(); // 区域统计范畴总分
        $areaExamMoldTotalScore = array(); // 区域考核层级总分
        
        $areaExamScopeTotalRate = array(); // 区域统计范畴总分
        $areaExamMoldTotalRate = array(); // 区域考核层级总分


        foreach ($areaNameList as $name) {
            $areaStudentCount[$name]['totalCount'] = 0;
            $areaStudentCount[$name]['excellentCount'] = 0;
            $areaStudentCount[$name]['passCount'] = 0;
            $areaStudentCount[$name]['failCount'] = 0;
        }

        // 统计区域总人数、优秀人数、及格人数、未及格人数；区域最高列表、最低分列表
        foreach (self::$schoolInfo['schoolList'] as $schoolName) {
            $areaName = self::$schoolInfo['schoolArea'][$schoolName];

            $areaStudentCount[$areaName]['totalCount'] = $areaStudentCount[$areaName]['totalCount'] + self::$studentScore['totalSchoolCount'][$schoolName]['totalCount'];
            $areaStudentCount[$areaName]['excellentCount'] = $areaStudentCount[$areaName]['excellentCount'] + self::$studentScore['totalSchoolCount'][$schoolName]['excellentCount'];
            $areaStudentCount[$areaName]['passCount'] = $areaStudentCount[$areaName]['passCount'] + self::$studentScore['totalSchoolCount'][$schoolName]['passCount'];
            $areaStudentCount[$areaName]['failCount'] = $areaStudentCount[$areaName]['failCount'] + self::$studentScore['totalSchoolCount'][$schoolName]['failCount'];

            $areaHighestScoreList[$areaName][] = self::$comprehensiveIndicatorsData['totalSchoolHighestScore'][$schoolName];
            $areaLowestScoreList[$areaName][] = self::$comprehensiveIndicatorsData['totalSchoolLowestScore'][$schoolName];
        }

        // 统计区域最高、最低分
        foreach ($areaNameList as $name) {
            rsort($areaHighestScoreList[$name]);
            sort($areaLowestScoreList[$name]);
            $areaHighestScore[$name] = $areaHighestScoreList[$name][0];
            $areaLowestScore[$name] = $areaLowestScoreList[$name][0];
        }

        // 统计区域总得分
        foreach ($studentScoreList as $key => $value) {
            $areaName = self::$schoolInfo['schoolArea'][$studentSchoolList[$key]];
            $areaTotalScore[$areaName] = number_format($areaTotalScore[$areaName] + $value, 2, '.', '');
        }

        // 统计全区得分率
        $totalRate = number_format($totalAverageScore / $courseTotalScore, 2, '.', '');

        // 统计区域总平均分、总得分率
        foreach ($areaNameList as $key => $name) {
            $areaTotalAverageScore[$name] = number_format($areaTotalScore[$name] / $areaStudentCount[$name]['totalCount'], 2, '.', '');
            $areaTotalRate[$name] = number_format($areaTotalAverageScore[$name] / $courseTotalScore, 2, '.', '');
        }

        // 统计全区、区域标准差分子
        foreach ($studentScoreList as $key => $value) {
            $areaName = self::$schoolInfo['schoolArea'][$studentSchoolList[$key]];
            $totalCValueNumerator = number_format($totalCValueNumerator + number_format(pow(number_format($value - $totalAverageScore, 2, '.', '') , 2), 2, '.', ''), 2, '.', '');
            $areaCValueNumerator[$areaName] = number_format($areaCValueNumerator[$areaName] + number_format(pow(number_format($value - $areaTotalAverageScore[$areaName], 2, '.', '') , 2), 2, '.', ''), 2, '.', '');
        }

         // 统计全区标准差
        $totalCValue = number_format(sqrt($totalCValueNumerator / $totalStudentCount), 2, '.', '');

        // 统计区域标准差
        foreach ($areaNameList as $key => $name) {
            $areaTotalCValue[$name] = number_format(sqrt($areaCValueNumerator[$name] / $areaStudentCount[$name]['totalCount']), 2, '.', '');
        }

        // 统计区域知识范畴总得分
        foreach (self::$scoreStatistics['examScopeSchoolTotalScore'] as $itemName => $schoolScore) {
            foreach ($schoolScore as $schoolName => $value) {
                $areaName = self::$schoolInfo['schoolArea'][$schoolName];
                $areaExamScopeTotalScore[$itemName][$areaName]['totalScore'] = number_format($areaExamScopeTotalScore[$itemName][$areaName]['totalScore'] + $value['totalScore'], 2, '.', '');
                $areaExamScopeTotalScore[$itemName][$areaName]['excellentScore'] = number_format($areaExamScopeTotalScore[$itemName][$areaName]['excellentScore'] + $value['excellentScore'], 2, '.', '');
                $areaExamScopeTotalScore[$itemName][$areaName]['passScore'] = number_format($areaExamScopeTotalScore[$itemName][$areaName]['passScore'] + $value['passScore'], 2, '.', '');
                $areaExamScopeTotalScore[$itemName][$areaName]['failScore'] = number_format($areaExamScopeTotalScore[$itemName][$areaName]['failScore'] + $value['failScore'], 2, '.', '');
            }
        }

        // 统计区域知识范畴得分率
        foreach ($areaExamScopeTotalScore as $itemName => $areaScore) {
            foreach ($areaScore as $name => $value) {
                $areaExamScopeTotalRate[$itemName][$name]['totalRate'] = number_format($value['totalScore'] / $areaStudentCount[$name]['totalCount'] / self::$detailTableData['examScopeTotalScore'][$itemName], 2, '.', '');
                $areaExamScopeTotalRate[$itemName][$name]['excellentRate'] = number_format($value['excellentScore'] / $areaStudentCount[$name]['excellentCount'] / self::$detailTableData['examScopeTotalScore'][$itemName], 2, '.', '');
                $areaExamScopeTotalRate[$itemName][$name]['passRate'] = number_format($value['passScore'] / $areaStudentCount[$name]['passCount'] / self::$detailTableData['examScopeTotalScore'][$itemName], 2, '.', '');
                $areaExamScopeTotalRate[$itemName][$name]['failRate'] = number_format($value['failScore'] / $areaStudentCount[$name]['failCount'] / self::$detailTableData['examScopeTotalScore'][$itemName], 2, '.', '');
            }
        }

        // 统计区域考核层级总得分
        foreach (self::$scoreStatistics['examMoldSchoolTotalScore'] as $itemName => $schoolScore) {
            foreach ($schoolScore as $schoolName => $value) {
                $areaName = self::$schoolInfo['schoolArea'][$schoolName];
                $areaExamMoldTotalScore[$itemName][$areaName]['totalScore'] = number_format($areaExamMoldTotalScore[$itemName][$areaName]['totalScore'] + $value['totalScore'], 2, '.', '');
                $areaExamMoldTotalScore[$itemName][$areaName]['excellentScore'] = number_format($areaExamMoldTotalScore[$itemName][$areaName]['excellentScore'] + $value['excellentScore'], 2, '.', '');
                $areaExamMoldTotalScore[$itemName][$areaName]['passScore'] = number_format($areaExamMoldTotalScore[$itemName][$areaName]['passScore'] + $value['passScore'], 2, '.', '');
                $areaExamMoldTotalScore[$itemName][$areaName]['failScore'] = number_format($areaExamMoldTotalScore[$itemName][$areaName]['failScore'] + $value['failScore'], 2, '.', '');
            }
        }

        // 统计区域考核层级得分率
        foreach ($areaExamMoldTotalScore as $itemName => $areaScore) {
            foreach ($areaScore as $name => $value) {
                $areaExamMoldTotalRate[$itemName][$name]['totalRate'] = number_format($value['totalScore'] / $areaStudentCount[$name]['totalCount'] / self::$detailTableData['examMoldTotalScore'][$itemName], 2, '.', '');
                $areaExamMoldTotalRate[$itemName][$name]['excellentRate'] = number_format($value['excellentScore'] / $areaStudentCount[$name]['excellentCount'] / self::$detailTableData['examMoldTotalScore'][$itemName], 2, '.', '');
                $areaExamMoldTotalRate[$itemName][$name]['passRate'] = number_format($value['passScore'] / $areaStudentCount[$name]['passCount'] / self::$detailTableData['examMoldTotalScore'][$itemName], 2, '.', '');
                $areaExamMoldTotalRate[$itemName][$name]['failRate'] = number_format($value['failScore'] / $areaStudentCount[$name]['failCount'] / self::$detailTableData['examMoldTotalScore'][$itemName], 2, '.', '');
            }
        }

        
        
        

        $areaValueData = array(
            'areaExamScopeTotalRate' => $areaExamScopeTotalRate, // 区域知识范畴得分率
            'areaExamMoldTotalRate'  => $areaExamMoldTotalRate, // 区域考核层级得分率
            'areaExamScopeTotalScore'=> $areaExamScopeTotalScore, // 区域知识范畴总得分
            'areaExamMoldTotalScore' => $areaExamMoldTotalScore, // 区域考核层级总得分

            'totalStudentCount'      => $totalStudentCount, // 全区学生人数
            'totalAverageScore'      => $totalAverageScore, // 全区平均分
            'totalHighestScore'      => $totalHighestScore, // 全区最高分
            'totalLowestScore'       => $totalLowestScore, // 全区最低分
            'totalRate'              => $totalRate, // 全区得分率
            'totalCValue'            => $totalCValue, // 全区标准差
            'areaStudentCount'       => $areaStudentCount, // 区域学生人数
            'areaTotalAverageScore'  => $areaTotalAverageScore, // 区域总平均分
            'areaHighestScore'       => $areaHighestScore, // 区域最高分
            'areaLowestScore'        => $areaLowestScore, // 区域最低分
            'areaTotalRate'          => $areaTotalRate, // 区域总得分率
            'areaTotalCValue'        => $areaTotalCValue, // 区域总标准差
        );

        var_export('===========$areaValueData==============');
        var_export($areaValueData);
        exit();

        return $areaValueData;
    }

}

?>