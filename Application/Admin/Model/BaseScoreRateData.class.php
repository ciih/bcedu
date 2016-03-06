<?php

/**
 * 设置、获得分数率
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class BaseScoreRateData {

    /**
     * 分数线.xls(获取考试基础分数率)
     * @var string
     */
    const SCORE_LINE_NAME = '分数线';

    /**
     * 其它信息主路径(包括：片区、分数线等)
     * @var array
     */
    protected static $excelTemplatePath = '/Excel/Template/';

    /**
     * 获取优秀、及格、不及格得分率
     * @param $course 考试科目
     */
    public function getBaseScoreRateData($course)
    {

        $filePath = self::$excelTemplatePath;
        $filename = self::SCORE_LINE_NAME;

        $excelFile = new \Admin\Model\ExeclFile();
        $excelData = $excelFile->openExcel($filePath, $filename);

        $baseScoreRateData = array(); // 基本项内容

        $num = 0;

        foreach($excelData->getRowIterator() as $kr => $row){

            $cellIterator = $row->getCellIterator();
            if ($kr == 1){
                foreach($cellIterator as $kc => $cell){
                    if($course == $cell->getValue()) {
                        $num = $kc;
                    }
                }
            }
            else {
                foreach($cellIterator as $kc => $cell){
                    if($kc == $num) {
                        $baseScoreRateData[] = $cell->getValue();
                    }
                }       
            }
        }

        return $baseScoreRateData;

    }

    /**
     * 设置优秀、及格、不及格得分率
     * @param $examInfoData 考试信息
     * @param $courseData 考试科目
     * @param $data 分数
     */
    public function setBaseScoreRateData($examInfoData, $courseData, $data)
    {

        $excelRoot = dirname(dirname(dirname(dirname(__FILE__))));

        $basePath = $excelRoot.self::$excelTemplatePath;

        $filename = iconv("utf-8", "gb2312", self::SCORE_LINE_NAME);

        $letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

        vendor("PHPExcel.PHPExcel");

        vendor("PHPExcel.PHPExcel.IOFactory");

        $objPHPExcel = new \PHPExcel;

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");

        $objSheet = $objPHPExcel->getActiveSheet();

        for ($x = 0; $x < count($courseData); $x++) {
            $objSheet->getCell($letter[$x].'1')->setValue($courseData[$x]);
            $objSheet->getCell($letter[$x].'2')->setValue($data[$x][0]);
            $objSheet->getCell($letter[$x].'3')->setValue($data[$x][1]);
        }

        $objWriter->save($basePath.$filename.'.xls');
    }

}

?>