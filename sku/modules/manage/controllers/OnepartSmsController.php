<?php 
/**
   * 盖网一份子项目及短信后台管理
   * ==============================================
   * Deng写于2016年4月1日
   * ------------------------------------------------------------------------------------
   * 公司源码文件，未经授权不许任何使用和传播。
   * ==============================================
   **/
class OnepartSmsController extends MController{
    public function filters() {
        return array(
            'rights',
        );
    }
    public $smsUrl='';
	public $source = '';
	public $signkey = '';
	
    
    /**
       * 一分子后台短信模板配置
    **/

    public function actionYfzSmsModel() {
        $this->breadcrumbs = array(Yii::t('home', '短信通道'), Yii::t('home', '短信模板'));
        $this->_settingConfig($this->action->id);
    }
    
    
	
	/**
     * 修改配置文件
     * 文件名规则：控制器+Config 后缀，模型+ConfigForm后缀
     *
     * @param string $actionId   $this->action->id  控制器名称
     */
    private function _settingConfig($actionId) {
        $modelForm = ucfirst($actionId) . 'Form';
        $name = substr($actionId, 0, -6);
        $viewFileName = strtolower($name);
//        Tool::pr($viewFileName);
        $model = new $modelForm;
        //Ajax 验证,如果视图开启Ajax验证.这个是必须存在的
        if (isset($_POST['ajax']) && $_POST['ajax'] === $this->id . '-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        $model->setAttributes($this->getConfig($viewFileName));

        //ajax表单验证
        $this->performAjaxValidation($model);

        if (isset($_POST[$modelForm])) {
            $model->attributes = $_POST[$modelForm];
            if ($model->validate()) {
                $string = serialize($model->attributes);
                $value = WebConfig::model()->findByAttributes(array('name' => $viewFileName));
                if ($value) {
                    $webConfig = WebConfig::model();
                    $webConfig->id = $value->id;
                } else {
                    $webConfig = new WebConfig();
                }

                $webConfig->name = $viewFileName;
                $webConfig->value = $string;
//                $file = Yii::getPathOfAlias('common') . DS . 'webConfig' . DS . $viewFileName . '.config.inc';
                if ($webConfig->save()) { //向得到的文件路劲指定的文件里面插入数据
                
                    if (Tool::cache($viewFileName . 'config')->get($viewFileName)) {
                        Tool::cache($viewFileName . 'config')->set($viewFileName, $string);
                    } else {
                        Tool::cache($viewFileName . 'config')->add($viewFileName, $string);
                    }
                    //更新orderapi项目redis网站配置缓存@author xiaoyan.luo
//                    Tool::orderApiPost('config/updateCache',array('configName' => $viewFileName . 'config', 'value' => $string));
                    $this->setFlash('success', Yii::t('home', '数据保存成功'));
                    @SystemLog::record(Yii::app()->user->name . "修改配置文件：" . $this->action->id);
                } else {
                    $this->setFlash('error', Yii::t('home', '数据保存失败，请检查相关目录权限'));
                }
            }
        }
        //CActiveForm widget 参数
        $formConfig = array(
            'id' => $this->id . '-form',
            'enableAjaxValidation' => true,
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
            ),
        );
        $this->render($actionId, array('model' => $model, 'formConfig' => $formConfig));
    }
	
	/*手动发送短信*/
	public function actionYfzSendSms(){
		
		if ($this->isAjax() && $this->isPost()) {
			$phone = $this->getPost('phone');
			$mobile = explode(';', $phone);
            $sms_content=$this->getPost('sms_content');
			$apiMember = new ApiMember();
			$rs = $apiMember->sendSms($mobile, $sms_content, ApiMember::SMS_TYPE_ONLINE_ORDER,0, ApiMember::SKU_SEND_SMS);
			if ($rs['status']==200) {
                $tip = array();
                $tip['success'] = '发送短信成功！';
                exit(json_encode($tip));
            } else {
                $tip = array();
                $tip['error'] = $rs['msg'];
                exit(json_encode($tip));
            }
		}
	}
	
	/*短信发送记录*/
	/*public function actionYfzSendSmsRecord(){
		$this->breadcrumbs = array(Yii::t('home', '短信通道'), Yii::t('home', '短信发送记录'));
		$model = new SmsLog;
		$criteria = new CDbCriteria();
		$criteria->select = '*';
		$criteria->addCondition('t.type = :type');    
        $criteria->params[':type'] = SmsLog::TYPE_ONLINE_ORDER; 
		$criteria->addCondition('t.source = :source');    
        $criteria->params[':source'] = SmsLog::SKU_SEND_SMS; 
		$criteria->order = 'id desc';
		$count = $model->count($criteria);
        $pages = new CPagination($count);
	    $pages->pageSize = 10;
        $pages->applyLimit($criteria);
        $record = $model->findAll($criteria);
		
		$this->render('yfzSendSmsRecord', array(
		    'model' => $model,
            'record' => $record,
			'pages' => $pages,
        ));
	}*/
	
	 /**
     * 短信发送记录
     */
    public function actionYfzSendSmsRecord() {
        $model = new SmsLog('search');
        $model->unsetAttributes();
        if (isset($_GET['SmsLog']))
            $model->attributes = $_GET['SmsLog'];       
        $this->render('yfzSendSmsRecord', array(
            'model' => $model,
        ));
    }
	
	public function _createEncryption($json_data){
		return substr(md5($json_data.$this->signkey),5,20);
	}

}