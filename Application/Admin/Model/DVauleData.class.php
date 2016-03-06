<?php

/**
 * 获取C/E/D值
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class DVauleData {

    /**
     * 获取学生分数(小题分)
     * @var array
     */
    protected static $studentScoreData;

    /**
     * 获取分数统计
     * @var array
     */
    protected static $scoreStatisticsData;

    /**
     * 获取学生所在学校列表
     * @var array
     */
    protected static $studentSchoolList;

    /**
     * 构造
     */
    function __construct($studentScoreData, $scoreStatisticsData)
    {
        self::$studentScoreData = $studentScoreData;
        self::$scoreStatisticsData = $scoreStatisticsData;
    }

    /**
     * 获取标准差及D值统计
     */
    public function getDVauleData()
    {
        $baseScore          = self::$studentScoreData['baseScore'];
        $examScopeScoreList = self::$studentScoreData['examScopeScoreList'];
        $examMoldScoreList  = self::$studentScoreData['examMoldScoreList'];
        $studentScoreList   = self::$studentScoreData['studentScoreList'];
        $studentSchoolList  = self::$studentScoreData['studentSchoolList'];

        $examScopeTotalAverageScore  = self::$scoreStatisticsData['examScopeTotalAverageScore'];
        $examScopeSchoolAverageScore = self::$scoreStatisticsData['examScopeSchoolAverageScore'];

        $examMoldTotalAverageScore  = self::$scoreStatisticsData['examMoldTotalAverageScore'];
        $examMoldSchoolAverageScore = self::$scoreStatisticsData['examMoldSchoolAverageScore'];
        

        $examScopeTotalNumerator = array(); // 全区考核范畴对照组分子
        $examScopeSchoolNumerator = array(); // 全校考核范畴对比组分子

        $examMoldTotalNumerator = array(); // 全区考核层级对照组分子
        $examMoldSchoolNumerator = array(); // 全校考核层级对比组分子

        $cValue = array();
        $eValue = array();

        $dValue = array();

        $dVauleData = array(); // C/E/D值数据

        // $examScopeTotalNumerator['totalScore'] = 0;
        // $examScopeTotalNumerator['excellentScore'] = 0;
        // $examScopeTotalNumerator['passScore'] = 0;
        // $examScopeTotalNumerator['failScore'] = 0;

        // $examMoldTotalNumerator['totalScore'] = 0;
        // $examMoldTotalNumerator['excellentScore'] = 0;
        // $examMoldTotalNumerator['passScore'] = 0;
        // $examMoldTotalNumerator['failScore'] = 0;

        foreach ($examScopeScoreList as $num => $listItem) {
            foreach ($listItem as $examScopeName => $score) {
                $examScopeTotalNumerator[$examScopeName]['totalScore'] = number_format($examScopeTotalNumerator[$examScopeName]['totalScore'] + pow($score - $examScopeTotalAverageScore[$examScopeName]['totalScore'] , 2), 2, '.', '');
                $examScopeSchoolNumerator[$examScopeName][$studentSchoolList[$num]]['totalScore'] = number_format($examScopeTotalNumerator[$examScopeName][$studentSchoolList[$num]]['totalScore'] + pow($score - $examScopeSchoolAverageScore[$examScopeName][$studentSchoolList[$num]]['totalScore'] , 2), 2, '.', '');
                if($studentScoreList[$num] >= $baseScore['excellentScore']) {
                    $examScopeTotalNumerator[$examScopeName]['excellentScore'] = number_format($examScopeTotalNumerator[$examScopeName]['excellentScore'] + pow($score - $examScopeTotalAverageScore[$examScopeName]['excellentScore'] , 2), 2, '.', '');
                    $examScopeSchoolNumerator[$examScopeName][$studentSchoolList[$num]]['excellentScore'] = number_format($examScopeTotalNumerator[$examScopeName]['excellentScore'] + pow($score - $examScopeSchoolAverageScore[$examScopeName][$studentSchoolList[$num]]['excellentScore'] , 2), 2, '.', '');
                } elseif($studentScoreList[$num] >= $baseScore['passScore'] && $studentScoreList[$num] < $baseScore['excellentScore']) {
                    $examScopeTotalNumerator[$examScopeName]['passScore'] = number_format($examScopeTotalNumerator[$examScopeName]['passScore'] + pow($score - $examScopeTotalAverageScore[$examScopeName]['passScore'] , 2), 2, '.', '');
                    $examScopeSchoolNumerator[$examScopeName][$studentSchoolList[$num]]['passScore'] = number_format($examScopeTotalNumerator[$examScopeName]['passScore'] + pow($score - $examScopeSchoolAverageScore[$examScopeName][$studentSchoolList[$num]]['passScore'] , 2), 2, '.', '');
                } else {
                    $examScopeTotalNumerator[$examScopeName]['failScore'] = number_format($examScopeTotalNumerator[$examScopeName]['failScore'] + pow($score - $examScopeTotalAverageScore[$examScopeName]['failScore'] , 2), 2, '.', '');
                    $examScopeSchoolNumerator[$examScopeName][$studentSchoolList[$num]]['failScore'] = number_format($examScopeTotalNumerator[$examScopeName]['failScore'] + pow($score - $examScopeSchoolAverageScore[$examScopeName][$studentSchoolList[$num]]['failScore'] , 2), 2, '.', '');
                }
            }
        }

        $dVauleData = array(
            'examScopeTotalNumerator'        => $examScopeTotalNumerator, // 考核范畴分数统计
            'examScopeSchoolNumerator'        => $examScopeSchoolNumerator, // 考核层级分数统计
            /*'cValue'      => $cValue, // 对比组标准差统计
            'eValue'      => $eValue, // 对照组标准差统计
            'dValue'      => $dValue, // D值*/
        );

        var_export('================dVauleData========================');
        var_export($dVauleData);
        exit();

        /*foreach ($exam as $examItem => $item) {
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
        }*/

        /*foreach ($type as $typeItem => $item) {
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
        }*/

        /*foreach ($cValue['exam']['schoolScore'] as $schoolName => $schoolList) {
            foreach ($schoolList as $itemName => $itemList) {
                foreach ($itemList as $key => $value) {
                    if($key == 'totalScore') {
                        $dValue['exam']['schoolScore'][$schoolName][$itemName][$key] = number_format(($scoreStatisticsData['examAverageScore'][$itemName]['schoolScore'][$schoolName][$key] - $scoreStatisticsData['examAverageScore'][$itemName]['total'][$key]) / sqrt(((($scoreStatisticsData['schoolCount'][$schoolName]['totalCount']-1) * pow($value ,2)) + (($scoreStatisticsData['count']['totalCount']-1) * pow($eValue['exam']['total'][$itemName][$key] ,2))) / ($scoreStatisticsData['schoolCount'][$schoolName]['totalCount'] + $scoreStatisticsData['count']['totalCount'] - 2)), 2, '.', '');
                    } elseif($key == 'excellentScore'){
                        $dValue['exam']['schoolScore'][$schoolName][$itemName][$key] = number_format(($scoreStatisticsData['examAverageScore'][$itemName]['schoolScore'][$schoolName][$key] - $scoreStatisticsData['examAverageScore'][$itemName]['total'][$key]) / sqrt(((($scoreStatisticsData['schoolCount'][$schoolName]['excellentCount']-1) * pow($value ,2)) + (($scoreStatisticsData['count']['excellentCount']-1) * pow($eValue['total'][$itemName][$key] ,2))) / ($scoreStatisticsData['schoolCount'][$schoolName]['excellentCount'] + $scoreStatisticsData['count']['excellentCount'] - 2)), 2, '.', '');
                    } elseif($key == 'passScore'){
                        $dValue['exam']['schoolScore'][$schoolName][$itemName][$key] = number_format(($scoreStatisticsData['examAverageScore'][$itemName]['schoolScore'][$schoolName][$key] - $scoreStatisticsData['examAverageScore'][$itemName]['total'][$key]) / sqrt(((($scoreStatisticsData['schoolCount'][$schoolName]['passCount']-1) * pow($value ,2)) + (($scoreStatisticsData['count']['passCount']-1) * pow($eValue['total'][$itemName][$key] ,2))) / ($scoreStatisticsData['schoolCount'][$schoolName]['passCount'] + $scoreStatisticsData['count']['passCount'] - 2)), 2, '.', '');
                    } elseif($key == 'failScore'){
                        $dValue['exam']['schoolScore'][$schoolName][$itemName][$key] = number_format(($scoreStatisticsData['examAverageScore'][$itemName]['schoolScore'][$schoolName][$key] - $scoreStatisticsData['examAverageScore'][$itemName]['total'][$key]) / sqrt(((($scoreStatisticsData['schoolCount'][$schoolName]['failCount']-1) * pow($value ,2)) + (($scoreStatisticsData['count']['failCount']-1) * pow($eValue['total'][$itemName][$key] ,2))) / ($scoreStatisticsData['schoolCount'][$schoolName]['failCount'] + $scoreStatisticsData['count']['failCount'] - 2)), 2, '.', '');
                    }
                }
            }
        }*/

        /*foreach ($cValue['type']['schoolScore'] as $schoolName => $schoolList) {
            foreach ($schoolList as $itemName => $itemList) {
                foreach ($itemList as $key => $value) {
                    if($key == 'totalScore') {
                        $dValue['type']['schoolScore'][$schoolName][$itemName][$key] = number_format(($scoreStatisticsData['typeAverageScore'][$itemName]['schoolScore'][$schoolName][$key] - $scoreStatisticsData['typeAverageScore'][$itemName]['total'][$key]) / sqrt(((($scoreStatisticsData['schoolCount'][$schoolName]['totalCount']-1) * pow($value ,2)) + (($scoreStatisticsData['count']['totalCount']-1) * pow($eValue['type']['total'][$itemName][$key] ,2))) / ($scoreStatisticsData['schoolCount'][$schoolName]['totalCount'] + $scoreStatisticsData['count']['totalCount'] - 2)), 2, '.', '');
                    } elseif($key == 'excellentScore'){
                        $dValue['type']['schoolScore'][$schoolName][$itemName][$key] = number_format(($scoreStatisticsData['typeAverageScore'][$itemName]['schoolScore'][$schoolName][$key] - $scoreStatisticsData['typeAverageScore'][$itemName]['total'][$key]) / sqrt(((($scoreStatisticsData['schoolCount'][$schoolName]['excellentCount']-1) * pow($value ,2)) + (($scoreStatisticsData['count']['excellentCount']-1) * pow($eValue['total'][$itemName][$key] ,2))) / ($scoreStatisticsData['schoolCount'][$schoolName]['excellentCount'] + $scoreStatisticsData['count']['excellentCount'] - 2)), 2, '.', '');
                    } elseif($key == 'passScore'){
                        $dValue['type']['schoolScore'][$schoolName][$itemName][$key] = number_format(($scoreStatisticsData['typeAverageScore'][$itemName]['schoolScore'][$schoolName][$key] - $scoreStatisticsData['typeAverageScore'][$itemName]['total'][$key]) / sqrt(((($scoreStatisticsData['schoolCount'][$schoolName]['passCount']-1) * pow($value ,2)) + (($scoreStatisticsData['count']['passCount']-1) * pow($eValue['total'][$itemName][$key] ,2))) / ($scoreStatisticsData['schoolCount'][$schoolName]['passCount'] + $scoreStatisticsData['count']['passCount'] - 2)), 2, '.', '');
                    } elseif($key == 'failScore'){
                        $dValue['type']['schoolScore'][$schoolName][$itemName][$key] = number_format(($scoreStatisticsData['typeAverageScore'][$itemName]['schoolScore'][$schoolName][$key] - $scoreStatisticsData['typeAverageScore'][$itemName]['total'][$key]) / sqrt(((($scoreStatisticsData['schoolCount'][$schoolName]['failCount']-1) * pow($value ,2)) + (($scoreStatisticsData['count']['failCount']-1) * pow($eValue['total'][$itemName][$key] ,2))) / ($scoreStatisticsData['schoolCount'][$schoolName]['failCount'] + $scoreStatisticsData['count']['failCount'] - 2)), 2, '.', '');
                    }
                }
            }
        }*/

        /*$data = array(
            'exam'        => $exam, // 考核范畴分数统计
            'type'        => $type, // 考核层级分数统计
            'cValue'      => $cValue, // 对比组标准差统计
            'eValue'      => $eValue, // 对照组标准差统计
            'dValue'      => $dValue, // D值
        );*/

        /*var_export($chenhong);
        var_export('===============data[dValue] start=================');
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

        

        $dVauleData = array(
        );

        return $dVauleData;
    }
}

?>