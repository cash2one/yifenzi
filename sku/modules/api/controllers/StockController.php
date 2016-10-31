<?php
/**
 * 库存接口控制器
 * 
 * @author leo8705
 *
 */

class StockController extends SAPIController {

    /**
     * 取单个商品库存
     * 
     * @param p 项目
     * @param target 商品id或者条形码
     * 
     */
    public function actionGetOne() {
    	$data = $this->data;
    	$rs = GoodsStock::apiGetOne($this->project,$data['outlets'],$data['target']);
    	 
    	if($rs['result']==true){
    		$this->_success($rs);
    	}else{
    		$this->_error(ErrorCode::getErrorStr($rs['error_code']).(isset($rs['error_msg'])?':'.$rs['error_msg']:''),$rs['error_code']);
    	}
    	
    }
    
    /**
     * 根据列表取商品库存
     *
     * @param p 项目
     * @param list 商品id或者条形码
     *
     */
    public function actionGetByList() {
    	$data = $this->data;
        
    	$rs = GoodsStock::apiGetList($this->project,$data['outlets'],$data['list']);
    	 
    	if($rs['result']==true){
    		$this->_success($rs);
    	}else{
    		$this->_error(ErrorCode::getErrorStr($rs['error_code']).(isset($rs['error_msg'])?':'.$rs['error_msg']:''),$rs['error_code']);
    	}
    	
    }

    /**
     * 创建商品库存记录
     *
     * @param p 项目
     * @param target 商品id或者条形码
     *
     */
    public function actionCreate() {
    	$data = $this->data;
    
    	$rs = GoodsStock::createByProject($this->project,$data);
    	
    	if($rs['result']==true){
    		$this->_success(Yii::t('apiModule.order','操作成功！'));
    	}else{
    		$this->_error(ErrorCode::getErrorStr($rs['error_code']).(isset($rs['error_msg'])?':'.$rs['error_msg']:''),$rs['error_code']);
    	}
    	
    }
    
    
    /**
     * 根据列表创建商品库存
     *
     * @param list   json 数据
     * @param target 商品id或者条形码
     *
     */
    public function actionCreateByList() {
    	$data = $this->data;
    
    	$arr_data =  $data['list'];
    	$rs = GoodsStock::createListByProject($this->project,$arr_data);
    	
    	if($rs['result']==true){
    		$this->_success(Yii::t('apiModule.order','操作成功！'));
    	}else{
    		$this->_error(ErrorCode::getErrorStr($rs['error_code']).(isset($rs['error_msg'])?':'.$rs['error_msg']:''),$rs['error_code']);
    	}
    }
    
    /**
     * 更新商品库存
     *
     * @param p 项目
     * @param target 商品id或者条形码
     *
     */
    public function actionSet() {
    	$data = $this->data;
    	$rs = GoodsStock::stockSet($this->project,$data['outlets'],$data['target'],$data['num'],'更新商品库存');
    	if($rs['result']==true){
    		$this->_success(Yii::t('apiModule.order','操作成功！'));
    	}else{
    		$this->_error(ErrorCode::getErrorStr($rs['error_code']).(isset($rs['error_msg'])?':'.$rs['error_msg']:''),$rs['error_code']);
    	}
    }
    
    /**
     * 批量设置商品库存
     *
     * @param p 项目
     * @param target 商品id或者条形码
     *
     */
    public function actionSetList() {
        $data = $this->data;
    	$rs = GoodsStock::stockSetList($this->project,$data['outlets'],$data['targets'],$data['nums'],'批量更新商品库存');
    	if($rs['result']==true){
    		$this->_success(Yii::t('apiModule.order','操作成功！'));
    	}else{
    		$this->_error(ErrorCode::getErrorStr($rs['error_code']).(isset($rs['error_msg'])?':'.$rs['error_msg']:''),$rs['error_code']);
    	}
    }
    
    /**
     * 批量更新商品变动库存
     *
     * @param p 项目
     * @param target 商品id或者条形码
     *
     */
    public function actionChangeList() {
    	$data = $this->data;
    	$remark = isset($data['remark'])?$data['remark']:'批量更新商品变动库存';
    	$rs = GoodsStock::stockChangeList($this->project,$data['outlets'],$data['targets'],$data['nums'],$remark);
    	if($rs['result']==true){
    		$this->_success(Yii::t('apiModule.order','操作成功！'));
    	}else{
    		$this->_error(ErrorCode::getErrorStr($rs['error_code']).(isset($rs['error_msg'])?':'.$rs['error_msg']:''),$rs['error_code']);
    	}
    }
    
    
    /**
     * 商品库存 入库
     *
     * @param p 项目
     * @param target 商品id或者条形码
     *
     */
    public function actionIn() {
    	$data = $this->data;
    	$rs = GoodsStock::stockIn($this->project,$data['outlets'],$data['target'],$data['num']);
    	
    	if($rs['result']==true){
    		$this->_success(Yii::t('apiModule.order','操作成功！'));
    	}else{
    		$this->_error(ErrorCode::getErrorStr($rs['error_code']).(isset($rs['error_msg'])?':'.$rs['error_msg']:''),$rs['error_code']);
    	}
    }
    
    
    
