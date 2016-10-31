<?php
/**
 * 超市模块控制器父类
 * @author leo8705
 */
class SuperController extends PController {
	
    public $layout = 'main';
    public $menu = array();
    public $breadcrumbs = array();
    public $assistantId;			//店员id
    protected $params;
    
    public function beforeAction($action) {
    	
    	//设置params
    	$params = require(ConfigDir . DS . 'params.php');
    	Yii::app()->setParams($params);
    	
    	return parent::beforeAction($action);
    }
    
    
    /**
     * 权限检查，用于操作删改数据的时候，检查是否属于该会员的数据
     * @param $superId
     * @throws CHttpException
     */
    public function checkAccess($superId) {
    	if ($superId !== $this->superId) {
    		throw new CHttpException(403,'你没有权限修改别人的数据！');
    	}
    }
    

}