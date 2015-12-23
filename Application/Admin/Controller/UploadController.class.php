<?php
namespace Admin\Controller;
use Think\Controller;

class UploadController extends Controller {
    public function index(){

        if (!session('?username')) {
            redirectUrl('admin');
        }
        
        $username = session('username');
        $pagename = strtolower(CONTROLLER_NAME);

        $adminCss = getLoadCssStatic('admin_other');
        $adminJs = getLoadJsStatic('admin_other');
        $this->assign('adminCss', $adminCss);
        $this->assign('adminJs', $adminJs);

        $this->assign('username', $username);
        $this->assign('pagename', $pagename);

        $this->display();
    }

    public function file() {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728;// 设置附件上传大小
        $upload->exts      =     array('zip', 'rar');// 设置附件上传类型
        $upload->rootPath  =     dirname(dirname(dirname(dirname(__FILE__)))); // 设置附件上传根目录
        $upload->savePath  =      '/TMP/'; // 设置附件上传（子）目录
        // 上传文件 
        $fileInfo   =   $upload->upload();
        if(!$fileInfo) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功 获取上传文件信息
            foreach($fileInfo as $file){
                $zipPath = $file['savepath'].$file['savename'];
            }
        }

        date_default_timezone_set('Asia/Shanghai');

        $zipRoot = dirname(dirname(dirname(dirname(__FILE__))));
        $filePath = $zipRoot.$zipPath;

        vendor("PHPZip.phpzip");

        $unzipSubname = date("Y-m-d");

        $archive  = new \PHPZip();


        $savepath  = './Data/'.$unzipSubname;
        $array     = $archive->GetZipInnerFilesInfo($filePath);
        $filecount = 0;
        $dircount  = 0;
        $failfiles = array();

        set_time_limit(0);  // 修改为不限制超时时间(默认为30秒)
         
        for($i=0; $i<count($array); $i++) {
            if($array[$i][folder] == 0){
                if($archive->unZip($filePath, $savepath, $i) > 0){
                    $filecount++;
                }else{
                    $failfiles[] = $array[$i][filename];
                }
            }else{
                $dircount++;
            }
        }
     
        set_time_limit(0);

        $this->assign("waitSecond","5");

        if(count($failfiles) > 0) {// 上传错误提示错误信息
            $this->error('上传文件解压失败，请返回重新上传！');
        }

    }
}