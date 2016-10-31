<?php
/**
 * 游戏店铺发货
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/11/26 17:32
 */
class GameStoreDeliveryController extends PController{
    /**
     * 添加发货记录
     */
    public function actionCreate($id) {
        $model = new GameStoreDelivery();
        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);
        $member = GameStoreMember::model()->find('id = :id',array('id' => $id));

        $this->gameStoreCheck($member->store_id);//检查权限
        $model->delivery_items = $member['items_info'];
        $model->delivery_time = date("Y-m-d H:i:s");
        if (isset($_POST['GameStoreDelivery'])) {
            $model->attributes = $this->getPost('GameStoreDelivery');
            $model->delivery_time = strtotime($_POST['GameStoreDelivery']['delivery_time']);
            $model->receive_member_id = $member['member_id'];
            $model->delivery_store_id = $this->gameStoreId;
            $model->order_id = $id;
            if($model->find('order_id = :order_id',array(':order_id' => $id))){
                $this->setFlash('error', Yii::t('GameStoreDelivery', '已提交发货记录，请不要重复提交'));
                $this->redirect(array('index'));
            }
            if ($model->save()){
                GameStoreMember::model()->updateAll(array('status' => GameStoreMember::STATUS_DELIVERY),'id = :id', array(':id' => $id));
                $this->setFlash('success', Yii::t('GameStoreDelivery', '添加发货记录') . Yii::t('GameStoreDelivery', '成功'));
                ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeUpdate, $model->id, '添加发货记录:' . $model->delivery_items);
                $this->redirect(array('index'));
            }
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * 修改发货记录
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);
        $this->gameStoreCheck($model->delivery_store_id);//检查权限
        $model->delivery_time = $this->format()->formatDatetime($model->delivery_time);
        if (isset($_POST['GameStoreDelivery'])) {
            $model->attributes = $this->getPost('GameStoreDelivery');
            $model->delivery_time = strtotime($_POST['GameStoreDelivery']['delivery_time']);
            if ($model->save()){
                $this->setFlash('success', Yii::t('GameStoreDelivery', '修改发货记录') . Yii::t('GameStoreDelivery', '成功'));
                ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeUpdate, $model->id, '修改发货记录:' . $model->delivery_items);
                $this->redirect(array('index'));
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * 查看发货记录
     */
    public function actionIndex()
    {
        $this->gameStoreCheck($this->gameStoreId);//检查权限
        $model = new GameStoreDelivery();
        $model->delivery_store_id = $this->gameStoreId;
        $criteria = new CDbCriteria;
        if (isset($_POST['GameStoreDelivery'])) {
            $model->attributes = $this->getPost('GameStoreDelivery');
            $criteria->compare('delivery_items', $model->delivery_items, true);
        }
        $criteria->with = array('info' => array('select' => 'info.real_name,info.mobile,info.member_address'));
        $criteria->compare('delivery_store_id', $model->delivery_store_id);
        $criteria->order = 't.delivery_time DESC';

        // 分页
        $count = $model->count($criteria);
        $pager = new CPagination($count);
        $pager->pageSize = 13;
        $pager->applyLimit($criteria);
        $items = GameStoreDelivery::model()->findAll($criteria);
        $model->search();

        $this->render('index', array(
            'pages' => $pager,
            'items' => $items,
            'model' => $model
        ));
    }
}