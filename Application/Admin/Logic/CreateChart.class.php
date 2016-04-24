<?php

/**
 * 建立word文档中的chart
 * @author chenhong
 */

namespace Admin\Logic;
use Think\Model;

class CreateChart {

    const KEY = "__gen_data__";

    /**
     * 科目
     * @var string
     */
    protected static $course;

    /**
     * 考试信息
     * @var array
     */
    protected static $examInfoData;

    /**
     * 获取科目双向细目表
     * @var array
     */
    protected static $detailTableData;

    /**
     * 构造
     * @param $date 日期
     * @param $foldername 文件夹名称（包含信息：学年、学期、年级、考试名称）
     * @param $course 查询科目
     */
    function __construct($date, $foldername, $course)
    {
        // 获取考试数据目录
        $examInfoObj = new \Admin\Model\ExamInfoData($date, $foldername);
        self::$examInfoData = $examInfoObj->getExamInfoData();

        // 获取当前查询科目
        self::$course = $course;

        // 获取双向明细表数据(考核范畴、考核层级)
        $detailTableObj = new \Admin\Model\DetailTableData(self::$examInfoData, self::$course);
        self::$detailTableData = $detailTableObj->getDetailTableData();
    }

    private function getSign($json, $case){
        return substr(md5($json."--".$case."--".self::KEY), 16, 8);
    }

    /**
     * 建立word chart图片
     */
    public function creatChartPie()
    {
        $examScopeData = self::$detailTableData['examScopeTotalScore'];

        // 生成图表
        $phantomPath = 'D:/webserver/phantomjs/bin/phantomjs.exe ';
        $dataStr = base64_encode(json_encode($examScopeData));
        $case = "pie1";
        $subDir = iconv("utf-8", "gb2312", $foldername);

        $sign = $this->getSign($dataStr, $case);

        $phantomBaseDir = dirname(dirname(dirname(dirname(__FILE__))))."/Phantom/";

        do{
            $workDir = $phantomBaseDir.$subDir."/";
            $dataJsDir = $phantomBaseDir.$foldername."/";
        }while(file_exists($workDir));

        $old = umask(0);
        mkdir($workDir, 0777);
        umask($old);

        $jsTplFile = $phantomBaseDir."/examscoppie.tpl.js";
        $jsFile = $workDir."/data.js";

        $baseUrl = "http://www.bcedu.com/index.php";
        $picFile = "examscopepie.png";

        $workDir2 = str_replace("\\", '/', $dataJsDir);

        $js = file_get_contents($jsTplFile);
        $js = str_replace(array(
            "{baseurl}", 
            "{workdir}", 
            "{pic}", 
            "{data}",
            "{case}",
            "{sign}"
        ), array(
            $baseUrl,
            $workDir2, 
            $picFile,
            rawurlencode($dataStr),
            $case,
            $sign   
        ), $js);

        file_put_contents($jsFile, $js);
        exec($phantomPath.$jsFile); 

        // 等待截图完成
        sleep(2);
    }
}

?>