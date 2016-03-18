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

        $ZValueData = array(
            'schoolZValue'            => $schoolZValue, // 学校Z值
            // 以下为查询各项数值
            // 'CValue'                  => $CValue, // 学校C值
            // 'totalStudentCount'       => $totalStudentCount, // 全区参加考试人数
            // 'totalSchoolStudentCount' => $totalSchoolStudentCount, // 全校参加考试人数
            // 'totalAverageScore'       => $totalAverageScore, // 全区平均分
            // 'totalSchoolAverageScore' => $totalSchoolAverageScore, // 全校平均分
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
                        $scienceData[$schoolName][$itemScience[$kc-$num]] = number_format($cell->getValue(), 2, '.', '');
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
                        $artsData[$schoolName][$itemArts[$kc-$num]] = number_format($cell->getValue(), 2, '.', '');
                        $num++;
                    }
                }
            }
        }

        $scoreDate = self::getComprehensiveIndicatorsData();

        foreach ($artsData as $key => $value) {
            
        }

        // 在这里要将把所有平均分都整合好

        var_export('====================');
        var_export($artsData);
        exit();




        $ZValueData = array(
            'schoolZValue'            => $schoolZValue, // 学校Z值
            // 以下为查询各项数值
            // 'CValue'                  => $CValue, // 学校C值
            // 'totalStudentCount'       => $totalStudentCount, // 全区参加考试人数
            // 'totalSchoolStudentCount' => $totalSchoolStudentCount, // 全校参加考试人数
            // 'totalAverageScore'       => $totalAverageScore, // 全区平均分
            // 'totalSchoolAverageScore' => $totalSchoolAverageScore, // 全校平均分
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

        return $ZValueData;

        $filePath = self::$basePath.self::$examInfoData['mainDir'].'/'.self::$course.'/';
        $filename = self::$comprehensiveIndicators;

        $excelData = self::$excelFile->openExcel($filePath, $filename);

        $keys = array(); // 平均分字段名

        $totalStudentCount = 0; // 全区参加考试人数
        $totalSchoolStudentCount = array(); // 全校参加考试人数
        $totalAverageScore = 0; // 全区平均分
        $totalSchoolAverageScore = array(); // 全校平均分

        $CValueNumerator = 0; // 标准差C值分子

        $CValue = 0; // 标准差C值

        $schoolZValue = array(); // 增值性评价Z值

        $ZValueData = array(); // Z值数据

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
                }
            }
        }

        array_pop($totalSchoolStudentCount);

        $totalStudentCount = count($totalSchoolStudentCount);

        $totalAverageScore = array_pop($totalSchoolAverageScore);

        foreach ($totalSchoolAverageScore as $value) {
            $CValueNumerator = number_format($CValueNumerator + number_format(pow(number_format($value - $totalAverageScore, 2, '.', '') , 2), 2, '.', ''), 2, '.', '');
        }

        $CValue = number_format(sqrt(number_format($CValueNumerator / $totalStudentCount, 2, '.', '')), 2, '.', '');

        foreach ($totalSchoolAverageScore as $key => $value) {
            $schoolZValue[$key] = number_format(number_format($value - $totalAverageScore, 2, '.', '') / $CValue, 2, '.', '');
        }

        $ZValueData = array(
            'schoolZValue'            => $schoolZValue, // 学校Z值
            // 以下为查询各项数值
            // 'CValue'                  => $CValue, // 学校C值
            // 'totalStudentCount'       => $totalStudentCount, // 全区参加考试人数
            // 'totalSchoolStudentCount' => $totalSchoolStudentCount, // 全校参加考试人数
            // 'totalAverageScore'       => $totalAverageScore, // 全区平均分
            // 'totalSchoolAverageScore' => $totalSchoolAverageScore, // 全校平均分
        );

        return $ZValueData;
    }

}

?>