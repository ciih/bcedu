<?php

/**
 * 获取学校列表
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class SchoolListData {

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

        $schoolArea = array(); // 学校区域
        $schoolList = array(); // 学校列表

        $schoolData = array(); // 学校信息

        foreach($excelData->getRowIterator() as $kr => $row){

            $cellIterator = $row->getCellIterator();
            if ($kr == 1){
                foreach($cellIterator as $kc => $cell){
                    if(!empty($cell->getValue())) {
                        $schoolArea[] = $cell->getValue();
                    }
                }
            }
            else {
                foreach($cellIterator as $kc => $cell){
                    $rets[$kc][] = $cell->getValue();
                }       
            }
        }

        switch ($schoolType)
        {
            case 'junior':
                for ($x = 0; $x < count($rets); $x++) {
                  foreach($rets[$x] as $schoolName){
                    $schoolList[$schoolName] = $schoolArea[$x];
                  }
                }
                break;
            case 'middle':
                for ($x = 0; $x < count($rets); $x++) {
                  foreach($rets[$x] as $schoolName){
                    $schoolList[$schoolName] = $schoolArea[$x];
                  }
                }
                break;
            case 'high':
                for ($x = 0; $x < count($rets); $x++) {
                  foreach($rets[$x] as $schoolName){
                    $schoolList[$schoolName] = $schoolArea[$x];
                  }
                }
                break;
            /*case 'high':
                foreach($rets as $schoolName){
                    $schoolList[$schoolName] = $schoolArea[0];
                }
                break;*/
        }

        $schoolData = array(
            'schoolArea'       => $schoolArea, // 学校区域
            'schoolList'       => $schoolList, // 学校列表
        );

        return $schoolData;
    }
}

?>