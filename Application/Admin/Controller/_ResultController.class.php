<?php
namespace Admin\Controller;
use Think\Controller;

class ResultController extends Controller {
    public function index(){

        if (!session('?username')) {
            redirectUrl('admin');
        }
        
        $username = session('username');

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('xlsx', 'xls');// 设置附件上传类型
        $upload->rootPath  =      dirname(dirname(dirname(dirname(__FILE__)))); // 设置附件上传根目录
        $upload->savePath  =      '/TMP/'; // 设置附件上传（子）目录
        // 上传文件 
        $fileInfo   =   $upload->upload();
        if(!$fileInfo) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功 获取上传文件信息
            foreach($fileInfo as $file){
                $xlsPath = $file['savepath'].$file['savename'];
            }
        }

        date_default_timezone_set('Asia/Shanghai');

        $xlsRoot = dirname(dirname(dirname(dirname(__FILE__))));
        $filePath = $xlsRoot.'/'.$xlsPath;

        vendor("PHPExcel.PHPExcel.IOFactory");
        $objPHPExcel = \PHPExcel_IOFactory::load($filePath);
        $objWorksheet = $objPHPExcel->getSheet(0);

        $keys = array();
        $rets = array();

        foreach($objWorksheet->getRowIterator() as $kr => $row){

            $cellIterator = $row->getCellIterator();
            if ($kr == 1){
                foreach($cellIterator as $kc => $cell){
                    $keys[] = $cell->getValue();
                }
            }
            else {
                foreach($cellIterator as $kc => $cell){
                    if ($kc == 0){
                        $currNo = $cell->getValue();
                        if(!empty($currNo)) {
                            $rets[$currNo][$keys[$kc]] = $cell->getValue();
                        } else {
                            break;
                        }
                    }
                    else{
                        $rets[$currNo][$keys[$kc]] = $cell->getValue();
                    }
                }       
            }
        }

        $adminCss = getLoadCssStatic('admin_other');
        $adminJs = getLoadJsStatic('admin_other');
        $this->assign('adminCss', $adminCss);
        $this->assign('adminJs', $adminJs);

        $this->assign('username', $username);

        $this->assign("keys", $keys);
        $this->assign("rets", $rets);
        $this->display();
    }
}