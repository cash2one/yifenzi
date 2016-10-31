<?php
/**
 * 售货机控制器
 * 
 * 需要使用一机一密解密
 * 
 * @author hao.liang
 */
class MMachineController extends VMAPIController 
{
    protected $encryptField;
//     protected $prefix = 'VD';//检查验证码前缀
//     protected $cache = 'fileCache';//使用什么缓存
    
    public function actionActivation()
    {
    	try {
            if ($this->getParam('onlyTest')==1) {
                $post = $this->getParams();
            }else{
                $this->params = array('shopId','devicePassword','activeCode','deviceID');
                $requiredFields = array('shopId','devicePassword','activeCode','deviceID');
                $decryptFields = array('shopId','devicePassword','activeCode','deviceID');
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            }
	        $className = $this->className;
	        $goodsClassName = $this->goodsClassName;
	        
	        //判断商家编码是否存在并且激活码是否一致
	        if($this->vending['activation_code']!=$post['activeCode'])
	        	
	        	$this->_error(Yii::t('apiModule.vendingMachine','激活码不正确'));

      		if ($this->vending['is_activate'] == $className::IS_ACTIVATE_YES)
      			$this->_error(Yii::t('apiModule.vendingMachine','售货机已激活'));
      		
      		//生成公钥 私钥
      		$keyArr = Fun::createRsaKey($this->vending['activation_code']);
      		
      		//将公钥 私钥 ，密码存入数据表
      		$devicePassword = isset($post['devicePassword']) ? $post['devicePassword'] : '';
      		$deviceID = isset($post['deviceID']) ? $post['deviceID'] : '';
      		//修改
      		$time = time();
      		$result = Yii::app()->db->createCommand()
      			->update($className::model()->tableName(),
	      				array(	'is_activate'=>$className::IS_ACTIVATE_YES,
	      						'status'=>$className::STATUS_ENABLE,
	      						'device_id' => $deviceID,
	      						'password' => RSA::passwordEncrypt($devicePassword),
	      						'public_key' =>$keyArr['publicKey'],
	      						'private_key'=>$keyArr['privateKey'],
	      						'setup_time'=>$time,
	      						'update_time'=>$time,
	      				),
      					'code=:code', array(':code' => $this->vending['code'])
      				);
      		if (!$result) $this->_error(Yii::t('apiModule.vendingMachine','激活失败'));
      		//获取省市地区id
      		$in = $this->vending['province_id'] . ',' . $this->vending['city_id'] . ',' . $this->vending['district_id'];  //拼接查询条件
      		$sql = "SELECT name FROM {{region}} WHERE id IN($in) ORDER BY FIND_IN_SET(id,'$in')";
      		$region = Yii::app()->db->createCommand($sql)->queryAll();

      		//返回的数据
      		$data = array(
      				'id'=>$this->vending['id'],			//省id
      				'provinceID'=>$this->vending['province_id'],			//省id
      				'cityId'=>$this->vending['city_id'],					//市id
      				'districtId'=>$this->vending['district_id'],			//地区id
      				'moneyType'=>$this->vending['symbol'],					//币种
      				'shopName'=>$this->vending['name'],						//贩卖机名称
      				'discount'=>'0',										//折扣目前没做处理
      				'netKey'=>$keyArr['publicKeyApk']						//访问接口
      		);
      		if($region){
      			$data['provinceName'] = $region[0]['name'];
      			$data['cityName'] = $region[1]['name'];
      			$data['districtName'] = $region[2]['name'];
      		}else{
      			$data['provinceName'] = 0;
      			$data['cityName'] = 0;
      			$data['districtName'] = 0;
      		}
      		$this->_success($data);
      	}catch (Exception $e){
        	$this->_error($e->getMessage());
        };
    }
    
    
    /**
     * 更新机器管理密码接口
     */
    public function actionUpdateManPwd(){
    	try {
            if ($this->getParam('onlyTest')==1) {
                $post = $this->getParams();
            }else{
                $this->params =	  array('shopId','originalPwd','newPwd');
                $requiredFields = array('shopId','originalPwd','newPwd');
                $decryptFields =  array('shopId','originalPwd','newPwd');
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
            }

	    	
	    	$className = $this->className;
	    	$goodsClassName = $this->goodsClassName;

	    	if($this->vending['status'] != $className::STATUS_ENABLE || $this->vending['is_activate'] != $className::IS_ACTIVATE_YES)
	    		$this->_error(Yii::t('apiModule.vendingMachine','售货机没激活或没启用'));
	    	
	    	if(!Validator::checkPassword($post['newPwd'])){
	    		$this->_error(Yii::t('apiModule.vendingMachine','密码包含特殊字符'));
	    	}
	    	
	    	if($this->vending['password']!=RSA::passwordEncrypt($post['originalPwd']))
	    		$this->_error(Yii::t('apiModule.vendingMachine','原密码错误'));
	    	
	    	if($this->vending['password']==RSA::passwordEncrypt($post['newPwd']))
	    		$this->_error(Yii::t('apiModule.vendingMachine','原密码和新密码一致'));
	    	//修改密码
	    	$result = Yii::app()->db->createCommand()
	    	->update($className::model()->tableName(),
	    			array(	'update_time'=>time(),
	    					'password'=>RSA::passwordEncrypt($post['newPwd'])
	    			),
	    			'code=:code', array(':code' => $this->vending['code'])
	    	);

	    	if (!$result) $this->_error(Yii::t('apiModule.vendingMachine','修改失败'));
	    	$this->_success();
    	}catch (Exception $e){
    		$this->_error($e->getMessage());
    	}
    }
    
