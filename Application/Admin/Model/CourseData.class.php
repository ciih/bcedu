<?php

/**
 * 获取学科列表
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class CourseData {

    /**
     * 考试信息
     * @var array
     */
    protected static $examInfo;

    /**
     * 学科表名
     * @var string
     */
    protected static $courseAnalysisName = '学科分析';

    /**
     * 构造
     * @param $examInfoData 文件夹名称（包含信息：学年、学期、年级、考试名称）
     */
    function __construct($examInfoData)
    {
        self::$examInfo = $examInfoData;
    }

    /**
     * 获取考试科目数据
     */
    public function getCourseData()
    {
        $filePath = self::$examInfo['rootDir'].self::$examInfo['uploadDate'].'/'.self::$examInfo['fullname'].'/'.self::$examInfo['mainDir'].'/';
        $filename = self::$courseAnalysisName;

        $excelFile = new \Admin\Model\ExeclFile();
        $excelData = $excelFile->openExcel($filePath, $filename);

        $keys = array(); // 基本项标题
        $rets = array(); // 基本项内容

        $courseData = array(); // 学科

        foreach($excelData->getRowIterator() as $kr => $row){

            $cellIterator = $row->getCellIterator();
            if($kr > 1) {
                foreach($cellIterator as $kc => $cell){
                    if($kc == 0) {
                        $courseData[] = $cell->getValue();
                    }
                }
            }
        }

        return $courseData;
    }
}

?>