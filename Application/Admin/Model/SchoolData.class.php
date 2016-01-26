<?php

/**
 * 获取学校列表
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class SchoolData {
   
    /**
     * Excel表目录
     * @var string
     */
    const EXCEL_DIR = '/Excel/Template';

    /**
     * 小学表名
     * @var string
     */
    const JUNIOR_NAME = '小学';

    /**
     * 各科综合表名
     * @var string
     */
    const MIDDLE_NAME = '初中';

    /**
     * 平均分表名
     * @var string
     */
    const HIGH_NAME = '高中';

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
     * 获取小学列表
     */
    private function getData($typeName)
    {
        $filename = $typeName . '列表';
        $data = self::openExcel($filename);

        $keys = array(); // 基本项标题
        $rets = array(); // 基本项内容

        $schoolData = array(); // 学校列表

        foreach($data->getRowIterator() as $kr => $row){

            $cellIterator = $row->getCellIterator();
            if ($kr == 1){
                foreach($cellIterator as $kc => $cell){
                    $keys[] = $cell->getValue();
                }
            }
            else {
                foreach($cellIterator as $kc => $cell){
                    $rets[$kc][] = $cell->getValue();
                }       
            }
        }

        switch ($typeName)
        {
            case '小学':
                for ($x = 0; $x < count($rets); $x++) {
                  foreach($rets[$x] as $val){
                    $schoolData[$keys[$x]][] = $val;
                  }
                }
                break;
            case '初中':
                for ($x = 0; $x < count($rets); $x++) {
                  foreach($rets[$x] as $val){
                    $schoolData[$keys[$x]][] = $val;
                  }
                }
                break;
            case '高中':
                foreach($rets as $val){
                    $schoolData[$keys[0]] = $val;
                }
                break;
        }

        return $schoolData;

    }

    /**
     * 获取学校列表
     * @param $type 学校列表
     */
    public function getSchoolData($type)
    {

        switch ($type) {
            case 'junior' :
                $data  = self::getData(self::JUNIOR_NAME); // 小学列表
                break;
            case 'middle' :
                $data  = self::getData(self::MIDDLE_NAME); // 中学列表
                break;
            case 'high' :
                $data  = self::getData(self::HIGH_NAME); // 高中列表
                break;
        }

        return $data;
    }

}

?>