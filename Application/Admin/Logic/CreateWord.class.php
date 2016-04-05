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


        $PHPWord->setDefaultFontName('微软雅黑');
        // $PHPWord->setDefaultFontName('楷体_GB2312');
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

        $tableTitleStyleFont = array('size'=>9);
        $tableStyleParagraph = array('align'=>'center');

        $tableCommentStyleFont = array('size'=>9);

        $choiceQuestionsContentStyleFont = array('size'=>12);
        $choiceQuestionsContentStyleParagraph = array('spacing'=>80);

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
        $section->addText('         本报告的评价对象为实际参加该次'.self::$course.'学科的考生。以下将根据考生在不同知识范畴、能力层级的作答表现，分析不同区域学校以及不同水平考生的水平。', $contentStyleFont, $contentStyleParagraph);

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

        $section->addText('3.各校得分率分析比较', $subTitleStyleFont, $subTitleStyleParagraph);
        $section->addText('3.1 各校知识范畴得分率比较分析', $subSmallTitleStyleFont);
        $section->addText('表3.1 各校对比全区知识范畴得分率对比表', $tableTitleStyleFont, $tableStyleParagraph);

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

        $schoolNameNumber = floor(count(self::$detailTableData['examScopeName']) / 2);

        foreach (self::$schoolInfoData['schoolList'] as $schoolName) {
            foreach (self::$detailTableData['examScopeName'] as $key => $examScopeName) {
                if($key != count(self::$detailTableData['examScopeName']) - 1) {
                    $cellStyleValue = '';
                } else {
                    $cellStyleValue = $cellStyleLast;
                }
                $tableSchoolZSFC->addRow(300);
                
                if($schoolNameNumber == $key) {
                    $tableSchoolZSFC->addCell(2500, $cellStyleValue)->addText($schoolName, $cellStyleFont, $cellStyle); 
                } else {
                    $tableSchoolZSFC->addCell(2500, $cellStyleValue)->addText('', $cellStyleFont, $cellStyle); 
                }

                $tableSchoolZSFC->addCell(2500, $cellStyleValue)->addText($examScopeName, $cellStyleFont, $cellStyle); 

                $tableSchoolZSFC->addCell(1600, $cellStyleValue)->addText(self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate'], $cellStyleFont, $cellStyle);

                $totalRateRecord[$examScopeName] = self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']; // 统计全区得分率情况

                if(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['totalRate'] == 0 || self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['totalRate'] == self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellStyleFont;
                } elseif(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['totalRate'] > self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellRedStyleFont;
                    $schoolTotalRateRecord[$schoolName] = $schoolTotalRateRecord[$schoolName] + 1; // 统计全校得分率记录
                } elseif(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['totalRate'] < self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellGreenStyleFont;
                }

                if(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['totalScore'] >= 0.5) {
                    $dValueTxt = '(+++)';
                    $schoolTotalDValueRecord[$schoolName][$examScopeName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['totalScore'] >= 0.2 && self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['totalScore'] < 0.5) {
                    $dValueTxt = '(+)';
                    $schoolTotalDValueRecord[$schoolName][$examScopeName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['totalScore'] <= -0.5) {
                    $dValueTxt = '(---)';
                    $schoolTotalDValueRecord[$schoolName][$examScopeName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['totalScore'] <= -0.2 && self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['totalScore'] > -0.5) {
                    $dValueTxt = '(-)';
                    $schoolTotalDValueRecord[$schoolName][$examScopeName] = 1; // 统计有D值的记录
                } else {
                    $dValueTxt = '';
                    $schoolTotalDValueRecord[$schoolName][$examScopeName] = 0; // 统计有D值的记录
                }
                $tableSchoolZSFC->addCell(1600, $cellStyleValue)->addText(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['totalRate'].$dValueTxt, $cellStyleFontColor, $cellStyle);

                if(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['excellentRate'] == 0 || self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['excellentRate'] == self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellStyleFont;
                } elseif(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['excellentRate'] > self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellRedStyleFont;
                    $schoolExcellentRateRecord[$schoolName] = $schoolExcellentRateRecord[$schoolName] + 1; // 统计全校得分率记录
                } elseif(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['excellentRate'] < self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellGreenStyleFont;
                }
                
                if(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['excellentScore'] >= 0.5) {
                    $dValueTxt = '(+++)';
                    $schoolExcellentDValueRecord[$schoolName][$examScopeName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['excellentScore'] >= 0.2 && self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['excellentScore'] < 0.5) {
                    $dValueTxt = '(+)';
                    $schoolExcellentDValueRecord[$schoolName][$examScopeName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['excellentScore'] <= -0.5) {
                    $dValueTxt = '(---)';
                    $schoolExcellentDValueRecord[$schoolName][$examScopeName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['excellentScore'] <= -0.2 && self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['excellentScore'] > -0.5) {
                    $dValueTxt = '(-)';
                    $schoolTotalDValueRecord[$schoolName][$examScopeName] = 1; // 统计有D值的记录
                } else {
                    $dValueTxt = '';
                    $schoolExcellentDValueRecord[$schoolName][$examScopeName] = 0; // 统计有D值的记录
                }
                $tableSchoolZSFC->addCell(1600, $cellStyleValue)->addText(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['excellentRate'].$dValueTxt, $cellStyleFontColor, $cellStyle); 

                if(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['passRate'] == 0 || self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['passRate'] == self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellStyleFont;
                } elseif(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['passRate'] > self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellRedStyleFont;
                    $schoolPassRateRecord[$schoolName] = $schoolPassRateRecord[$schoolName] + 1; // 统计全校得分率记录
                } elseif(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['passRate'] < self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellGreenStyleFont;
                }
                
                if(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['passScore'] >= 0.5) {
                    $dValueTxt = '(+++)';
                    $schoolPassDValueRecord[$schoolName][$examScopeName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['passScore'] >= 0.2 && self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['passScore'] < 0.5) {
                    $dValueTxt = '(+)';
                    $schoolPassDValueRecord[$schoolName][$examScopeName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['passScore'] <= -0.5) {
                    $dValueTxt = '(---)';
                    $schoolPassDValueRecord[$schoolName][$examScopeName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['passScore'] <= -0.2 && self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['passScore'] > -0.5) {
                    $dValueTxt = '(-)';
                    $schoolPassDValueRecord[$schoolName][$examScopeName] = 1; // 统计有D值的记录
                } else {
                    $dValueTxt = '';
                    $schoolPassDValueRecord[$schoolName][$examScopeName] = 0; // 统计有D值的记录
                }

                $tableSchoolZSFC->addCell(1600, $cellStyleValue)->addText(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['passRate'].$dValueTxt, $cellStyleFontColor, $cellStyle); 

                if(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['failRate'] == 0 || self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['failRate'] == self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellStyleFont;
                } elseif(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['failRate'] > self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellRedStyleFont;
                    $schoolFailRateRecord[$schoolName] = $schoolFailRateRecord[$schoolName] + 1; // 统计全校得分率记录
                } elseif(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['failRate'] < self::$scoreStatisticsData['examScopeTotalRate'][$examScopeName]['totalRate']) {
                    $cellStyleFontColor = $cellGreenStyleFont;
                }
                
                if(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['failScore'] >= 0.5) {
                    $dValueTxt = '(+++)';
                    $schoolFailDValueRecord[$schoolName][$examScopeName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['failScore'] >= 0.2 && self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['failScore'] < 0.5) {
                    $dValueTxt = '(+)';
                    $schoolFailDValueRecord[$schoolName][$examScopeName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['failScore'] <= -0.5) {
                    $dValueTxt = '(---)';
                    $schoolFailDValueRecord[$schoolName][$examScopeName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['failScore'] <= -0.2 && self::$dValueData['examScopeTotalDValue'][$examScopeName][$schoolName]['failScore'] > -0.5) {
                    $dValueTxt = '(-)';
                    $schoolFailDValueRecord[$schoolName][$examScopeName] = 1; // 统计有D值的记录
                } else {
                    $dValueTxt = '';
                    $schoolFailDValueRecord[$schoolName][$examScopeName] = 0; // 统计有D值的记录
                }
                $tableSchoolZSFC->addCell(1600, $cellStyleValue)->addText(self::$scoreStatisticsData['examScopeSchoolRate'][$examScopeName][$schoolName]['failRate'].$dValueTxt, $cellStyleFontColor, $cellStyle);
            }
        }

        $section->addTextBreak();

        $section->addText('     表3.1的数据表明：', $contentStyleFont, $contentStyleParagraph);
        $section->addText('     不同学校全体及不同水平组考生各知识范畴作答表现存在差异，具体如下：', $contentStyleFont, $contentStyleParagraph);

        arsort($totalRateRecord);

        $section->addText('         1）   不同学校全体及不同水平组考生在'.array_keys($totalRateRecord)[0].'知识范畴作答表现好，得分率为'.(array_values($totalRateRecord)[0] * 100).'%；在'.array_keys($totalRateRecord)[count($totalRateRecord)-1].'表现相对较低为'.(array_values($totalRateRecord)[count($totalRateRecord)-1] * 100).'%。', $contentStyleFont, $contentStyleParagraph);

        foreach (self::$schoolInfoData['schoolList'] as $schoolName) { 
            arsort($schoolTotalDValueRecord[$schoolName]);
            $num = 0;
            // $schoolD1Txt[$schoolName] = $schoolD1Txt[$schoolName].'的';
            foreach ($schoolTotalDValueRecord[$schoolName] as $key => $value) {
                if($value == 1 && $num == 0) {
                    if(array_values($schoolTotalDValueRecord[$schoolName])[1] == 1) {
                        $schoolD1Txt[$schoolName] = $schoolD1Txt[$schoolName].$key.'、';
                    } else {
                        $schoolD1Txt[$schoolName] = $schoolD1Txt[$schoolName].$key;
                    }
                } elseif($value == 1 && $num == 1) {
                    if(array_values($schoolTotalDValueRecord[$schoolName])[2] == 1) {
                        $schoolD1Txt[$schoolName] = $schoolD1Txt[$schoolName].$key.'、';
                    } else {
                        $schoolD1Txt[$schoolName] = $schoolD1Txt[$schoolName].$key;
                    }
                } elseif($value == 1 && $num == 2) {
                    if(array_values($schoolTotalDValueRecord[$schoolName])[3] == 1) {
                        $schoolD1Txt[$schoolName] = $schoolD1Txt[$schoolName].$key.'等';
                    } else {
                        $schoolD1Txt[$schoolName] = $schoolD1Txt[$schoolName].$key;
                    }
                }
                $num++;
            }
        }

        foreach (self::$schoolInfoData['schoolList'] as $schoolName) {
            if($schoolTotalRateRecord[$schoolName] == count(self::$detailTableData['examScopeName']) && array_values($schoolTotalDValueRecord[$schoolName])[0] > 0) {
                $schoolName11[] = $schoolName;
            } elseif($schoolTotalRateRecord[$schoolName] < count(self::$detailTableData['examScopeName']) && array_values($schoolTotalDValueRecord[$schoolName])[0] > 0) {
                $schoolName12[] = $schoolName;
            } else {
                $schoolName13[] = $schoolName;
            }
        }

        if(count($schoolName11) > 1) {
            foreach ($schoolName11 as $key => $name) {
                if($key < count($schoolName11) - 1) {
                    $schoolName11List = $schoolName11List.$name.'、';
                } else {
                    $schoolName11List = $schoolName11List.$name;
                }
            }
            $examScopeSecondTxt = $examScopeSecondTxt.$schoolName11List.'考生各知识范畴的得分率均高于全区考生得分率，经效果量检验，';
            foreach ($schoolName11 as $key => $name) {
                if($key < count($schoolName11) - 1) {
                    $examScopeSecondTxt = $examScopeSecondTxt.$name.$schoolD1Txt[$name].'；';
                } else {
                    $examScopeSecondTxt = $examScopeSecondTxt.$name.$schoolD1Txt[$name];
                }
                
            }
            $examScopeSecondTxt = $examScopeSecondTxt.'具有实际意义上的差异显著性。';
        } elseif(count($schoolName11) == 1) {
            $examScopeSecondTxt = $examScopeSecondTxt.$schoolName11[0].'考生各知识范畴的得分率均高于全区考生得分率，经效果量检验，';
            $examScopeSecondTxt = $examScopeSecondTxt.'该校'.$schoolD1Txt[$schoolName11[0]].'具有实际意义上的差异显著性。';
        }

        if(count($schoolName12) > 1) {
            foreach ($schoolName12 as $key => $name) {
                if($key < count($schoolName12) - 1) {
                    $schoolName12List = $schoolName12List.$name.'中的'.$schoolD1Txt[$name].'；';
                } else {
                    $schoolName12List = $schoolName12List.$name.'中的'.$schoolD1Txt[$name].'，';
                }
            }
            $examScopeSecondTxt = $examScopeSecondTxt.$schoolName12List.'经效果量检验，具有实际意义上的差异显著性。';
        } elseif(count($schoolName12) == 1) {
            $schoolName12List = $schoolName12List.$schoolName12[0].'中的'.$schoolD1Txt[$schoolName12[0]];
            $examScopeSecondTxt = $examScopeSecondTxt.$schoolName2List.'经效果量检验，具有实际意义上的差异显著性。';
        }

        if(count($schoolName11) == 0 && count($schoolName12) == 0 && count($schoolName13) > 0) {
            $examScopeSecondTxt = $examScopeSecondTxt.'所有学校全体考生的各知识范畴经效果量检验，均不具有实际意义上的差异显著性。';
        } elseif(count($schoolName13) > 0) {
            $examScopeSecondTxt = $examScopeSecondTxt.'其他学校全体考生的各知识范畴经效果量检验，均不具有实际意义上的差异显著性。';
        }

        $section->addText('         2）   比较不同学校全体组考生各知识范畴的作答表现可知，'.$examScopeSecondTxt, $contentStyleFont, $contentStyleParagraph);


        foreach (self::$schoolInfoData['schoolList'] as $schoolName) { 
            arsort($schoolExcellentDValueRecord[$schoolName]);
            $num = 0;
            // $schoolD2Txt[$schoolName] = $schoolD2Txt[$schoolName].'的';
            foreach ($schoolExcellentDValueRecord[$schoolName] as $key => $value) {
                if($value == 1 && $num == 0) {
                    if(array_values($schoolExcellentDValueRecord[$schoolName])[1] == 1) {
                        $schoolD2Txt[$schoolName] = $schoolD2Txt[$schoolName].$key.'、';
                    } else {
                        $schoolD2Txt[$schoolName] = $schoolD2Txt[$schoolName].$key;
                    }
                } elseif($value == 1 && $num == 1) {
                    if(array_values($schoolExcellentDValueRecord[$schoolName])[2] == 1) {
                        $schoolD2Txt[$schoolName] = $schoolD2Txt[$schoolName].$key.'、';
                    } else {
                        $schoolD2Txt[$schoolName] = $schoolD2Txt[$schoolName].$key;
                    }
                } elseif($value == 1 && $num == 2) {
                    if(array_values($schoolTotalDValueRecord[$schoolName])[3] == 1) {
                        $schoolD2Txt[$schoolName] = $schoolD2Txt[$schoolName].$key.'等';
                    } else {
                        $schoolD2Txt[$schoolName] = $schoolD2Txt[$schoolName].$key;
                    }
                }
                $num++;
            }
        }

        foreach (self::$schoolInfoData['schoolList'] as $schoolName) { 
            foreach (self::$detailTableData['examScopeName'] as $key => $examScopeName) {
                if($schoolExcellentDValueRecord[$schoolName][$examScopeName] == 1) {
                    $schoolExcellentDValueCount[$schoolName] = $schoolExcellentDValueCount[$schoolName] + 1;
                }
            }
        }

        foreach (self::$schoolInfoData['schoolList'] as $schoolName) {
            if($schoolExcellentRateRecord[$schoolName] == count(self::$detailTableData['examScopeName']) && $schoolExcellentDValueCount[$schoolName] == count(self::$detailTableData['examScopeName'])) {
                $schoolName21[] = $schoolName;
            } elseif($schoolExcellentRateRecord[$schoolName] == count(self::$detailTableData['examScopeName']) && $schoolExcellentDValueCount[$schoolName] > 0) {
                $schoolName22[] = $schoolName;
            } else {
                $schoolName23[] = $schoolName;
            }
        }

        if(count($schoolName21) > 1) {
            foreach ($schoolName21 as $key => $name) {
                if($key < count($schoolName21) - 1) {
                    $schoolName21List = $schoolName21List.$name.'、';
                } else {
                    $schoolName21List = $schoolName21List.$name;
                }
            }
            $examScopeThirdTxt = $examScopeThirdTxt.$schoolName21List.'考生各知识范畴的得分率均高于全区考生得分率，经效果量检验，';
            $examScopeThirdTxt = $examScopeThirdTxt.'均具有实际意义上的显著性。';
        } elseif(count($schoolName21) == 1) {
            $examScopeThirdTxt = $examScopeThirdTxt.$schoolName21[0].'考生各知识范畴的得分率均高于全区考生得分率，经效果量检验，';
            $examScopeThirdTxt = $examScopeThirdTxt.'该校均具有实际意义上的显著性。';
        }

        if(count($schoolName22) > 1) {
            foreach ($schoolName22 as $key => $name) {
                if($key < count($schoolName22) - 1) {
                    $schoolName22List = $schoolName22List.$name.'中的'.$schoolD2Txt[$name].'；';
                } else {
                    $schoolName22List = $schoolName22List.$name.'中的'.$schoolD2Txt[$name].'，';
                }
            }
            $examScopeThirdTxt = $examScopeThirdTxt.$schoolName22List.'经效果量检验，具有实际意义上的差异显著性。';
        } elseif(count($schoolName22) == 1) {
            $schoolName22List = $schoolName22List.$schoolName22[0].'中的'.$schoolD2Txt[$schoolName22[0]];
            $examScopeThirdTxt = $examScopeThirdTxt.$schoolName22List.'经效果量检验，具有实际意义上的差异显著性。';
        }

        if(count($schoolName21) == 0 && count($schoolName22) == 0 && count($schoolName23) > 0) {
            $examScopeThirdTxt = $examScopeThirdTxt.'所有学校全体考生的各知识范畴经效果量检验，均不具有实际意义上的差异显著性。';
        } elseif(count($schoolName23) > 0) {
            $examScopeThirdTxt = $examScopeThirdTxt.'其他学校全体考生的各知识范畴经效果量检验，均不具有实际意义上的差异显著性。';
        }

        $section->addText('         3）   比较不同学校优秀组考生各知识范畴的作答表现可知，'.$examScopeThirdTxt, $contentStyleFont, $contentStyleParagraph);


        foreach (self::$schoolInfoData['schoolList'] as $schoolName) { 
            arsort($schoolPassDValueRecord[$schoolName]);
            $num = 0;
            // $schoolD3Txt[$schoolName] = $schoolD3Txt[$schoolName].'的';
            foreach ($schoolPassDValueRecord[$schoolName] as $key => $value) {
                if($value == 1 && $num == 0) {
                    if(array_values($schoolPassDValueRecord[$schoolName])[1] == 1) {
                        $schoolD3Txt[$schoolName] = $schoolD3Txt[$schoolName].$key.'、';
                    } else {
                        $schoolD3Txt[$schoolName] = $schoolD3Txt[$schoolName].$key;
                    }
                } elseif($value == 1 && $num == 1) {
                    if(array_values($schoolPassDValueRecord[$schoolName])[2] == 1) {
                        $schoolD3Txt[$schoolName] = $schoolD3Txt[$schoolName].$key.'、';
                    } else {
                        $schoolD3Txt[$schoolName] = $schoolD3Txt[$schoolName].$key;
                    }
                } elseif($value == 1 && $num == 2) {
                    if(array_values($schoolPassDValueRecord[$schoolName])[3] == 1) {
                        $schoolD3Txt[$schoolName] = $schoolD3Txt[$schoolName].$key.'等';
                    } else {
                        $schoolD3Txt[$schoolName] = $schoolD3Txt[$schoolName].$key;
                    }
                }
                $num++;
            }
        }

        foreach (self::$schoolInfoData['schoolList'] as $schoolName) {
            if($schoolPassRateRecord[$schoolName] == count(self::$detailTableData['examScopeName']) && array_values($schoolPassDValueRecord[$schoolName])[0] > 0) {
                $schoolName31[] = $schoolName;
            } elseif($schoolPassRateRecord[$schoolName] < count(self::$detailTableData['examScopeName']) && array_values($schoolPassDValueRecord[$schoolName])[0] > 0) {
                $schoolName32[] = $schoolName;
            } else {
                $schoolName33[] = $schoolName;
            }
        }

        if(count($schoolName31) > 1) {
            foreach ($schoolName31 as $key => $name) {
                if($key < count($schoolName31) - 1) {
                    $schoolName31List = $schoolName31List.$name.'、';
                } else {
                    $schoolName31List = $schoolName31List.$name;
                }
            }
            $examScopeFourTxt = $examScopeFourTxt.$schoolName31List.'考生各知识范畴的得分率均高于全区考生得分率，经效果量检验，';
            foreach ($schoolName31 as $key => $name) {
                if($key < count($schoolName31) - 1) {
                    $examScopeFourTxt = $examScopeFourTxt.$name.$schoolD3Txt[$name].'；';
                } else {
                    $examScopeFourTxt = $examScopeFourTxt.$name.$schoolD3Txt[$name];
                }
                
            }
            $examScopeFourTxt = $examScopeFourTxt.'具有实际意义上的差异显著性。';
        } elseif(count($schoolName31) == 1) {
            $examScopeFourTxt = $examScopeFourTxt.$schoolName31[0].'考生各知识范畴的得分率均高于全区考生得分率，经效果量检验，';
            $examScopeFourTxt = $examScopeFourTxt.'该校'.$schoolD3Txt[$schoolName31[0]].'具有实际意义上的差异显著性。';
        }

        if(count($schoolName32) > 1) {
            foreach ($schoolName32 as $key => $name) {
                if($key < count($schoolName32) - 1) {
                    $schoolName32List = $schoolName32List.$name.'中的'.$schoolD3Txt[$name].'；';
                } else {
                    $schoolName32List = $schoolName32List.$name.'中的'.$schoolD3Txt[$name].'，';
                }
            }
            $examScopeFourTxt = $examScopeFourTxt.$schoolName32List.'经效果量检验，具有实际意义上的差异显著性。';
        } elseif(count($schoolName32) == 1) {
            $schoolName32List = $schoolName32List.$schoolName32[0].'中的'.$schoolD3Txt[$schoolName32[0]];
            $examScopeFourTxt = $examScopeFourTxt.$schoolName32List.'经效果量检验，具有实际意义上的差异显著性。';
        }

        if(count($schoolName31) == 0 && count($schoolName32) == 0 && count($schoolName33) > 0) {
            $examScopeFourTxt = $examScopeFourTxt.'所有学校全体考生的各知识范畴经效果量检验，均不具有实际意义上的差异显著性。';
        } elseif(count($schoolName33) > 0) {
            $examScopeFourTxt = $examScopeFourTxt.'其他学校全体考生的各知识范畴经效果量检验，均不具有实际意义上的差异显著性。';
        }

        $section->addText('         4）   及格组中'.$examScopeFourTxt, $contentStyleFont, $contentStyleParagraph);

        foreach (self::$schoolInfoData['schoolList'] as $schoolName) { 
            arsort($schoolFailDValueRecord[$schoolName]);
            $num = 0;
            // $schoolD4Txt[$schoolName] = $schoolD4Txt[$schoolName].'的';
            foreach ($schoolFailDValueRecord[$schoolName] as $key => $value) {
                if($value == 1 && $num == 0) {
                    if(array_values($schoolFailDValueRecord[$schoolName])[1] == 1) {
                        $schoolD4Txt[$schoolName] = $schoolD4Txt[$schoolName].$key.'、';
                    } else {
                        $schoolD4Txt[$schoolName] = $schoolD4Txt[$schoolName].$key;
                    }
                } elseif($value == 1 && $num == 1) {
                    if(array_values($schoolFailDValueRecord[$schoolName])[2] == 1) {
                        $schoolD4Txt[$schoolName] = $schoolD4Txt[$schoolName].$key.'、';
                    } else {
                        $schoolD4Txt[$schoolName] = $schoolD4Txt[$schoolName].$key;
                    }
                } elseif($value == 1 && $num == 2) {
                    if(array_values($schoolFailDValueRecord[$schoolName])[3] == 1) {
                        $schoolD4Txt[$schoolName] = $schoolD4Txt[$schoolName].$key.'等';
                    } else {
                        $schoolD4Txt[$schoolName] = $schoolD4Txt[$schoolName].$key;
                    }
                }
                $num++;
            }
        }

        foreach (self::$schoolInfoData['schoolList'] as $schoolName) {
            if($schoolFailRateRecord[$schoolName] == count(self::$detailTableData['examScopeName']) && array_values($schoolFailDValueRecord[$schoolName])[0] > 0) {
                $schoolName41[] = $schoolName;
            } elseif($schoolFailRateRecord[$schoolName] < count(self::$detailTableData['examScopeName']) && array_values($schoolFailDValueRecord[$schoolName])[0] > 0) {
                $schoolName42[] = $schoolName;
            } else {
                $schoolName43[] = $schoolName;
            }
        }

        if(count($schoolName41) > 1) {
            foreach ($schoolName41 as $key => $name) {
                if($key < count($schoolName41) - 1) {
                    $schoolName41List = $schoolName41List.$name.'、';
                } else {
                    $schoolName41List = $schoolName41List.$name;
                }
            }
            $examScopeFiveTxt = $examScopeFiveTxt.$schoolName41List.'考生各知识范畴的得分率均高于全区考生得分率，经效果量检验，';
            foreach ($schoolName41 as $key => $name) {
                if($key < count($schoolName41) - 1) {
                    $examScopeFiveTxt = $examScopeFiveTxt.$name.$schoolD4Txt[$name].'；';
                } else {
                    $examScopeFiveTxt = $examScopeFiveTxt.$name.$schoolD4Txt[$name];
                }
                
            }
            $examScopeFiveTxt = $examScopeFiveTxt.'具有实际意义上的差异显著性。';
        } elseif(count($schoolName41) == 1) {
            $examScopeFiveTxt = $examScopeFiveTxt.$schoolName41[0].'考生各知识范畴的得分率均高于全区考生得分率，经效果量检验，';
            $examScopeFiveTxt = $examScopeFiveTxt.'该校'.$schoolD4Txt[$schoolName41[0]].'具有实际意义上的差异显著性。';
        }

        if(count($schoolName42) > 1) {
            foreach ($schoolName42 as $key => $name) {
                if($key < count($schoolName42) - 1) {
                    $schoolName42List = $schoolName42List.$name.'中的'.$schoolD4Txt[$name].'；';
                } else {
                    $schoolName42List = $schoolName42List.$name.'中的'.$schoolD4Txt[$name].'，';
                }
            }
            $examScopeFiveTxt = $examScopeFiveTxt.$schoolName42List.'经效果量检验，具有实际意义上的差异显著性。';
        } elseif(count($schoolName42) == 1) {
            $schoolName42List = $schoolName42List.$schoolName42[0].'中的'.$schoolD4Txt[$schoolName42[0]];
            $examScopeFiveTxt = $examScopeFiveTxt.$schoolName42List.'经效果量检验，具有实际意义上的差异显著性。';
        }

        if(count($schoolName41) == 0 && count($schoolName42) == 0 && count($schoolName43) > 0) {
            $examScopeFiveTxt = $examScopeFiveTxt.'所有学校全体考生的各知识范畴经效果量检验，均不具有实际意义上的差异显著性。';
        } elseif(count($schoolName43) > 0) {
            $examScopeFiveTxt = $examScopeFiveTxt.'其他学校全体考生的各知识范畴经效果量检验，均不具有实际意义上的差异显著性。';
        }

        $section->addText('         5）   未及格组中'.$examScopeFiveTxt, $contentStyleFont, $contentStyleParagraph);

        $section->addTextBreak(2);

        $section->addText('3.2 各校不同能力层级得分率比较分析', $subSmallTitleStyleFont);
        $section->addText('表3.2 各校对比全区能力层级得分率对比表', $tableTitleStyleFont, $tableStyleParagraph);

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

        $schoolNameNumber = floor(count(self::$detailTableData['examMoldName']) / 2);

        foreach (self::$schoolInfoData['schoolList'] as $schoolName) {
            foreach (self::$detailTableData['examMoldName'] as $key => $examMoldName) {
                if($key != count(self::$detailTableData['examMoldName']) - 1) {
                    $cellStyleValue = '';
                } else {
                    $cellStyleValue = $cellStyleLast;
                }
                $tableSchoolNLCJ->addRow(300);
                
                if($schoolNameNumber == $key) {
                    $tableSchoolNLCJ->addCell(2500, $cellStyleValue)->addText($schoolName, $cellStyleFont, $cellStyle); 
                } else {
                    $tableSchoolNLCJ->addCell(2500, $cellStyleValue)->addText('', $cellStyleFont, $cellStyle); 
                }
                
                $tableSchoolNLCJ->addCell(2500, $cellStyleValue)->addText($examMoldName, $cellStyleFont, $cellStyle); 

                $tableSchoolNLCJ->addCell(1600, $cellStyleValue)->addText(self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate'], $cellStyleFont, $cellStyle);

                $examMoldTotalRateRecord[$examMoldName] = self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']; // 统计全区得分率情况

                if(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['totalRate'] == 0 || self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['totalRate'] == self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellStyleFont;
                } elseif(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['totalRate'] > self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellRedStyleFont;
                    $schoolExamMoldTotalRateRecord[$schoolName] = $schoolExamMoldTotalRateRecord[$schoolName] + 1; // 统计全校得分率记录
                } elseif(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['totalRate'] < self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellGreenStyleFont;
                }

                if(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['totalScore'] >= 0.5) {
                    $dValueTxt = '(+++)';
                    $schoolExamMoldTotalDValueRecord[$schoolName][$examMoldName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['totalScore'] >= 0.2 && self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['totalScore'] < 0.5) {
                    $dValueTxt = '(+)';
                    $schoolExamMoldTotalDValueRecord[$schoolName][$examMoldName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['totalScore'] <= -0.5) {
                    $dValueTxt = '(---)';
                    $schoolExamMoldTotalDValueRecord[$schoolName][$examMoldName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['totalScore'] <= -0.2 && self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['totalScore'] > -0.5) {
                    $dValueTxt = '(-)';
                    $schoolExamMoldTotalDValueRecord[$schoolName][$examMoldName] = 1; // 统计有D值的记录
                } else {
                    $dValueTxt = '';
                    $schoolExamMoldTotalDValueRecord[$schoolName][$examMoldName] = 0; // 统计有D值的记录
                }
                $tableSchoolNLCJ->addCell(1600, $cellStyleValue)->addText(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['totalRate'].$dValueTxt, $cellStyleFontColor, $cellStyle);

                if(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['excellentRate'] == 0 || self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['excellentRate'] == self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellStyleFont;
                } elseif(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['excellentRate'] > self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellRedStyleFont;
                    $schoolExamMoldExcellentRateRecord[$schoolName] = $schoolExamMoldExcellentRateRecord[$schoolName] + 1; // 统计全校得分率记录
                } elseif(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['excellentRate'] < self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellGreenStyleFont;
                }
                
                if(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['excellentScore'] >= 0.5) {
                    $dValueTxt = '(+++)';
                    $schoolExamMoldExcellentDValueRecord[$schoolName][$examMoldName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['excellentScore'] >= 0.2 && self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['excellentScore'] < 0.5) {
                    $dValueTxt = '(+)';
                    $schoolExamMoldExcellentDValueRecord[$schoolName][$examMoldName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['excellentScore'] <= -0.5) {
                    $dValueTxt = '(---)';
                    $schoolExamMoldExcellentDValueRecord[$schoolName][$examMoldName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['excellentScore'] <= -0.2 && self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['excellentScore'] > -0.5) {
                    $dValueTxt = '(-)';
                    $schoolExamMoldExcellentDValueRecord[$schoolName][$examMoldName] = 1; // 统计有D值的记录
                } else {
                    $dValueTxt = '';
                    $schoolExamMoldExcellentDValueRecord[$schoolName][$examMoldName] = 0; // 统计有D值的记录
                }
                $tableSchoolNLCJ->addCell(1600, $cellStyleValue)->addText(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['excellentRate'].$dValueTxt, $cellStyleFontColor, $cellStyle); 

                if(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['passRate'] == 0 || self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['passRate'] == self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellStyleFont;
                } elseif(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['passRate'] > self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellRedStyleFont;
                    $schoolExamMoldPassRateRecord[$schoolName] = $schoolExamMoldPassRateRecord[$schoolName] + 1; // 统计全校得分率记录
                } elseif(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['passRate'] < self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellGreenStyleFont;
                }
                
                if(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['passScore'] >= 0.5) {
                    $dValueTxt = '(+++)';
                    $schoolExamMoldPassDValueRecord[$schoolName][$examMoldName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['passScore'] >= 0.2 && self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['passScore'] < 0.5) {
                    $dValueTxt = '(+)';
                    $schoolExamMoldPassDValueRecord[$schoolName][$examMoldName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['passScore'] <= -0.5) {
                    $dValueTxt = '(---)';
                    $schoolExamMoldPassDValueRecord[$schoolName][$examMoldName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['passScore'] <= -0.2 && self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['passScore'] > -0.5) {
                    $dValueTxt = '(-)';
                    $schoolExamMoldPassDValueRecord[$schoolName][$examMoldName] = 1; // 统计有D值的记录
                } else {
                    $dValueTxt = '';
                    $schoolExamMoldPassDValueRecord[$schoolName][$examMoldName] = 0; // 统计有D值的记录
                }
                $tableSchoolNLCJ->addCell(1600, $cellStyleValue)->addText(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['passRate'].$dValueTxt, $cellStyleFontColor, $cellStyle); 

                if(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['failRate'] == 0 || self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['failRate'] == self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellStyleFont;
                } elseif(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['failRate'] > self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellRedStyleFont;
                    $schoolExamMoldFailRateRecord[$schoolName] = $schoolExamMoldFailRateRecord[$schoolName] + 1; // 统计全校得分率记录
                } elseif(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['failRate'] < self::$scoreStatisticsData['examMoldTotalRate'][$examMoldName]['totalRate']) {
                    $cellStyleFontColor = $cellGreenStyleFont;
                }
                
                if(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['failScore'] >= 0.5) {
                    $dValueTxt = '(+++)';
                    $schoolExamMoldFailDValueRecord[$schoolName][$examMoldName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['failScore'] >= 0.2 && self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['failScore'] < 0.5) {
                    $dValueTxt = '(+)';
                    $schoolExamMoldFailDValueRecord[$schoolName][$examMoldName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['failScore'] <= -0.5) {
                    $dValueTxt = '(---)';
                    $schoolExamMoldFailDValueRecord[$schoolName][$examMoldName] = 1; // 统计有D值的记录
                } elseif(self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['failScore'] <= -0.2 && self::$dValueData['examMoldTotalDValue'][$examMoldName][$schoolName]['failScore'] > -0.5) {
                    $dValueTxt = '(-)';
                    $schoolExamMoldFailDValueRecord[$schoolName][$examMoldName] = 1; // 统计有D值的记录
                } else {
                    $dValueTxt = '';
                    $schoolExamMoldFailDValueRecord[$schoolName][$examMoldName] = 0; // 统计有D值的记录
                }
                $tableSchoolNLCJ->addCell(1600, $cellStyleValue)->addText(self::$scoreStatisticsData['examMoldSchoolRate'][$examMoldName][$schoolName]['failRate'].$dValueTxt, $cellStyleFontColor, $cellStyle);
            }
        }

        $section->addTextBreak();

        $section->addText('     表3.2的数据表明：', $contentStyleFont, $contentStyleParagraph);
        $section->addText('     不同学校全体及不同水平组考生各能力层级作答表现存在差异，具体如下：', $contentStyleFont, $contentStyleParagraph);

        arsort($examMoldTotalRateRecord);

        $section->addText('         1）   不同学校全体及不同水平组考生在'.array_keys($examMoldTotalRateRecord)[0].'能力层级作答表现好，得分率为'.(array_values($examMoldTotalRateRecord)[0] * 100).'%；在'.array_keys($examMoldTotalRateRecord)[count($examMoldTotalRateRecord)-1].'表现相对较低为'.(array_values($examMoldTotalRateRecord)[count($examMoldTotalRateRecord)-1] * 100).'%。', $contentStyleFont, $contentStyleParagraph);

        foreach (self::$schoolInfoData['schoolList'] as $schoolName) { 
            arsort($schoolExamMoldTotalDValueRecord[$schoolName]);
            $num = 0;
            // $schoolExamMoldD1Txt[$schoolName] = $schoolExamMoldD1Txt[$schoolName].'的';
            foreach ($schoolExamMoldTotalDValueRecord[$schoolName] as $key => $value) {
                if($value == 1 && $num == 0) {
                    if(array_values($schoolExamMoldTotalDValueRecord[$schoolName])[1] == 1) {
                        $schoolExamMoldD1Txt[$schoolName] = $schoolExamMoldD1Txt[$schoolName].$key.'、';
                    } else {
                        $schoolExamMoldD1Txt[$schoolName] = $schoolExamMoldD1Txt[$schoolName].$key;
                    }
                } elseif($value == 1 && $num == 1) {
                    if(array_values($schoolExamMoldTotalDValueRecord[$schoolName])[2] == 1) {
                        $schoolExamMoldD1Txt[$schoolName] = $schoolExamMoldD1Txt[$schoolName].$key.'、';
                    } else {
                        $schoolExamMoldD1Txt[$schoolName] = $schoolExamMoldD1Txt[$schoolName].$key;
                    }
                } elseif($value == 1 && $num == 2) {
                    if(array_values($schoolExamMoldTotalDValueRecord[$schoolName])[3] == 1) {
                        $schoolExamMoldD1Txt[$schoolName] = $schoolExamMoldD1Txt[$schoolName].$key.'等';
                    } else {
                        $schoolExamMoldD1Txt[$schoolName] = $schoolExamMoldD1Txt[$schoolName].$key;
                    }
                }
                $num++;
            }
        }

        foreach (self::$schoolInfoData['schoolList'] as $schoolName) {
            if($schoolExamMoldTotalRateRecord[$schoolName] == count(self::$detailTableData['examMoldName']) && array_values($schoolExamMoldTotalDValueRecord[$schoolName])[0] > 0) {
                $schoolExamMoldName11[] = $schoolName;
            } elseif($schoolExamMoldTotalRateRecord[$schoolName] < count(self::$detailTableData['examMoldName']) && array_values($schoolExamMoldTotalDValueRecord[$schoolName])[0] > 0) {
                $schoolExamMoldName12[] = $schoolName;
            } else {
                $schoolExamMoldName13[] = $schoolName;
            }
        }

        if(count($schoolExamMoldName11) > 1) {
            foreach ($schoolExamMoldName11 as $key => $name) {
                if($key < count($schoolExamMoldName11) - 1) {
                    $schoolExamMoldName11List = $schoolExamMoldName11List.$name.'、';
                } else {
                    $schoolExamMoldName11List = $schoolExamMoldName11List.$name;
                }
            }
            $examMoldSecondTxt = $examMoldSecondTxt.$schoolExamMoldName11List.'考生各能力层级的得分率均高于全区考生得分率，经效果量检验，';
            foreach ($schoolExamMoldName11 as $key => $name) {
                if($key < count($schoolExamMoldName11) - 1) {
                    $examMoldSecondTxt = $examMoldSecondTxt.$name.$schoolExamMoldD1Txt[$name].'；';
                } else {
                    $examMoldSecondTxt = $examMoldSecondTxt.$name.$schoolExamMoldD1Txt[$name];
                }
                
            }
            $examMoldSecondTxt = $examMoldSecondTxt.'具有实际意义上的差异显著性。';
        } elseif(count($schoolExamMoldName11) == 1) {
            $examMoldSecondTxt = $examMoldSecondTxt.$schoolExamMoldName11[0].'考生各能力层级的得分率均高于全区考生得分率，经效果量检验，';
            $examMoldSecondTxt = $examMoldSecondTxt.'该校'.$schoolExamMoldD1Txt[$schoolExamMoldName11[0]].'具有实际意义上的差异显著性。';
        }

        if(count($schoolExamMoldName12) > 1) {
            foreach ($schoolExamMoldName12 as $key => $name) {
                if($key < count($schoolExamMoldName12) - 1) {
                    $schoolExamMoldName12List = $schoolExamMoldName12List.$name.'中的'.$schoolExamMoldD1Txt[$name].'；';
                } else {
                    $schoolExamMoldName12List = $schoolExamMoldName12List.$name.'中的'.$schoolExamMoldD1Txt[$name].'，';
                }
            }
            $examMoldSecondTxt = $examMoldSecondTxt.$schoolExamMoldName12List.'经效果量检验，具有实际意义上的差异显著性。';
        } elseif(count($schoolExamMoldName12) == 1) {
            $schoolExamMoldName12List = $schoolExamMoldName12List.$schoolExamMoldName12[0].'中的'.$schoolExamMoldD1Txt[$schoolExamMoldName12[0]];
            $examMoldSecondTxt = $examMoldSecondTxt.$schoolName2List.'经效果量检验，具有实际意义上的差异显著性。';
        }

        if(count($schoolExamMoldName11) == 0 && count($schoolExamMoldName12) == 0 && count($schoolExamMoldName13) > 0) {
            $examMoldSecondTxt = $examMoldSecondTxt.'所有学校全体考生的各能力层级经效果量检验，均不具有实际意义上的差异显著性。';
        } elseif(count($schoolExamMoldName13) > 0) {
            $examMoldSecondTxt = $examMoldSecondTxt.'其他学校全体考生的各能力层级经效果量检验，均不具有实际意义上的差异显著性。';
        }

        $section->addText('         2）   比较不同学校全体组考生各能力层级的作答表现可知，'.$examMoldSecondTxt, $contentStyleFont, $contentStyleParagraph);


        foreach (self::$schoolInfoData['schoolList'] as $schoolName) { 
            arsort($schoolExamMoldExcellentDValueRecord[$schoolName]);
            $num = 0;
            // $schoolExamMoldD2Txt[$schoolName] = $schoolExamMoldD2Txt[$schoolName].'的';
            foreach ($schoolExamMoldExcellentDValueRecord[$schoolName] as $key => $value) {
                if($value == 1 && $num == 0) {
                    if(array_values($schoolExamMoldExcellentDValueRecord[$schoolName])[1] == 1) {
                        $schoolExamMoldD2Txt[$schoolName] = $schoolExamMoldD2Txt[$schoolName].$key.'、';
                    } else {
                        $schoolExamMoldD2Txt[$schoolName] = $schoolExamMoldD2Txt[$schoolName].$key;
                    }
                } elseif($value == 1 && $num == 1) {
                    if(array_values($schoolExamMoldExcellentDValueRecord[$schoolName])[2] == 1) {
                        $schoolExamMoldD2Txt[$schoolName] = $schoolExamMoldD2Txt[$schoolName].$key.'、';
                    } else {
                        $schoolExamMoldD2Txt[$schoolName] = $schoolExamMoldD2Txt[$schoolName].$key;
                    }
                } elseif($value == 1 && $num == 2) {
                    if(array_values($schoolExamMoldExcellentDValueRecord[$schoolName])[3] == 1) {
                        $schoolExamMoldD2Txt[$schoolName] = $schoolExamMoldD2Txt[$schoolName].$key.'等';
                    } else {
                        $schoolExamMoldD2Txt[$schoolName] = $schoolExamMoldD2Txt[$schoolName].$key;
                    }
                }
                $num++;
            }
        }

        foreach (self::$schoolInfoData['schoolList'] as $schoolName) { 
            foreach (self::$detailTableData['examMoldName'] as $key => $examMoldName) {
                if($schoolExamMoldExcellentDValueRecord[$schoolName][$examMoldName] == 1) {
                    $schoolExamMoldExcellentDValueCount[$schoolName] = $schoolExamMoldExcellentDValueCount[$schoolName] + 1;
                }
            }
        }

        foreach (self::$schoolInfoData['schoolList'] as $schoolName) {
            if($schoolExamMoldExcellentRateRecord[$schoolName] == count(self::$detailTableData['examMoldName']) && $schoolExamMoldExcellentDValueCount[$schoolName] == count(self::$detailTableData['examMoldName'])) {
                $schoolExamMoldName21[] = $schoolName;
            } elseif($schoolExamMoldExcellentRateRecord[$schoolName] == count(self::$detailTableData['examMoldName']) && $schoolExamMoldExcellentDValueCount[$schoolName] > 0) {
                $schoolExamMoldName22[] = $schoolName;
            } else {
                $schoolExamMoldName23[] = $schoolName;
            }
        }

        if(count($schoolExamMoldName21) > 1) {
            foreach ($schoolExamMoldName21 as $key => $name) {
                if($key < count($schoolExamMoldName21) - 1) {
                    $schoolExamMoldName21List = $schoolExamMoldName21List.$name.'、';
                } else {
                    $schoolExamMoldName21List = $schoolExamMoldName21List.$name;
                }
            }
            $examMoldThirdTxt = $examMoldThirdTxt.$schoolExamMoldName21List.'考生各能力层级的得分率均高于全区考生得分率，经效果量检验，';
            $examMoldThirdTxt = $examMoldThirdTxt.'均具有实际意义上的显著性。';
        } elseif(count($schoolExamMoldName21) == 1) {
            $examMoldThirdTxt = $examMoldThirdTxt.$schoolExamMoldName21[0].'考生各能力层级的得分率均高于全区考生得分率，经效果量检验，';
            $examMoldThirdTxt = $examMoldThirdTxt.'该校均具有实际意义上的显著性。';
        }

        if(count($schoolExamMoldName22) > 1) {
            foreach ($schoolExamMoldName22 as $key => $name) {
                if($key < count($schoolExamMoldName22) - 1) {
                    $schoolExamMoldName22List = $schoolExamMoldName22List.$name.'中的'.$schoolExamMoldD2Txt[$name].'；';
                } else {
                    $schoolExamMoldName22List = $schoolExamMoldName22List.$name.'中的'.$schoolExamMoldD2Txt[$name].'，';
                }
            }
            $examMoldThirdTxt = $examMoldThirdTxt.$schoolExamMoldName22List.'经效果量检验，具有实际意义上的差异显著性。';
        } elseif(count($schoolExamMoldName22) == 1) {
            $schoolExamMoldName22List = $schoolExamMoldName22List.$schoolExamMoldName22[0].'中的'.$schoolExamMoldD2Txt[$schoolExamMoldName22[0]];
            $examMoldThirdTxt = $examMoldThirdTxt.$schoolExamMoldName22List.'经效果量检验，具有实际意义上的差异显著性。';
        }

        if(count($schoolExamMoldName21) == 0 && count($schoolExamMoldName22) == 0 && count($schoolExamMoldName23) > 0) {
            $examMoldThirdTxt = $examMoldThirdTxt.'所有学校全体考生的各能力层级经效果量检验，均不具有实际意义上的差异显著性。';
        } elseif(count($schoolExamMoldName23) > 0) {
            $examMoldThirdTxt = $examMoldThirdTxt.'其他学校全体考生的各能力层级经效果量检验，均不具有实际意义上的差异显著性。';
        }


        $section->addText('         3）   比较不同学校优秀组考生各能力层级的作答表现可知，'.$examMoldThirdTxt, $contentStyleFont, $contentStyleParagraph);


        foreach (self::$schoolInfoData['schoolList'] as $schoolName) { 
            arsort($schoolExamMoldPassDValueRecord[$schoolName]);
            $num = 0;
            // $schoolExamMoldD3Txt[$schoolName] = $schoolExamMoldD3Txt[$schoolName].'的';
            foreach ($schoolExamMoldPassDValueRecord[$schoolName] as $key => $value) {
                if($value == 1 && $num == 0) {
                    if(array_values($schoolExamMoldPassDValueRecord[$schoolName])[1] == 1) {
                        $schoolExamMoldD3Txt[$schoolName] = $schoolExamMoldD3Txt[$schoolName].$key.'、';
                    } else {
                        $schoolExamMoldD3Txt[$schoolName] = $schoolExamMoldD3Txt[$schoolName].$key;
                    }
                } elseif($value == 1 && $num == 1) {
                    if(array_values($schoolExamMoldPassDValueRecord[$schoolName])[2] == 1) {
                        $schoolExamMoldD3Txt[$schoolName] = $schoolExamMoldD3Txt[$schoolName].$key.'、';
                    } else {
                        $schoolExamMoldD3Txt[$schoolName] = $schoolExamMoldD3Txt[$schoolName].$key;
                    }
                } elseif($value == 1 && $num == 2) {
                    if(array_values($schoolExamMoldPassDValueRecord[$schoolName])[3] == 1) {
                        $schoolExamMoldD3Txt[$schoolName] = $schoolExamMoldD3Txt[$schoolName].$key.'等';
                    } else {
                        $schoolExamMoldD3Txt[$schoolName] = $schoolExamMoldD3Txt[$schoolName].$key;
                    }
                }
                $num++;
            }
        }

        foreach (self::$schoolInfoData['schoolList'] as $schoolName) {
            if($schoolExamMoldPassRateRecord[$schoolName] == count(self::$detailTableData['examMoldName']) && array_values($schoolExamMoldPassDValueRecord[$schoolName])[0] > 0) {
                $schoolExamMoldName31[] = $schoolName;
            } elseif($schoolExamMoldPassRateRecord[$schoolName] < count(self::$detailTableData['examMoldName']) && array_values($schoolExamMoldPassDValueRecord[$schoolName])[0] > 0) {
                $schoolExamMoldName32[] = $schoolName;
            } else {
                $schoolExamMoldName33[] = $schoolName;
            }
        }

        if(count($schoolExamMoldName31) > 1) {
            foreach ($schoolExamMoldName31 as $key => $name) {
                if($key < count($schoolExamMoldName31) - 1) {
                    $schoolExamMoldName31List = $schoolExamMoldName31List.$name.'、';
                } else {
                    $schoolExamMoldName31List = $schoolExamMoldName31List.$name;
                }
            }
            $examMoldFourTxt = $examMoldFourTxt.$schoolExamMoldName31List.'考生各能力层级的得分率均高于全区考生得分率，经效果量检验，';
            foreach ($schoolExamMoldName31 as $key => $name) {
                if($key < count($schoolExamMoldName31) - 1) {
                    $examMoldFourTxt = $examMoldFourTxt.$name.$schoolExamMoldD3Txt[$name].'；';
                } else {
                    $examMoldFourTxt = $examMoldFourTxt.$name.$schoolExamMoldD3Txt[$name];
                }
                
            }
            $examMoldFourTxt = $examMoldFourTxt.'具有实际意义上的差异显著性。';
        } elseif(count($schoolExamMoldName31) == 1) {
            $examMoldFourTxt = $examMoldFourTxt.$schoolExamMoldName31[0].'考生各能力层级的得分率均高于全区考生得分率，经效果量检验，';
            $examMoldFourTxt = $examMoldFourTxt.'该校'.$schoolExamMoldD3Txt[$schoolExamMoldName31[0]].'具有实际意义上的差异显著性。';
        }

        if(count($schoolExamMoldName32) > 1) {
            foreach ($schoolExamMoldName32 as $key => $name) {
                if($key < count($schoolExamMoldName32) - 1) {
                    $schoolExamMoldName32List = $schoolExamMoldName32List.$name.'中的'.$schoolExamMoldD3Txt[$name].'；';
                } else {
                    $schoolExamMoldName32List = $schoolExamMoldName32List.$name.'中的'.$schoolExamMoldD3Txt[$name].'，';
                }
            }
            $examMoldFourTxt = $examMoldFourTxt.$schoolExamMoldName32List.'经效果量检验，具有实际意义上的差异显著性。';
        } elseif(count($schoolExamMoldName32) == 1) {
            $schoolExamMoldName32List = $schoolExamMoldName32List.$schoolExamMoldName32[0].'中的'.$schoolExamMoldD3Txt[$schoolExamMoldName32[0]];
            $examMoldFourTxt = $examMoldFourTxt.$schoolExamMoldName32List.'经效果量检验，具有实际意义上的差异显著性。';
        }

        if(count($schoolExamMoldName31) == 0 && count($schoolExamMoldName32) == 0 && count($schoolExamMoldName33) > 0) {
            $examMoldFourTxt = $examMoldFourTxt.'所有学校全体考生的各能力层级经效果量检验，均不具有实际意义上的差异显著性。';
        } elseif(count($schoolExamMoldName33) > 0) {
            $examMoldFourTxt = $examMoldFourTxt.'其他学校全体考生的各能力层级经效果量检验，均不具有实际意义上的差异显著性。';
        }


        $section->addText('         4）   及格组中'.$examMoldFourTxt, $contentStyleFont, $contentStyleParagraph);

        foreach (self::$schoolInfoData['schoolList'] as $schoolName) { 
            arsort($schoolExamMoldFailDValueRecord[$schoolName]);
            $num = 0;
            // $schoolExamMoldD4Txt[$schoolName] = $schoolExamMoldD4Txt[$schoolName].'的';
            foreach ($schoolExamMoldFailDValueRecord[$schoolName] as $key => $value) {
                if($value == 1 && $num == 0) {
                    if(array_values($schoolExamMoldFailDValueRecord[$schoolName])[1] == 1) {
                        $schoolExamMoldD4Txt[$schoolName] = $schoolExamMoldD4Txt[$schoolName].$key.'、';
                    } else {
                        $schoolExamMoldD4Txt[$schoolName] = $schoolExamMoldD4Txt[$schoolName].$key;
                    }
                } elseif($value == 1 && $num == 1) {
                    if(array_values($schoolExamMoldFailDValueRecord[$schoolName])[2] == 1) {
                        $schoolExamMoldD4Txt[$schoolName] = $schoolExamMoldD4Txt[$schoolName].$key.'、';
                    } else {
                        $schoolExamMoldD4Txt[$schoolName] = $schoolExamMoldD4Txt[$schoolName].$key;
                    }
                } elseif($value == 1 && $num == 2) {
                    if(array_values($schoolExamMoldFailDValueRecord[$schoolName])[3] == 1) {
                        $schoolExamMoldD4Txt[$schoolName] = $schoolExamMoldD4Txt[$schoolName].$key.'等';
                    } else {
                        $schoolExamMoldD4Txt[$schoolName] = $schoolExamMoldD4Txt[$schoolName].$key;
                    }
                }
                $num++;
            }
        }

        foreach (self::$schoolInfoData['schoolList'] as $schoolName) {
            if($schoolExamMoldFailRateRecord[$schoolName] == count(self::$detailTableData['examMoldName']) && array_values($schoolExamMoldFailDValueRecord[$schoolName])[0] > 0) {
                $schoolExamMoldName41[] = $schoolName;
            } elseif($schoolExamMoldFailRateRecord[$schoolName] < count(self::$detailTableData['examMoldName']) && array_values($schoolExamMoldFailDValueRecord[$schoolName])[0] > 0) {
                $schoolExamMoldName42[] = $schoolName;
            } else {
                $schoolExamMoldName43[] = $schoolName;
            }
        }

        if(count($schoolExamMoldName41) > 1) {
            foreach ($schoolExamMoldName41 as $key => $name) {
                if($key < count($schoolExamMoldName41) - 1) {
                    $schoolExamMoldName41List = $schoolExamMoldName41List.$name.'、';
                } else {
                    $schoolExamMoldName41List = $schoolExamMoldName41List.$name;
                }
            }
            $examMoldFiveTxt = $examMoldFiveTxt.$schoolExamMoldName41List.'考生各能力层级的得分率均高于全区考生得分率，经效果量检验，';
            foreach ($schoolExamMoldName41 as $key => $name) {
                if($key < count($schoolExamMoldName41) - 1) {
                    $examMoldFiveTxt = $examMoldFiveTxt.$name.$schoolExamMoldD4Txt[$name].'；';
                } else {
                    $examMoldFiveTxt = $examMoldFiveTxt.$name.$schoolExamMoldD4Txt[$name];
                }
                
            }
            $examMoldFiveTxt = $examMoldFiveTxt.'具有实际意义上的差异显著性。';
        } elseif(count($schoolExamMoldName41) == 1) {
            $examMoldFiveTxt = $examMoldFiveTxt.$schoolExamMoldName41[0].'考生各能力层级的得分率均高于全区考生得分率，经效果量检验，';
            $examMoldFiveTxt = $examMoldFiveTxt.'该校'.$schoolExamMoldD4Txt[$schoolExamMoldName41[0]].'具有实际意义上的差异显著性。';
        }

        if(count($schoolExamMoldName42) > 1) {
            foreach ($schoolExamMoldName42 as $key => $name) {
                if($key < count($schoolExamMoldName42) - 1) {
                    $schoolExamMoldName42List = $schoolExamMoldName42List.$name.'中的'.$schoolExamMoldD4Txt[$name].'；';
                } else {
                    $schoolExamMoldName42List = $schoolExamMoldName42List.$name.'中的'.$schoolExamMoldD4Txt[$name].'，';
                }
            }
            $examMoldFiveTxt = $examMoldFiveTxt.$schoolExamMoldName42List.'经效果量检验，具有实际意义上的差异显著性。';
        } elseif(count($schoolExamMoldName42) == 1) {
            $schoolExamMoldName42List = $schoolExamMoldName42List.$schoolExamMoldName42[0].'中的'.$schoolExamMoldD4Txt[$schoolExamMoldName42[0]];
            $examMoldFiveTxt = $examMoldFiveTxt.$schoolExamMoldName42List.'经效果量检验，具有实际意义上的差异显著性。';
        }

        if(count($schoolExamMoldName41) == 0 && count($schoolExamMoldName42) == 0 && count($schoolExamMoldName43) > 0) {
            $examMoldFiveTxt = $examMoldFiveTxt.'所有学校全体考生的各能力层级经效果量检验，均不具有实际意义上的差异显著性。';
        } elseif(count($schoolExamMoldName43) > 0) {
            $examMoldFiveTxt = $examMoldFiveTxt.'其他学校全体考生的各能力层级经效果量检验，均不具有实际意义上的差异显著性。';
        }

        $section->addText('         5）   未及格组中'.$examMoldFiveTxt, $contentStyleFont, $contentStyleParagraph);

        $section->addTextBreak(2);


        $section->addText('4.全体考生客观题水平分析', $subTitleStyleFont, $subTitleStyleParagraph);
        $section->addText('表4.1 全区考生'.self::$course.'科目客观题分析表', $tableTitleStyleFont, $tableStyleParagraph);

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
                if($key == '选A率%') {
                    $kgtRate[$i]['A'] = $name;
                } elseif($key == '选B率%') {
                    $kgtRate[$i]['B'] = $name;
                } elseif($key == '选C率%') {
                    $kgtRate[$i]['C'] = $name;
                } elseif($key == '选D率%') {
                    $kgtRate[$i]['D'] = $name;
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

        $section->addText('     注：区分度评价标准:>0.4区分度较高;0.3~0.39 区分度中等;0.2~0.29 区分度一般;<0.2区分度较低', $tableCommentStyleFont);
        $section->addText('     难度评价标准:  >0.9容易;0.7~0.9较易;0.4~0.7中等;<0.4偏难;', $tableCommentStyleFont);

        $section->addTextBreak();

        $section->addText('表2.0的数据表明', $contentStyleFont);

        for ($i = 0; $i < count(self::$choiceQuestionsAnalysisData); $i++) {
            if(array_values($kgtRate[$i])[1] > 17) {
                $item = array_keys($kgtRate[$i])[1];
                $txt = '，选项'.$item.'的干扰性最强达到了'.array_values($kgtRate[$i])[1].'%';
            } else {
                $txt = '';
            }

            if($i % 2 == 0){
                $section->addText('     '.($i+1).'）'.self::$choiceQuestionsAnalysisData[$i]['题号'].'：该题难度'.self::$choiceQuestionsAnalysisData[$i]['难度评价标准'].'('.self::$choiceQuestionsAnalysisData[$i]['难度'].')，'.self::$choiceQuestionsAnalysisData[$i]['区分度评价标准'].'('.self::$choiceQuestionsAnalysisData[$i]['区分度'].')，有'.self::$choiceQuestionsAnalysisData[$i]['选'.self::$choiceQuestionsAnalysisData[$i]['答案'].'率%'].'%的学生选择了正确答案('.self::$choiceQuestionsAnalysisData[$i]['答案'].')，得分率>'.self::$choiceQuestionsAnalysisData[$i]['得分率'].'%'.$txt, $choiceQuestionsContentStyleFont, $choiceQuestionsContentStyleParagraph);
            }
            else {
                $section->addText('     '.($i+1).'）'.self::$choiceQuestionsAnalysisData[$i]['题号'].'：该题从难度上讲属于'.self::$choiceQuestionsAnalysisData[$i]['难度评价标准'].'('.self::$choiceQuestionsAnalysisData[$i]['难度'].')，'.self::$choiceQuestionsAnalysisData[$i]['区分度评价标准'].'('.self::$choiceQuestionsAnalysisData[$i]['区分度'].')，正确答案('.self::$choiceQuestionsAnalysisData[$i]['答案'].')的选择率达到了'.self::$choiceQuestionsAnalysisData[$i]['选'.self::$choiceQuestionsAnalysisData[$i]['答案'].'率%'].'%，其余选项的选择率都低于'.array_values($kgtRate[$i])[1].'%'.$txt, $choiceQuestionsContentStyleFont, $choiceQuestionsContentStyleParagraph);
            }
        }
        
        $section = \PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
        $section->save($wordSaveDir.iconv("utf-8", "gb2312", self::$course).'.docx');

    }
}

?>