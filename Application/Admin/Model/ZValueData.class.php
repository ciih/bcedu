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
     * 科目
     * @var string
     */
    protected static $course;

    /**
     * 综合指标表名
     * @var string
     */
    protected static $comprehensiveIndicators = '综合指标';

    /**
     * 构造
     * @param $examInfoData 文件夹名称（包含信息：学年、学期、年级、考试名称）
     */
    function __construct($date, $foldername, $course)
    {
        // 获取考试数据目录
        $examInfoObj = new \Admin\Model\ExamInfoData($date, $foldername);
        self::$examInfoData = $examInfoObj->getExamInfoData();

        self::$basePath = self::$examInfoData['rootDir'].self::$examInfoData['uploadDate'].'/'.self::$examInfoData['fullname'].'/';

        // 获取当前查询科目
        self::$course = $course;

        // 获取学校列表
        $schoolInfoObj = new \Admin\Model\SchoolInfoData();
        self::$schoolInfoData = $schoolInfoObj->getSchoolData(self::$examInfoData['schoolType']);

        self::$excelFile = new \Admin\Model\ExeclFile();
    }

    /**
     * 获取双向明细表数据
     */
    public function getZValueData()
    {
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