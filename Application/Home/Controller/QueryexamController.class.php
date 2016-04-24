<?php
namespace Home\Controller;
use Think\Controller;
class QueryexamController extends Controller {
    public function ajax_get_exam(){
    	$schooltype = I('schooltype');
    	$count = I('count');

    	$examTable = M('exam');
    	if(empty($count)) {
    		$data = $examTable->where("schooltype='$schooltype'")->order('id desc')->select();
    	} else {
    		$data = $examTable->where("schooltype='$schooltype'")->order('id desc')->limit($count)->select();
    	}
        
        $this->ajaxReturn (json_encode($data),'JSON');
    }

    public function ajax_get_school(){
        $schooltype = I('schooltype');
        
        // 获取学校列表
        $schoolInfoObj = new \Admin\Model\SchoolInfoData();
        $schoolInfoData = $schoolInfoObj->getSchoolData($schooltype);
        
        $this->ajaxReturn (json_encode($schoolInfoData['schoolList']),'JSON');
    }

    public function ajax_get_zvalue(){
        $type = I('datatype');
        
        $examTable = M('exam');

        if($type == 'single') {
            $fullname = I('fullname');

            $data = $examTable->where("fullname='$fullname'")->order('id desc')->select();

            if(!empty($data)) {
                $date = $data[0]['uploaddate'];
            
                // 获取考试数据目录
                $ZValueObj = new \Admin\Model\ZValueData($date, $fullname);
                $ZValueData = $ZValueObj->getZValueData();
                
                $this->ajaxReturn (json_encode($ZValueData),'JSON');
            }
        } elseif($type == 'multi') {
            $schoolyear = I('schoolyear');
            $schoolterm = I('schoolterm');
            $schoolgrade = I('schoolgrade');
            $course = I('course');

            if($schoolterm == '全年') {
                $data = $examTable->where("schoolyear='$schoolyear' AND grade='$schoolgrade'")->order('id desc')->select();
            } else {
                $data = $examTable->where("schoolyear='$schoolyear' AND schoolterm='$schoolterm' AND grade='$schoolgrade'")->order('id desc')->select();
            }

            foreach ($data as $key => $value) {
                $ZValueObj[$key] = new \Admin\Model\ZValueData($value['uploaddate'], $value['fullname']);
                $ZValueArr[$key] = $ZValueObj[$key]->getZValueData();
                foreach ($ZValueArr[$key] as $schoolName => $score) {
                    $ZValueData[$key]['schoolterm'] = $value['schoolterm'];
                    $ZValueData[$key]['examname'] = $value['examname'];
                    $ZValueData[$key]['schoolName'][] = $schoolName;
                    $ZValueData[$key]['score'][] = $score[$course];
                }
            }

            $this->ajaxReturn (json_encode($ZValueData),'JSON');
        } elseif($type == 'contrast') {
            $schoolyear = I('schoolyear');
            $schoolterm = I('schoolterm');
            $schoolgrade = I('schoolgrade');
            $course = I('course');
            foreach ($schoolyear as $key => $value) {
                if($schoolterm[$key] == '全年') {
                    $data[$key] = $examTable->where("schoolyear='$schoolyear[$key]' AND grade='$schoolgrade[$key]'")->order('id desc')->select();
                } else {
                    $data[$key] = $examTable->where("schoolyear='$schoolyear[$key]' AND schoolterm='$schoolterm[$key]' AND grade='$schoolgrade[$key]'")->order('id desc')->select();
                }
            }

            foreach ($data as $num => $item) {
                foreach ($item as $key => $value) {
                    $ZValueObj[$num][$key] = new \Admin\Model\ZValueData($value['uploaddate'], $value['fullname']);
                    $ZValueArr[$num][$key] = $ZValueObj[$num][$key]->getZValueData();
                    foreach ($ZValueArr[$num][$key] as $schoolName => $score) {
                        $ZValueData[$num][$key]['schoolyear'] = $value['schoolyear'];
                        $ZValueData[$num][$key]['schoolterm'] = $value['schoolterm'];
                        $ZValueData[$num][$key]['examname'] = $value['examname'];
                        $ZValueData[$num][$key]['schoolName'][] = $schoolName;
                        $ZValueData[$num][$key]['score'][] = $score[$course[$num]];
                    }
                }
            }

            $this->ajaxReturn (json_encode($ZValueData),'JSON');
        }
    }

    public function ajax_get_scattervalue(){
        $fullname = I('fullname');
        $course = I('course');

        $examTable = M('exam');
        $data = $examTable->where("fullname='$fullname'")->order('id desc')->select();


        if(!empty($data)) {
            $date = $data[0]['uploaddate'];

            // 获取考试数据目录
            $scatterValueObj = new \Admin\Model\ScatterValueData($date, $fullname, $course);
            $scatterValueData = $scatterValueObj->getScatterValueData();
            
            $this->ajaxReturn (json_encode($scatterValueData),'JSON');
        }
    }
}