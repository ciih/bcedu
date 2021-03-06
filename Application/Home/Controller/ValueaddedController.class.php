<?php
namespace Home\Controller;
use Think\Controller;
class ValueaddedController extends Controller {
    public function index(){
        if (!session('?username')) {
            redirectUrl('index');
        }
        
        $username = session('username');
        $schoolgroup = session('schoolgroup');
        $role = session('role');
        
        // 增值性评价
        $loadCss     = getLoadCssStatic('detail');
        $loadJs      = getLoadJsStatic('detail');
        $loadPageJs  = getLoadPageJsStatic('valueadded');

        $page = strtolower(CONTROLLER_NAME);
        $type = I('type');
        $schoolname = I('schoolname');

        $examTable = M('exam');
        $data = $examTable->where("schooltype='$type'")->order('id desc')->getField('schoolyear', true);
        
        $schoolyear = array_unique($data);

        $this->assign('loadCss', $loadCss);
        $this->assign('loadJs', $loadJs);
        $this->assign('loadPageJs', $loadPageJs);

        $this->assign('username', $username);
        $this->assign('schoolgroup', $schoolgroup);
        $this->assign('role', $role);

        $this->assign('page', $page);
        $this->assign('type', $type);
        $this->assign('schoolname', $schoolname);
        
        $this->assign('schoolyear', $schoolyear);

        $this->display();
    }

    public function single(){
        if (!session('?username')) {
            redirectUrl('index');
        }
        
        $username = session('username');
        $schoolgroup = session('schoolgroup');
        $role = session('role');
        
        // 增值性评价
        $loadCss     = getLoadCssStatic('detail');
        $loadJs      = getLoadJsStatic('detail');
        $loadPageJs  = getLoadPageJsStatic('valueaddedsingle');

        $this->assign('username', $username);
        $this->assign('schoolgroup', $schoolgroup);
        $this->assign('role', $role);

        $page = strtolower(CONTROLLER_NAME);
        $type = I('type');
        $schoolname = I('schoolname');

        $this->assign('loadCss', $loadCss);
        $this->assign('loadJs', $loadJs);
        $this->assign('loadPageJs', $loadPageJs);

        $this->assign('page', $page);
        $this->assign('type', $type);
        $this->assign('schoolname', $schoolname);
        
        $this->assign('schoolyear', $schoolyear);

        $this->display();
    }

    public function multi(){
        if (!session('?username')) {
            redirectUrl('index');
        }
        
        $username = session('username');
        $schoolgroup = session('schoolgroup');
        $role = session('role');
        
        // 增值性评价
        $loadCss     = getLoadCssStatic('detail');
        $loadJs      = getLoadJsStatic('detail');
        $loadPageJs  = getLoadPageJsStatic('valueaddedmulti');

        $this->assign('username', $username);
        $this->assign('schoolgroup', $schoolgroup);
        $this->assign('role', $role);

        $page = strtolower(CONTROLLER_NAME);
        $type = I('type');
        $schoolname = I('schoolname');

        $this->assign('loadCss', $loadCss);
        $this->assign('loadJs', $loadJs);
        $this->assign('loadPageJs', $loadPageJs);

        $this->assign('page', $page);
        $this->assign('type', $type);
        $this->assign('schoolname', $schoolname);
        
        $this->assign('schoolyear', $schoolyear);

        $this->display();
    }

    public function contrast(){
        if (!session('?username')) {
            redirectUrl('index');
        }
        
        $username = session('username');
        $schoolgroup = session('schoolgroup');
        $role = session('role');
        
        // 增值性评价
        $loadCss     = getLoadCssStatic('detail');
        $loadJs      = getLoadJsStatic('detail');
        $loadPageJs  = getLoadPageJsStatic('valueaddedcontrast');

        $this->assign('username', $username);
        $this->assign('schoolgroup', $schoolgroup);
        $this->assign('role', $role);

        $page = strtolower(CONTROLLER_NAME);
        $type = I('type');
        $schoolname = I('schoolname');

                
        // 获取考试数据目录
        /*$zValueObj = new \Admin\Model\ZValueData($date, $foldername);
        $zValueData = $zValueObj->getZValueData();

        var_export('===========$zValueData==========');
        var_export($zValueData);
        exit();*/

        $this->assign('loadCss', $loadCss);
        $this->assign('loadJs', $loadJs);
        $this->assign('loadPageJs', $loadPageJs);

        $this->assign('page', $page);
        $this->assign('type', $type);
        $this->assign('schoolname', $schoolname);
        
        $this->assign('schoolyear', $schoolyear);

        $this->display();
    }
}