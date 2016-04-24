<?php

/**
 * 获取增值性评价
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class ScatterValueData {

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
    protected static $course;

    /**
     * 小题分表名
     * @var string
     */
    protected static $itemScore = '小题分';


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

        // 获取学校列表
        $schoolInfoObj = new \Admin\Model\SchoolInfoData();
        self::$schoolInfoData = $schoolInfoObj->getSchoolData(self::$examInfoData['schoolType']);

        self::$course = $course;

        self::$excelFile = new \Admin\Model\ExeclFile();

    }

    /**
     * 获取散点图数据
     */
    public function getScatterValueData()
    {
        $filePath = self::$basePath.self::$examInfoData['mainDir'].'/'.self::$course.'/';

        $filename = self::$itemScore;

        $excelData = self::$excelFile->openExcel($filePath, $filename);

        $CValueNumerator = 0; // 标准差C值分子

        $CValue = array(); // 标准差C值

        $scatterValue = array(); // 散点图数值

        $scatterValueData = array(); // 散点图数据

        $studentSchoolList = array();
        $studentScoreList = array();

        $totalScore = 0;

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
                        $totalScore = $totalScore + $cell->getValue();
                    }
                }
            }
        }

        $studentCount = count($studentScoreList);

        $averageScore = number_format($totalScore / $studentCount, 2, '.', '');

        foreach ($studentScoreList as $studentScore) {
            $CValueNumerator = number_format($CValueNumerator + number_format(pow(number_format($studentScore - $averageScore, 2, '.', '') , 2), 2, '.', ''), 2, '.', '');
        }

        $CValue = number_format(sqrt($CValueNumerator / $studentCount), 2, '.', '');

        foreach ($studentScoreList as $num => $studentScore) {
            // $scatterValue[$num][] = $studentScoreList[$num];
            $scatterValue[$num][] = $num;
            $scatterValue[$num][] = floatval(number_format(number_format($studentScore - $averageScore, 2, '.', '') / $CValue, 2, '.', ''));
        }

        $scatterValueData = array(
            'scatterValue'            => $scatterValue, // 散点图数值
            // 以下为查询各项数值
            // 'CValue'                  => $CValue, // 学校C值
            // 'CValueNumerator'         => $CValueNumerator, // 学校C值分子
            // 'averageScore' => $averageScore, // 全校平均分
            // 'studentCount'   => $studentCount, // 全校总人数
        );

        return $scatterValueData;
    }
}

?>