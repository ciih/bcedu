<?php

/**
 * 获取学科双向明细表
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class DetailTableData {
   
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
     * 科目
     * @var string
     */
    protected static $queryCourse = '';

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
    private function getData()
    {

        $detailTableData = array(); // 明细表数据

        $data = array();  // 获取后的数据

        $examType = array(); // 考试类型
        $examTypeNumber = array(); // 考试类型题号
        $examTypeScore = array(); // 考试类型题分
        $examCount = array(); // 试题的数量
        $examNumber = array(); //考试题号

        $type = array(); // 考核层级要求项名称
        $typeStartPos = 0; // 考核层级要求的起始位置
        $typeNumber = array(); // 考核层级类型包含的题号
        $typeScore = array(); // 考核层级类型分数
        $typeScorePos = 0; // 考核层级类型分数位置

        $score = array(); // 分数列表
        $totalScore = 0; // 总分

        $filename = self::$mainDir.'.'.self::$queryCourse;

        $detailTableFile = self::openExcel($filename);

        foreach($detailTableFile->getRowIterator() as $kr => $row){

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
                        $type[] = $cell->getValue();
                    }
                }
            }

            if($kr > 6) {
                foreach($cellIterator as $kc => $cell){
                    $data['item'][$kc-1][] = $cell->getValue();
                    if ($typeScorePos == $kc) {
                        $score[] = $cell->getValue();
                    }
                }
            }

        }

        $score = array_slice($score, 0, -3);

        foreach ($score as $kg => $value) {
            $totalScore = $totalScore + $value;
        }

        $examTypeData = array_unique(array_slice($data['item'][0],0,-3));
        foreach ($examTypeData as $kf => $val) {
            $examType[] = $val;
        }

        $examCount = count(array_slice($data['item'][5],0,-3));

        for ($j = 0; $j < $examCount; $j++) {
            if(empty($data['item'][5][$j])) {
                $examNumber[] = $data['item'][4][$j];
            } else {
                $examNumber[] = $data['item'][4][$j].'_'.$data['item'][5][$j];
            }
        }

        for ($k = 0; $k < count($type); $k++) {
            $arrType = array_slice($data['item'][$typeStartPos - 1 + $k],0,-3); 
            foreach($arrType as $kd => $val){
                if(!empty($val)) {
                    $typeNumber[$type[$k]][] = $examNumber[$kd];
                    $typeScore[$type[$k]] = $typeScore[$type[$k]] + $score[$kd];
                }
                else {
                    $typeScore[$type[$k]] = $typeScore[$type[$k]] + 0;
                }
            } 
        }

        for ($l = 0; $l < count($examType); $l++) {
            $arr = array_slice($data['item'][0],0,-3); 
            foreach($arr as $ke => $val){
                if($val == $examType[$l]) {
                    $examTypeNumber[$examType[$l]][] = $examNumber[$ke];
                    $examTypeScore[$examType[$l]] = $examTypeScore[$examType[$l]] + $score[$ke];
                }
            }
        }

        $detailTableData = array(
            'course'     => self::$queryCourse,
            'examName'   => $examType, // 考试范畴
            'examNumber' => $examTypeNumber, // 考试范畴题号
            'examScore'  => $examTypeScore, // 考试范畴总分
            'typeName'   => $type, // 考试层级名称
            'typeNumber' => $typeNumber, // 考试层级题号
            'typeScore'  => $typeScore, // 考试层级总分
            'totalScore' => $totalScore // 本科考试总分
        );

        return $detailTableData;

    }

    /**
     * 获取学校列表
     * @param $data 分数
     */
    public function getDetailTableData($date, $foldername, $course)
    {

        self::$dateDir = $date;
        self::$mainDir = $foldername;
        self::$queryCourse = $course;

        $data = self::getData();

        return $data;

    }

}

?>