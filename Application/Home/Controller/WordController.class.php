<?php
namespace Home\Controller;
use Think\Controller;

ini_set("display_errors", true);

class WordController extends Controller {
    public function index(){
		date_default_timezone_set('Asia/Shanghai');

		$data1 = array(
			"语言知识" => array(
				"G5" => 0.63, "G4" => 0.80 , "G3" => 0.65 , "G2" => 0.51
			), 
			"文学常识和名句名篇" => array(
				"G5" => 0.73 , "G4" => 0.92 , "G3" => 0.78 , "G2" => 0.54
			), 
			"古代诗文阅读" => array(
				"G5" => 0.58 , "G4" => 0.76 , "G3" => 0.60 , "G2" => 0.46
			), 		
			"现代文阅读" => array(
				"G5" => 0.74 , "G4" => 0.86 , "G3" => 0.76 , "G2" => 0.66 
			),
			"写作" => array(
				"G5" => 0.73 , "G4" => 0.77 , "G3" => 0.73 , "G2" => 0.71
			)		
		);

        $phantomBaseDir = dirname(dirname(dirname(dirname(__FILE__))))."/Phantom/";

        do{
        	$workDir = $phantomBaseDir.time()."/";
        }while(file_exists($workDir));

        $old = umask(0);
        mkdir($workDir, 0777);
        umask($old);

        $jsTplFile = $phantomBaseDir."/data.tpl.js";
        $jsFile = $workDir."/data.js";

        $baseUrl = "http://liuguanyu.bcedu.com:9001";
        $picFile = "data.pic.png";

		$js = file_get_contents($jsTplFile);
		$js = str_replace(array("{baseurl}", "{workdir}", "{pic}"), array($baseUrl, $workDir, $picFile), $js);

        file_put_contents($jsFile, $js);
        exec("/usr/local/bin/node /usr/local/bin/phantomjs ".$jsFile); 

        // 等待截图完成
        sleep(1);

        $image = file_get_contents($workDir."test.png");

        @unlink($workDir.$picFile);
        @unlink($jsFile);
        @rmdir($workDir);

        vendor("PHPWord.PHPWord");
       
        $PHPWord = new \PHPWord();
        $wordBaseDir = dirname(dirname(dirname(dirname(__FILE__))))."/Tmp/";
		$document = $PHPWord->loadTemplate($wordBaseDir.'Template.docx');

		$document->setValue('Value1', $data1["语言知识"]["G5"]);
		$document->setValue('Value2', $data1["文学常识和名句名篇"]["G5"]);
		$document->setValue('Value3', $data1["古代诗文阅读"]["G5"]);
		$document->setValue('Value4', $data1["现代文阅读"]["G5"]);
		$document->setValue('Value5', $data1["写作"]["G5"]);

		$document->save($wordBaseDir.'hello.docx');
		header("Content-Disposition: attachment; filename='hello.docx'");
		echo file_get_contents($wordBaseDir.'hello.docx');
		unlink($wordBaseDir.'hello.docx');  // remove temp file
    }

    public function datapic(){
		date_default_timezone_set('Asia/Shanghai');

		$data1 = array(
			"语言知识" => array(
				"G5" => 0.63, "G4" => 0.80 , "G3" => 0.65 , "G2" => 0.51
			), 
			"文学常识和名句名篇" => array(
				"G5" => 0.73 , "G4" => 0.92 , "G3" => 0.78 , "G2" => 0.54
			), 
			"古代诗文阅读" => array(
				"G5" => 0.58 , "G4" => 0.76 , "G3" => 0.60 , "G2" => 0.46
			), 		
			"现代文阅读" => array(
				"G5" => 0.74 , "G4" => 0.86 , "G3" => 0.76 , "G2" => 0.66 
			),
			"写作" => array(
				"G5" => 0.73 , "G4" => 0.77 , "G3" => 0.73 , "G2" => 0.71
			)		
		);   
		
        $this->display();		 	
    }
}