    /**
     * 商品库存 出库
     *
     * @param p 项目
     * @param target 商品id或者条形码
     *
     */
    public function actionOut() {
    	$data = $this->data;
    	$rs = GoodsStock::stockOut($this->project,$data['outlets'],$data['target'],$data['num'],'out');
    	
    	if($rs['result']==true){
    		$this->_success(Yii::t('apiModule.order','操作成功！'));
    	}else{
    		$this->_error(ErrorCode::getErrorStr($rs['error_code']).(isset($rs['error_msg'])?':'.$rs['error_msg']:''),$rs['error_code']);
    	}
    
    }
    
    
    /**
     * 商品库存 冻结
     *
     * @param p 项目
     * @param target 商品id或者条形码
     *
     */
    public function actionFrozen() {
    	$data = $this->data;
    	$rs = GoodsStock::stockFrozen($this->project,$data['outlets'],$data['target'],$data['num'],'Frozen');
    	 
    	if($rs['result']==true){
    		$this->_success(Yii::t('apiModule.order','操作成功！'));
    	}else{
    		$this->_error(ErrorCode::getErrorStr($rs['error_code']).(isset($rs['error_msg'])?':'.$rs['error_msg']:''),$rs['error_code']);
    	}
    }
    
    
    /**
     * 商品库存 冻结 商品列表
     *
     * @param p 项目
     * @param target 商品id或者条形码
     *
     */
    public function actionFrozenList() {
    	$data = $this->data;          
    	$rs = GoodsStock::stockFrozenList($this->project,$data['outlets'],$data['targets'],$data['nums'],'');
    
    	if($rs['result']==true){
    		$this->_success(Yii::t('apiModule.order','操作成功！'));
    	}else{
    		$this->_error(ErrorCode::getErrorStr($rs['error_code']).(isset($rs['error_msg'])?':'.$rs['error_msg']:''),$rs['error_code']);
    	}
    }
    
    
    /**
     * 商品库存 冻结还原
     *
     * @param p 项目
     * @param target 商品id或者条形码
     *
     */
    public function actionFrozenRestore() {
    	$data = $this->data;
    	$rs = GoodsStock::stockFrozenRestore($this->project,$data['outlets'],$data['target'],$data['num'],'FrozenRestore');
    
    	if($rs['result']==true){
    		$this->_success(Yii::t('apiModule.order','操作成功！'));
    	}else{
    		$this->_error(ErrorCode::getErrorStr($rs['error_code']).(isset($rs['error_msg'])?':'.$rs['error_msg']:''),$rs['error_code']);
    	}
    }
    
    /**
     * 商品库存 冻结还原
     *
     * @param p 项目
     * @param target 商品id或者条形码
     *
     */
    public function actionFrozenRestoreList() {
    	$data = $this->data;
    	$rs = GoodsStock::stockFrozenRestoreList($this->project,$data['outlets'],$data['targets'],$data['nums'],GoodsStockBalance::NODE_FROZEN_STOCK_RESTORE,GoodsStockBalance::NODE_STOCK_IN,'FrozenRestore');
    
    	if($rs['result']==true){
    		$this->_success(Yii::t('apiModule.order','操作成功！'));
    	}else{
    		$this->_error(ErrorCode::getErrorStr($rs['error_code']).(isset($rs['error_msg'])?':'.$rs['error_msg']:''),$rs['error_code']);
    	}
    	
    }
    
    
    /**
     * 扣除商品冻结库存 
     *
     * @param p 项目
     * @param target 商品id或者条形码
     *
     */
    public function actionFrozenOut() {
    	$data = $this->data;
//     	$this->_checkEncryption($data);
    	$rs = GoodsStock::stockFrozenOut($this->project,$data['outlets'],$data['target'],$data['num'],'test out');
    
    	if($rs['result']==true){
    		$this->_success(Yii::t('apiModule.order','操作成功！'));
    	}else{
    		$this->_error(ErrorCode::getErrorStr($rs['error_code']).(isset($rs['error_msg'])?':'.$rs['error_msg']:''),$rs['error_code']);
    	}
    }
    
    /**
     * 扣除商品冻结库存
     *
     * @param p 项目
     * @param target 商品id或者条形码
     *
     */
    public function actionFrozenOutList() {
    	$data = $this->data;
    	//     	$this->_checkEncryption($data);
    	$rs = GoodsStock::stockFrozenOutList($this->project,$data['outlets'],$data['targets'],$data['nums'],'');
    
    	if($rs['result']==true){
    		$this->_success(Yii::t('apiModule.order','操作成功！'));
    	}else{
    		$this->_error(ErrorCode::getErrorStr($rs['error_code']).(isset($rs['error_msg'])?':'.$rs['error_msg']:''),$rs['error_code']);
    	}
    }
    
    
}