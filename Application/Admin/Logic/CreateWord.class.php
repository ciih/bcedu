<?php

/**
 * 建立word文档
 * @author chenhong
 */

namespace Admin\Logic;
use Think\Model;

class CreateWord {

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
     * 获取学校列表
     * @var array
     */
    protected static $schoolInfoData;

    /**
     * 获取考试课程列表
     * @var string
     */
    protected static $courseListData;

    /**
     * 获取分数率
     * @var array
     */
    protected static $baseScoreRateData;

    /**
     * 获取学科分析
     * @var array
     */
    protected static $courseAnalysisData;

    /**
     * 获取科目双向细目表
     * @var array
     */
    protected static $detailTableData;

    /**
     * 获取综合指标
     * @var array
     */
    protected static $comprehensiveIndicatorsData;

    /**
     * 获取学生分数(小题分)
     * @var array
     */
    protected static $studentScoreData;

    /**
     * 获取分数统计
     * @var array
     */
    protected static $scoreStatisticsData;

    /**
     * 获取客观题统计
     * @var array
     */
    protected static $choiceQuestionsAnalysisData;

    /**
     * 获取D值
     * @var array
     */
    protected static $dValueData;

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

        // 获取学校列表
        $schoolInfoObj = new \Admin\Model\SchoolInfoData();
        self::$schoolInfoData = $schoolInfoObj->getSchoolData(self::$examInfoData['schoolType']);

        // 获取当前科目得分率
        $baseScoreRateObj = new \Admin\Model\BaseScoreRateData();
        self::$baseScoreRateData = $baseScoreRateObj->getBaseScoreRateData(self::$course);

        // 获取所有科目列表
        $courseObj = new \Admin\Model\CourseData(self::$examInfoData);
        self::$courseListData = $courseObj->getCourseData();

        // 获取双向明细表数据(考核范畴、考核层级)
        $detailTableObj = new \Admin\Model\DetailTableData(self::$examInfoData, self::$course);
        self::$detailTableData = $detailTableObj->getDetailTableData();

        // 获取成绩数据
        $examDataObj = new \Admin\Model\ExcelData(self::$examInfoData, self::$schoolInfoData, self::$baseScoreRateData, self::$courseListData, self::$detailTableData, self::$course);

        // 获取课程分析数据(难度、区分度、信度)
        self::$courseAnalysisData = $examDataObj->getCourseAnalysisData();

        // 获取综合指标数据(学校人数、平均分、最高分、最低分)
        self::$comprehensiveIndicatorsData = $examDataObj->getComprehensiveIndicatorsData();

        // 获取小题分数据(考核范畴、考核层级各学生分数；学生分数列表；学生所属学校列表；全区、各学校人数统计；人数百分比、累计百分比)
        self::$studentScoreData = $examDataObj->getStudentScoreData();

        // 获取课程分数统计数据(全区、各学校考核范畴各项目分数；全区、各学校考核层级各项目分数)
        self::$scoreStatisticsData = $examDataObj->getScoreStatisticsData();

        // 获取客观题统计
        self::$choiceQuestionsAnalysisData = $examDataObj->getChoiceQuestionsAnalysisData();

