<?php
/**
 * 售货机管理
 *
 * 操作(增删查改)
 * @author leo8705
 */
class MachineController extends PController
{

    public function init()
    {
        $this->pageTitle = Yii::t('partnerModule.machine', '小微企业联盟') ;
        $this->curr_menu_name = '/partner/machine/list';
    }
    
    /**
     * 检查当前售货机是否属于当前商家
     * @param unknown $model
     */
    private function _checkAccess($model){
    	if (!$model->member_id || $model->member_id != $this->curr_act_member_id) {
    		throw new CHttpException(403,Yii::t('partnerModule.machine','你没有权限修改别人的数据！'));
    		exit();
    	}
    }
    

    /**
     * 检查售货机商品数量
     * @mid
     * Enter description here ...
     */
    private function _checkVendingMachineGoodsNumber($mid){
    	$mid = $mid*1;
    	if( VendingMachineGoods::model()->count(" machine_id={$mid} AND status=".VendingMachineGoods::STATUS_ENABLE)>VendingMachineGoods::MAX_ENABLE_GOODS_NUMBER_PER_MACHINE){
    		$this->setFlash('error',Yii::t('partnerModule.machine', '售货机上架商品数量不能超过 ').VendingMachineGoods::MAX_ENABLE_GOODS_NUMBER_PER_MACHINE);
    		$this->redirect($_SERVER['HTTP_REFERER']);
    		exit();
    	}
    
    }
    
    
    /**
     * 申请售货机
     */
    public function actionCreate()
    {
        
        $this->pageTitle = Yii::t('partnerModule.machine','盖网售货机申请 _ ').$this->pageTitle;      
        $this->curr_menu_name = '';
        $model = new VendingMachine();
         $model->member_id = $this->curr_act_member_id;
         $model->scenario = 'create';  
        $this->performAjaxValidation($model);
    

        if (isset($_POST['VendingMachine'])) {
                   $count= $model->count('create_time>:time',array(':time'=>  strtotime(date('Y-m-d'))));
//                    $count = count($m);
                   $model->code = date('Ymd').$count+1;
                   $activation_code = '';
                   for($i=0;$i<5;$i++){
                        $activation_code .=mt_rand(1,9);
                   }
                   $model->activation_code = $activation_code;
//                    if(empty(VendingMachine::model()->count('activation_code=:aid',array(':aid'=>$activation_code)))){
//                         $model->activation_code = $activation_code;
//                    };                
        	$model->attributes = $this->getPost('VendingMachine');
        	$model->member_id = $this->curr_act_member_id;
        	$model->create_time = time();
            $model->partner_id = $this->curr_act_partner_id;
        	$model->status = VendingMachine::STATUS_APPLY;
            $memberTotalPayPreStoreLimit = Tool::getConfig('amountlimit', 'memberTotalPayPreStoreLimit');
            $model->max_amount_preday = !empty($memberTotalPayPreStoreLimit)?$memberTotalPayPreStoreLimit:'';
        	$model->password = RSA::passwordEncrypt($model->password);
        	
        	$fee_config = $this->getConfig('assign');
        	$model->fee = isset($fee_config['machineDefaultFee'])?$fee_config['machineDefaultFee']:8;
        	
        	$saveDir = 'VendingMachine/' . date('Y/n/j');
        	$model = UploadedFile::uploadFile($model, 'thumb', $saveDir, Yii::getPathOfAlias('att'));
        	if ($model->save()) {
        		UploadedFile::saveFile('thumb', $model->thumb);
        		//已放到模型处理
//                                     $stores = new Stores();
//                                     $stores->stype = Stores::MACHINE;
//                                     $stores->target_id = $model->id;
//                                     $stores->create_time = $model->create_time;
//                                     $stores->save();
        		$this->setFlash('success', Yii::t('partnerModule.machine','添加售货机成功'));
        		ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'添加售货机:'.$model->name);
        		  $this->redirect(array('list'));
        	} else {
        		$this->setFlash('error', Yii::t('partnerModule.machine','添加售货机失败'));
        	}
        }
        
