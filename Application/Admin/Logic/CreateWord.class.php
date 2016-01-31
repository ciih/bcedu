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

        $wordBaseDir = dirname(dirname(dirname(dirname(__FILE__))))."/Word/Template/";
        $document = $PHPWord->loadTemplate($wordBaseDir.'Template.docx');


        $wordSaveDate = date("Y-m-d");
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

        $contentStyleFont = array();
        $contentStyleParagraph = array('spacing'=>60);

        $tableTitleStyleFont = array('bold'=>true, 'size'=>9);
        $tableStyleParagraph = array('align'=>'center');


        $styleTable = array(
            'borderColor'=>'fff',
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


        // Add tableTJRS 
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
        $examNameScore = substr($examNameScore, 0, -1).'分';
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
            $tableNLCJ->addCell(1600)->addText($studentData['detailTable']['examScore'][$key], $cellStyleFont, $cellStyle); 
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
        $typeNameScore = substr($typeNameScore, 0, -1).'分';
        arsort($studentData['detailTable']['tpeScore']);


        $section->addText('     由以上图表的数据分析表明：', $contentStyleFont, $contentStyleParagraph);
        $section->addText('     1）  本科目考试此次共包含'.count($studentData['detailTable']['typeName']).'能力层级('.$typeNameList.')，所占比值分别为'.$typeNameScore.'。其中，'.array_keys($studentData['detailTable']['typeScore'])[0].'该知识范畴所占比重最大(详见图2.1)。', $contentStyleFont, $contentStyleParagraph);
        $section->addText('     2）  根据全体考生作答表现可知，得分率最高为'.array_keys($typeTotalRate)[0].'('.$typeTotalRate[array_keys($typeTotalRate)[0]].')；其次为'.array_keys($typeTotalRate)[1].'('.$typeTotalRate[array_keys($typeTotalRate)[1]].');最低的为'.array_keys($typeTotalRate)[count($typeTotalRate)-1].'，得分率为'.$typeTotalRate[array_keys($typeTotalRate)[count($typeTotalRate)-1]].'。', $contentStyleFont, $contentStyleParagraph);
        if(count($typeExcellentRateCount) > 0){
            $txt = '部分高于';
        }
        else {
            $txt = '均高于';
        }
        $section->addText('     3）  该科达到优秀水平考生的各能力层级的得分率'.$txt.'全体考生平均水平(详见图2.2)；其中'.$typeExcellentRateDiffCount[0].'高于全体水平最多，得分率相差'.$typeExcellentRateDiffCount[1].'。', $contentStyleFont, $contentStyleParagraph);
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


        $section->addText('3.各校得分率分析比较', $subSmallTitleStyleFont);
        $section->addText('3.1 各校知识范畴得分率比较分析', $tableTitleStyleFont, $tableStyleParagraph);







        $section = \PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
        $section->save($wordSaveDir.iconv("utf-8", "gb2312", $course).'.docx');




    }

}

?>