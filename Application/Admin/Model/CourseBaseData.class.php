<?php

/**
 * 获取学科双向明细表
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class CourseBaseData {
   
    /**
     * Excel表目录
     * @var string
     */
    const EXCEL_DIR = '/Data';

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
     * 打开excel表
     * @return string $objWorksheet 返回相应excel文件的工作薄
     */
    private function openExcel($filename)
    {
        vendor("PHPExcel.PHPExcel.IOFactory");

        $excelRoot = dirname(dirname(dirname(dirname(__FILE__))));

        $dateDir = self::$dateDir;
        $mainDir = iconv("utf-8", "gb2312", self::$mainDir);
        $filename = iconv("utf-8", "gb2312", $filename);

        $filePath = $excelRoot.self::EXCEL_DIR.'/'.$dateDir.'/'.$mainDir.'/'.$filename.'.xls';

        $objPHPExcel = \PHPExcel_IOFactory::load($filePath);
        $objWorksheet = $objPHPExcel->getSheet(0);

        return $objWorksheet;
    }

    /**
     * 获取小学列表
     */
    private function getData($course)
    {

        $courseData = array(); // 明细表数据

        $data = array();  // 获取后的数据

        $type = array(); // 考核层级要求项名称
        $typeStartPos = 0; // 考核层级要求的起始位置
        $typeNumber = array(); // 考核层级类型包含的题号
        $typeScore = array(); // 考核层级类型分数
        $typeScorePos = 0; // 考核层级类型分数位置

        $examType = array(); // 考试类型
        $examTypeNumber = array(); // 考试类型题号
        $examTypeScore = array(); // 考试类型题分
        $examCount = array(); // 试题的数量
        $examNumber = array(); //考试题号

        $score = array(); // 分数列表
        $totalScore = array(); // 总分

        for ($i = 0; $i < count($course); $i++) {

            $filename = self::$mainDir.'.'.$course[$i];

            $courseData[] = self::openExcel($filename);

            foreach($courseData[$i]->getRowIterator() as $kr => $row){

                $cellIterator = $row->getCellIterator();

                if($kr == 3) {
                    foreach($cellIterator as $kc => $cell){
                        if($cell->getValue() == '考核层级要求') {
                            $typeStartPos = $kc;
                        }
                        if($cell->getValue() == '预估难度') {
                            $typeScorePos = $kc + 1;
                        }
                    }
                }

                if($kr == 5) {
                    foreach($cellIterator as $kc => $cell){
                        if($kc >= $typeStartPos && !empty($cell->getValue())) {
                            $type[$course[$i]][] = $cell->getValue();
                        }
                    }
                }

                if($kr > 6) {
                    foreach($cellIterator as $kc => $cell){
                        $data[$course[$i]]['item'][$kc-1][] = $cell->getValue();
                        if ($typeScorePos == $kc) {
                            $score[$course[$i]][] = $cell->getValue();
                        }
                    }
                }

            }
            
            $score[$course[$i]] = array_slice($score[$course[$i]],0,-3);
            foreach ($score[$course[$i]] as $kg => $value) {
                $totalScore[$course[$i]] = $totalScore[$course[$i]] + $value;
            }

            $examTypeData = array_unique(array_slice($data[$course[$i]]['item'][0],0,-3));
            foreach ($examTypeData as $kf => $val) {
                $examType[$course[$i]][] = $val;
            }

            $examCount[$course[$i]] = count(array_slice($data[$course[$i]]['item'][5],0,-3));

            for ($j = 0; $j < $examCount[$course[$i]]; $j++) {
                if(empty($data[$course[$i]]['item'][5][$j])) {
                    $examNumber[$course[$i]][] = $data[$course[$i]]['item'][4][$j];
                } else {
                    $examNumber[$course[$i]][] = $data[$course[$i]]['item'][4][$j].'_'.$data[$course[$i]]['item'][5][$j];
                }
            }

            for ($k = 0; $k < count($type[$course[$i]]); $k++) {
                $arrType = array_slice($data[$course[$i]]['item'][$typeStartPos - 1 + $k],0,-3); 
                foreach($arrType as $kd => $val){
                    if(!empty($val)) {
                        $typeNumber[$course[$i]][$type[$course[$i]][$k]][] = $examNumber[$course[$i]][$kd];
                        $typeScore[$course[$i]][$type[$course[$i]][$k]] = $typeScore[$course[$i]][$type[$course[$i]][$k]] + $score[$course[$i]][$kd];
                    }
                } 
            }

            for ($l = 0; $l < count($examType[$course[$i]]); $l++) {
                $arr = array_slice($data[$course[$i]]['item'][0],0,-3); 
                foreach($arr as $ke => $val){
                    if($val == $examType[$course[$i]][$l]) {
                        $examTypeNumber[$course[$i]][$examType[$course[$i]][$l]][] = $examNumber[$course[$i]][$ke];
                        $examTypeScore[$course[$i]][$examType[$course[$i]][$l]] = $examTypeScore[$course[$i]][$examType[$course[$i]][$l]] + $score[$course[$i]][$ke];
                    }
                }
            }
        }

        $courseBaseData = array(
            'exam'       => $examType, 
            'examNumber' => $examTypeNumber, 
            'examScore'  => $examTypeScore,
            'type'       => $type,
            'typeNumber' => $typeNumber,
            'typeScore'  => $examTypeScore,
            'totalScore' => $totalScore
        );

        return $courseBaseData;

    }

    /**
     * 获取学校列表
     * @param $data 分数
     */
    public function getCourseBaseData($date, $foldername)
    {

        self::$dateDir = $date;
        self::$mainDir = $foldername;

        $courseData = new \Admin\Model\CourseData();
        $course = $courseData->getCourseData($date, $foldername);

        $data = self::getData($course);

        return $data;

    }

}

?>