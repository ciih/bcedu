<?php

/**
 * 设置、获得分数率
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class ScoreRateData {
   
    /**
     * Excel表目录
     * @var string
     */
    const EXCEL_DIR = '/Excel/Template';

    /**
     * 分数线表名
     * @var string
     */
    const SCORE_NAME = '分数线';

    /**
     * 打开excel表
     * @param string $filename 文件名
     * @return string $objWorksheet 返回相应excel文件的工作薄
     */
    private function openExcel($filename)
    {
        vendor("PHPExcel.PHPExcel.IOFactory");

        $excelRoot = dirname(dirname(dirname(dirname(__FILE__))));

        $filename = iconv("utf-8", "gb2312", $filename);

        $filePath = $excelRoot.self::EXCEL_DIR.'/'.$filename.'.xls';

        $objPHPExcel = \PHPExcel_IOFactory::load($filePath);
        $objWorksheet = $objPHPExcel->getSheet(0);

        return $objWorksheet;
    }

    /**
     * 获取优秀、及格、不及格得分率
     */
    public function getScoreRateData()
    {
        $data = self::openExcel(self::SCORE_NAME);

        $keys = array(); // 基本项标题
        $rets = array(); // 基本项内容

        foreach($data->getRowIterator() as $kr => $row){

            $cellIterator = $row->getCellIterator();
            if ($kr == 1){
                foreach($cellIterator as $kc => $cell){
                    $keys[] = $cell->getValue();
                }
            }
            else {
                foreach($cellIterator as $kc => $cell){
                    $rets[$keys[$kc]][] = $cell->getValue() / 100;
                }       
            }
        }

        return $rets;

    }

    /**
     * 设置优秀、及格、不及格得分率
     * @param $data 分数
     */
    public function setScoreRateData($date, $foldername, $data)
    {

        $excelRoot = dirname(dirname(dirname(dirname(__FILE__))));

        $filename = iconv("utf-8", "gb2312", self::SCORE_NAME);

        $letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

        vendor("PHPExcel.PHPExcel");

        vendor("PHPExcel.PHPExcel.IOFactory");

        $objPHPExcel = new \PHPExcel;

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");

        $objSheet = $objPHPExcel->getActiveSheet();

        $objCourse = new \Admin\Model\CourseData();

        $course = $objCourse->getCourseData($date, $foldername);

        for ($x = 0; $x < count($course); $x++) {
            $objSheet->getCell($letter[$x].'1')->setValue($course[$x]);
            $objSheet->getCell($letter[$x].'2')->setValue($data[$x][0]);
            $objSheet->getCell($letter[$x].'3')->setValue($data[$x][1]);
        }

        $objWriter->save($excelRoot.self::EXCEL_DIR.'/'.$filename.'.xls');

    }

}

?>