<?php

/**
 * 建立word文档
 * @author chenhong
 */

namespace Admin\Logic;
use Think\Model;

class CreateWord {
   

    /**
     * 建立word文件
     * @param $foldername 标题
     * @param $course 科目
     */
    public function creatWordFile($date, $foldername, $course)
    {

        $studentObj = new \Admin\Model\StudentData();
        $studentData = $studentObj->getStudentData($date, $foldername, $course);
        // var_export($studentData);

        $folderArr = explode("_" , $foldername);

        $courseTitle = implode('', $folderArr);

        if($course != '数学(理)' && $course != '数学(文)') {
            $courseName = str_split($course, 3);
        }
        elseif($course == '数学(理)') {
            $courseName[] = '数';
            $courseName[] = '学';
            $courseName[] = '(理)';
        }
        elseif($course == '数学(文)') {
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

        $wordSaveDate = $date;
        $wordSaveDir = dirname(dirname(dirname(dirname(__FILE__))))."/Word/".$wordSaveDate."/";

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

        $tableTitleStyleFont = array('bold'=>true, 'size'=>9);
        $tableStyleParagraph = array('align'=>'center');


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
        $section->addText($courseTitle, $coverStyleFont, $coverStyleParagraph);
        $section->addText('考试水平评价及教学质量分析报告', $coverStyleFont, $coverStyleParagraph);
        $section->addTextBreak(4);

        for ($i = 0; $i < count($courseName); $i++) { 
            $section->addText($courseName[$i], $coverStyleFont, $coverStyleParagraph);
        }

        $section->addTextBreak(10);

        $section->addText('1.总体', $subTitleStyleFont, $subTitleStyleParagraph);
        $section->addText('     1)本次语文科目考试共涉及'.count($studentData['averageScore']['schoolName']).'所学校，共计'.$studentData['averageScore']['amountStudentCount'].'人', $contentStyleFont, $contentStyleParagraph);
        $section->addText('     2) 该试卷总分为'.$studentData['detailTable']['totalScore'].'分，平均分为'.$studentData['averageScore']['amountAverageScore'].'分。', $contentStyleFont, $contentStyleParagraph);
        $section->addText('     3)试卷难度为'.$studentData['courseAnalysis']['difficulty'].'，区分度为'.$studentData['courseAnalysis']['distinguish'].'，信度为'.$studentData['courseAnalysis']['reliability'].'；', $contentStyleFont, $contentStyleParagraph);
        $section->addText('     4)数据表明试卷难度'.$studentData['courseAnalysis']['difficultyTxt'].'，且区分度'.$studentData['courseAnalysis']['distinguishTxt'].'，信度'.$studentData['courseAnalysis']['reliabilityTxt'].'。', $contentStyleFont, $contentStyleParagraph);
        $section->addText('     5)根据此次考试('.$studentData['courseAnalysis']['course'].'学科)所确定的优秀率、及格率，确定各水平线：', $contentStyleFont, $contentStyleParagraph);
        $section->addText('             1、优秀水平：>='.($studentData['scoreRate'][0]*100).'%', $contentStyleFont, $contentStyleParagraph);
        $section->addText('             2、及格水平：>='.($studentData['scoreRate'][1]*100).'%', $contentStyleFont, $contentStyleParagraph);
        $section->addText('             3、未及格：   <'.($studentData['scoreRate'][1]*100).'%', $contentStyleFont, $contentStyleParagraph);
        $section->addText('         本报告的评价对象为实际参加该次语文学科的考生。以下将根据考生在不同知识范畴、能力层级的作答表现，分析不同区域学校以及不同水平考生的水平。', $contentStyleFont, $contentStyleParagraph);

        $section->addTextBreak(15);

        $section->addText('2.全体及不同水平组考生分析', $subTitleStyleFont, $subTitleStyleParagraph);
        $section->addText('2.1总体水平概况分析', $subSmallTitleStyleFont);
        $section->addText('表2.1 语文学科全区不同水平组考生的人数及所占百分比(%)', $tableTitleStyleFont, $tableStyleParagraph);


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
        $tableTJRS->addCell(2200)->addText($studentData['studentCountRate']['count']['excellentCount'], $cellGreenStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText($studentData['studentCountRate']['rate']['excellentRate'], $cellGreenStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText($studentData['studentCountRate']['cumulativeCount']['excellentCount'], $cellGreenStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText($studentData['studentCountRate']['cumulativeRate']['excellentRate'], $cellGreenStyleFont, $cellStyle); 
         
        // Add row设置行高 
        $tableTJRS->addRow(300); 
         
        $tableTJRS->addCell(2200)->addText('及格', $cellRedStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText($studentData['studentCountRate']['count']['passCount'], $cellRedStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText($studentData['studentCountRate']['rate']['passRate'], $cellRedStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText($studentData['studentCountRate']['cumulativeCount']['passCount'], $cellRedStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText($studentData['studentCountRate']['cumulativeRate']['passRate'], $cellRedStyleFont, $cellStyle);
        
        // Add row设置行高 
        $tableTJRS->addRow(300); 
         
        $tableTJRS->addCell(2200)->addText('未及格', $cellPurpleStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText($studentData['studentCountRate']['count']['failCount'], $cellPurpleStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText($studentData['studentCountRate']['rate']['failRate'], $cellPurpleStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText($studentData['studentCountRate']['cumulativeCount']['failCount'], $cellPurpleStyleFont, $cellStyle); 
        $tableTJRS->addCell(2200)->addText($studentData['studentCountRate']['cumulativeRate']['failRate'], $cellPurpleStyleFont, $cellStyle);

        $section->addTextBreak();

        $section->addText('         表1.1的数据表明：', $contentStyleFont, $contentStyleParagraph);
        $section->addText('         1)  本次考试共有'.$studentData['studentCountRate']['count']['excellentCount'].'名考生达到优秀水平，占全体考生的'.$studentData['studentCountRate']['rate']['excellentRate'].'%。共有'.$studentData['studentCountRate']['count']['passCount'].'名考生达到及格水平，占全体考生的'.$studentData['studentCountRate']['rate']['passRate'].'%。累计比例为'.$studentData['studentCountRate']['cumulativeRate']['passRate'].'%', $contentStyleFont, $contentStyleParagraph);
        $section->addText('         2)  本次考试共有'.$studentData['studentCountRate']['count']['failCount'].'名考生未达到及格水平，比例为'.$studentData['studentCountRate']['rate']['failRate'].'%。', $contentStyleFont, $contentStyleParagraph);
    

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

        $examTotalRate = array();
        $examExcellentRate = array();
        $examPassRate = array();
        $examFailRate = array();

        $examExcellentRateCount = array();
        $examExcellentRateDiffCount = array('知识范畴',0);
        $examPassRateCount = array();
        $examPassRateDiffCount = array('知识范畴',0);
        $examFailRateCount = array();
        $examFailRateDiffCount = array('知识范畴',0);

        $examNameCount = $studentData['detailTable']['examName'];
        $typeNameCount = $studentData['detailTable']['typeName'];

        foreach ($studentData['scoreStatisticsRate']['exam'] as $key => $name) {
            $tableZSFC->addRow(300); 
             
            $tableZSFC->addCell(2500)->addText($key, $cellStyleFont, $cellStyle); 
            $tableZSFC->addCell(1600)->addText($studentData['detailTable']['examScore'][$key], $cellStyleFont, $cellStyle); 
            $tableZSFC->addCell(1600)->addText($name['total']['totalRate'], $cellStyleFont, $cellStyle); 
            $tableZSFC->addCell(1600)->addText($name['total']['excellentRate'], $cellBlueStyleFont, $cellStyle); 
            $tableZSFC->addCell(1600)->addText($name['total']['passRate'], $cellGreenStyleFont, $cellStyle); 
            $tableZSFC->addCell(1600)->addText($name['total']['failRate'], $cellRedStyleFont, $cellStyle);

            $examTotalRate[$key] = $name['total']['totalRate'];
            $examExcellentRate[$key] = $name['total']['excellentRate'];
            $examPassRate[$key] = $name['total']['passRate'];
            $examFailRate[$key] = $name['total']['failRate'];

            if($name['total']['excellentRate'] <= $name['total']['totalRate']) {
                $examExcellentRateCount[] = $key;
            }
            else {
                $examDiffScore = $name['total']['excellentRate'] - $name['total']['totalRate'];
                if($examExcellentRateDiffCount[1] < $examDiffScore) {
                    $examExcellentRateDiffCount[0] = $key;
                    $examExcellentRateDiffCount[1] = $examDiffScore;
                }
            }

            if($name['total']['passRate'] <= $name['total']['totalRate']) {
                $examPassRateCount[] = $key;
            }
            else {
                $examDiffScore = $name['total']['passRate'] - $name['total']['totalRate'];
                if($examPassRateDiffCount[1] < $examDiffScore) {
                    $examPassRateDiffCount[0] = $key;
                    $examPassRateDiffCount[1] = $examDiffScore;
                }
            }

            if($name['total']['failRate'] > $name['total']['totalRate']) {
                $examFailRateCount[] = $key;
            }
            else {
                $examDiffScore = $name['total']['totalRate'] - $name['total']['failRate'];
                if($examFailRateDiffCount[1] < $examDiffScore) {
                    $examFailRateDiffCount[0] = $key;
                    $examFailRateDiffCount[1] = $examDiffScore;
                }
            }

        }

        arsort($examTotalRate);
        arsort($examExcellentRate);
        arsort($examPassRate);
        arsort($examFailRate);

        $section->addTextBreak(4);

        $examNameList = implode('、', $studentData['detailTable']['examName']);
        $examNameScore = implode('分、', $studentData['detailTable']['examScore']);
        // $examNameScore = substr($examNameScore, 0, -1).'分';
        $examNameScore = $examNameScore.'分';
        arsort($studentData['detailTable']['examScore']);


        $section->addText('     由以上图表的数据分析表明：', $contentStyleFont, $contentStyleParagraph);
        $section->addText('     1）  本科目考试此次共包含'.count($studentData['detailTable']['examName']).'个知识范畴('.$examNameList.')，所占比值分别为'.$examNameScore.'。其中，'.array_keys($studentData['detailTable']['examScore'])[0].'该知识范畴所占比重最大(详见图2.1)。', $contentStyleFont, $contentStyleParagraph);
        $section->addText('     2）  根据全体考生作答表现可知，得分率最高为'.array_keys($examTotalRate)[0].'('.$examTotalRate[array_keys($examTotalRate)[0]].')；其次为'.array_keys($examTotalRate)[1].'('.$examTotalRate[array_keys($examTotalRate)[1]].');最低的为'.array_keys($examTotalRate)[count($examTotalRate)-1].'，得分率为'.$examTotalRate[array_keys($examTotalRate)[count($examTotalRate)-1]].'。', $contentStyleFont, $contentStyleParagraph);
        if(count($examExcellentRateCount) > 0){
            $txt = '部分高于';
        }
        else {
            $txt = '均高于';
        }
        $section->addText('     3）  该科达到优秀水平考生的各知识范畴的得分率'.$txt.'全体考生平均水平(详见图2.2)；其中'.$examExcellentRateDiffCount[0].'高于全体水平最多，得分率相差'.$examExcellentRateDiffCount[1].'。', $contentStyleFont, $contentStyleParagraph);
        if(count($examPassRateCount) > 0){
            $txt = '部分高于';
        }
        else {
            $txt = '均高于';
        }
        $section->addText('     4）  达到及格水平考生的各知识范畴的得分率'.$txt.'全区平均水平。', $contentStyleFont, $contentStyleParagraph);
        if(count($examFailRateCount) > 0){
            $txt = '部分低于';
        }
        else {
            $txt = '均低于';
        }
        $section->addText('     5） 未及格考生水平组的各知识范畴得分率'.$txt.'全区平均水平；其中'.$examFailRateDiffCount[0].'低于全体水平最多，得分率相差'.$examFailRateDiffCount[1].'。', $contentStyleFont, $contentStyleParagraph);


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

        $typeTotalRate = array();
        $typeExcellentRate = array();
        $typePassRate = array();
        $typeFailRate = array();

        $typeExcellentRateCount = array();
        $typeExcellentRateDiffCount = array('能力层级',0);
        $typePassRateCount = array();
        $typePassRateDiffCount = array('能力层级',0);
        $typeFailRateCount = array();
        $typeFailRateDiffCount = array('能力层级',0);

        foreach ($studentData['scoreStatisticsRate']['type'] as $key => $name) {
            $tableNLCJ->addRow(300); 
             
            $tableNLCJ->addCell(2500)->addText($key, $cellStyleFont, $cellStyle); 
            $tableNLCJ->addCell(1600)->addText($studentData['detailTable']['typeScore'][$key], $cellStyleFont, $cellStyle); 
            $tableNLCJ->addCell(1600)->addText($name['total']['totalRate'], $cellStyleFont, $cellStyle); 
            $tableNLCJ->addCell(1600)->addText($name['total']['excellentRate'], $cellBlueStyleFont, $cellStyle); 
            $tableNLCJ->addCell(1600)->addText($name['total']['passRate'], $cellGreenStyleFont, $cellStyle); 
            $tableNLCJ->addCell(1600)->addText($name['total']['failRate'], $cellRedStyleFont, $cellStyle);

            $typeTotalRate[$key] = $name['total']['totalRate'];
            $typeExcellentRate[$key] = $name['total']['excellentRate'];
            $typePassRate[$key] = $name['total']['passRate'];
            $typeFailRate[$key] = $name['total']['failRate'];

            if($name['total']['excellentRate'] <= $name['total']['totalRate']) {
                $typeExcellentRateCount[] = $key;
            }
            else {
                $typeDiffScore = $name['total']['excellentRate'] - $name['total']['totalRate'];
                if($typeExcellentRateDiffCount[1] < $typeDiffScore) {
                    $typeExcellentRateDiffCount[0] = $key;
                    $typeExcellentRateDiffCount[1] = $typeDiffScore;
                }
            }

            if($name['total']['passRate'] <= $name['total']['totalRate']) {
                $typePassRateCount[] = $key;
            }
            else {
                $typeDiffScore = $name['total']['passRate'] - $name['total']['totalRate'];
                if($typePassRateDiffCount[1] < $typeDiffScore) {
                    $typePassRateDiffCount[0] = $key;
                    $typePassRateDiffCount[1] = $typeDiffScore;
                }
            }

            if($name['total']['failRate'] > $name['total']['totalRate']) {
                $typeFailRateCount[] = $key;
            }
            else {
                $typeDiffScore = $name['total']['totalRate'] - $name['total']['failRate'];
                if($typeFailRateDiffCount[1] < $typeDiffScore) {
                    $typeFailRateDiffCount[0] = $key;
                    $typeFailRateDiffCount[1] = $typeDiffScore;
                }
            }
        }

        arsort($typeTotalRate);
        arsort($typeExcellentRate);
        arsort($typePassRate);
        arsort($typeFailRate);

        $section->addTextBreak(4);

        $typeNameList = implode('、', $studentData['detailTable']['typeName']);
        $typeNameScore = implode('分、', $studentData['detailTable']['typeScore']);
        // $typeNameScore = substr($typeNameScore, 0, -1).'分';
        $typeNameScore = $typeNameScore.'分';
        arsort($studentData['detailTable']['tpeScore']);


        $section->addText('     由以上图表的数据分析表明：', $contentStyleFont, $contentStyleParagraph);
        $section->addText('     1）  本科目考试此次共包含'.count($studentData['detailTable']['typeName']).'能力层级('.$typeNameList.')，所占比值分别为'.$typeNameScore.'。其中，'.array_keys($studentData['detailTable']['typeScore'])[0].'该能力层级所占比重最大(详见图2.3)。', $contentStyleFont, $contentStyleParagraph);
        $section->addText('     2）  根据全体考生作答表现可知，得分率最高为'.array_keys($typeTotalRate)[0].'('.$typeTotalRate[array_keys($typeTotalRate)[0]].')；其次为'.array_keys($typeTotalRate)[1].'('.$typeTotalRate[array_keys($typeTotalRate)[1]].');最低的为'.array_keys($typeTotalRate)[count($typeTotalRate)-1].'，得分率为'.$typeTotalRate[array_keys($typeTotalRate)[count($typeTotalRate)-1]].'。', $contentStyleFont, $contentStyleParagraph);
        if(count($typeExcellentRateCount) > 0){
            $txt = '部分高于';
        }
        else {
            $txt = '均高于';
        }
        $section->addText('     3）  该科达到优秀水平考生的各能力层级的得分率'.$txt.'全体考生平均水平(详见图2.4)；其中'.$typeExcellentRateDiffCount[0].'高于全体水平最多，得分率相差'.$typeExcellentRateDiffCount[1].'。', $contentStyleFont, $contentStyleParagraph);
        if(count($typePassRateCount) > 0){
            $txt = '部分高于';
        }
        else {
            $txt = '均高于';
        }
        $section->addText('     4）  达到及格水平考生的各能力层级的得分率'.$txt.'全区平均水平。', $contentStyleFont, $contentStyleParagraph);
        if(count($typeFailRateCount) > 0){
            $txt = '部分低于';
        }
        else {
            $txt = '均低于';
        }
        $section->addText('     5） 未及格考生水平组的各能力层级得分率'.$txt.'全区平均水平；其中'.$typeFailRateDiffCount[0].'低于全体水平最多，得分率相差'.$typeFailRateDiffCount[1].'。', $contentStyleFont, $contentStyleParagraph);

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
        }
        
        $section = \PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
        $section->save($wordSaveDir.iconv("utf-8", "gb2312", $course).'.docx');

    }

}

?>