    /**
     * 生鲜机闪退状态修改
     * @param int status 闪退状态
     * @param string version 版本号
     */
    public function actionUpdateStatus(){
        try {
            if ($this->getParam('onlyTest')==1) {
                $post = $this->getParams();
            }else{
                $this->params =array('shopId','status','version');
                $requiredFields = array('shopId','status','version');
                $decryptFields =  array('shopId','status','version');
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
            }

	    	
	    	$className = $this->className;
	    	$goodsClassName = $this->goodsClassName;

	    	//修改状态
	    	$result = Yii::app()->db->createCommand()
	    	->update($className::model()->tableName(),
	    			array('update_time'=>time(),
                                    'flash_back_status'=>$post['status'],
                                     'version'=>$post['version']
	    			),
	    			'code=:code', array( ':code' => $this->vending['code'])
	    	);

	    	if (!$result) $this->_error(Yii::t('apiModule.'.$goodsClassName,'修改失败'));
	    	$this->_success();
    	}catch (Exception $e){
    		$this->_error($e->getMessage());
    	}
    }
    
    /**
     * 生鲜机人员签到管理
     * @author zehui.hong
     * @param int shopId 机器编码
     * @param int mobile 手机号码
     * @param string name 姓名
     * @param int  type 类型
     */
    public function actionRecord(){
        if($this->getParam('onlyTest')==1){
            $post = array(
                'mobile' => $this->getParam('mobile'),
            'name' =>$this->getParam('name'),
                'code'=>$this->getParam('code')
            );
        }else{
                $this->params =array('shopId','mobile','name','type','code');
                $requiredFields = array('shopId','mobile','name','type','code');
                $decryptFields =  array('shopId','mobile','name','type','code');
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
        }
        $machine_id =  $this->vending['id'];
        $rs = $this->checkVerifyCode($post['mobile'], $post['code']);
        if($rs == true){
        $model = new Record();
        $model->attributes = $post;
        $model->machine_id = $machine_id;
        $model->create_time = time();
//        var_dump($model->attributes);
        if($model->save()){
            $this->_success('签到成功');
        }else{
             foreach ($model->errors as $v) {
                            $m_errors .= implode(',', $v);
             }
             $this->_error($m_errors);
        }
        }
    }
}