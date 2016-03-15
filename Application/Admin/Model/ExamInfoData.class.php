<?php

/**
 * 获取、写入考试信息
 * @author chenhong
 */

namespace Admin\Model;
use Think\Model;

class ExamInfoData {
   
    /**
     * Excel表目录
     * @var string
     */
    const EXCEL_DIR = '/Data/';
   
    /**
     * 学生分数主目录
     * @var string
     */
    protected static $mainDir = '全区报表';

    /**
     * 日期
     * 为上传考试成绩时的日期
     * @var string
     */
    protected static $uploadDate;

    /**
     * 文件夹名
     * 为上传考试成绩时打包文件的文件名（默认规则是当次考试的考试名称）
     * @var string
     */
    protected static $foldername;

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
    function __construct($date, $foldername)
    {
        self::$uploadDate = $date;
        self::$foldername = $foldername;
    }

    /**
     * 根据文件夹分析出学年、学期、年级、考试名称，并入库
     * @param string $foldername 文件夹名
     */
    public function writeExamInfo($courseData)
    {
        $foldernameData = self::getExamInfoData();
        $fullname = $foldernameData['fullname'];
        $uploadDate = self::$uploadDate;
        $examInfoData = M('exam');
        // $examInfo = $examInfoData->where("fullname='$fullname'")->find();
        $data = array(); // 写入数据信息
        if(!$examInfo){
            $data['schooltype'] = self::$schoolType;
            $data['schoolyear'] = $foldernameData['schoolYear'];
            $data['schoolterm'] = $foldernameData['schoolTerm'];
            $data['grade']      = $foldernameData['grade'];
            $data['examname']   = $foldernameData['examName'];
            $data['fullname']   = $foldernameData['fullname'];
            $data['uploaddate'] = $uploadDate;
            $data['courselist'] = implode(',',$courseData);
            $examInfoData->data($data)->add();
        } else {
            $data['uploaddate'] = $uploadDate;
            $examInfoData->where("fullname='$fullname'")->setField($data);
        }
    }

    /**
     * 得到考试相关信息
     */
    public function getExamInfoData()
    {

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
            'uploadDate' => self::$uploadDate, // 上传考试日期
            'rootDir'    => self::EXCEL_DIR, // 考试根目录
            'fullname'   => self::$foldername, // 考试全称
            'mainDir'    => self::$mainDir, // 考试主目录
            'schoolYear' => $schoolYear, // 学年
            'schoolTerm' => $schoolTerm, // 学期
            'grade'      => $grade, // 年级
            'examName'   => $examName, // 考试名称
            'schoolType' => self::$schoolType, // 考试名称
        );

        return $data;
    }
}

?>