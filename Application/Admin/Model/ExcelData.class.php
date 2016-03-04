<?php

/**
 * 获取Excel数据
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class ExcelData {
   
    /**
     * Excel表目录
     * @var string
     */
    const EXCEL_DIR = '/Data/';
   
    /**
     * 学生分数目录
     * @var string
     */
    protected static $scoreDir;

    /**
     * 学科表名
     * @var string
     */
    protected static $courseAnalysisName = '学科分析';

    /**
     * 日期
     * 为上传考试成绩时的日期（而不是考试日期）
     * @var string
     */
    protected static $date;

    /**
     * 文件夹名
     * 为上传考试成绩时打包文件的文件名（默认规则是当次考试的考试名称）
     * @var string
     */
    protected static $foldername;

    /**
     * 科目
     * @var string
     */
    protected static $course;

    /**
     * 学校类型
     * @var string
     */
    protected static $schoolType;

    /**
     * 构造
     * @param $date 日期
     * @param $foldername 文件夹名称（包含信息：学年、学期、年级、考试名称）
     * @param $course 查询科目
     */
    function __construct($date, $foldername, $course)
    {
        self::$scoreDir = iconv("utf-8", "gb2312", '全区报表');

        self::$date = $date;
        self::$foldername = iconv("utf-8", "gb2312", $foldername);
        self::$course = iconv("utf-8", "gb2312", $course);

    }

    /**
     * 打开excel表
     * @return string $objWorksheet 返回相应excel文件的工作薄
     */
    private function openExcel($filePath, $filename)
    {
        vendor("PHPExcel.PHPExcel.IOFactory");

        $excelRoot = dirname(dirname(dirname(dirname(__FILE__))));

        $excelPath = $excelRoot . $filePath . $filename . '.xls';

        $objPHPExcel = \PHPExcel_IOFactory::load($excelPath);
        $objWorksheet = $objPHPExcel->getSheet(0);

        return $objWorksheet;
    }

    /**
     * 根据文件夹分析出学年、学期、年级、考试名称，并入库
     * @param string $foldername 文件夹名
     */
    public function writeExamInfo()
    {
        $examData = M('exam');
        $examResult = $examData->where("fullname='$foldername'")->find();
        if(!$examResult){
            $foldernameData = self::analysisFoldername();
            $data['schoolYear'] = $foldernameData['schoolYear'];
            $data['schoolTerm'] = $foldernameData['schoolTerm'];
            $data['grade'] = $foldernameData['grade'];
            $data['examName'] = $foldernameData['examName'];
            $data['fullname'] = $foldername;
            $data['uploadDate'] = $date;
            $examData->data($data)->add();
        }
    }

    /**
     * 分析文件夹信息，根据文件夹分析出学年、学期、年级、考试名称，并入库
     * @param string $foldername 文件夹名
     */
    public function analysisFoldername()
    {
        var_export(self::$foldername);
        $schoolYear = substr(self::$foldername,0,9); // 学年
        $schoolTerm = substr(self::$foldername,12,12); // 学期

        if(substr(self::$foldername,24,3) == '高') {
            $grade = substr(self::$foldername,24,12); // 年级
        } else {
            $grade = substr(self::$foldername,24,9); // 年级
        }

        $examName = substr(self::$foldername,-12); // 考试名称

        if($grade == '高三年级' || $grade == '高二年级' || $grade == '高一年级')
        {
            self::$schoolType = 'high';
        }
        elseif ($grade == '九年级' || $grade == '八年级' || $grade == '七年级')
        {
            self::$schoolType = 'middle';
        }
        elseif ($grade == '六年级' || $grade == '五年级' || $grade == '四年级')
        {
            self::$schoolType = 'junior';
        }

        $data = array(
            'schoolYear' => $schoolYear, // 学年
            'schoolTerm' => $schoolTerm, // 学期
            'grade'      => $grade, // 年级
            'examName'   => $examName, // 考试名称
            'schoolType'   => self::$schoolType, // 考试名称
        );

        return $data;
    }

    /**
     * 获取考试科目数据
     */
    public function getCourseData()
    {
        $filePath = self::EXCEL_DIR.self::$date.'/'.self::$foldername.'/'.self::$scoreDir.'/';
        $filename = iconv("utf-8", "gb2312", self::$courseAnalysisName);

        $excelData = self::openExcel($filePath, $filename);

        $keys = array(); // 基本项标题
        $rets = array(); // 基本项内容

        $courseData = array(); // 学科

        foreach($excelData->getRowIterator() as $kr => $row){

            $cellIterator = $row->getCellIterator();
            if($kr > 1) {
                foreach($cellIterator as $kc => $cell){
                    if($kc == 0) {
                        $courseData[] = $cell->getValue();
                    }
                }
            }
        }

        return $courseData;
    }
}

?>