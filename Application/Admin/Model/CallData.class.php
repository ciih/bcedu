<?php

/**
 * 获取学校列表
 * @author chenhong
 */

/**
* 此处数据可以单独先把需要的excel表里面的数据事先读取出来，然后根据不同需求将数据加工处理，现在将读取数据与逻辑写在一起实在有些乱，不便于查找
*/

namespace Admin\Model;
use Think\Model;

class CallData {
    /**
     * 获取学校列表
     * @param $data 分数
     */
    public function getStudentData($date, $foldername, $course)
    {

        $schoolObj = new \Admin\Model\ExcelData($date, $foldername, $course);
        $analysisFoldernameData = $schoolObj->analysisFoldername();
        $schoolData = $schoolObj->getCourseData();

        var_export('===============schoolData=================');
        var_export($analysisFoldernameData);
        var_export($schoolData);

    }

}

?>