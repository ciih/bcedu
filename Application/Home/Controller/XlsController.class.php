<?php
namespace Home\Controller;
use Think\Controller;

function padStr($str, $len){
	$pad = "";
	for($i = 0; $i < $len; ++$i){
		$pad .= "0";
	}

	$str = $pad.$str;
	return substr($str, -1 * $len);
}

class XlsController extends Controller {
    public function index(){
		date_default_timezone_set('Asia/Shanghai');

        $xlsDir = dirname(dirname(dirname(dirname(__FILE__))))."/Tmp";
        $file = $xlsDir."/47.xlsx";

        vendor("PHPExcel.PHPExcel.IOFactory");
        $objPHPExcel = \PHPExcel_IOFactory::load($file);
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
	        			$rets[$currNo] = array();
	        		}
	        		else{
	        			$rets[$currNo][$keys[$kc]] = $cell->getValue();
	        		}
	        	} 		
        	}
        }

        $this->assign("keys", $keys);
        $this->assign("rets", $rets);
        $this->display();
    }
}
