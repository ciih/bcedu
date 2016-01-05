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
     * 考试基本信息表名
     * @var string
     */
    const INFO_NAME = '基本信息';

    /**
     * 各科综合表名
     * @var string
     */
    const COURSE_COMPREHENSIVE_NAME = '各科综合';

    /**
     * 平均分表名
     * @var string
     */
    const AVERAGE_SCORE_NAME = '平均分对比';

    /**
     * 学科分析表名
     * @var string
     */
    const SUBJECT_ANALYSIS_NAME = '学科分析';

    /**
     * 学生成绩表名
     * @var string
     */
    const STUDENT_SCORE_NAME = '学生成绩';

    /**
     * 总分_基本指标表名
     * @var string
     */
    const SCORE_BASE_NAME = '总分_基本指标';

    /**
     * 总分_综合指标表名
     * @var string
     */
    const SCORE_COMPREHENSIVE_NAME = '总分_综合指标';


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
     * 班级目录
     * @var string
     */
    protected static $classDir = '班级报表';

    /**
     * 全区目录
     * @var string
     */
    protected static $totalDir = '全区报表';

    /**
     * 学校目录
     * @var string
     */
    protected static $schoolDir = '学校报表';

    /**
     * 打开excel表
     * @param string $filepath excel文件路径
     * @param string $filename excel文件名
     * @return string $objWorksheet 返回相应excel文件的工作薄
     */
    private function openExcel($filename)
    {
        vendor("PHPExcel.PHPExcel.IOFactory");

        $excelRoot = dirname(dirname(dirname(dirname(__FILE__))));

        $filename = iconv("utf-8", "gb2312", $filename);

        $filePath = $excelRoot.self::EXCEL_DIR.'/'.self::$dateDir.'/'.self::$mainDir.'/'.$filename.'.xls';

        $objPHPExcel = \PHPExcel_IOFactory::load($filePath);
        $objWorksheet = $objPHPExcel->getSheet(0);

        return $objWorksheet;
    }


    /**
     * 获取基本信息数据
     * @param $date 子文件夹
     * @param $mainDir 文件名
     */
    private function getCommonData()
    {

    	$excelData = self::openExcel(self::INFO_NAME);

        $keys = array(); // 基本项标题
        $rets = array(); // 基本项内容

        $type = ''; // 学校类型
        $title = ''; // 考试名称

        $school = array(); // 学校名称
        $course = array(); // 学科名称
        $score = array(); // 学科分数
    	$area = array(); // 学校分区


        foreach($excelData->getRowIterator() as $kr => $row){

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
		$title = $rets[1][0];

        switch ($type)
		{
			case '小学':
			    for ($x = 0; $x < 3; $x++) {
			      $area[] = $keys[$x+2];
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
			      $area[] = $keys[$x+2];
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

		$commonData = array(
        	'keys' => $keys,
            'type' => $type, 
            'title' => $title,
            'school' => $school,
            'course' => $course,
            'score' => $score
        );

        return $commonData;
    }


    /**
     * 获取各科数据
     */
    private function getCourseData()
    {

    	var_dump(self::$mainDir);
    	// $excelData = self::openExcel($filename);
    }

    /**
     * 获取各科数据
     */
    private function courseData()
    {


    	// $excelData = self::openExcel($date, $mainDir, $filename);
    }


    /**
     * 获取双向明细表数据
     */
    private function getScheduleData()
    {

    	var_dump(self::$mainDir);
    	// $excelData = self::openExcel($filename);
    }

    private function initData($dateDir, $mainDir)
    {
    	self::$dateDir = $dateDir;
    	self::$mainDir = iconv("utf-8", "gb2312", $mainDir);
    }

    /**
     * 获取学校列表
     * @param $date 子文件夹
     * @param $mainDir 文件名
     */
    public function getData($dateDir, $mainDir)
    {

    	self::initData($dateDir, $mainDir);

    	$commonData  = self::getCommonData();
    	// $summaryData = self::getCourseData();
    	
        $excelData = array(
        	'common' => $commonData
        );

        return $excelData;
    }


    /**
     * 获取基本信息
     * @param $date 子文件夹
     * @param $mainDir 文件名
     */
    public function getData($dateDir, $mainDir)
    {

    	self::initData($dateDir, $mainDir);

    	$commonData  = self::getCommonData();
    	// $summaryData = self::getCourseData();
    	
        $excelData = array(
        	'common' => $commonData
        );

        return $excelData;
    }

}

?>