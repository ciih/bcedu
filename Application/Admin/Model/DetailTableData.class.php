<?php

/**
 * 获取学科双向明细表
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class DetailTableData {

    /**
     * 考试信息
     * @var array
     */
    protected static $examInfo;

    /**
     * 科目
     * @var string
     */
    protected static $course;

    /**
     * 构造
     * @param $examInfoData 文件夹名称（包含信息：学年、学期、年级、考试名称）
     */
    function __construct($examInfoData, $course)
    {
        self::$examInfo = $examInfoData;
        self::$course = $course;
    }

    /**
     * 获取双向明细表数据
     */
    public function getDetailTableData()
    {
        $filePath = self::$examInfo['rootDir'].self::$examInfo['uploadDate'].'/'.self::$examInfo['fullname'].'/';
        $filename = self::$examInfo['fullname'].'.'.self::$course;
        
        $excelFile = new \Admin\Model\ExeclFile();
        $excelData = $excelFile->openExcel($filePath, $filename);

        $examScope = array(); // 考核范畴
        $examScopeNumber = array(); // 考核范畴题号
        $examScopeTotalScore = array(); // 考核范畴题分
        $examCount = array(); // 考核范畴试题的数量
        $examNumber = array(); //考核范畴题号

        $examMold = array(); // 考核层级类型名称
        $examMoldStartPos = 0; // 考核层级在文件的起始位置
        $examMoldNumber = array(); // 考核层级类型所包含的题号
        $examMoldTotalScore = array(); // 考核层级各类型分数
        $examMoldScorePos = 0; // 考核层级各类型分数起始位置
        $examMoldItemName = array('考核层级要求', '预估难度'); // 考核层级名称

        $score = array(); // 分数列表
        $totalScore = 0; // 总分

        $data = array();  // 获取数据后，解析时暂存数据
        $detailTableData = array(); // 明细表数据

        foreach($excelData->getRowIterator() as $kr => $row){

            $cellIterator = $row->getCellIterator();

            if($kr == 3) {
                foreach($cellIterator as $kc => $cell){
                    if($cell->getValue() == $examMoldItemName[0]) {
                        $examMoldStartPos = $kc;
                    }
                    if($cell->getValue() == $examMoldItemName[1]) {
                        $examMoldScorePos = $kc + 1;
                    }
                }
            }

            if($kr == 5) {
                foreach($cellIterator as $kc => $cell){
                    if($kc >= $examMoldStartPos && !empty($cell->getValue())) {
                        $examMold[] = $cell->getValue();
                    }
                }
            }

            if($kr > 6) {
                foreach($cellIterator as $kc => $cell){
                    $data['item'][$kc-1][] = $cell->getValue();
                    if ($examMoldScorePos == $kc) {
                        $score[] = $cell->getValue();
                    }
                }
            }

        }

        $score = array_slice($score, 0, -3);

        foreach ($score as $kg => $value) {
            $totalScore = $totalScore + $value;
        }

        $examScopeData = array_unique(array_slice($data['item'][0],0,-3));
        foreach ($examScopeData as $kf => $val) {
            $examScope[] = $val;
        }

        $examCount = count(array_slice($data['item'][5],0,-3));

        for ($j = 0; $j < $examCount; $j++) {
            if(empty($data['item'][5][$j])) {
                $examNumber[] = $data['item'][4][$j];
            } else {
                $examNumber[] = $data['item'][4][$j].'_'.$data['item'][5][$j];
            }
        }

        // 考核范畴各类型分数
        for ($l = 0; $l < count($examScope); $l++) {
            $arr = array_slice($data['item'][0],0,-3); 
            foreach($arr as $ke => $val){
                if($val == $examScope[$l]) {
                    $examScopeNumber[$examScope[$l]][] = $examNumber[$ke];
                    $examScopeTotalScore[$examScope[$l]] = $examScopeTotalScore[$examScope[$l]] + $score[$ke];
                }
            }
        }

        // 考核层级各类型分数
        for ($k = 0; $k < count($examMold); $k++) {
            $arrexamMold = array_slice($data['item'][$examMoldStartPos - 1 + $k],0,-3); 
            foreach($arrexamMold as $kd => $val){
                if(!empty($val)) {
                    $examMoldNumber[$examMold[$k]][] = $examNumber[$kd];
                    $examMoldTotalScore[$examMold[$k]] = $examMoldTotalScore[$examMold[$k]] + $score[$kd];
                }
                else {
                    $examMoldTotalScore[$examMold[$k]] = $examMoldTotalScore[$examMold[$k]] + 0;
                }
            } 
        }

        $detailTableData = array(
            'course'              => self::$course, // 当前科目
            'examScopeName'       => $examScope, // 考试范畴
            'examScopeNumber'     => $examScopeNumber, // 考试范畴题号
            'examScopeTotalScore' => $examScopeTotalScore, // 考试范畴总分
            'examMoldName'        => $examMold, // 考试层级名称
            'examMoldNumber'      => $examMoldNumber, // 考试层级题号
            'examMoldTotalScore'  => $examMoldTotalScore, // 考试层级总分
            'totalScore'          => $totalScore // 本科考试总分
        );

        return $detailTableData;
    }

}

?>