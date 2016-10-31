<?php
/**
 * 商家客户端专用接口控制器
 * 
 * @author leo8705
 *
 */

class PStoreController extends PAPIController {

    /**
     * 获取店铺列表
     *
     *
     */
    public function actionList() {
    	$list = Yii::app()->db->createCommand()
    	->select('id,name,category_id,mobile,CONCAT("'.ATTR_DOMAIN.'/",logo) as logo,province_id,city_id,district_id,street,zip_code,status,open_time,delivery_time')
    	->from(Supermarkets::model()->tableName())
    	->where('member_id=:member_id AND status='.Supermarkets::STATUS_ENABLE,array(':member_id'=>$this->member))
    	->queryAll();
    	
    	
    	foreach ($list as $k=>$v){
    		$list[$k]['province_name'] = Region::getName($v['province_id']);
    		$list[$k]['city_name'] = Region::getName($v['city_id']);
    		$list[$k]['district_name'] = Region::getName($v['district_id']);
    		$list[$k]['status_name'] = Supermarkets::getStatus($v['status']);
    	}

    	$this->_success($list);
    	 
    }
    
    
    /**
     * 获取生鲜机列表
     *
     *
     */
    public function actionFreshMachineList() {
    	$list = Yii::app()->db->createCommand()
    	->select('id,name,category_id,mobile,CONCAT("'.ATTR_DOMAIN.'/",thumb) as thumb,province_id,city_id,district_id,address,status')
    	->from(FreshMachine::model()->tableName())
    	->where('member_id=:member_id AND status='.Supermarkets::STATUS_ENABLE,array(':member_id'=>$this->member))
    	->queryAll();
    	 
    	 
    	foreach ($list as $k=>$v){
    		$list[$k]['province_name'] = Region::getName($v['province_id']);
    		$list[$k]['city_name'] = Region::getName($v['city_id']);
    		$list[$k]['district_name'] = Region::getName($v['district_id']);
    		$list[$k]['status_name'] = FreshMachine::getStatus($v['status']);
    	}
    
    	$this->_success($list);
    
    }
    

    public function actionCateList(){
    	$list = StoreCategory::getCategoryList();
        $list = array_values($list);
    	$this->_success($list);
    }
    
