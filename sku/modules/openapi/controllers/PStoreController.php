<?php
/**
 * 店铺控制器
 *
 * 提供门店、店铺、
 *
 * @author leo8705
 *
 */

class PStoreController extends SuperController {
	
	/**
	 * 店铺分类
	 * 
	 */
	public function actionCateList(){
		$list = StoreCategory::getCategoryList();
		$this->_success($list);
	}
	
	
    /**
     * 申请、添加、修改超市门店
     */
    public function actionSuperSave(){
        try {

            $fields = array('token','sid','name','category_id','mobile','logo','province_id','city_id','district_id','street',
                'zip_code','is_delivery','delivery_start_amount','delivery_mini_amount','delivery_fee','lng','lat','open_time','isCreate');
            $this->params = $fields;
            $requiredFields = array('token','sid','name','category_id','mobile','logo','province_id','city_id','district_id','street','zip_code','is_delivery','lng','lat','open_time','isCreate');
            $decryptFields = array('token','sid');

            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
            }
            $isCreate = $post['isCreate'];
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
                $model = UploadedFile::uploadFile($model, 'logo', $saveDir, Yii::getPathOfAlias('att'));
            }
            if($isCreate == 2){
                $model = $this->store;
                $model->scenario = 'update';
                if (empty($model)) {
                    $this->_error( Yii::t('store', '请先添加门店'));
                }
                $oldFile = $model->logo;
                $model->attributes = $post;
                $saveDir = 'superStore/' . date('Y/n/j');
                $model = UploadedFile::uploadFile($model, 'logo', $saveDir, Yii::getPathOfAlias('att'), $oldFile);
                $model->status = Supermarkets::STATUS_APPLY;
            }
            if($model->save()){
                if(isset($oldFile)){
                    UploadedFile::saveFile('logo', $model->logo, $oldFile, true);
                    Yii::app()->db->createCommand()->update(SuperGoods::model()->tableName(), array('status' => SuperGoods::STATUS_DISABLE), 'super_id=:id', array(':id' =>$model->id));
                    ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeUpdate, $model->id, '修改超市门店信息:' . $model->name);
                    $this->_success( Yii::t('store', '修改超市门店信息成功'));
                }else{
                    UploadedFile::saveFile('logo', $model->logo);
                    $this->_success(Yii::t('store', '添加超市门店成功'));
                }

            }else{
                $this->_error(Yii::t('store', isset($oldFile)?'修改超市门店失败':'添加超市门店失败'));
            }

        }catch (Exception $e){

            $this->_error($e->getMessage());
        }
    }
//
//    /**
//     * 门店超市切换
//     */
//    public function actionChange(){
//        try{
//            $fields = array('token','superId');
//            $this->params = $fields;
//            $requiredFields = array('token','superId');
//            $decryptFields = array('token','superId');
//
//            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
//                $post = $_REQUEST;
//            }else{
//                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
//            }
//            $id = $post['superId'];
//            $this->_setSuper($id);
//            $this->_success('超市门店切换成功');
//
//        }catch (Exception $e){
//
//            $this->_error($e->getMessage());
//        }
//    }

    /**
     * 查看当前门店信息
     */
    public function actionView(){
        try{
            $fields = array('token','sid');
            $this->params = $fields;
            $requiredFields = array('token');
            $decryptFields = array('token','sid');

            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
            }
            $model = Supermarkets::model()->findByPk($this->super_id);
            $cate = StoreCategory::model()->findByPk($model->category_id);
            if (!$model) {
                $this->_error( Yii::t('store', '请先添加门店'));

            }else{
                $this->_success(array('model'=>$model,'cate'=>$cate));
            }
        }catch (Exception $e){

            $this->_error($e->getMessage());
        }

    }

    /**
     * 超市门店列表
     */
    public function actionStoreList(){
        try{
            $this->params = array('token','sid','num','lastId');
            $requiredFields = array('token');
            $decryptFields = array('token','sid');
            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
            }
            $lastId = ( isset($post['lastId']) && is_numeric($post['lastId']) ) ? $post['lastId'] :-1;
            $limit = ( isset($post['num']) && is_numeric($post['num']) )? $post['num'] : 8;
            $limit = ($limit > 20) ?  20 : $limit;     //显示多少条
            $data = Yii::app()->db->createCommand()
                ->select("id,name,mobile,type,status")
                ->from("{{supermarkets}}")
                ->where('member_id=:member_id AND id > :lastId',array(':member_id'=>$this->member,':lastId'=>$lastId))
                ->order('id ASC')
                ->limit($limit)
                ->queryAll();
            $list = array();
            if(!empty($data)){
                foreach($data as $k =>$v){
                    $v['status'] = Supermarkets::getStatus($v['status']);
                    $v['type'] = StoreCategory::getCategoryName($v['type']);
                    $list[] = $v;
                }
            }
            $curr_super = 0;
            if (!empty($list)) {
                //默认取第一个为当前超市门店
                if (empty($this->super_id)) {
                    $this->super_id = $list[0]['id'];
                }
                $curr_super = Supermarkets::model()->find("id={$this->super_id}");
            }
            $this->_success(array('list'=>$list,'currSuper'=>$curr_super));

        }catch (Exception $e){
            $this->_error($e->getMessage());
        }

    }


}