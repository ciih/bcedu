<?php

/**
 * 获取增值性评价
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class ZValueData {

    /**
     * 考试信息
     * @var array
     */
    protected static $examInfoData;

    /**
     * Excel表主路径
     * @var string
     */
    protected static $basePath;

    /**
     * 打开excel表对象
     * @var obj
     */
    protected static $excelFile;

    /**
     * 获取学校列表
     * @var array
     */
    protected static $schoolInfoData;

    /**
     * 获取考试课程列表
     * @var string
     */
    protected static $courseListData;
   
    /**
     * 平均分对比表
     * @var string
     */
    protected static $averageContrast = '平均分对比';
   
    /**
     * 平均分对比_理科表
     * @var string
     */
    protected static $averageContrastScience = '平均分对比_理科';

    /**
     * 平均分对比_文科表
     * @var string
     */
    protected static $averageContrastArts = '平均分对比_文科';

    /**
     * 综合指标表名
     * @var string
     */
    protected static $comprehensiveIndicators = '综合指标';


    /**
     * 构造
     * @param $examInfoData 文件夹名称（包含信息：学年、学期、年级、考试名称）
     */
    function __construct($date, $foldername)
    {
        // 获取考试数据目录
        $examInfoObj = new \Admin\Model\ExamInfoData($date, $foldername);
        self::$examInfoData = $examInfoObj->getExamInfoData();

        self::$basePath = self::$examInfoData['rootDir'].self::$examInfoData['uploadDate'].'/'.self::$examInfoData['fullname'].'/';

        // 获取学校列表
        $schoolInfoObj = new \Admin\Model\SchoolInfoData();
        self::$schoolInfoData = $schoolInfoObj->getSchoolData(self::$examInfoData['schoolType']);

        // 获取所有科目列表
        $courseObj = new \Admin\Model\CourseData(self::$examInfoData);
        self::$courseListData = $courseObj->getCourseData();

        self::$excelFile = new \Admin\Model\ExeclFile();
    }

    /**
     * 获取综合指标
     */
    private function getComprehensiveIndicatorsData()
    {
        $fileCPath = self::$basePath.self::$examInfoData['mainDir'].'/'.'语文'.'/';
        $fileEPath = self::$basePath.self::$examInfoData['mainDir'].'/'.'英语'.'/';
        $filename = self::$comprehensiveIndicators;

        $excelCData = self::$excelFile->openExcel($fileCPath, $filename);
        $excelEData = self::$excelFile->openExcel($fileEPath, $filename);

        $keys = array(); // 平均分字段名

        $totalCAverageScore = 0; // 全区语文平均分
        $totalEAverageScore = 0; // 全区英语平均分

        $totalSchoolCAverageScore = array(); // 全校语文平均分
        $totalSchoolEAverageScore = array(); // 全校英语平均分

        $comprehensiveIndicatorsData = array(); // 平均分对比数据

        foreach($excelCData->getRowIterator() as $kr => $row){
            $cellIterator = $row->getCellIterator();

            if ($kr > 1) {
                foreach($cellIterator as $kc => $cell){
                    if ($kc == 0) {
                        $schoolName = $cell->getValue();
                    }
                    if ($kc == 3) {
                        $totalSchoolCAverageScore[$schoolName] = number_format($cell->getValue(), 2, '.', '');
                    }
                }
            }
        }

        foreach($excelEData->getRowIterator() as $kr => $row){
            $cellIterator = $row->getCellIterator();

            if ($kr > 1) {
                foreach($cellIterator as $kc => $cell){
                    if ($kc == 0) {
                        $schoolName = $cell->getValue();
                    }
                    if ($kc == 3) {
                        $totalSchoolEAverageScore[$schoolName] = number_format($cell->getValue(), 2, '.', '');
                    }
                }
            }
        }

        $totalCAverageScore = array_pop($totalSchoolCAverageScore);

        $totalEAverageScore = array_pop($totalSchoolEAverageScore);

        $comprehensiveIndicatorsData = array(
            'totalCAverageScore'       => $totalCAverageScore, // 全区语文平均分
            'totalSchoolCAverageScore' => $totalSchoolCAverageScore, // 全校语文平均分
            'totalEAverageScore'       => $totalEAverageScore, // 全区英语平均分
            'totalSchoolEAverageScore' => $totalSchoolEAverageScore, // 平均分对比数据
        );

        return $comprehensiveIndicatorsData;
    }

    /**
     * 获取其他数据
     */
    private function getZValueDefaultData()
    {
        $filePath = self::$basePath.self::$examInfoData['mainDir'].'/';

        $filename = self::$averageContrast;

        $excelData = self::$excelFile->openExcel($filePath, $filename);

        $CValueNumerator = array(); // 标准差C值分子

        $CValue = array(); // 标准差C值

        $schoolZValue = array(); // 增值性评价Z值

        $ZValueData = array(); // Z值数据

        foreach($excelData->getRowIterator() as $kr => $row){
            $cellIterator = $row->getCellIterator();

            $num = 1;

            if($kr == 1) {
                foreach($cellIterator as $kc => $cell){
                    if ($kc % 2 == 1) {
                        $item[] = $cell->getValue();
                    }
                }
            } elseif($kr > 1) {
                foreach($cellIterator as $kc => $cell){
                    if ($kc == 0) {
                        $schoolName = $cell->getValue();
                    } elseif($kc % 2 == 1) {
                        $data[$schoolName][$item[$kc-$num]] = number_format($cell->getValue(), 2, '.', '');
                        $num++;
                    }
                }
            }
        }

        foreach ($data as $schoolName => $item) {
            foreach ($item as $key => $value) {
                if($schoolName != '全体') {
                    $averageScoreStudentData[$schoolName][$key] = $value;
                } else {
                    $averageScoreTotalData[$key] = $value;
                }
            }
        }

        foreach ($averageScoreStudentData as $schoolName => $item) {
            foreach ($item as $key => $value) {
                $CValueNumerator[$key] = number_format($CValueNumerator[$key] + number_format(pow(number_format($value - $averageScoreTotalData[$key], 2, '.', '') , 2), 2, '.', ''), 2, '.', '');
                $totalScore[$key] = number_format($totalScore[$key] + $value, 2, '.', '');
            }
        }

        $totalStudentCount = count(self::$schoolInfoData['schoolList']);

        foreach ($totalScore as $key => $value) {
            $averageScoreSchoolData[$key] = number_format($value / $totalStudentCount, 2, '.', '');
        }

        foreach ($CValueNumerator as $key => $value) {
            $CValue[$key] = number_format(sqrt(number_format($value / $totalStudentCount, 2, '.', '')), 2, '.', '');
        }

        foreach ($averageScoreStudentData as $schoolName => $item) {
            foreach ($item as $key => $value) {
                $schoolZValue[$schoolName][$key] = number_format(number_format($value - $averageScoreSchoolData[$key], 2, '.', '') / $CValue[$key], 2, '.', '');
            }
        }

        foreach ($schoolZValue as $schoolName => $item) {
            foreach ($item as $key => $value) {
                foreach (self::$courseListData as $courseName) {
                    $name = $courseName.'均分';
                    $totalName = '总分均分'; // 此处还需要确认字段名称
                    if($key == $name) {
                        $schoolZValue[$schoolName][$courseName] = $value;
                        unset($schoolZValue[$schoolName][$key]);
                    } elseif($key == $totalName) {
                        $schoolZValue[$schoolName]['总体'] = $value;
                        unset($schoolZValue[$schoolName][$key]);
                    }
                }
            }
        }

        $ZValueData = array(
            'schoolZValue'            => $schoolZValue, // 学校Z值
            // 以下为查询各项数值
            // 'CValue'                  => $CValue, // 学校C值
            // 'CValueNumerator'         => $CValueNumerator, // 学校C值分子
            // 'totalSchoolStudentCount' => $totalSchoolStudentCount, // 全校参加考试人数
            // 'averageScoreTotalData'   => $averageScoreTotalData, // 全区平均分
            // 'averageScoreSchoolData'  => $averageScoreSchoolData, // 全校平均分
        );

        return $ZValueData;
    }

    /**
     * 获取高三、高二数据
     */
    private function getZValueHighData()
    {
        $filePath = self::$basePath.self::$examInfoData['mainDir'].'/';

        $filenameScience = self::$averageContrastScience;
        $filenameArts = self::$averageContrastArts;

        $excelScienceData = self::$excelFile->openExcel($filePath, $filenameScience);
        $excelArtsData = self::$excelFile->openExcel($filePath, $filenameArts);

        $CValueNumerator = array(); // 标准差C值分子

        $CValue = array(); // 标准差C值

        $schoolZValue = array(); // 增值性评价Z值

        $ZValueData = array(); // Z值数据

        foreach($excelScienceData->getRowIterator() as $kr => $row){
            $cellIterator = $row->getCellIterator();

            $num = 1;

            if($kr == 1) {
                foreach($cellIterator as $kc => $cell){
                    if ($kc % 2 == 1) {
                        $itemScience[] = $cell->getValue();
                    }
                }
            } elseif($kr > 1) {
                foreach($cellIterator as $kc => $cell){
                    if ($kc == 0) {
                        $schoolName = $cell->getValue();
                    } elseif($kc % 2 == 1) {
                        if($itemScience[$kc-$num] == '理科总分均分') {
                            $scienceExtraData[$schoolName][$itemScience[$kc-$num]] = number_format($cell->getValue(), 2, '.', '');
                        } else {
                            $scienceData[$schoolName][$itemScience[$kc-$num]] = number_format($cell->getValue(), 2, '.', '');
                        }
                        $num++;
                    }
                }
            }
        }

        foreach($excelArtsData->getRowIterator() as $kr => $row){
            $cellIterator = $row->getCellIterator();

            $num = 1;

            if($kr == 1) {
                foreach($cellIterator as $kc => $cell){
                    if ($kc % 2 == 1) {
                        $itemArts[] = $cell->getValue();
                    }
                }
            } elseif($kr > 1) {
                foreach($cellIterator as $kc => $cell){
                    if ($kc == 0) {
                        $schoolName = $cell->getValue();
                    } elseif($kc % 2 == 1) {
                        if($itemArts[$kc-$num] == '文科总分均分') {
                            $artsExtraData[$schoolName][$itemArts[$kc-$num]] = number_format($cell->getValue(), 2, '.', '');
                        } else {
                            $artsData[$schoolName][$itemArts[$kc-$num]] = number_format($cell->getValue(), 2, '.', '');
                        }
                        $num++;
                    }
                }
            }
        }

        $scoreDate = self::getComprehensiveIndicatorsData();

        foreach ($scienceData as $schoolName => $item) {
            foreach ($item as $key => $value) {
                if($schoolName != '全体') {
                    if($key == '语文均分') {
                        $averageScoreStudentData[$schoolName][$key] = $scoreDate['totalSchoolCAverageScore'][$schoolName];
                    } elseif($key == '英语均分') {
                        $averageScoreStudentData[$schoolName][$key] = $scoreDate['totalSchoolEAverageScore'][$schoolName];
                    } else {
                        $averageScoreStudentData[$schoolName][$key] = $value;
                    }
                } else {
                    if($key == '语文均分') {
                        $averageScoreTotalData[$key] = $scoreDate['totalCAverageScore'];
                    } elseif($key == '英语均分') {
                        $averageScoreTotalData[$key] = $scoreDate['totalEAverageScore'];
                    } else {
                        $averageScoreTotalData[$key] = $value;
                    }
                }
            }
        }

        foreach ($artsData as $schoolName => $item) {
            foreach ($item as $key => $value) {
                if($schoolName != '全体') {
                    if($key == '语文均分') {
                        $averageScoreStudentData[$schoolName][$key] = $scoreDate['totalSchoolCAverageScore'][$schoolName];
                    } elseif($key == '英语均分') {
                        $averageScoreStudentData[$schoolName][$key] = $scoreDate['totalSchoolEAverageScore'][$schoolName];
                    } else {
                        $averageScoreStudentData[$schoolName][$key] = $value;
                    }
                } else {
                    if($key == '语文均分') {
                        $averageScoreTotalData[$key] = $scoreDate['totalCAverageScore'];
                    } elseif($key == '英语均分') {
                        $averageScoreTotalData[$key] = $scoreDate['totalEAverageScore'];
                    } else {
                        $averageScoreTotalData[$key] = $value;
                    }
                }
            }
        }
        
        foreach ($scienceExtraData as $schoolName => $item) {
            foreach ($item as $key => $value) {
                if($schoolName != '全体') {
                    $averageScoreStudentData[$schoolName][$key] = $scienceExtraData[$schoolName][$key];
                } else {
                    $averageScoreTotalData[$schoolName][$key] = $scienceExtraData[$schoolName][$key];
                }
            }
        }
        
        foreach ($artsExtraData as $schoolName => $item) {
            foreach ($item as $key => $value) {
                if($schoolName != '全体') {
                    $averageScoreStudentData[$schoolName][$key] = $artsExtraData[$schoolName][$key];
                } else {
                    $averageScoreTotalData[$schoolName][$key] = $artsExtraData[$schoolName][$key];
                }
            }
        }

        foreach ($averageScoreStudentData as $schoolName => $item) {
            foreach ($item as $key => $value) {
                $CValueNumerator[$key] = number_format($CValueNumerator[$key] + number_format(pow(number_format($value - $averageScoreTotalData[$key], 2, '.', '') , 2), 2, '.', ''), 2, '.', '');
                $totalScore[$key] = number_format($totalScore[$key] + $value, 2, '.', '');
            }
        }

        $totalStudentCount = count(self::$schoolInfoData['schoolList']);

        foreach ($totalScore as $key => $value) {
            $averageScoreSchoolData[$key] = number_format($value / $totalStudentCount, 2, '.', '');
        }

        foreach ($CValueNumerator as $key => $value) {
            $CValue[$key] = number_format(sqrt(number_format($value / $totalStudentCount, 2, '.', '')), 2, '.', '');
        }

        foreach ($averageScoreStudentData as $schoolName => $item) {
            foreach ($item as $key => $value) {
                $schoolZValue[$schoolName][$key] = number_format(number_format($value - $averageScoreSchoolData[$key], 2, '.', '') / $CValue[$key], 2, '.', '');
            }
        }

        foreach ($schoolZValue as $schoolName => $item) {
            foreach ($item as $key => $value) {
                foreach (self::$courseListData as $courseName) {
                    $name = $courseName.'均分';
                    $scienceName = '理科总分均分';
                    $artsName = '文科总分均分';
                    if($key == $name) {
                        $schoolZValue[$schoolName][$courseName] = $value;
                        unset($schoolZValue[$schoolName][$key]);
                    } elseif($key == $scienceName) {
                        $schoolZValue[$schoolName]['理科'] = $value;
                        unset($schoolZValue[$schoolName][$key]);
                    } elseif($key == $artsName) {
                        $schoolZValue[$schoolName]['文科'] = $value;
                        unset($schoolZValue[$schoolName][$key]);
                    }
                }
            }
        }

        $ZValueData = array(
            'schoolZValue'            => $schoolZValue, // 学校Z值
            // 以下为查询各项数值
            // 'CValue'                  => $CValue, // 学校C值
            // 'CValueNumerator'         => $CValueNumerator, // 学校C值分子
            // 'totalSchoolStudentCount' => $totalSchoolStudentCount, // 全校参加考试人数
            // 'averageScoreTotalData'   => $averageScoreTotalData, // 全区平均分
            // 'averageScoreSchoolData'  => $averageScoreSchoolData, // 全校平均分
        );

        return $ZValueData;
    }


    /**
     * 获取双向明细表数据
     */
    public function getZValueData()
    {
        if(self::$examInfoData['grade'] =='高二年级' || self::$examInfoData['grade'] == '高三年级') {
            $ZValueData = self::getZValueHighData();
        } else {
            $ZValueData = self::getZValueDefaultData();
        }

        return $ZValueData['schoolZValue'];
    }
}

?>