        // 获取课程分析数据
        $dValueObj = new \Admin\Model\DValueData(self::$schoolInfoData, self::$detailTableData, self::$studentScoreData, self::$scoreStatisticsData);
        self::$dValueData = $dValueObj->getDValueData();
    }

    /**
     * 建立word文件
     * @param $foldername 标题
     * @param $course 科目
     */
    public function creatWordFile()
    {

        if(self::$course != '数学(理)' && self::$course != '数学(文)') {
            $courseName = str_split(self::$course, 3);
        }
        elseif(self::$course == '数学(理)') {
            $courseName[] = '数';
            $courseName[] = '学';
            $courseName[] = '(理)';
        }
        elseif(self::$course == '数学(文)') {
            $courseName[] = '数';
            $courseName[] = '学';
            $courseName[] = '(文)';
        }

        $courseName[] = '学';
        $courseName[] = '科';

        vendor("PHPWord.PHPWord");

        $PHPWord = new \PHPWord();

        $sectionStyle = array(
            'orientation' => null,
            'marginLeft' => 900,
            'marginRight' => 900,
            'marginTop' => 1400,
            'marginBottom' => 1000
        );


        $PHPWord->setDefaultFontName('楷体_GB2312');
        $PHPWord->setDefaultFontSize(14);

        $wordSaveDir = dirname(dirname(dirname(dirname(__FILE__))))."/Data/Word/".iconv("utf-8", "gb2312", self::$examInfoData['fullname'])."/";

        if (!file_exists($wordSaveDir))
        {
            mkdir ($wordSaveDir);
        }

        $section = $PHPWord->createSection($sectionStyle);

        $coverStyleFont = array('size'=>26.25);
        $coverStyleParagraph = array('align'=>'center', 'spacing'=>40);

        $subTitleStyleFont = array('bold'=>true, 'size'=>16);
        $subTitleStyleParagraph = array('spacing'=>30);

        $subSmallTitleStyleFont = array('bold'=>true, 'size'=>14);

        $contentStyleFont = array('spacing'=>40, 'size'=>12);
        $contentStyleParagraph = array('spacing'=>60);

        $tableTitleStyleFont = array('size'=>10);

        $choiceQuestionsContentStyleFont = array('spacing'=>180, 'size'=>12);

        $styleTable = array(
            'borderTopSize'=>6,
            'borderBottomSize'=>6,
            'cellMargin'=>50
        );

        $styleFirstRow = array(
            'borderTopSize'=>20,
            'borderBottomSize'=>6
        ); 

        $cellStyle = array(
            'valign'=>'center',
            'align'=>'center'
        );

        $cellStyleLast = array(
            'borderBottomSize'=>6
        );

        $cellRedStyleFont = array(
            'size'=>12,
            'color'=>'#ff0000'
        );

        $cellGreenStyleFont = array(
            'size'=>12,
            'color'=>'#0eab1a'
        );

        $cellPurpleStyleFont = array(
            'size'=>12,
            'color'=>'#7030a0'
            
        );

        $cellBlueStyleFont = array(
            'size'=>12,
            'color'=>'#0070c0'
            
        );

        $cellStyleFont = array(
            'size'=>12
        ); 


        $PHPWord->addTableStyle('myTableStyle', $styleTable, $styleFirstRow);

        $section->addTextBreak(2);
        $section->addText(self::$examInfoData['fullname'], $coverStyleFont, $coverStyleParagraph);
        $section->addText('考试水平评价及教学质量分析报告', $coverStyleFont, $coverStyleParagraph);
        $section->addTextBreak(4);

        for ($i = 0; $i < count($courseName); $i++) { 
            $section->addText($courseName[$i], $coverStyleFont, $coverStyleParagraph);
        }

        if(self::$course != '数学(理)' && self::$course != '数学(文)') {
            $section->addTextBreak(12);
        } else {
            $section->addTextBreak(8);
        }

        $section->addText('1.总体', $subTitleStyleFont, $subTitleStyleParagraph);
        $section->addText('     1)本次'.self::$course.'科目考试共涉及'.count(self::$schoolInfoData['schoolList']).'所学校，共计'.self::$comprehensiveIndicatorsData['totalStudentCount'].'人', $contentStyleFont, $contentStyleParagraph);
        $section->addText('     2) 该试卷总分为'.self::$detailTableData['totalScore'].'分，平均分为'.self::$comprehensiveIndicatorsData['totalAverageScore'].'分。', $contentStyleFont, $contentStyleParagraph);
        $section->addText('     3)试卷难度为'.self::$courseAnalysisData['difficulty'].'，区分度为'.self::$courseAnalysisData['distinguish'].'，信度为'.self::$courseAnalysisData['reliability'].'；', $contentStyleFont, $contentStyleParagraph);
        $section->addText('     4)数据表明试卷难度'.self::$courseAnalysisData['difficultyTxt'].'，且区分度'.self::$courseAnalysisData['distinguishTxt'].'，信度'.self::$courseAnalysisData['reliabilityTxt'].'。', $contentStyleFont, $contentStyleParagraph);
        $section->addText('     5)根据此次考试('.self::$course.'学科)所确定的优秀率、及格率，确定各水平线：', $contentStyleFont, $contentStyleParagraph);
        $section->addText('             1、优秀水平：>='.(self::$baseScoreRateData[0]).'%', $contentStyleFont, $contentStyleParagraph);
        $section->addText('             2、及格水平：>='.(self::$baseScoreRateData[1]).'%', $contentStyleFont, $contentStyleParagraph);
        $section->addText('             3、未及格：   <'.(self::$baseScoreRateData[1]).'%', $contentStyleFont, $contentStyleParagraph);
        $section->addText('         本报告的评价对象为实际参加该次语文学科的考生。以下将根据考生在不同知识范畴、能力层级的作答表现，分析不同区域学校以及不同水平考生的水平。', $contentStyleFont, $contentStyleParagraph);

        $section->addTextBreak(17);

        $section->addText('2.全体及不同水平组考生分析', $subTitleStyleFont, $subTitleStyleParagraph);
        $section->addText('2.1总体水平概况分析', $subSmallTitleStyleFont);
        $section->addText('表2.1 '.self::$course.'学科全区不同水平组考生的人数及所占百分比(%)', $tableTitleStyleFont, $tableStyleParagraph);

        // Add tableTJRS（统计人数）
        $tableTJRS = $section->addTable('myTableStyle'); 
         
        // Add row设置行高 
        $tableTJRS->addRow(300); 
         
        $tableTJRS->addCell(2200)->addText('水平组', $cellStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText('人数', $cellStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText('百分比', $cellStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText('累计人数', $cellStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText('累计百分比', $cellStyleFont, $cellStyle); 
         
        // Add row设置行高 
        $tableTJRS->addRow(300); 
         
        $tableTJRS->addCell(2200)->addText('优秀', $cellGreenStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText(self::$studentScoreData['totalCount']['excellentCount'], $cellGreenStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText(self::$studentScoreData['totalRate']['excellentRate'], $cellGreenStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText(self::$studentScoreData['cumulativeCount']['excellentCount'], $cellGreenStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText(self::$studentScoreData['cumulativeRate']['excellentRate'], $cellGreenStyleFont, $cellStyle); 
         
        // Add row设置行高 
        $tableTJRS->addRow(300); 
         
        $tableTJRS->addCell(2200)->addText('及格', $cellRedStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText(self::$studentScoreData['totalCount']['passCount'], $cellRedStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText(self::$studentScoreData['totalRate']['passRate'], $cellRedStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText(self::$studentScoreData['cumulativeCount']['passCount'], $cellRedStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText(self::$studentScoreData['cumulativeRate']['passRate'], $cellRedStyleFont, $cellStyle);
        
        // Add row设置行高 
        $tableTJRS->addRow(300); 
         
        $tableTJRS->addCell(2200)->addText('未及格', $cellPurpleStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText(self::$studentScoreData['totalCount']['failCount'], $cellPurpleStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText(self::$studentScoreData['totalRate']['failRate'], $cellPurpleStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText(self::$studentScoreData['cumulativeCount']['failCount'], $cellPurpleStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText(self::$studentScoreData['cumulativeRate']['failRate'], $cellPurpleStyleFont, $cellStyle);

        $section->addTextBreak();

        $section->addText('         表1.1的数据表明：', $contentStyleFont, $contentStyleParagraph);
        $section->addText('         1)  本次考试共有'.self::$studentScoreData['totalCount']['excellentCount'].'名考生达到优秀水平，占全体考生的'.self::$studentScoreData['totalRate']['excellentRate'].'%。共有'.self::$studentScoreData['totalCount']['passCount'].'名考生达到及格水平，占全体考生的'.self::$studentScoreData['totalRate']['passRate'].'%。累计比例为'.self::$studentScoreData['cumulativeRate']['passRate'].'%', $contentStyleFont, $contentStyleParagraph);
        $section->addText('         2)  本次考试共有'.self::$studentScoreData['totalCount']['failCount'].'名考生未达到及格水平，比例为'.self::$studentScoreData['totalRate']['failRate'].'%。', $contentStyleFont, $contentStyleParagraph);
        $section->addText('2.2 全体及不用水平组考生各知识范畴水平分析', $subSmallTitleStyleFont);
        $section->addText('表2.2全体及不同水平组考生各知识范畴得分率比较', $tableTitleStyleFont, $tableStyleParagraph);

        // Add tableZSFC（知识范畴）
        $tableZSFC = $section->addTable('myTableStyle'); 

        // Add row设置行高 
        $tableZSFC->addRow(300); 
         
        $tableZSFC->addCell(2500)->addText('知识范畴', $cellStyleFont, $cellStyle); 
        $tableZSFC->addCell(1600)->addText('满分值', $cellStyleFont, $cellStyle); 
        $tableZSFC->addCell(1600)->addText('全体', $cellStyleFont, $cellStyle); 
        $tableZSFC->addCell(1600)->addText('优秀', $cellBlueStyleFont, $cellStyle); 
        $tableZSFC->addCell(1600)->addText('及格', $cellGreenStyleFont, $cellStyle); 
        $tableZSFC->addCell(1600)->addText('未及格', $cellRedStyleFont, $cellStyle); 

        $examScopeList = self::$scoreStatisticsData['examScopeTotalRate'];
        $examScopeScore = self::$detailTableData['examScopeTotalScore'];

        foreach ($examScopeList as $examScopeName => $scorerRate) {
            $tableZSFC->addRow(300); 
             
            $tableZSFC->addCell(2500)->addText($examScopeName, $cellStyleFont, $cellStyle); 
            $tableZSFC->addCell(1600)->addText($examScopeScore[$examScopeName], $cellStyleFont, $cellStyle); 
            $tableZSFC->addCell(1600)->addText($scorerRate['totalRate'], $cellStyleFont, $cellStyle); 
            $tableZSFC->addCell(1600)->addText($scorerRate['excellentRate'], $cellBlueStyleFont, $cellStyle); 
            $tableZSFC->addCell(1600)->addText($scorerRate['passRate'], $cellGreenStyleFont, $cellStyle); 
            $tableZSFC->addCell(1600)->addText($scorerRate['failRate'], $cellRedStyleFont, $cellStyle);

            $examScopeTotalRate[$examScopeName] = $scorerRate['totalRate'];
            if($scorerRate['excellentRate'] > $scorerRate['totalRate']) {
                $examScopeTotalExcellentRateCount++;
                $examScopeExcellentRateDiffCount[$examScopeName] = $scorerRate['excellentRate'] - $scorerRate['totalRate'];
            }
            if($scorerRate['passRate'] > $scorerRate['totalRate']) {
                $examScopeTotalpassRateCount++;
            }
            if($scorerRate['failRate'] < $scorerRate['totalRate']) {
                $examScopeTotalfailRateCount++;
                $examScopefailRateDiffCount[$examScopeName] = $scorerRate['totalRate'] - $scorerRate['failRate'];
            }
        }

        $section->addTextBreak();

        $section->addText('     由以上图表的数据分析表明：', $contentStyleFont, $contentStyleParagraph);
    
        if(count(self::$detailTableData['examScopeName']) <= 6) {
            foreach (self::$detailTableData['examScopeName'] as $key => $name) {
                if($key < 5) {
                    $examScopeTxt = $examScopeTxt.$name.'('.$examScopeScore[$name].'分)、';
                }
                if($key == 5) {
                    $examScopeTxt = $examScopeTxt.$name.'('.$examScopeScore[$name].'分)。';
                }
            }
        } else {
            foreach (self::$detailTableData['examScopeName'] as $key => $name) {
                if($key < 5) {
                    $examScopeTxt = $examScopeTxt.$name.'('.$examScopeScore[$name].'分)、';
                }
                if($key == 5) {
                    $examScopeTxt = $examScopeTxt.$name.'('.$examScopeScore[$name].'分)等。';
                }
            }
        }

        arsort($examScopeScore);
        arsort($examScopeTotalRate);
        arsort($examScopeExcellentRateDiffCount);
        arsort($examScopefailRateDiffCount);

        $section->addText('     1）  本科目考试此次共包含'.count(self::$detailTableData['examScopeName']).'个知识范畴，包括：'.$examScopeTxt.'其中，'.array_keys($examScopeScore)[0].'('.array_values($examScopeScore)[0].'分)该知识范畴所占比重最大。', $contentStyleFont, $contentStyleParagraph);

        $section->addText('     2）  根据全体考生作答表现可知，得分率最高为'.array_keys($examScopeTotalRate)[0].'('.array_values($examScopeTotalRate)[0].')；其次为'.array_keys($examScopeTotalRate)[1].'('.array_values($examScopeTotalRate)[1].');最低的为'.array_keys($examScopeTotalRate)[count($examScopeTotalRate)-1].'，得分率为'.array_values($examScopeTotalRate)[count($examScopeTotalRate)-1].'。', $contentStyleFont, $contentStyleParagraph);

        if($examScopeTotalExcellentRateCount == count(self::$detailTableData['examScopeName'])){
            $txt = '均高于';
        }
        else {
            $txt = '部分高于';
        }

        $section->addText('     3）  该科达到优秀水平考生的各知识范畴的得分率'.$txt.'全体考生平均水平；其中'.array_keys($examScopeExcellentRateDiffCount)[0].'高于全体水平最多，得分率相差'.array_values($examScopeExcellentRateDiffCount)[0].'。', $contentStyleFont, $contentStyleParagraph);

        if($examScopeTotalpassRateCount == count(self::$detailTableData['examScopeName'])){
            $txt = '均高于';
        }
        else {
            $txt = '部分高于';
        }

        $section->addText('     4）  达到及格水平考生的各知识范畴的得分率'.$txt.'全区平均水平。', $contentStyleFont, $contentStyleParagraph);

        if($examScopeTotalfailRateCount == count(self::$detailTableData['examScopeName'])){
            $txt = '均低于';
        }
        else {
            $txt = '部分低于';
        }

        $section->addText('     5） 未及格考生水平组的各知识范畴得分率'.$txt.'全区平均水平；其中'.array_keys($examScopefailRateDiffCount)[0].'低于全体水平最多，得分率相差'.array_values($examScopefailRateDiffCount)[0].'。', $contentStyleFont, $contentStyleParagraph);


        $section->addText('2.3全体及不用水平组考生各能力层级水平分析', $subSmallTitleStyleFont);
        $section->addText('表2.3全体及不同水平组考生各能力层级得分率比较', $tableTitleStyleFont, $tableStyleParagraph);

        // Add tableNLCJ（能力层级）
        $tableNLCJ = $section->addTable('myTableStyle'); 

        // Add row设置行高 
        $tableNLCJ->addRow(300); 
         
        $tableNLCJ->addCell(2500)->addText('能力层级', $cellStyleFont, $cellStyle); 
        $tableNLCJ->addCell(1600)->addText('满分值', $cellStyleFont, $cellStyle); 
        $tableNLCJ->addCell(1600)->addText('全体', $cellStyleFont, $cellStyle); 
        $tableNLCJ->addCell(1600)->addText('优秀', $cellBlueStyleFont, $cellStyle); 
        $tableNLCJ->addCell(1600)->addText('及格', $cellGreenStyleFont, $cellStyle); 
        $tableNLCJ->addCell(1600)->addText('未及格', $cellRedStyleFont, $cellStyle); 

        $examMoldList = self::$scoreStatisticsData['examMoldTotalRate'];
        $examMoldScore = self::$detailTableData['examMoldTotalScore'];

        foreach ($examMoldList as $examMoldName => $scorerRate) {
            $tableNLCJ->addRow(300); 
             
            $tableNLCJ->addCell(2500)->addText($examMoldName, $cellStyleFont, $cellStyle); 
            $tableNLCJ->addCell(1600)->addText($examMoldScore[$examMoldName], $cellStyleFont, $cellStyle); 
            $tableNLCJ->addCell(1600)->addText($scorerRate['totalRate'], $cellStyleFont, $cellStyle); 
            $tableNLCJ->addCell(1600)->addText($scorerRate['excellentRate'], $cellBlueStyleFont, $cellStyle); 
            $tableNLCJ->addCell(1600)->addText($scorerRate['passRate'], $cellGreenStyleFont, $cellStyle); 
            $tableNLCJ->addCell(1600)->addText($scorerRate['failRate'], $cellRedStyleFont, $cellStyle);

            $examMoldTotalRate[$examMoldName] = $scorerRate['totalRate'];
            if($scorerRate['excellentRate'] > $scorerRate['totalRate']) {
                $examMoldTotalExcellentRateCount++;
                $examMoldExcellentRateDiffCount[$examMoldName] = $scorerRate['excellentRate'] - $scorerRate['totalRate'];
            }
            if($scorerRate['passRate'] > $scorerRate['totalRate']) {
                $examMoldTotalpassRateCount++;
            }
            if($scorerRate['failRate'] < $scorerRate['totalRate']) {
                $examMoldTotalfailRateCount++;
                $examMoldfailRateDiffCount[$examMoldName] = $scorerRate['totalRate'] - $scorerRate['failRate'];
            }
        }

        $section->addTextBreak();

        $section->addText('     由以上图表的数据分析表明：', $contentStyleFont, $contentStyleParagraph);
    
        if(count(self::$detailTableData['examMoldName']) <= 6) {
            foreach (self::$detailTableData['examMoldName'] as $key => $name) {
                if($key < 5) {
                    $examMoldTxt = $examMoldTxt.$name.'('.$examMoldScore[$name].'分)、';
                }
                if($key == 5) {
                    $examMoldTxt = $examMoldTxt.$name.'('.$examMoldScore[$name].'分)。';
                }
            }
        } else {
            foreach (self::$detailTableData['examMoldName'] as $key => $name) {
                if($key < 5) {
                    $examMoldTxt = $examMoldTxt.$name.'('.$examMoldScore[$name].'分)、';
                }
                if($key == 5) {
                    $examMoldTxt = $examMoldTxt.$name.'('.$examMoldScore[$name].'分)等。';
                }
            }
        }

        arsort($examMoldScore);
        arsort($examMoldTotalRate);
        arsort($examMoldExcellentRateDiffCount);
        arsort($examMoldfailRateDiffCount);

        $section->addText('     1）  本科目考试此次共包含'.count(self::$detailTableData['examMoldName']).'个能力层级，包括：'.$examMoldTxt.'其中，'.array_keys($examMoldScore)[0].'('.array_values($examMoldScore)[0].'分)该能力层级所占比重最大。', $contentStyleFont, $contentStyleParagraph);

        $section->addText('     2）  根据全体考生作答表现可知，得分率最高为'.array_keys($examMoldTotalRate)[0].'('.array_values($examMoldTotalRate)[0].')；其次为'.array_keys($examMoldTotalRate)[1].'('.array_values($examMoldTotalRate)[1].');最低的为'.array_keys($examMoldTotalRate)[count($examMoldTotalRate)-1].'，得分率为'.array_values($examMoldTotalRate)[count($examMoldTotalRate)-1].'。', $contentStyleFont, $contentStyleParagraph);

        if($examMoldTotalExcellentRateCount == count(self::$detailTableData['examMoldName'])){
            $txt = '均高于';
        }
        else {
            $txt = '部分高于';
        }

        $section->addText('     3）  该科达到优秀水平考生的各能力层级的得分率'.$txt.'全体考生平均水平；其中'.array_keys($examMoldExcellentRateDiffCount)[0].'高于全体水平最多，得分率相差'.array_values($examMoldExcellentRateDiffCount)[0].'。', $contentStyleFont, $contentStyleParagraph);

        if($examMoldTotalpassRateCount == count(self::$detailTableData['examMoldName'])){
            $txt = '均高于';
        }
        else {
            $txt = '部分高于';
        }

        $section->addText('     4）  达到及格水平考生的各能力层级的得分率'.$txt.'全区平均水平。', $contentStyleFont, $contentStyleParagraph);

        if($examMoldTotalfailRateCount == count(self::$detailTableData['examMoldName'])){
            $txt = '均低于';
        }
        else {
            $txt = '部分低于';
        }

        $section->addText('     5） 未及格考生水平组的各能力层级得分率'.$txt.'全区平均水平；其中'.array_keys($examMoldfailRateDiffCount)[0].'低于全体水平最多，得分率相差'.array_values($examMoldfailRateDiffCount)[0].'。', $contentStyleFont, $contentStyleParagraph);

        $section->addTextBreak();

        // Add tableSchoolZSFC（学校知识范畴）
        $tableSchoolZSFC = $section->addTable('myTableStyle'); 

        // Add row设置行高 
        $tableSchoolZSFC->addRow(300); 
         
        $tableSchoolZSFC->addCell(2500)->addText('学校', $cellStyleFont, $cellStyle); 
        $tableSchoolZSFC->addCell(2500)->addText('知识范畴', $cellStyleFont, $cellStyle); 
        $tableSchoolZSFC->addCell(1600)->addText('全区', $cellStyleFont, $cellStyle); 
        $tableSchoolZSFC->addCell(1600)->addText('全体', $cellStyleFont, $cellStyle); 
        $tableSchoolZSFC->addCell(1600)->addText('优秀', $cellBlueStyleFont, $cellStyle); 
        $tableSchoolZSFC->addCell(1600)->addText('及格', $cellGreenStyleFont, $cellStyle); 
        $tableSchoolZSFC->addCell(1600)->addText('未及格', $cellRedStyleFont, $cellStyle); 

        foreach (self::$schoolInfoData['schoolList'] as $schoolName) {
            foreach (self::$detailTableData['examScopeName'] as $key => $examScopeName) {
                if($key != count(self::$detailTableData['examScopeName']) - 1) {
                    $cellStyleValue = '';
                } else {
                    $cellStyleValue = $cellStyleLast;
                }
                $tableSchoolZSFC->addRow(300); 
                
                $tableSchoolZSFC->addCell(2500, $cellStyleValue)->addText($schoolName, $cellStyleFont, $cellStyle); 
                $tableSchoolZSFC->addCell(2500, $cellStyleValue)->addText($examScopeName, $cellStyleFont, $cellStyle); 

                $tableSchoolZSFC->addCell(1600, $cellStyleValue)->addText(self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate'], $cellStyleFont, $cellStyle);

                if(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['totalRate'] == 0 || self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['totalRate'] == self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellStyleFont;
                } elseif(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['totalRate'] > self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellRedStyleFont;
                } elseif(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['totalRate'] < self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellGreenStyleFont;
                }

                if(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['totalScore'] >= 0.5) {
                    $dValueTxt = '(+++)';
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['totalScore'] >= 0.2 && self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['totalScore'] < 0.5) {
                    $dValueTxt = '(+)';
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['totalScore'] <= -0.5) {
                    $dValueTxt = '(---)';
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['totalScore'] <= -0.2 && self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['totalScore'] > -0.5) {
                    $dValueTxt = '(-)';
                } else {
                    $dValueTxt = '';
                }
                $tableSchoolZSFC->addCell(1600, $cellStyleValue)->addText(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['totalRate'].$dValueTxt, $cellStyleFontColor, $cellStyle);

                if(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['excellentRate'] == 0 || self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['excellentRate'] == self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellStyleFont;
                } elseif(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['excellentRate'] > self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellRedStyleFont;
                } elseif(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['excellentRate'] < self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellGreenStyleFont;
                }
                
                if(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['excellentScore'] >= 0.5) {
                    $dValueTxt = '(+++)';
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['excellentScore'] >= 0.2 && self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['excellentScore'] < 0.5) {
                    $dValueTxt = '(+)';
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['excellentScore'] <= -0.5) {
                    $dValueTxt = '(---)';
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['excellentScore'] <= -0.2 && self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['excellentScore'] > -0.5) {
                    $dValueTxt = '(-)';
                } else {
                    $dValueTxt = '';
                }
                $tableSchoolZSFC->addCell(1600, $cellStyleValue)->addText(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['excellentRate'].$dValueTxt, $cellStyleFontColor, $cellStyle); 

                if(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['passRate'] == 0 || self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['passRate'] == self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellStyleFont;
                } elseif(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['passRate'] > self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellRedStyleFont;
                } elseif(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['passRate'] < self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellGreenStyleFont;
                }
                
                if(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['passScore'] >= 0.5) {
                    $dValueTxt = '(+++)';
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['passScore'] >= 0.2 && self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['passScore'] < 0.5) {
                    $dValueTxt = '(+)';
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['passScore'] <= -0.5) {
                    $dValueTxt = '(---)';
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['passScore'] <= -0.2 && self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['passScore'] > -0.5) {
                    $dValueTxt = '(-)';
                } else {
                    $dValueTxt = '';
                }
                $tableSchoolZSFC->addCell(1600, $cellStyleValue)->addText(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['passRate'].$dValueTxt, $cellStyleFontColor, $cellStyle); 

                if(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['failRate'] == 0 || self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['failRate'] == self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellStyleFont;
                } elseif(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['failRate'] > self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellRedStyleFont;
                } elseif(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['failRate'] < self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellGreenStyleFont;
                }
                
                if(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['failScore'] >= 0.5) {
                    $dValueTxt = '(+++)';
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['failScore'] >= 0.2 && self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['failScore'] < 0.5) {
                    $dValueTxt = '(+)';
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['failScore'] <= -0.5) {
                    $dValueTxt = '(---)';
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['failScore'] <= -0.2 && self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['failScore'] > -0.5) {
                    $dValueTxt = '(-)';
                } else {
                    $dValueTxt = '';
                }
                $tableSchoolZSFC->addCell(1600, $cellStyleValue)->addText(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['failRate'].$dValueTxt, $cellStyleFontColor, $cellStyle);
            }
        }

        $section->addText('     表3.1的数据表明：', $contentStyleFont, $contentStyleParagraph);
        $section->addText('     不同学校全体及不同水平组考生各知识范畴作答表现存在差异，具体如下：', $contentStyleFont, $contentStyleParagraph);



        $section->addTextBreak();


        // Add tableSchoolNLCJ（学校能力层级）
        $tableSchoolNLCJ = $section->addTable('myTableStyle'); 

        // Add row设置行高 
        $tableSchoolNLCJ->addRow(300); 
         
        $tableSchoolNLCJ->addCell(2500)->addText('学校', $cellStyleFont, $cellStyle); 
        $tableSchoolNLCJ->addCell(2500)->addText('能力层级', $cellStyleFont, $cellStyle); 
        $tableSchoolNLCJ->addCell(1600)->addText('全区', $cellStyleFont, $cellStyle); 
        $tableSchoolNLCJ->addCell(1600)->addText('全体', $cellStyleFont, $cellStyle); 
        $tableSchoolNLCJ->addCell(1600)->addText('优秀', $cellBlueStyleFont, $cellStyle); 
        $tableSchoolNLCJ->addCell(1600)->addText('及格', $cellGreenStyleFont, $cellStyle); 
        $tableSchoolNLCJ->addCell(1600)->addText('未及格', $cellRedStyleFont, $cellStyle); 

        foreach (self::$schoolInfoData['schoolList'] as $schoolName) {
            foreach (self::$detailTableData['examMoldName'] as $key => $examMoldName) {
                if($key != count(self::$detailTableData['examMoldName']) - 1) {
                    $cellStyleValue = '';
                } else {
                    $cellStyleValue = $cellStyleLast;
                }
                $tableSchoolNLCJ->addRow(300); 
                
                $tableSchoolNLCJ->addCell(2500, $cellStyleValue)->addText($schoolName, $cellStyleFont, $cellStyle); 
                $tableSchoolNLCJ->addCell(2500, $cellStyleValue)->addText($examMoldName, $cellStyleFont, $cellStyle);
                $tableSchoolNLCJ->addCell(1600, $cellStyleValue)->addText(self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate'], $cellStyleFont, $cellStyle);

                if(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['totalRate'] == 0 || self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['totalRate'] == self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellStyleFont;
                } elseif(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['totalRate'] > self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellRedStyleFont;
                } elseif(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['totalRate'] < self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellGreenStyleFont;
                }

                if(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['totalScore'] >= 0.5) {
                    $dValueTxt = '(+++)';
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['totalScore'] >= 0.2 && self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['totalScore'] < 0.5) {
                    $dValueTxt = '(+)';
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['totalScore'] <= -0.5) {
                    $dValueTxt = '(---)';
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['totalScore'] <= -0.2 && self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['totalScore'] > -0.5) {
                    $dValueTxt = '(-)';
                } else {
                    $dValueTxt = '';
                }
                $tableSchoolNLCJ->addCell(1600, $cellStyleValue)->addText(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['totalRate'].$dValueTxt, $cellStyleFontColor, $cellStyle);

                if(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['excellentRate'] == 0 || self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['excellentRate'] == self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellStyleFont;
                } elseif(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['excellentRate'] > self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellRedStyleFont;
                } elseif(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['excellentRate'] < self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellGreenStyleFont;
                }
                
                if(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['excellentScore'] >= 0.5) {
                    $dValueTxt = '(+++)';
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['excellentScore'] >= 0.2 && self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['excellentScore'] < 0.5) {
                    $dValueTxt = '(+)';
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['excellentScore'] <= -0.5) {
                    $dValueTxt = '(---)';
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['excellentScore'] <= -0.2 && self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['excellentScore'] > -0.5) {
                    $dValueTxt = '(-)';
                } else {
                    $dValueTxt = '';
                }
                $tableSchoolNLCJ->addCell(1600, $cellStyleValue)->addText(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['excellentRate'].$dValueTxt, $cellStyleFontColor, $cellStyle); 

                if(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['passRate'] == 0 || self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['passRate'] == self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellStyleFont;
                } elseif(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['passRate'] > self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellRedStyleFont;
                } elseif(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['passRate'] < self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellGreenStyleFont;
                }
                
                if(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['passScore'] >= 0.5) {
                    $dValueTxt = '(+++)';
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['passScore'] >= 0.2 && self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['passScore'] < 0.5) {
                    $dValueTxt = '(+)';
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['passScore'] <= -0.5) {
                    $dValueTxt = '(---)';
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['passScore'] <= -0.2 && self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['passScore'] > -0.5) {
                    $dValueTxt = '(-)';
                } else {
                    $dValueTxt = '';
                }
                $tableSchoolNLCJ->addCell(1600, $cellStyleValue)->addText(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['passRate'].$dValueTxt, $cellStyleFontColor, $cellStyle); 

                if(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['failRate'] == 0 || self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['failRate'] == self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellStyleFont;
                } elseif(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['failRate'] > self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellRedStyleFont;
                } elseif(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['failRate'] < self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellGreenStyleFont;
                }
                
                if(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['failScore'] >= 0.5) {
                    $dValueTxt = '(+++)';
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['failScore'] >= 0.2 && self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['failScore'] < 0.5) {
                    $dValueTxt = '(+)';
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['failScore'] <= -0.5) {
                    $dValueTxt = '(---)';
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['failScore'] <= -0.2 && self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['failScore'] > -0.5) {
                    $dValueTxt = '(-)';
                } else {
                    $dValueTxt = '';
                }
                $tableSchoolNLCJ->addCell(1600, $cellStyleValue)->addText(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['failRate'].$dValueTxt, $cellStyleFontColor, $cellStyle);
            }
        }




















        $section->addTextBreak(2);


        $section->addText('4.全体考生客观题水平分析', $subTitleStyleFont, $subTitleStyleParagraph);
        $section->addText('表4.1 全区考生语文科目客观题分析表', $tableTitleStyleFont, $tableStyleParagraph);

        // Add tableKGT（客观题水平分析）
        $tableKGT = $section->addTable('myTableStyle'); 

        // Add row设置行高 
        $tableKGT->addRow(300); 
         
        $tableKGT->addCell(1600)->addText('题号', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('答案', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('人数', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('平均分', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('标准差', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('得分率', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('难度', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('区分度', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('选A率%', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('选B率%', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('选C率%', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('选D率%', $cellStyleFont, $cellStyle); 

        $kgtRate = array();

        for ($i = 0; $i < count(self::$choiceQuestionsAnalysisData); $i++) { 
            foreach (self::$choiceQuestionsAnalysisData[$i] as $key => $name) {
                if($key == '选A率%' || $key == '选B率%' || $key == '选C率%' || $key == '选D率%') {
                    $kgtRate[$i][] = $name;
                }
            }
            arsort($kgtRate[$i]);
        }

        for ($i = 0; $i < count(self::$choiceQuestionsAnalysisData); $i++) { 
            $tableKGT->addRow(300);
            foreach (self::$choiceQuestionsAnalysisData[$i] as $key => $name) { 
                if($key != '难度评价标准' && $key != '区分度评价标准') {
                    if($key == '平均分') {
                        $tableKGT->addCell(1200)->addText($name, $cellPurpleStyleFont, $cellStyle);
                    } elseif($key == '选A率%' || $key == '选B率%' || $key == '选C率%' || $key == '选D率%') {
                        if($name == array_values($kgtRate[$i])[0]) {
                            $tableKGT->addCell(1200)->addText($name, $cellRedStyleFont, $cellStyle);
                        } else {
                            $tableKGT->addCell(1200)->addText($name, $cellStyleFont, $cellStyle);
                        }
                    } else {
                        $tableKGT->addCell(1200)->addText($name, $cellStyleFont, $cellStyle);
                    }
                }
            }
        }

        $section->addText('     注：区分度评价标准:>0.4区分度较高;0.3~0.39 区分度中等;0.2~0.29 区分度一般;<0.2区分度较低', $tableTitleStyleFont);
        $section->addText('         难度评价标准:  >0.9容易;0.7~0.9较易;0.4~0.7中等;<0.4偏难;', $tableTitleStyleFont);

        $section->addText('表2.0的数据表明', $contentStyleFont);

        for ($i = 0; $i < count(self::$choiceQuestionsAnalysisData); $i++) {
            if($kgtRate[$i][array_keys($kgtRate[$i])[1]] > 17) {
                if(array_keys($kgtRate[$i])[1] == 0) {
                    $item = 'A';
                }
                elseif(array_keys($kgtRate[$i])[1] == 1) {
                    $item = 'B';
                }
                elseif(array_keys($kgtRate[$i])[1] == 2) {
                    $item = 'C';
                }
                elseif(array_keys($kgtRate[$i])[1] == 3) {
                    $item = 'D';
                }
                $txt = '，选项'.$item.'的干扰性最强达到了'.$kgtRate[$i][array_keys($kgtRate[$i])[1]].'%';
            }
            else{
                $txt = '';
            }
            if($i / 2 == 0){
                $section->addText('     '.($i+1).'）'.self::$choiceQuestionsAnalysisData[$i]['题号'].'：该题难度'.self::$choiceQuestionsAnalysisData[$i]['难度评价标准'].'('.self::$choiceQuestionsAnalysisData[$i]['难度'].')，'.self::$choiceQuestionsAnalysisData[$i]['区分度评价标准'].'('.self::$choiceQuestionsAnalysisData[$i]['区分度'].')，有'.self::$choiceQuestionsAnalysisData[$i]['选'.self::$choiceQuestionsAnalysisData[$i]['答案'].'率%'].'%的学生选择了正确答案('.self::$choiceQuestionsAnalysisData[$i]['答案'].')，得分率>'.self::$choiceQuestionsAnalysisData[$i]['得分率'].'%'.$txt, $choiceQuestionsContentStyleFont);
            }
            else {
                $section->addText('     '.($i+1).'）'.self::$choiceQuestionsAnalysisData[$i]['题号'].'：该题从难度上讲属于'.self::$choiceQuestionsAnalysisData[$i]['难度评价标准'].'('.self::$choiceQuestionsAnalysisData[$i]['难度'].')，'.self::$choiceQuestionsAnalysisData[$i]['区分度评价标准'].'('.self::$choiceQuestionsAnalysisData[$i]['区分度'].')，正确答案('.self::$choiceQuestionsAnalysisData[$i]['答案'].')的选择率达到了'.self::$choiceQuestionsAnalysisData[$i]['选'.self::$choiceQuestionsAnalysisData[$i]['答案'].'率%'].'%，其余选项的选择率都低于'.$kgtRate[$i][array_keys($kgtRate[$i])[1]].'%'.$txt, $choiceQuestionsContentStyleFont);
            }
        }


        
        $section = \PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
        $section->save($wordSaveDir.iconv("utf-8", "gb2312", self::$course).'.docx');

        /*

        $examSchoolTotalRate = array();
        $examSchoolExcellentRate = array();
        $examSchoolPassRate = array();
        $examSchoolFailRate = array();

        $examSchoolRecord = array();

        foreach ($studentData['averageScore']['schoolName'] as $name) {
            for ($i = 0; $i < count($studentData['detailTable']['examName']); $i++) {
                if($i != count($studentData['detailTable']['examName']) - 1) {
                    $tableSchoolZSFC->addRow(300); 
                    
                    $tableSchoolZSFC->addCell(2500)->addText($name, $cellStyleFont, $cellStyle); 
                    $tableSchoolZSFC->addCell(2500)->addText($studentData['detailTable']['examName'][$i], $cellStyleFont, $cellStyle); 

                    $tableSchoolZSFC->addCell(1600)->addText($studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['total']['totalRate'], $cellStyleFont, $cellStyle); 
                    $tableSchoolZSFC->addCell(1600)->addText($studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['schoolScore'][$name]['totalRate'], $cellStyleFont, $cellStyle); 
                    $tableSchoolZSFC->addCell(1600)->addText($studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['schoolScore'][$name]['excellentRate'], $cellBlueStyleFont, $cellStyle); 
                    $tableSchoolZSFC->addCell(1600)->addText($studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['schoolScore'][$name]['passRate'], $cellGreenStyleFont, $cellStyle); 
                    $tableSchoolZSFC->addCell(1600)->addText($studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['schoolScore'][$name]['failRate'], $cellRedStyleFont, $cellStyle);
                }
                else {
                    $tableSchoolZSFC->addRow(300); 
                    
                    $tableSchoolZSFC->addCell(2500, $cellStyleLast)->addText($name, $cellStyleFont, $cellStyle); 
                    $tableSchoolZSFC->addCell(2500, $cellStyleLast)->addText($studentData['detailTable']['examName'][$i], $cellStyleFont, $cellStyle); 

                    $tableSchoolZSFC->addCell(1600, $cellStyleLast)->addText($studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['total']['totalRate'], $cellStyleFont, $cellStyle); 
                    $tableSchoolZSFC->addCell(1600, $cellStyleLast)->addText($studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['schoolScore'][$name]['totalRate'], $cellStyleFont, $cellStyle); 
                    $tableSchoolZSFC->addCell(1600, $cellStyleLast)->addText($studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['schoolScore'][$name]['excellentRate'], $cellBlueStyleFont, $cellStyle); 
                    $tableSchoolZSFC->addCell(1600, $cellStyleLast)->addText($studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['schoolScore'][$name]['passRate'], $cellGreenStyleFont, $cellStyle); 
                    $tableSchoolZSFC->addCell(1600, $cellStyleLast)->addText($studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['schoolScore'][$name]['failRate'], $cellRedStyleFont, $cellStyle);
                }

                $examSchoolTotalRate[$studentData['detailTable']['examName'][$i]] = $studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['total']['totalRate'];
                
                if($studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['schoolScore'][$name]['totalRate'] > $studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['total']['totalRate']) {
                    $examSchoolRecord[$name][] = 1;
                }
                else {
                    $examSchoolRecord[$name][] = 0;
                }
            }
        }

        arsort($examSchoolTotalRate);

        $schoolName1 = array();
        $schoolName2 = array();
        $schoolCourseNum = array();

        foreach ($examSchoolRecord as $key => $value) {
            
            for ($i = 0; $i < count($value); $i++) { 
                if($value[$i] == 0) {
                    break;
                }
                else {
                    if($i == count($value) - 1 && $value[$i] == 1) {
                        $schoolName1[] = $key;
                    }
                }
            }
            
            for ($i = 0; $i < count($value); $i++) { 
                if($value[$i] == 1) {
                    break;
                }
                else {
                    if($i == count($value) - 1 && $value[$i] == 0) {
                        $schoolName2[] = $key;
                    }
                }
            }
        }

        $num = 0;
        foreach ($examSchoolRecord as $key => $value) {

            if ($num > 1) {// 随便找个学校当样板
                for ($i = 0; $i < count($value); $i++) { 
                    if($value[$i] == 1) {
                        $schoolCourseNum[0] = $key;
                        $schoolCourseNum[1] = $value;
                        break;
                    }
                }
                break;
            }
            $num++;
        }

        $num = 0;
        foreach ($examSchoolRecord as $key => $value) {

            if ($num > 3) {// 随便找个学校当样板
                for ($i = 0; $i < count($value); $i++) { 
                    if($value[$i] == 0) {
                        $schoolCourseNum[3] = $key;
                        $schoolCourseNum[4] = $value;
                        break;
                    }
                }
                break;
            }
            $num++;
        }

        for ($i = 0; $i < count($schoolCourseNum[1]); $i++) { 
            if($schoolCourseNum[1][$i] == 1) {
                $str1 = $str1 . '、' . $studentData['detailTable']['examName'][$i];
            }
        }

        for ($i = 0; $i < count($schoolCourseNum[4]); $i++) { 
            if($schoolCourseNum[4][$i] == 0) {
                $str2 = $str2 . '、' . $studentData['detailTable']['examName'][$i];
            }
        }


        $schoolName1 = implode('、', $schoolName1);
        if(!empty($schoolName2)) {
            $schoolName2 = implode('、', $schoolName2);
        }
        else {
            $schoolName2 = '无';
        }

        $section->addText('     表3.1的数据表明：', $contentStyleFont, $contentStyleParagraph);
        $section->addText('     不同学校全体及不同水平组考生各知识范畴作答表现存在差异，具体如下：', $contentStyleFont, $contentStyleParagraph);
        $section->addText('         1）   不同学校全体及不同水平组考生在'.array_keys($examSchoolTotalRate)[0].'知识范畴作答表现好，得分率为'.$examSchoolTotalRate[array_keys($examSchoolTotalRate)[0]].'%；在'.array_keys($examSchoolTotalRate)[count($examSchoolTotalRate)-1].'表现相对较差为'.$examSchoolTotalRate[array_keys($examSchoolTotalRate)[count($examSchoolTotalRate)-1]].'%。', $contentStyleFont, $contentStyleParagraph);
        $section->addText('         2）   比较不同学校全体组考生各知识范畴的作答表现可知，'.$schoolName1.'考生各知识范畴的得分率均高于全区考生得分率； '.$schoolCourseNum[0].'中的'.$str1.'知识范畴的得分率均高于全区考生；'.$schoolCourseNum[3].'的'.$str2.'知识范畴得分率低于全区考生；'.$schoolName2.'考生各知识范畴考生的得分率均低于全区考生；', $contentStyleFont, $contentStyleParagraph);

        $examSchoolExcellentRecord = array();
        $examSchoolExcellentRecord1 = array();
        foreach ($studentData['averageScore']['schoolName'] as $name) {
            for ($i = 0; $i < count($studentData['detailTable']['examName']); $i++) {
                if($studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['schoolScore'][$name]['excellentRate'] > $studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['total']['totalRate']) {
                    $examSchoolExcellentRecord[$name][] = 1;
                }
                else {
                    $examSchoolExcellentRecord[$name][] = 0;
                }

                if($studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['schoolScore'][$name]['excellentRate'] >= $studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['total']['totalRate']) {
                    $examSchoolExcellentRecord1[$name][] = 1;
                }
                else {
                    $examSchoolExcellentRecord1[$name][] = 0;
                }
            }
        }


        $schoolName3 = array();
        $schoolName4 = array();
        $schoolName5 = array();
        foreach ($examSchoolExcellentRecord as $key => $value) {
            
            for ($i = 0; $i < count($value); $i++) { 
                if($value[$i] == 0) {
                    break;
                }
                else {
                    if($i == count($value) - 1 && $value[$i] == 1) {
                        $schoolName3[] = $key;
                    }
                }
            }
            
            for ($i = 0; $i < count($value); $i++) { 
                if($value[$i] == 0) {
                    break;
                }
                else {
                    if($i == count($value) - 1 && $value[$i] == 0) {
                        $schoolName4[] = $key;
                    }
                }
            }
        }
        foreach ($examSchoolExcellentRecord1 as $key => $value) {
            
            for ($i = 0; $i < count($value); $i++) { 
                if($value[$i] == 0) {
                    break;
                }
                else {
                    if($i == count($value) - 1 && $value[$i] == 1) {
                        $schoolName5[] = $key;
                    }
                }
            }
        }
        $schoolName3 = implode('、', $schoolName3);
        $schoolName4 = implode('、', $schoolName4);
        $schoolName5 = implode('、', $schoolName5);

        $section->addText('         3）   比较同水平组不同学校考生各知识范畴的作答表现， 优秀组'.$schoolName3.'考生在各知识范畴均高于全区得分率；'.$schoolName5.'考生在各知识范畴均高于或等于全区得分率；', $contentStyleFont, $contentStyleParagraph);

        $examSchoolPassRecord = array();
        foreach ($studentData['averageScore']['schoolName'] as $name) {
            for ($i = 0; $i < count($studentData['detailTable']['examName']); $i++) {
                if($studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['schoolScore'][$name]['passRate'] >= $studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['total']['totalRate']) {
                    $examSchoolPassRecord[$name][] = 1;
                }
                else {
                    $examSchoolPassRecord[$name][] = 0;
                }
            }
        }


        $schoolName6 = array();
        $schoolName7 = array();
        foreach ($examSchoolPassRecord as $key => $value) {
            
            for ($i = 0; $i < count($value); $i++) { 
                if($value[$i] == 0) {
                    break;
                }
                else {
                    if($i == count($value) - 1 && $value[$i] == 1) {
                        $schoolName6[] = $key;
                    }
                }
            }
            
            for ($i = 0; $i < count($value); $i++) { 
                if($value[$i] == 0) {
                    break;
                }
                else {
                    if($i == count($value) - 1 && $value[$i] == 1) {
                        $schoolName7[] = $key;
                    }
                }
            }
        }

        $schoolName6 = implode('、', $schoolName6);
        $schoolName7 = implode('、', $schoolName7);

        $section->addText('         4）   及格组中'.$schoolName6.'考生在各知识范畴的得分率均高于或等于全区考生得分率； 其余学校在各知识范畴的得分率均等于或低于全区考生得分率；', $contentStyleFont, $contentStyleParagraph);

        $examSchoolFailRecord = array();
        foreach ($studentData['averageScore']['schoolName'] as $name) {
            for ($i = 0; $i < count($studentData['detailTable']['examName']); $i++) {
                if($studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['schoolScore'][$name]['failRate'] != 0 && $studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['schoolScore'][$name]['failRate'] < $studentData['scoreStatisticsRate']['exam'][$studentData['detailTable']['examName'][$i]]['total']['totalRate']) {
                    $examSchoolFailRecord[$name][] = 1;
                }
                else {
                    $examSchoolFailRecord[$name][] = 0;
                }
            }
        }

        $schoolName8 = array();
        foreach ($examSchoolFailRecord as $key => $value) {
            
            for ($i = 0; $i < count($value); $i++) { 
                if($value[$i] == 0) {
                    break;
                }
                else {
                    if($i == count($value) - 1 && $value[$i] == 1) {
                        $schoolName8[] = $key;
                    }
                }
            }
        }

        if(count($schoolName8) == count($studentData['averageScore']['schoolName'])) {
            $schoolName8 = '所有学校考生';
        }
        else {
            $schoolName8 = implode('、', $schoolName8);
        }


        $section->addText('         5）   未及格组中'.$schoolName8.'在各知识范畴的得分率均等于或低于全区考生得分率；', $contentStyleFont, $contentStyleParagraph);
        
        $section->addTextBreak(2);

        $section->addText('3.2 各校不同能力层级得分率比较分析', $subSmallTitleStyleFont);
        $section->addText('表3.2 各校对比全区能力层级得分率对比表', $tableTitleStyleFont, $tableStyleParagraph);

        // Add tableSchoolNLCJ（学校能力层级）
        $tableSchoolNLCJ = $section->addTable('myTableStyle'); 

        // Add row设置行高 
        $tableSchoolNLCJ->addRow(300); 
         
        $tableSchoolNLCJ->addCell(2500)->addText('学校', $cellStyleFont, $cellStyle); 
        $tableSchoolNLCJ->addCell(2500)->addText('知识范畴', $cellStyleFont, $cellStyle); 
        $tableSchoolNLCJ->addCell(1600)->addText('全区', $cellStyleFont, $cellStyle); 
        $tableSchoolNLCJ->addCell(1600)->addText('全体', $cellStyleFont, $cellStyle); 
        $tableSchoolNLCJ->addCell(1600)->addText('优秀', $cellBlueStyleFont, $cellStyle); 
        $tableSchoolNLCJ->addCell(1600)->addText('及格', $cellGreenStyleFont, $cellStyle); 
        $tableSchoolNLCJ->addCell(1600)->addText('未及格', $cellRedStyleFont, $cellStyle); 

        $typeSchoolTotalRate = array();
        $typeSchoolExcellentRate = array();
        $typeSchoolPassRate = array();
        $typeSchoolFailRate = array();

        $typeSchoolRecord = array();

        foreach ($studentData['averageScore']['schoolName'] as $name) {
            for ($i = 0; $i < count($studentData['detailTable']['typeName']); $i++) {
                if($i != count($studentData['detailTable']['typeName']) - 1) {
                    $tableSchoolNLCJ->addRow(300); 
                    
                    $tableSchoolNLCJ->addCell(2500)->addText($name, $cellStyleFont, $cellStyle); 
                    $tableSchoolNLCJ->addCell(2500)->addText($studentData['detailTable']['typeName'][$i], $cellStyleFont, $cellStyle); 

                    $tableSchoolNLCJ->addCell(1600)->addText($studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['total']['totalRate'], $cellStyleFont, $cellStyle); 
                    $tableSchoolNLCJ->addCell(1600)->addText($studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['schoolScore'][$name]['totalRate'], $cellStyleFont, $cellStyle); 
                    $tableSchoolNLCJ->addCell(1600)->addText($studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['schoolScore'][$name]['excellentRate'], $cellBlueStyleFont, $cellStyle); 
                    $tableSchoolNLCJ->addCell(1600)->addText($studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['schoolScore'][$name]['passRate'], $cellGreenStyleFont, $cellStyle); 
                    $tableSchoolNLCJ->addCell(1600)->addText($studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['schoolScore'][$name]['failRate'], $cellRedStyleFont, $cellStyle);
                }
                else {
                    $tableSchoolNLCJ->addRow(300); 
                    
                    $tableSchoolNLCJ->addCell(2500, $cellStyleLast)->addText($name, $cellStyleFont, $cellStyle); 
                    $tableSchoolNLCJ->addCell(2500, $cellStyleLast)->addText($studentData['detailTable']['examName'][$i], $cellStyleFont, $cellStyle); 

                    $tableSchoolNLCJ->addCell(1600, $cellStyleLast)->addText($studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['total']['totalRate'], $cellStyleFont, $cellStyle); 
                    $tableSchoolNLCJ->addCell(1600, $cellStyleLast)->addText($studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['schoolScore'][$name]['totalRate'], $cellStyleFont, $cellStyle); 
                    $tableSchoolNLCJ->addCell(1600, $cellStyleLast)->addText($studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['schoolScore'][$name]['excellentRate'], $cellBlueStyleFont, $cellStyle); 
                    $tableSchoolNLCJ->addCell(1600, $cellStyleLast)->addText($studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['schoolScore'][$name]['passRate'], $cellGreenStyleFont, $cellStyle); 
                    $tableSchoolNLCJ->addCell(1600, $cellStyleLast)->addText($studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['schoolScore'][$name]['failRate'], $cellRedStyleFont, $cellStyle);
                }

                $typeSchoolTotalRate[$studentData['detailTable']['typeName'][$i]] = $studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['total']['totalRate'];
                
                if($studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['schoolScore'][$name]['totalRate'] >= $studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['total']['totalRate']) {
                    $typeSchoolRecord[$name][] = 1;
                }
                else {
                    $typeSchoolRecord[$name][] = 0;
                }
            }
        }

        arsort($typeSchoolTotalRate);

        $schoolName1 = array();
        $schoolName2 = array();

        foreach ($typeSchoolRecord as $key => $value) {
            
            for ($i = 0; $i < count($value); $i++) { 
                if($value[$i] == 0) {
                    break;
                }
                else {
                    if($i == count($value) - 1 && $value[$i] == 1) {
                        $schoolName1[] = $key;
                    }
                }
            }
            
            for ($i = 0; $i < count($value); $i++) { 
                if($value[$i] == 1) {
                    break;
                }
                else {
                    if($i == count($value) - 1 && $value[$i] == 0) {
                        $schoolName2[] = $key;
                    }
                }
            }
        }

        $schoolName1 = implode('、', $schoolName1);
        if(!empty($schoolName2)) {
            $schoolName2 = '其他学校';
        }
        else {
            $schoolName2 = '无';
        }

        $section->addText('     表3.2的数据表明：', $contentStyleFont, $contentStyleParagraph);
        $section->addText('     不同学校全体及不同水平组考生各能力层级作答表现存在差异，具体如下：', $contentStyleFont, $contentStyleParagraph);
        $section->addText('         1）   不同学校全体及不同水平组考生在'.array_keys($typeSchoolTotalRate)[0].'能力层级的得分率相对较高为'.$typeSchoolTotalRate[array_keys($typeSchoolTotalRate)[0]].'%；在'.array_keys($typeSchoolTotalRate)[count($typeSchoolTotalRate)-2].'、'.array_keys($typeSchoolTotalRate)[count($typeSchoolTotalRate)-1].'作答得分率相对较低分别为'.$typeSchoolTotalRate[array_keys($typeSchoolTotalRate)[count($typeSchoolTotalRate)-2]].'%和'.$typeSchoolTotalRate[array_keys($typeSchoolTotalRate)[count($typeSchoolTotalRate)-1]].'%。', $contentStyleFont, $contentStyleParagraph);
        $section->addText('         2）   比较不同学校全体组考生各能力层级的作答表现可知，'.$schoolName1.'考生各知识范畴的得分率均高于或等于全区考生得分率；'.$schoolName1.'中的各能力层级的得分率均等于或低于全区考生', $contentStyleFont, $contentStyleParagraph);

        $typeSchoolExcellentRecord = array();
        $typeSchoolExcellentRecord1 = array();
        foreach ($studentData['averageScore']['schoolName'] as $name) {
            for ($i = 0; $i < count($studentData['detailTable']['typeName']); $i++) {
                if($studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['schoolScore'][$name]['excellentRate'] > $studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['total']['totalRate']) {
                    $typeSchoolExcellentRecord[$name][] = 1;
                }
                else {
                    $typeSchoolExcellentRecord[$name][] = 0;
                }

                if($studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['schoolScore'][$name]['excellentRate'] >= $studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['total']['totalRate']) {
                    $typeSchoolExcellentRecord1[$name][] = 1;
                }
                else {
                    $typeSchoolExcellentRecord1[$name][] = 0;
                }
            }
        }

        $schoolName3 = array();
        foreach ($typeSchoolExcellentRecord as $key => $value) {
            
            for ($i = 0; $i < count($value); $i++) { 
                if($value[$i] == 0) {
                    break;
                }
                else {
                    if($i == count($value) - 1 && $value[$i] == 1) {
                        $schoolName3[] = $key;
                    }
                }
            }
        }

        if(count($schoolName3) == count($studentData['averageScore']['schoolName'])) {
            $schoolName3 = '所有学校考生';
        }
        else {
            $schoolName3 = implode('、', $schoolName3);
        }

        $section->addText('         3）   比较同水平组不同学校考生各能力层级的作答表现， 优秀组'.$schoolName3.'考生在各能力层级均高于或等于全区得分率；', $contentStyleFont, $contentStyleParagraph);

        $typeSchoolPassRecord = array();
        foreach ($studentData['averageScore']['schoolName'] as $name) {
            for ($i = 0; $i < count($studentData['detailTable']['typeName']); $i++) {
                if($studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['schoolScore'][$name]['passRate'] >= $studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['total']['totalRate']) {
                    $typeSchoolPassRecord[$name][] = 1;
                }
                else {
                    $typeSchoolPassRecord[$name][] = 0;
                }
            }
        }


        $schoolName6 = array();
        $schoolName7 = array();
        foreach ($typeSchoolPassRecord as $key => $value) {
            
            for ($i = 0; $i < count($value); $i++) { 
                if($value[$i] == 0) {
                    break;
                }
                else {
                    if($i == count($value) - 1 && $value[$i] == 1) {
                        $schoolName6[] = $key;
                    }
                }
            }
            
            for ($i = 0; $i < count($value); $i++) { 
                if($value[$i] == 0) {
                    break;
                }
                else {
                    if($i == count($value) - 1 && $value[$i] == 1) {
                        $schoolName7[] = $key;
                    }
                }
            }
        }

        $schoolName6 = implode('、', $schoolName6);
        $schoolName7 = implode('、', $schoolName7);

        $section->addText('         4）   及格组中'.$schoolName6.'考生在各能力范畴的得分率均高于或等于全区考生得分率； '.$schoolName7.'在各能力层级的得分率均等于或低于全区考生得分率；', $contentStyleFont, $contentStyleParagraph);

        $examSchoolFailRecord = array();
        foreach ($studentData['averageScore']['schoolName'] as $name) {
            for ($i = 0; $i < count($studentData['detailTable']['typeName']); $i++) {
                if($studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['schoolScore'][$name]['failRate'] != 0 && $studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['schoolScore'][$name]['failRate'] < $studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['total']['totalRate']) {
                    $typeSchoolFailRecord[$name][] = 1;
                }
                elseif($studentData['scoreStatisticsRate']['type'][$studentData['detailTable']['typeName'][$i]]['schoolScore'][$name]['failRate'] == 0) {
                    $typeSchoolFailRecord[$name][] = 0.5;
                }
                else {
                    $typeSchoolFailRecord[$name][] = 0;
                }
            }
        }

        $schoolName8 = array();
        foreach ($typeSchoolFailRecord as $key => $value) {
            
            for ($i = 0; $i < count($value); $i++) { 
                if($value[$i] == 0 && $value[$i] != 0.5 ) {
                    break;
                }
                else {
                    if($i == count($value) - 1 && ($value[$i] == 1 || $value[$i] == 0.5)) {
                        $schoolName8[] = $key;
                    }
                }
            }
        }

        if(count($schoolName8) == count($studentData['averageScore']['schoolName'])) {
            $schoolName8 = '所有学校';
        }
        else {
            $schoolName8 = implode('、', $schoolName8);
        }

        $section->addText('         5）   未及格组中'.$schoolName8.'考生在各知识范畴的得分率均等于或低于全区考生得分率；', $contentStyleFont, $contentStyleParagraph);
        
        $section->addTextBreak(2);


        $section->addText('4.全体考生客观题水平分析', $subTitleStyleFont, $subTitleStyleParagraph);
        $section->addText('表4.1 全区考生语文科目客观题分析表', $tableTitleStyleFont, $tableStyleParagraph);

        // Add tableKGT（客观题水平分析）
        $tableKGT = $section->addTable('myTableStyle'); 

        // Add row设置行高 
        $tableKGT->addRow(300); 
         
        $tableKGT->addCell(1600)->addText('题号', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('答案', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('人数', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('平均分', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('标准差', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('得分率', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('难度', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('区分度', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('选A率%', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('选B率%', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('选C率%', $cellStyleFont, $cellStyle); 
        $tableKGT->addCell(1600)->addText('选D率%', $cellStyleFont, $cellStyle); 

        $kgtRate = array();

        for ($i = 0; $i < count($studentData['choiceQuestionsAnalysis']); $i++) { 
            $tableKGT->addRow(300);
            foreach ($studentData['choiceQuestionsAnalysis'][$i] as $key => $name) { 
                if($key != '难度评价标准' && $key != '区分度评价标准') {
                    $tableKGT->addCell(1200)->addText($name, $cellStyleFont, $cellStyle);
                }
                if($key == '选A率%' || $key == '选B率%' || $key == '选C率%' || $key == '选D率%') {
                    $kgtRate[$i][] = $name;
                }
            }
            arsort($kgtRate[$i]);
        }

        $section->addText('     注：区分度评价标准:>0.4区分度较高;0.3~0.39 区分度中等;0.2~0.29 区分度一般;<0.2区分度较低', $tableTitleStyleFont, $tableStyleParagraph);
        $section->addText('         难度评价标准:  >0.9容易;0.7~0.9较易;0.4~0.7中等;<0.4偏难;', $tableTitleStyleFont, $tableStyleParagraph);

        $section->addText('表2.0的数据表明', $contentStyleFont);

        for ($i = 0; $i < count($studentData['choiceQuestionsAnalysis']); $i++) {
            if($kgtRate[$i][array_keys($kgtRate[$i])[1]] > 17) {
                if(array_keys($kgtRate[$i])[1] == 0) {
                    $item = 'A';
                }
                elseif(array_keys($kgtRate[$i])[1] == 1) {
                    $item = 'B';
                }
                elseif(array_keys($kgtRate[$i])[1] == 2) {
                    $item = 'C';
                }
                elseif(array_keys($kgtRate[$i])[1] == 3) {
                    $item = 'D';
                }
                $txt = '，选项'.$item.'的干扰性最强达到了'.$kgtRate[$i][array_keys($kgtRate[$i])[1]].'%';
            }
            else{
                $txt = '';
            }
            if($i / 2 == 0){
                $section->addText('     '.($i+1).'）'.$studentData['choiceQuestionsAnalysis'][$i]['题号'].'：该题难度'.$studentData['choiceQuestionsAnalysis'][$i]['难度评价标准'].'('.$studentData['choiceQuestionsAnalysis'][$i]['难度'].')，'.$studentData['choiceQuestionsAnalysis'][$i]['区分度评价标准'].'('.$studentData['choiceQuestionsAnalysis'][$i]['区分度'].')，有'.$studentData['choiceQuestionsAnalysis'][$i]['选'.$studentData['choiceQuestionsAnalysis'][$i]['答案'].'率%'].'%的学生选择了正确答案('.$studentData['choiceQuestionsAnalysis'][$i]['答案'].')，得分率>'.$studentData['choiceQuestionsAnalysis'][$i]['得分率'].'%'.$txt, $contentStyleFont);
            }
            else {
                $section->addText('     '.($i+1).'）'.$studentData['choiceQuestionsAnalysis'][$i]['题号'].'：该题从难度上讲属于'.$studentData['choiceQuestionsAnalysis'][$i]['难度评价标准'].'('.$studentData['choiceQuestionsAnalysis'][$i]['难度'].')，'.$studentData['choiceQuestionsAnalysis'][$i]['区分度评价标准'].'('.$studentData['choiceQuestionsAnalysis'][$i]['区分度'].')，正确答案('.$studentData['choiceQuestionsAnalysis'][$i]['答案'].')的选择率达到了'.$studentData['choiceQuestionsAnalysis'][$i]['选'.$studentData['choiceQuestionsAnalysis'][$i]['答案'].'率%'].'%，其余选项的选择率都低于'.$kgtRate[$i][array_keys($kgtRate[$i])[1]].'%'.$txt, $contentStyleFont);
            }
        }*/
        
        /*$section = \PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
        $section->save($wordSaveDir.iconv("utf-8", "gb2312", $course).'.docx');*/

    }

}

?>