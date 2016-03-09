<?php

/**
 * 获取学校列表
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class SchoolInfoData {

    /**
     * 其它信息主路径(包括：片区、分数线等)
     * @var array
     */
    protected static $excelTemplatePath = '/Excel/Template/';

    /**
     * 获取参考学校列表数据
     */
    public function getSchoolData($schoolType)
    {
        $filePath = self::$excelTemplatePath;
        if($schoolType == 'junior') {
            $filename = '小学列表';
        } elseif($schoolType == 'middle') {
            $filename = '初中列表';
        } elseif($schoolType == 'high') {
            $filename = '高中列表';
        }

        $excelFile = new \Admin\Model\ExeclFile();
        $excelData = $excelFile->openExcel($filePath, $filename);

        $rets = array(); // 基本项内容

        $areaName = array(); // 区域名称
        $schoolArea = array(); // 学校区域
        $schoolList = array(); // 学校列表

        $schoolData = array(); // 学校信息

        foreach($excelData->getRowIterator() as $kr => $row){

            $cellIterator = $row->getCellIterator();
            if ($kr == 1){
                foreach($cellIterator as $kc => $cell){
                    if(!empty($cell->getValue())) {
                        $areaName[] = $cell->getValue();
                    }
                }
            }
            else {
                foreach($cellIterator as $kc => $cell){
                    $rets[$kc][] = $cell->getValue();
                }       
            }
        }

        for ($x = 0; $x < count($rets); $x++) {
          foreach($rets[$x] as $schoolName){
            $schoolList[] = $schoolName;
            $schoolArea[$schoolName] = $areaName[$x];
          }
        }

        $schoolData = array(
            'areaName'   => $areaName, // 区域名称
            'schoolArea' => $schoolArea, // 学校区域
            'schoolList' => $schoolList, // 学校列表
        );

        return $schoolData;
    }
}

?>