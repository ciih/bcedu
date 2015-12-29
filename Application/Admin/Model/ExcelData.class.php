<?php

/**
 * 获取Excel数据源
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class ExcelData {
   
    /**
     * Excel表目录
     * @var string
     */
    const EXCEL_DIR = '/Data';

    /**
     * 打开excel表
     * @param string $filepath excel文件路径
     * @param string $filename excel文件名
     * @return string $objWorksheet 返回相应excel文件的工作薄
     */
    private function openExcel($subpath, $filename)
    {
        vendor("PHPExcel.PHPExcel.IOFactory");

        $excelRoot = dirname(dirname(dirname(dirname(__FILE__))));

        $filename = iconv("utf-8", "gb2312", $filename);

        $filePath = $excelRoot.self::EXCEL_DIR.'/'.$subpath.'/'.$filename.'.xls';

        $objPHPExcel = \PHPExcel_IOFactory::load($filePath);
        $objWorksheet = $objPHPExcel->getSheet(0);

        return $objWorksheet;
    }



    /**
     * 获取基本信息
     * @param $date 子文件夹
     * @param $filename 文件名
     */
    public function getBaseInfo($date, $filename)
    {

    	$excelBaseInfo = self::openExcel($date, $filename);

        $keys = array(); // 基本项标题
        $rets = array(); // 基本项内容

        $type = ''; // 学校类型
        $name = ''; // 考试名称

        $school = array(); // 学校名称
        $course = array(); // 学科名称
        $score = array(); // 学科分数
    	$area = array(); // 学校分区


        foreach($excelBaseInfo->getRowIterator() as $kr => $row){

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
		
		$type = $rets[0][0];
		$name = $rets[1][0];

        switch ($type)
		{
			case '小学':
			    for ($x = 0; $x < 3; $x++) {
			      $area[] = $keys[$x];
				  foreach($rets[$x+2] as $val){
				  	$school[$area[$x]][] = $val;
				  }
				}
				foreach($keys as $i => $val){
					if($i > 4) {
						$course[] = $val;
					}
				}
				foreach($rets as $i => $val){
					if($i > 4) {
						$score[] = $val[0];
					}
				}
			  	break;
			case '初中':
			    for ($x = 0; $x < 3; $x++) {
			      $area[] = $keys[$x];
				  foreach($rets[$x+2] as $val){
				  	$school[$area[$x]][] = $val;
				  }
				}
				foreach($keys as $i => $val){
					if($i > 4) {
						$course[] = $val;
					}
				}
				foreach($rets as $i => $val){
					if($i > 4) {
						$score[] = $val[0];
					}
				}
			  	break;
			case '高中':
			    $area[] = $keys[2];
				foreach($rets[2] as $val){
					$school[$area[0]][] = $val;
				}
				foreach($keys as $i => $val){
					if($i > 2) {
						$course[] = $val;
					}
				}
				foreach($rets as $i => $val){
					if($i > 2) {
						$score[] = $val[0];
					}
				}
			  	break;
		}
        
        $baseData = array(
        	'data_field' => $keys,
            'type' => $type, 
            'exam_name' => $name,
            'school' => $school,
            'course' => $course,
            'score' => $score
        );

        return $baseData;
    }

}

?>