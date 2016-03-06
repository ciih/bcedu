<?php

/**
 * 获取Excel数据
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class ExeclFile {

    /**
     * 打开excel表
     * @return string $objWorksheet 返回相应excel文件的工作薄
     */
    public function openExcel($filePath, $filename)
    {
        vendor("PHPExcel.PHPExcel.IOFactory");

        $excelRoot = dirname(dirname(dirname(dirname(__FILE__))));

        $filePath = iconv("utf-8", "gb2312", $filePath);
        $filename = iconv("utf-8", "gb2312", $filename);

        $excelPath = $excelRoot . $filePath . $filename . '.xls';

        $objPHPExcel = \PHPExcel_IOFactory::load($excelPath);
        $objWorksheet = $objPHPExcel->getSheet(0);

        return $objWorksheet;
    }
}

?>