    /**
     * 申请、添加、修改超市门店
     */
    public function actionSuperSave(){
    	try {
    		$post = $this->getParams();
    		$isCreate = $post['act'];
    		if($isCreate == 1){
    			$model = new Supermarkets();
    			$model->member_id = $this->member;
    			$model->scenario = 'create';
    			$model->attributes = $post;
    			$model->partner_id = $this->partner;
    			$memberTotalPayPreStoreLimit = Tool::getConfig('amountlimit', 'memberTotalPayPreStoreLimit');
    			$model->max_amount_preday = !empty($memberTotalPayPreStoreLimit)?$memberTotalPayPreStoreLimit:'';
    			$model->create_time = time();
    			$saveDir = 'Supermarkets/' . date('Y/n/j');
                $_FILES = Tool::appUploadPic($model);
    			$model = UploadedFile::uploadFile($model, 'logo', $saveDir, Yii::getPathOfAlias('att'));
    		}
    		if($isCreate == 2){
    			$model = $this->store;
    			
    			$old_modle = clone $model;
    			$check_field = array('name','category_id','mobile','logo','type','province_id','city_id','district_id','street');
    			
    			$model->scenario = 'update';
    			if (empty($model)) {
    				$this->_error( Yii::t('store', '请先添加门店'));
    			}
    			$oldFile = $model->logo;
    			$model->attributes = $post;
    			$saveDir = 'superStore/' . date('Y/n/j');
                $_FILES = Tool::appUploadPic($model);
    			$model = UploadedFile::uploadFile($model, 'logo', $saveDir, Yii::getPathOfAlias('att'), $oldFile);

    			
    			foreach ($check_field as $val){
    				if ($old_modle->$val!=$model->$val) {
    					$model->status = Supermarkets::STATUS_APPLY;
    					break;
    				}
    			}
    			
    		}
    		if($model->save()){
    			if(isset($oldFile)){
    				UploadedFile::saveFile('logo', $model->logo, $oldFile, true);
    				if($model->status == Supermarkets::STATUS_APPLY) Yii::app()->db->createCommand()->update(SuperGoods::model()->tableName(), array('status' => SuperGoods::STATUS_DISABLE), 'super_id=:id', array(':id' =>$model->id));
    					
    				ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeUpdate, $model->id, '修改超市门店信息:' . $model->name);
    				$this->_success( Yii::t('store', '修改超市门店信息成功'));
    			}else{
    				UploadedFile::saveFile('logo', $model->logo);
    				$this->_success(Yii::t('store', '添加超市门店成功'));
    			}
    
    		}else{
//                var_dump($model->geterrors());
    			$this->_error(Yii::t('store', isset($oldFile)?'修改超市门店失败':'添加超市门店失败'));
    		}
    
    	}catch (Exception $e){
    
    		$this->_error($e->getMessage());
    	}
    }
    
    
    /**
     * 查看当前门店信息
     */
    public function actionView(){
    	try{
    		$post = $this->getParams();
            if ($this->getParam('onlyTest')==1) {
                $superId = $this->getParam('sid');
            }else{
                $superId =  $this->rsaObj->decrypt($this->getParam('sid'))*1;
            }

    		$model = Supermarkets::model()->findByPk($superId);
    		$cate = StoreCategory::model()->findByPk($model->category_id);
    		
    		$store = Yii::app()->db->createCommand()
    		->select('t.*,c.name as cate_name')
    		->from(Supermarkets::model()->tableName().' as t')
    		->leftJoin(StoreCategory::model()->tableName().' as c','t.category_id=c.id')
    		->where('t.member_id=:member_id AND t.id=:id',array(':member_id'=>$this->member,':id'=>$superId))
    		->queryRow();
    		
    		if (!$store) {
    			$this->_error( Yii::t('store', '请先添加门店'));
    		}else{
    			$cate = array();
    			$cate['id'] = $store['category_id'];
    			$cate['name'] = $store['cate_name'];
    			$this->_success(array('model'=>$store,'cate'=>$cate));
    		}
    	}catch (Exception $e){
    
    		$this->_error($e->getMessage());
    	}
    
    }
    
    
    /**
     * 添加或更新分类接口
     */
    public function actionCateSave(){
    	try{
    		$post = $this->getParams();
    		$this->_checkStore();
    		$store = $this->store;
    
    		if ($store->member_id!=$this->member) {
    			$this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
    		}
    
    		if ($post['act']==1) {
    			$cate = new GoodsCategory();
    			$cate->name = $post['name'];
    			$cate->member_id = $this->member;
    			$cate->save();
    		}
    
    		if ($post['act']==2 && !empty($post['cateId'])) {
    			$cate = GoodsCategory::model()->findByPk($post['cateId']);
    			 
    			if (empty($cate) || $cate->member_id!=$this->member) {
    				$this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
    			}
    			 
    			$cate->name = $post['name'];
    			$cate->member_id = $this->member;
    			$cate->save();
    		}
    
    		$this->_success('成功');
    	}catch (Exception $e){
    		$this->_error($e->getMessage());
    	}
    }

	/*
	 * 配送设定
	 * @param int is_delivery 配送方式
	 *
	 */
	public function actionSetUpDelivery()
	{
		$is_delivery = $this->getParam('is_delivery');
		$sid         = $this->getParam('sid');
		$post        = $this->getParams();

		$model = $this->store;
		$model->attributes = $post;

		if(empty($sid)) {
			$this->_error(ErrorCode::getErrorStr(ErrorCode::COMMON_PARAMS_LESS),ErrorCode::COMMON_PARAMS_LESS);
		}
		//保存数据
		if($post['act']==2 && isset($is_delivery) && array_key_exists($is_delivery,$model->DISTRIBUTION_OPTION)) {

			if(!$model->save(false)){
				$this->_error(ErrorCode::getErrorStr(ErrorCode::SAVE_DATA_FALSE),ErrorCode::SAVE_DATA_FALSE);
			}
		}

		$store = Supermarkets::model()->find('id=:id and member_id=:member_id',array(':id' => $sid,':member_id' => $this->member))->attributes;

		$this->_success($store,'SetUpDelivery');
	}

	/*
	 * 驻店配送员列表
	 *
	 */

	public function actionDeliverList()
	{
		$sid = $this->getParam('sid');
		if(empty($sid)) {
			$this->_error(ErrorCode::getErrorStr(ErrorCode::COMMON_PARAMS_LESS),ErrorCode::COMMON_PARAMS_LESS);
		}

		$deliverList = Yii::app()->db->createCommand()
			->select('c.id,c.mobile,c.name,c.status,c.bind_store,c.create_time')
			->from(Supermarkets::model()->tableName().' as t')
			->leftJoin(Distribution::model()->tableName().' as c','t.id=c.bind_store')
			->where('t.member_id=:member_id AND t.id=:id',array(':member_id'=>$this->member,':id'=>$sid))
			->queryAll();

		if(empty($deliverList)) {
			$this->_error('没有配送员信息',ErrorCode::GOOD_STOCK_NOT_EXIST);
		}

		$this->_success($deliverList,'DeliverList');
	}

	/*
	 *解绑配送员
	 *@param deliver_id 配送员id
	 */
	public function actionUnbundDeliver()
	{
		$deliver_id = $this->getParam('deliver_id');
		$sid        = $this->getParam('sid');

		if(empty($sid) || empty($deliver_id)) {
			$this->_error(ErrorCode::getErrorStr(ErrorCode::COMMON_PARAMS_LESS),ErrorCode::COMMON_PARAMS_LESS);
		}

		$model = $this->store;
		if(empty($model->id)){
			$this->_error('找不到商店信息',ErrorCode::GOOD_STOCK_NOT_EXIST);
		}

		//检查配送员信息
		$info = Distribution::model()->find('id=:id AND bind_store=:bind_store',array(':id' => intval($deliver_id),':bind_store' =>$sid));
		if(empty($info)){
			$this->_error('商店没有此配送员信息!',ErrorCode::GOOD_STOCK_NOT_EXIST);
		}
		$info->bind_store = NULL;
		if(!$info->save(false)){
			$this->_error(ErrorCode::getErrorStr(ErrorCode::SAVE_DATA_FALSE),ErrorCode::SAVE_DATA_FALSE);
		}

		$this->_success(array('deliver_id' => $deliver_id),'DeliverList');

	}


   
}