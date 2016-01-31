<?php
namespace Home\Controller;
use Think\Controller;

class WordController extends Controller {
	const KEY = "__gen_data__";

	private function getSign($json, $case){
		return substr(md5($json."--".$case."--".self::KEY), 16, 8);
	}

    public function index(){
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

		$dataStr = base64_encode(json_encode($data1));
		$case = "line1";

		$sign = $this->getSign($dataStr, $case);

        $phantomBaseDir = dirname(dirname(dirname(dirname(__FILE__))))."/Phantom/";

        do{
        	$workDir = $phantomBaseDir.time()."/";
        }while(file_exists($workDir));

        $old = umask(0);
        mkdir($workDir, 0777);
        umask($old);

        $jsTplFile = $phantomBaseDir."/data.tpl.js";
        $jsFile = $workDir."/data.js";

        $baseUrl = "http://chenhong.bcedu.com/index.php";
        $picFile = "data.pic.png";

		$js = file_get_contents($jsTplFile);
		$js = str_replace(array(
			"{baseurl}", 
			"{workdir}", 
			"{pic}", 
			"{data}",
			"{case}",
			"{sign}"
		), array(
			$baseUrl, 
			$workDir, 
			$picFile,
			rawurlencode($dataStr),
			$case,
			$sign	
		), $js);

        file_put_contents($jsFile, $js);
        exec("D:/server/nodejs/node /usr/local/bin/phantomjs ".$jsFile); 

        // 等待截图完成
        sleep(2);

        $image = file_get_contents($workDir."test.png");

        vendor("PHPWord.PHPWord");
       
        $PHPWord = new \PHPWord();
        $wordBaseDir = dirname(dirname(dirname(dirname(__FILE__))))."/Tmp/";
		$document = $PHPWord->loadTemplate($wordBaseDir.'Template.doc');

		$document->setValue('valuea', $data1["语言知识"]["G5"]);
		$document->setValue('valueb', $data1["文学常识和名句名篇"]["G5"]);
		$document->setValue('valuec', $data1["古代诗文阅读"]["G5"]);
		$document->setValue('valued', $data1["现代文阅读"]["G5"]);
		$document->setValue('valuee', $data1["写作"]["G5"]);

		$document->setValue('valuea1', $data1["语言知识"]["G4"]);
		$document->setValue('valueb1', $data1["文学常识和名句名篇"]["G4"]);
		$document->setValue('valuec1', $data1["古代诗文阅读"]["G4"]);
		$document->setValue('valued1', $data1["现代文阅读"]["G4"]);
		$document->setValue('valuee1', $data1["写作"]["G4"]);

		$document->setValue('valuea2', $data1["语言知识"]["G3"]);
		$document->setValue('valueb2', $data1["文学常识和名句名篇"]["G3"]);
		$document->setValue('valuec2', $data1["古代诗文阅读"]["G3"]);
		$document->setValue('valued2', $data1["现代文阅读"]["G3"]);
		$document->setValue('valuee2', $data1["写作"]["G3"]);

		$document->setValue('valuea3', $data1["语言知识"]["G2"]);
		$document->setValue('valueb3', $data1["文学常识和名句名篇"]["G2"]);
		$document->setValue('valuec3', $data1["古代诗文阅读"]["G2"]);
		$document->setValue('valued3', $data1["现代文阅读"]["G2"]);
		$document->setValue('valuee3', $data1["写作"]["G2"]);

		// set alt text to a picture http://accessproject.colostate.edu/udl/modules/word/tut_alt_text.php?display=pg_2
		// 设置一张占位图，此图大小和最终大小一致。然后把土的alt text设置为变量名如${placeholder}, 调用下面方法即可
    	$document->setImageValue($document->getImgFileName($document->seachImagerId("placeholder")), $workDir.$picFile);

		$document->save($wordBaseDir.'chenhong.doc');
		header("Content-Disposition: attachment; filename='chenhong.doc'");
		echo file_get_contents($wordBaseDir.'chenhong.doc');
		unlink($wordBaseDir.'chenhong.doc');  // remove temp file		
        @unlink($workDir.$picFile);
        @unlink($jsFile);
        @rmdir($workDir);
    }

    public function datapic(){
		$data = rawurldecode(I("data")); 
		$sign = I("sign");
		$case = I("case");

		if ($this->getSign($data, $case) == $sign){  
			switch ($case){
				case "line1":
				    $data = json_decode(base64_decode($data), true);

					$this->assign("xData", json_encode(array_keys($data)));

					$data2 = array();

					foreach($data as $k => $item){
						foreach ($item as $k2 => $sub){
							if (!isset($data2[$k2])){
								$data2[$k2] = array();
							}

							$data2[$k2][$k] = $sub;
						}
					}

					$this->assign("data2", $data2);
				    $this->display();
				    break;
			}
		}		 	
    }
}
