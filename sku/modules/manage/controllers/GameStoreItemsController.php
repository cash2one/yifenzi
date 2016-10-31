<?php
/**
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/11/19 14:31
 */
class GameStoreItemsController extends MController
{
    public function filters()
    {
        return array(
            'rights',
        );
    }

    /**
     * 游戏店铺商品列表
     * @param $storeId
     * @throws CHttpException
     */
    public function actionAdmin($storeId) {
        $store = GameStore::model()->findByPk((int) $storeId);
        if ($store === null) {
            throw new CHttpException(404, '请求的页面不存在.');
        }
        $dataProvider = new CActiveDataProvider('GameStoreItems', array(
            'criteria' => array('condition' => 'store_id = ' . (int) $storeId),
            'sort' => array(
                'defaultOrder' => 'id ASC',
            ),
        ));
        $this->render('admin', array(
            'dataProvider' => $dataProvider,
            'store' => $store,
        ));
    }

    /**
     * 添加商品
     * @param int $storeId
     */
    public function actionCreate($storeId) {
        $model = new GameStoreItems('Create');
        $this->performAjaxValidation($model);
        $model->start_date = date('Y-m-d');
        $model->end_date = date("Y-m-d", time() + (60 * 60 * 24 * 7));
        $model->start_time = date('H:00:00',time() + 3600);
        $model->end_time = date('H:00:00',time() + (60 * 60 * 5));
        if (isset($_POST['GameStoreItems'])) {
            $_POST['GameStoreItems']['create_time'] = time();
            $_POST['GameStoreItems']['update_time'] = time();
            $_POST['GameStoreItems']['store_id'] = (int) $storeId;
            $model->attributes = $_POST['GameStoreItems'];
            if($model->flag == GameStoreItems::SPECIAL_ITEM_FLAG){
                $model->bees_number = 0;
            }

            if ($model->save()) {
                @SystemLog::record(Yii::app()->user->name."添加商品：{$model->item_name}");
                $this->setFlash('success', Yii::t('GameStoreItems', '添加商品成功：') . $model->item_name);
                $this->redirect(array('admin','storeId' => $model->store_id));
            }else{
                $errors = $model->getErrors();
                $errStr = '';
                if(!empty($errors)){
                    foreach ($errors as $key => $value){
                        $errStr .= '\r\n'.$key.':';
                        if(is_array($value))foreach ($value as $val){
                            $errStr .= ' '.$val;
                        }
                    }
                }
                @SystemLog::record(Yii::app()->user->name."添加商品：{$model->item_name} 失败");
                $this->setFlash('error', Yii::t('GameStore', "添加商品失败：") .$errStr);
                $this->redirect(array('admin','storeId' => $model->store_id));
            }
        }
        $this->render('_form', array(
            'model' => $model,
        ));
    }

    /**
     * 编辑商品
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $itemName = $model->item_name;
        $itemFlag = $model->flag;
        $this->performAjaxValidation($model);
        if (isset($_POST['GameStoreItems'])) {
            $_POST['GameStoreItems']['update_time'] = time();
            $model->attributes = $_POST['GameStoreItems'];
            $model->item_name = $itemName;
            $model->flag = $itemFlag;
            if ($model->save()) {
                @SystemLog::record(Yii::app()->user->name."修改商品：{$model->item_name}");
                $this->setFlash('success', Yii::t('GameStoreItems', '修改商品成功：') . $model->item_name);
                $this->redirect(array('admin','storeId' => $model->store_id));
            }else{
                $errors = $model->getErrors();
                $errStr = '';
                if(!empty($errors)){
                    foreach ($errors as $key => $value){
                        $errStr .= '\r\n'.$key.':';
                        if(is_array($value))foreach ($value as $val){
                            $errStr .= ' '.$val;
                        }
                    }
                }
                @SystemLog::record(Yii::app()->user->name."修改商品：{$model->item_name} 失败");
                $this->setFlash('error', Yii::t('GameStoreItems', '修改商品失败：') . $model->item_name.$errStr);
                $this->redirect(array('admin','storeId' => $model->store_id));
            }
        }
        $this->render('_form', array(
            'model' => $model,
        ));
    }
}