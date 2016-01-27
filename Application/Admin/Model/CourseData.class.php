<?php

/**
 * 获取学科列表
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class CourseData {
   
    /**
     * Excel表目录
     * @var string
     */
    const EXCEL_DIR = '/Data';

    /**
     * 学科表名
     * @var string
     */
    const COURSE_NAME = '学科分析';

    /**
     * 日期目录
     * @var string
     */
    protected static $dateDir = '';

    /**
     * 主目录
     * @var string
     */
    protected static $mainDir = '';

    /**
     * 全区目录
     * @var string
     */
    protected static $totalDir = '全区报表';

    /**
     * 打开excel表
     * @return string $objWorksheet 返回相应excel文件的工作薄
     */
    private function openExcel()
    {
        vendor("PHPExcel.PHPExcel.IOFactory");

        $excelRoot = dirname(dirname(dirname(dirname(__FILE__))));

        $dateDir = self::$dateDir;
        $mainDir = iconv("utf-8", "gb2312", self::$mainDir);
        $totalDir = iconv("utf-8", "gb2312", self::$totalDir);
        $filename = iconv("utf-8", "gb2312", self::COURSE_NAME);


        $filePath = $excelRoot.self::EXCEL_DIR.'/'.$dateDir.'/'.$mainDir.'/'.$totalDir.'/'.$filename.'.xls';

        $objPHPExcel = \PHPExcel_IOFactory::load($filePath);
        $objWorksheet = $objPHPExcel->getSheet(0);

        return $objWorksheet;
    }

    /**
     * 获取小学列表
     */
    private function getData()
    {
        $data = self::openExcel();

        $keys = array(); // 基本项标题
        $rets = array(); // 基本项内容

        $courseData = array(); // 学科

        foreach($data->getRowIterator() as $kr => $row){

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

    /**
     * 获取学校列表
     * @param $data 分数
     */
    public function getCourseData($date, $foldername)
    {

        self::$dateDir = $date;
        self::$mainDir = $foldername;

        $data = self::getData();

        return $data;

    }

}

?>