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
            $schooltype = I('schooltype');
            $schoolyear = I('schoolyear');
            $schoolterm = I('schoolterm');
            $schoolgrade = I('schoolgrade');
            $course = I('course');

            if($schoolterm == '全年') {
                $data = $examTable->where("schoolyear='$schoolyear' AND grade='$schoolgrade'")->order('id desc')->select();
            } else {
                $data = $examTable->where("schoolyear='$schoolyear' AND schoolterm='$schoolterm' AND grade='$schoolgrade'")->order('id desc')->select();
            }

            // 获取学校列表
            $schoolInfoObj = new \Admin\Model\SchoolInfoData();
            $schoolInfoData = $schoolInfoObj->getSchoolData($schooltype);

            foreach ($data as $key => $value) {
                $ZValueObj[$key] = new \Admin\Model\ZValueData($value['uploaddate'], $value['fullname']);
                $ZValueArr[$key] = $ZValueObj[$key]->getZValueData();
                $courselistArr = [];
                if(count($ZValueArr[$key]) != count($schoolInfoData['schoolList'])) {
                    foreach ($schoolInfoData['schoolList'] as $name) {
                        if(!empty($ZValueArr[$key][$name])) {
                            foreach ($ZValueArr[$key][$name] as $coursename => $cont) {
                                $courselistArr[] = $coursename;
                            }
                            break;
                        }
                    }
                    foreach ($schoolInfoData['schoolList'] as $name) {
                        if(empty($ZValueArr[$key][$name])) {
                            $ZValueArr[$key][$name] = [];
                            foreach ($courselistArr as $coursename) {
                                $ZValueArr[$key][$name][$coursename] = '0';
                            }
                        }
                    }
                }
                foreach ($ZValueArr[$key] as $schoolName => $score) {
                    $ZValueData[$key]['schoolterm'] = $value['schoolterm'];
                    $ZValueData[$key]['examname'] = $value['examname'];
                }
            }

            foreach ($ZValueArr as $key => $value) {
                foreach ($schoolInfoData['schoolList'] as $name) {
                    $ZValueSortArr[$key][$name] = $value[$name];
                }
            }

            foreach ($ZValueSortArr as $key => $value) {
                foreach ($value as $schoolName => $score) {
                    $ZValueData[$key]['schoolName'][] = $schoolName;
                    $ZValueData[$key]['score'][] = $score[$course];
                }
            }

            $this->ajaxReturn (json_encode($ZValueData),'JSON');
        } elseif($type == 'contrast') {
            $schooltype = I('schooltype');
            $schoolyear = I('schoolyear');
            $schoolterm = I('schoolterm');
            $schoolgrade = I('schoolgrade');
            $course = I('course');
            foreach ($schoolyear as $key => $value) {
                $data[$key] = $examTable->where("schoolyear='$schoolyear[$key]' AND schoolterm='$schoolterm[$key]' AND grade='$schoolgrade[$key]'")->order('id desc')->select();
            }

            // 获取学校列表
            $schoolInfoObj = new \Admin\Model\SchoolInfoData();
            $schoolInfoData = $schoolInfoObj->getSchoolData($schooltype);

            foreach ($data as $num => $item) {
                foreach ($item as $key => $value) {
                    $ZValueObj[$num][$key] = new \Admin\Model\ZValueData($value['uploaddate'], $value['fullname']);
                    $ZValueArr[$num][$key] = $ZValueObj[$num][$key]->getZValueData();

                    $courselistArr[$num] = [];
                    if(count($ZValueArr[$num][$key]) != count($schoolInfoData['schoolList'])) {
                        foreach ($schoolInfoData['schoolList'] as $name) {
                            if(!empty($ZValueArr[$num][$key][$name])) {
                                foreach ($ZValueArr[$num][$key][$name] as $coursename => $cont) {
                                    $courselistArr[$num][] = $coursename;
                                }
                                break;
                            }
                        }
                        foreach ($schoolInfoData['schoolList'] as $name) {
                            if(empty($ZValueArr[$num][$key][$name])) {
                                $ZValueArr[$num][$key][$name] = [];
                                foreach ($courselistArr[$num] as $coursename) {
                                    $ZValueArr[$num][$key][$name][$coursename] = '0';
                                }
                            }
                        }
                    }
                    foreach ($ZValueArr[$num][$key] as $schoolName => $score) {
                        $ZValueData[$num][$key]['schoolterm'] = $value['schoolterm'];
                        $ZValueData[$num][$key]['examname'] = $value['examname'];
                    }

                    foreach ($ZValueArr[$num][$key] as $schoolName => $score) {
                        $ZValueData[$num][$key]['schoolyear'] = $value['schoolyear'];
                        $ZValueData[$num][$key]['schoolterm'] = $value['schoolterm'];
                        $ZValueData[$num][$key]['examname'] = $value['examname'];
                    }
                }

                foreach ($ZValueArr[$num] as $key => $value) {
                    foreach ($schoolInfoData['schoolList'] as $name) {
                        $ZValueSortArr[$num][$key][$name] = $value[$name];
                    }
                }

                foreach ($ZValueSortArr[$num] as $key => $value) {
                    foreach ($value as $schoolName => $score) {
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