        $this->render('create', array(
        		'model' => $model,
        ));
        
    }

    /**
     * 修改售货机信息
     */
//    public function actionUpdate($id)
//    {
//        $this->pageTitle = Yii::t('partnerGoods', '修改商品') . $this->pageTitle;
//        $model = $this->loadModel($id);
//        $model->scenario = 'update';
//		$this->_checkAccess($model);
//        $this->performAjaxValidation($model);
//
//        if (isset($_POST['Goods'])) {
//        	$oldFile = $model->thumb;
//            $model->attributes = $this->getPost('Goods');
//            $saveDir = 'partnerGoods/' . date('Y/n/j');
//            $model = UploadedFile::uploadFile($model, 'thumb', $saveDir, Yii::getPathOfAlias('att'),$oldFile);  // 上传图片
//            
//            $model->password = RSA::passwordDecrypt($model->password);
//
//            if ($model->save()) {
//                UploadedFile::saveFile('thumb', $model->thumb, $oldFile, true);
//                $this->setFlash('success', Yii::t('partnerGoods','修改商品成功'));
//                ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeUpdate,$model->id,'修改商品:'.$model->name);
//                $this->refresh();
//            } else {
//                $this->setFlash('error', Yii::t('partnerGoods','修改商品失败'));
//            }
//        }
//
//        $this->render('update', array(
//            'model' => $model,
//        ));
//    }


    
    
    /**
     * 盖网售货机管理列表
     * @author leo8705
     * Enter description here ...
     */
    public function actionList(){
    	$this->pageTitle = Yii::t('partnerModule.machine','盖网售货机管理列表 _ ').$this->pageTitle;
    	
    	$machine_model = new VendingMachine('search');
    	$machine_model->unsetAttributes();
    	$machine_model->member_id = $this->curr_act_member_id;
    	$lists = $machine_model->search();
    	$machine_data = $lists->getData();
    	$pager = $lists->pagination;
    
    	$this->render('machineList', array(
    			'machine_model' => $machine_model,
    			'machine_data'=>$machine_data,
    			'pager'=>$pager,
    	));
    }
    
    
    /**
     * 禁用Vending机器
     * @author leo8705
     * Enter description here ...
     * @param unknown_type $id
     */
    public function actionStop($id){
    	$this->_checkVendingMachine($id);
    	$model = $this->loadModel($this->curr_franchisee_id);
    
    	$machine_model = VendingMachine::model()->find("franchisee_id={$this->curr_franchisee_id} AND id={$id}");
    	$machine_model->status = VendingMachine::STATUS_DISABLE;
    	$machine_model->save();
    
    	$this->setFlash('success',Yii::t('partnerModule.machine', '设置成功'));
    	$this->redirect($_SERVER['HTTP_REFERER']);
    }
    
    
    /**
     * 启用Vending机器
     * @author leo8705
     * Enter description here ...
     * @param unknown_type $id
     */
    public function actionRun($id){
    	$this->_checkVendingMachine($id);
    	$model = $this->loadModel($this->curr_franchisee_id);
    
    	$machine_model = VendingMachine::model()->find("franchisee_id={$this->curr_franchisee_id} AND id={$id}");
    	$machine_model->status = VendingMachine::STATUS_ENABLE;
    	$machine_model->save();
    
    	$this->setFlash('success',Yii::t('partnerModule.machine', '设置成功'));
    	$this->redirect($_SERVER['HTTP_REFERER']);
    }
    
    
    
    
    /**
     * 盖网售货机商品管理列表
     * @author leo8705
     * Enter description here ...
     */
    public function actionMachineGoodsList(){
    	$mid = $this->getParam('mid')*1;
    	$this->pageTitle = Yii::t('partnerModule.machine','盖网售货机商品列表 _ ').$this->pageTitle;
    	$m_model = VendingMachine::model()->findByPk($mid);
    	$this->_checkAccess($m_model);

    	$model = new VendingMachineGoods('search');
        $model->status = 0;
    	$model->machine_id= $m_model->id;
         if($m_model->status ==VendingMachine::STATUS_APPLY || $m_model->status ==VendingMachine::STATUS_DISABLE){
            $this->setFlash('error', Yii::t('partnerModule.machine','该售货机未通过审核或被禁用！'));
            $this->redirect($this->createAbsoluteUrl('list'));
        }
    	if (isset($_GET[get_class($model)]))
    		$model->attributes = $this->getQuery(get_class($model));
    	$lists = $model->superSearch();
    	$goods_data = $lists->getData();
    	$pager = $lists->pagination;
    	
    	//查询库存
    	$good_ids = array();
    	foreach ($goods_data as $data){
    		$good_ids[] = $data->goods_id;
    	}
    	
    	$stocks = ApiStock::goodsStockList($m_model->id, $good_ids,API_PARTNER_VENDING_MACHINE_MODULES_PROJECT_ID);
    	
    	$this->render('machineGoodsList', array(
    			'm_model' => $m_model,
    			'model' => $model,
                                                    'mid'=>$mid,
    			'goods_data'=>$goods_data,
    			'pager'=>$pager,
    			'stocks'=>$stocks,
    	));
    	
    }
    
    
    /**
     * 盖网售货机商品添加
     * @author leo8705
     * Enter description here ...
     */
    public function actionGoodsAdd(){
    	$mid = $this->getParam('mid')*1;
    	$m_model = VendingMachine::model()->findByPk($mid);
    	$this->_checkAccess($m_model);
    	$this->pageTitle = Yii::t('partnerModule.machine', '添加售货机商品') . $this->pageTitle;
        $model = new VendingMachineGoods();
        $model->machine_id = $m_model->id;
//        $model->scenario = 'stock';
        $model->scenario = 'line';        
        $model->scenario = 'create';  
        $this->performAjaxValidation($model);
        if($m_model->status ==VendingMachine::STATUS_APPLY || $m_model->status ==VendingMachine::STATUS_DISABLE){
            $this->setFlash('error', Yii::t('partnerModule.machine','该售货机未通过审核或被禁用！'));
            $this->redirect($this->createAbsoluteUrl('list'));
        }
        if (isset($_POST['VendingMachineGoods'])) {
        	$model->attributes = $this->getPost('VendingMachineGoods');
//        	$model->machine_id = $m_model->id;
        	$model->create_time = time();
        	
        	//检查重复商品
        	if (VendingMachineGoods::model()->count(' machine_id=:machine_id AND goods_id=:goods_id  ',array(':machine_id'=>$m_model->id,':goods_id'=>$model->goods_id))) {
        		$this->setFlash('error', Yii::t('partnerModule.machine','售货机商品已存在！'));
        		$this->redirect($this->createAbsoluteUrl('goodsAdd',array('mid'=>$mid)));
        		return;
        	} 
        	
        	//接口创建库存
        	$stocks_rs = ApiStock::createStock($m_model->id, $model->goods_id,$model->stock,API_PARTNER_VENDING_MACHINE_MODULES_PROJECT_ID);
        	if (!isset($stocks_rs['result']) || $stocks_rs['result']!=true) {
        		$this->setFlash('error', Yii::t('partnerModule.machine','添加库存失败！'));
        		$this->redirect($this->createAbsoluteUrl('goodsAdd',array('mid'=>$mid)));
        		return;
        	}
        	
//        	var_dump($model->attributes);die;
                $goods = Goods::model()->findByPk($model->goods_id);
        	if ($model->save()) {
        		$this->setFlash('success', Yii::t('partnerModule.machine','添加售货机商品成功'));
        		
        		ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'添加售货机('.$m_model->name.')商品:'.$goods->name.' ,货道:'.$model->line);
        		$this->redirect($this->createAbsoluteUrl('machineGoodsList',array('mid'=>$mid)));
        	} else {
        		$this->setFlash('error', Yii::t('partnerModule.machine','添加售货机商品失败'));
        	}
        }
        
        $this->render('machineGoodsAdd', array(
        		'm_model' => $m_model,
        		'model' => $model,
        		'mid'=>$mid,
        ));

    }
    /**
     * 修改售货机商品
     */
    public function actionGoodsEdit(){
        $goods_id = $this->getParam('id');
        $mid = $this->getParam('mid');
//        var_dump($mid);die;
        $this->pageTitle = Yii::t('partnerModule.machine', '修改售货机商品') . $this->pageTitle;
        $m_model = VendingMachine::model()->findByPk($mid);
        $model = VendingMachineGoods::model()->find('goods_id=:gid and machine_id=:mid',array(':gid'=>$goods_id,':mid'=>$mid));
        $model->scenario = 'update';       
        $o_line = $model->line; 
        $this->performAjaxValidation($model);
        $goods = Goods::model()->findByPk($goods_id);
//        var_dump($model->attributes);  
        $this->_checkAccess($m_model);
        if(isset($_POST['VendingMachineGoods'])){
            $model->attributes = $this->getPost('VendingMachineGoods');
            if($model->save()){
                $this->setFlash('success', Yii::t('partnerModule.machine','修改售货机商品成功'));
                ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeUpdate,$model->id,'修改售货机('.$m_model->name.')商品货道:商品('.$goods->name.') |'.(($o_line==$model->line)?'无操作内容':'货道:'.$o_line.'->'.$model->line));
                $this->redirect($this->createAbsoluteUrl('machineGoodsList',array('mid'=>$mid)));
            }
        }
       
          $this->render('machineGoodsEdit', array(
        		'm_model' => $m_model,
        		'model' => $model,
        		'mid'=>$mid,
                                   'goods_id'=>$goods_id,
        ));
    }
    
    /**
     * 下架售货机商品
     * @author leo8705
     * Enter description here ...
     * @param unknown_type $id
     */
    public function actionGoodsDisable($id){
         $machine_goods = VendingMachineGoods::model()->findByPk($id);    
        if($machine_goods->status == VendingMachineGoods::STATUS_DISABLE){
            $this->setFlash('error', Yii::t('partnerModule.machine','商品审核未通过，已自动下架'));
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
    	$this->pageTitle = Yii::t('partnerModule.machine', '售货机商品下架') . $this->pageTitle;
    	$model = VendingMachineGoods::model()->findByPk($id);
    	$m_model = VendingMachine::model()->findByPk($model->machine_id);
    	$this->_checkAccess($m_model);
    	
    	$tran = Yii::app()->db->beginTransaction();
    	
    	$model->status = VendingMachineGoods::STATUS_DISABLE;

    	if ($model->save()) {
    		
    		//取消相关未支付订单
    		$orders = Yii::app()->db->createCommand()
    							->select('t.code')
    							->from(Order::model()->tableName().' t')
    							->leftJoin(OrdersGoods::model()->tableName().' g', 't.id=g.order_id')
    							->where('t.type='.Order::TYPE_MACHINE.'  AND t.status='.Order::STATUS_NEW.' AND t.pay_status='.Order::PAY_STATUS_NO.' AND g.sgid=:sgid ',array(':sgid'=>$id))
    							->queryAll();
    		foreach ($orders as $o){
    			Order::orderCancel($o['code'],true,Yii::t('partnerModule.machine','由于部分商品下架，本订单已自动取消'),false);
    		}
    		
    		$stock_rs = ApiStock::stockSet($model->machine_id, $model->goods_id,0,API_PARTNER_VENDING_MACHINE_MODULES_PROJECT_ID);
    		$goods = Goods::model()->findByPk($model->goods_id);
    		if (isset($stock_rs['result']) && $stock_rs['result']) {
    			$tran->commit();
    			$this->setFlash('success', Yii::t('partnerModule.machine','售货机商品下架成功'));
    			ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'设置售货机:'.$m_model->name.'(商品下架):'.$goods->name);
    		}else{
    			$tran->rollback();
    			$this->setFlash('error', Yii::t('partnerModule.machine','设置售货机商品下架失败，库存更新失败'));
    		}
    		
    	} else {
    		$this->setFlash('error', Yii::t('partnerModule.machine','设置售货机商品下架失败'));
    	}
    	$this->redirect($_SERVER['HTTP_REFERER']);
    	
    	
    }
    
    
    /**
     * 启用售货机商品
     * @author leo8705
     * Enter description here ...
     * @param unknown_type $id
     */
    public function actionGoodsEnable($id){
         $machine_goods = VendingMachineGoods::model()->findByPk($id);
        $goods = Goods::model()->find('id=:gid',array(':gid'=>$machine_goods->goods_id));

        if($goods->status == Goods::STATUS_NOPASS || $goods->status == Goods::STATUS_AUDIT){
            $this->setFlash('error', Yii::t('partnerModule.machine','商品审核未通过，不能上架'));
           $this->redirect($_SERVER['HTTP_REFERER']);
 
        }
    	$this->pageTitle = Yii::t('partnerModule.machine', '售货机商品上架') . $this->pageTitle;
    	$model = VendingMachineGoods::model()->findByPk($id);
    	$m_model = VendingMachine::model()->findByPk($model->machine_id);
    	$this->_checkAccess($m_model);
    	
    	$model->status = VendingMachineGoods::STATUS_ENABLE;
    	$goods = Goods::model()->findByPk($model->goods_id);
    	if ($model->save()) {
    		$this->setFlash('success', Yii::t('partnerModule.machine','售货机商品上架成功'));
    		ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeUpdate,$model->id,'设置售货机:'.$m_model->name.'(商品上架):'.$goods->name);
    	} else {
    		$this->setFlash('error', Yii::t('partnerModule.machine','设置售货机商品上架'));
    	}
    	$this->redirect($_SERVER['HTTP_REFERER']);
    }
    
    /**
     * 售货机商品入库
     * @author leo8705
     * Enter description here ...
     * @param unknown_type $id
     */
    public function actionGoodsStockIn($id){
    	$this->pageTitle = Yii::t('partnerModule.machine', '售货机商品进货') . $this->pageTitle;
    	$model = VendingMachineGoods::model()->findByPk($id);
    	$m_model = VendingMachine::model()->findByPk($model->machine_id);
    	$this->_checkAccess($m_model);
    	
    	$model->scenario = 'stock';
    	$this->performAjaxValidation($model);
    	
    	if (isset($_POST['VendingMachineGoods'])) {
    		$model->attributes = $this->getPost('VendingMachineGoods');
    	
    		$stock = ApiStock::goodsStockOne($m_model->id, $model->goods_id,API_PARTNER_VENDING_MACHINE_MODULES_PROJECT_ID);
    		$stock = isset($stock['result']['stock'])?$stock['result']['stock']*1:0;
    			
    		$stock_config = $this->params('stock');
    		if ($stock_config['maxStock']<=($stock+ $model->stock)) {
    			$this->setFlash('error', Yii::t('partnerModule.machine', '不能超过最大库存，最大库存为').$stock_config['maxStock']);
    			$this->redirect($this->createAbsoluteUrl('machineGoodsList',array('mid'=>$m_model->id)));
    		}
    			
    		
    		//接口创建库存
    		$stocks_rs = ApiStock::stockIn($m_model->id, $model->goods_id,$model->stock,API_PARTNER_VENDING_MACHINE_MODULES_PROJECT_ID);
//                                    $change_stock = empty($model->stock)?'无操作内容':$model->stock;
    		$goods = Goods::model()->findByPk($model->goods_id);	
    		if (isset($stocks_rs['result']) && $stocks_rs['result']==true) {
    			$this->setFlash('success', Yii::t('partnerModule.machine','售货机商品库存进货成功！'));
    			ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeUpdate,$model->id,'售货机('.$m_model->name.')商品进货:'.$goods->name.'| 数量:'.$model->stock);
    			$this->redirect($this->createAbsoluteUrl('machineGoodsList',array('mid'=>$m_model->id)));

    		} else {
    			$this->setFlash('error', Yii::t('partnerModule.machine','售货机商品库存进货失败'));
    		}
    	}
    	
    	$this->render('machineGoodsStockIn', array(
    			'model' => $model,
    	));
    	
    }
    
    /**
     * 售货机商品入库
     * @author leo8705
     * Enter description here ...
     * @param unknown_type $id
     */
    public function actionGoodsStockOut($id){
    	$this->pageTitle = Yii::t('partnerModule.machine', '售货机商品出货') . $this->pageTitle;
    	$model = VendingMachineGoods::model()->findByPk($id);
    	$m_model = VendingMachine::model()->findByPk($model->machine_id);
    	$this->_checkAccess($m_model);
    	
    	$model->scenario = 'stock';
    	$this->performAjaxValidation($model);
    	
    	if (isset($_POST['VendingMachineGoods'])) {
    		$model->attributes = $this->getPost('VendingMachineGoods');
    	
    		//接口创建库存
    		$stocks_rs = ApiStock::stockOut($m_model->id, $model->goods_id,$model->stock,API_PARTNER_VENDING_MACHINE_MODULES_PROJECT_ID);
//                                      $change_stock = empty($model->stock)?'无操作内容':$model->stock;
                                    $goods = Goods::model()->findByPk($model->goods_id);
    		if (isset($stocks_rs['result']) && $stocks_rs['result']==true) {
    			$this->setFlash('success', Yii::t('partnerModule.machine','售货机商品库存出货成功！'));
    			ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeUpdate,$model->id,'售货机('.$m_model->name.')商品出货:'.$goods->name.'| 数量:'.$model->stock);
    			$this->redirect($this->createAbsoluteUrl('machineGoodsList',array('mid'=>$m_model->id)));

    		} else {
    			$this->setFlash('error', Yii::t('partnerModule.machine','售货机商品库存出货失败'));
    		}
    	}
    	
    	$this->render('machineGoodsStockOut', array(
    			'model' => $model,
    	));
    }
    
    /*
     * 售货机信息编辑
     */
    public  function actionMachineUpdate($id){
  
       $model = VendingMachine::model()->findByPk($id);
       $model->scenario = 'update';
        $this->performAjaxValidation($model);
        if (isset($_POST['VendingMachine'])) {
            $oldFile = $model->thumb;
            $saveDir = 'VendingMachine/' . date('Y/n/j');
//            $model->attributes = $this->getPost('VendingMachine');
            $model->category_id = $_POST['VendingMachine']['category_id'];
            $model->province_id = $_POST['VendingMachine']['province_id'];
            $model->city_id = $_POST['VendingMachine']['city_id'];
            $model->district_id = $_POST['VendingMachine']['district_id'];
            $model->address = $_POST['VendingMachine']['address'];
            $model->lng = $_POST['VendingMachine']['lng'];
            $model->lat = $_POST['VendingMachine']['lat'];
            $model = UploadedFile::uploadFile($model, 'thumb', $saveDir, Yii::getPathOfAlias('att'),$oldFile);
             $model->status = VendingMachine::STATUS_APPLY;
            if ($model->save()) {
                  UploadedFile::saveFile('thumb', $model->thumb, $oldFile, true);
                  ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeUpdate,$model->id,'编辑售货机:'.$model->name);
                $this->setFlash('success',Yii::t('partnerModule.machine', '售货机：'). $model->name.Yii::t('partnerModule.machine', '，编辑成功'));                
                $this->redirect(array('list'));
            }else{
                $model->thumb = $oldFile;
                $this->setFlash('error', Yii::t('partnerModule.machine','修改售货机信息失败'));
                
            }
        }
        $this->render('update',array('model'=>$model));
    }

    /**
     * 售货机格子铺列表
     */
    public function  actionMachineCellStore($id){
        $mid =isset($id)?$id:$this->getParam('mid');
        $model = new VendingMachineCellStore('search');
        $model->status = 0;
        $model->machine_id = $id*1;
        if (isset($_GET[get_class($model)]))
            $model->attributes = $this->getQuery(get_class($model));
        $lists = $model->superSearch();
        $goods_data = $lists->getData();
        $pager = $lists->pagination;
        $this->render('machineCellStore', array(
            'model'=>$model,
            'list' => $goods_data,
            'mid'=>$mid,
            'pager'=>$pager
        ));
    }

    /**
     * 添加格子铺及关联商品
     */
    public function actionMachineCellStoreAdd(){
        $mid = $this->getParam('mid')*1;
        $m_model = VendingMachine::model()->findByPk($mid);
        $this->_checkAccess($m_model);
        $this->pageTitle = Yii::t('partnerModule.machine', '添加格子铺') . $this->pageTitle;
        $model = new VendingMachineCellStore();
        $count= $model->count('machine_id =:machine_id',array(':machine_id'=>  $mid));
        if($count>= VendingMachineCellStore::MAX_NUM){
            $this->setFlash('success', Yii::t('partnerModule.machine','添加格子铺不能超过{max}',array('{max}'=>VendingMachineCellStore::MAX_NUM)));
            $this->redirect(array('machine/machineCellStore/'.$mid));
        }
        $model->machine_id = $m_model->id;
        $model->scenario = 'create';
        $this->performAjaxValidation($model);
        if (isset($_POST['VendingMachineCellStore'])) {
            $model->attributes = $this->getPost('VendingMachineCellStore');
            $goods = Goods::model()->findByPk($model->goods_id);
            $model->create_time = time();
            if ($model->save()) {
            	
            	//设置库存为1
            	$s = new ApiStock();
            	$s->stockIn($mid, $model->goods_id, 1,API_MACHINE_CELL_STORE_PROJECT_ID);
            	
                $this->setFlash('success', Yii::t('partnerModule.machine','格子铺添加成功'));
//                ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'添加售货机商品:'.$model->name.'| id->'.$model->id);
                  ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'添加格子铺('.$m_model->name.')商品:'.$goods->name.' ,编码:'.$model->code);
                $this->redirect(array('machine/machineCellStore/'.$mid));
            } else {
                $this->setFlash('error', Yii::t('partnerModule.machine','格子铺添加失败'));
            }
        }
        $this->render('machineCellStoreAdd', array(
            'm_model' => $m_model,
            'model' => $model,
            'mid'=>$mid,
        ));
    }

    /**
     * 格子铺关联商品编辑
     */
    public function actionMachineCellStoreEdit($id){
        $mid = $this->getParam('mid')*1;
        $m_model = VendingMachine::model()->findByPk($mid);
        $this->_checkAccess($m_model);
        $this->pageTitle = Yii::t('partnerModule.machine', '添加格子铺') . $this->pageTitle;
        $model = VendingMachineCellStore::model()->findByPk($id);
        $old_code = $model->code;
        $this->performAjaxValidation($model);
        $goodsName = Yii::app()->db->createCommand()
            ->select('name')->from('{{goods}}')->where('id=:id',array(':id'=>$model->goods_id))->queryRow();
        if (isset($_POST['VendingMachineCellStore'])) {
            $model->attributes = $this->getPost('VendingMachineCellStore');
            $model->create_time = time();
             $goodsName2 = Yii::app()->db->createCommand()
            ->select('name')->from('{{goods}}')->where('id=:id',array(':id'=>$model->goods_id))->queryRow();
            $content = '';
            if($goodsName['name'] != $goodsName2['name']){
                $content .=$goodsName['name'].'->'.$goodsName2['name'].' | ';
            }
            if($old_code != $model->code){
                $content .=$old_code.'->'.$model->code;
            }
            $content = rtrim($content, ' | ');
            $content = empty($content)?'无修改操作':$content;
            if ($model->save()) {
                $this->setFlash('success', Yii::t('partnerModule.machine','格子铺编辑成功'));
                 ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeUpdate,$model->id,'编辑格子铺('.$m_model->name.')商品:'.$content);
                $this->redirect(array('machine/machineCellStore/'.$mid));
            } else {
                $this->setFlash('error', Yii::t('partnerModule.machine','格子铺编辑失败'));
            }
        }

        $this->render('machineCellStoreAdd', array(
            'm_model' => $m_model,
            'model' => $model,
            'name'=>$goodsName['name'],
            'mid'=>$mid,
        ));
    }

    /**
     * 删除格子铺
     */
    public function actionMachineCellStoreDel($id){
        VendingMachineCellStore::model()->deleteByPk($id);
        $mid = $this->getParam('mid')*1;
        $this->setFlash('success', Yii::t('partnerModule.machine','格子铺删除成功'));
        $this->redirect(array('machine/machineCellStore/'.$mid));

    }

    /**
     * 格子铺商品下架处理
     */
    public function actionGoodsShelves($id){
        VendingMachineCellStore::model()->updateByPk($id,array('status'=>VendingMachineCellStore::STATUS_DISABLE));
        $mid = $this->getParam('mid')*1;
        $model = VendingMachineCellStore::model()->findByPk($id);
        $vending_model = VendingMachine::model()->findByPk($model->machine_id);
        $goods = Goods::model()->findByPk($model->goods_id);
        $this->setFlash('success', Yii::t('partnerModule.machine','下架成功'));
        ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeUpdate,$model->id,'下架格子铺('.$vending_model->name.')商品:'.$goods->name);
        $this->redirect(array('machine/machineCellStore/'.$mid));
    }
    /**
     * 格子铺商品上架处理
     */
    public function actionGoodsAdded($id){
        VendingMachineCellStore::model()->updateByPk($id,array('status'=>VendingMachineCellStore::STATUS_ENABLE));
        $mid = $this->getParam('mid')*1;
        
        //设置库存为1
        $model = VendingMachineCellStore::model()->findByPk($id);
        $s = new ApiStock();
        $rs = $s->stockSet($mid, $model->goods_id, 1,API_MACHINE_CELL_STORE_PROJECT_ID);
        
//         var_dump($rs);exit();
        $vending_model = VendingMachine::model()->findByPk($model->machine_id);
        $goods = Goods::model()->findByPk($model->goods_id);
        $this->setFlash('success', Yii::t('partnerModule.machine','上架成功'));
        ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeUpdate,$model->id,'上架格子铺('.$vending_model->name.')商品:'.$goods->name);
        $this->redirect(array('machine/machineCellStore/'.$mid));
    }
public  function actionCheckGoodsNum(){
    $mid = $this->getParam('mid')*1;
    $model = new VendingMachineCellStore();
    $count= $model->count('machine_id =:machine_id',array(':machine_id'=>  $mid));
    if($count>= VendingMachineCellStore::MAX_NUM){
        exit(json_encode(array('error' => Yii::t('partnerModule.machine','添加格子铺不能超过{max}，如若添加请先移除后再添加！',array('{max}'=>VendingMachineCellStore::MAX_NUM)))));
    }else{
        exit(json_encode(array('success' => 1)));
    }
}

      

}
