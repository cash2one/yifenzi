<?php
/**
 * 游戏店铺商品
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/11/20 16:55
 */
class GameStoreItemsController extends PController
{
    /**
     * 添加游戏商品
     */
    public function actionCreate() {
        $this->gameStoreCheck($this->gameStoreId);//检查权限
        $model = new GameStoreItems('Create');

        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);
        $model->start_date = date('Y-m-d');
        $model->end_date = date("Y-m-d", time() + (60 * 60 * 24 * 7));
        $model->start_time = date('H:00:00',time() + 3600);
        $model->end_time = date('H:00:00',time() + (60 * 60 * 5));
        if (isset($_POST['GameStoreItems'])) {
            $model->attributes = $this->getPost('GameStoreItems');
            $model->store_id = $this->gameStoreId;
            $model->create_time = $model->update_time = time();
            if ($model->save()){
                $this->setFlash('success', Yii::t('GameStoreItems', '添加商品') . $model->item_name . Yii::t('GameStoreItems', '成功'));
                ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeInsert, $model->id, '游戏店铺添加商品:' . $model->item_name);
                $this->redirect(array('index'));
            }
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }


    /**
     * 添加游戏特殊商品
     */
    public function actionCreateFlag() {
        $this->gameStoreCheck($this->gameStoreId);//检查权限
        $rs = GameStore::model()->findByPk($this->gameStoreId);
        if($rs->franchise_stores <> GameStore::FRANCHISE_STORES_IS)
            $this->redirect(array('index'));

        $model = new GameStoreItems('Createflag');

        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);
        $model->start_date = date('Y-m-d');
        $model->end_date = date("Y-m-d", time() + (60 * 60 * 24 * 7));
        $model->start_time = date('H:00:00',time() + 3600);
        $model->end_time = date('H:00:00',time() + (60 * 60 * 5));
        $model->flag = GameStoreItems::SPECIAL_ITEM_FLAG;
        if (isset($_POST['GameStoreItems'])) {
            $model->attributes = $this->getPost('GameStoreItems');
            $model->store_id = $this->gameStoreId;
            $model->create_time = $model->update_time = time();
            if ($model->save()){
                $this->setFlash('success', Yii::t('GameStoreItems', '添加商品') . $model->item_name . Yii::t('GameStoreItems', '成功'));
                ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeInsert, $model->id, '游戏店铺添加商品:' . $model->item_name);
                $this->redirect(array('index'));
            }
        }

        $this->render('createflag', array(
            'model' => $model,
        ));
    }

    /**
     * 修改游戏商品
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $this->gameStoreCheck($model->store_id);//检查权限
        $itemName = $model->item_name;
        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);
        if (isset($_POST['GameStoreItems'])) {
            $model->attributes = $this->getPost('GameStoreItems');
            $model->item_name = $itemName;
            $model->update_time = time();
            if ($model->save()){
                $this->setFlash('success', Yii::t('GameStoreItems', '修改商品') . $model->item_name . Yii::t('GameStoreItems', '成功'));
                ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeUpdate, $model->id, '游戏店铺修改商品:' . $model->item_name);
                $this->redirect(array('index'));
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }


    /**
     * 修改游戏商品
     */
    public function actionUpdateFlag($id) {
        $model = $this->loadModel($id);
        $this->gameStoreCheck($model->store_id);//检查权限
        $rs = GameStore::model()->findByPk($this->gameStoreId);
        if($rs->franchise_stores <> GameStore::FRANCHISE_STORES_IS)
            $this->redirect(array('index'));

        $itemName = $model->item_name;
        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);
        if (isset($_POST['GameStoreItems'])) {
            $model->attributes = $this->getPost('GameStoreItems');
            $model->item_name = $itemName;
            $model->update_time = time();
            if ($model->save()){
                $this->setFlash('success', Yii::t('GameStoreItems', '修改商品') . $model->item_name . Yii::t('GameStoreItems', '成功'));
                ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeUpdate, $model->id, '游戏店铺修改商品:' . $model->item_name);
                $this->redirect(array('index'));
            }
        }

        $this->render('updateflag', array(
            'model' => $model,
        ));
    }

    /**
     * 查看游戏店铺商品
     */
    public function actionIndex()
    {
        $model = new GameStoreItems();
        $model->store_id = $this->getSession('gameStoreId');
        $this->gameStoreCheck($model->store_id);//检查权限
        $criteria = new CDbCriteria;
        if (isset($_POST['GameStoreItems'])) {
            $model->attributes = $this->getPost('GameStoreItems');
            $criteria->compare('item_name', $model->item_name, true);
        }
        $criteria->compare('store_id', $model->store_id);
        //$criteria->compare('item_status', $model->item_status);
        $criteria->order = 'id ASC';

        // 分页
        $count = $model->count($criteria);
        $pager = new CPagination($count);
        $pager->pageSize = 13;
        $pager->applyLimit($criteria);
        $items = GameStoreItems::model()->findAll($criteria);
        $model->search();

        //查询是否特殊商品店铺
        $store = Yii::app()->gw->createCommand()
            ->select('franchise_stores')
            ->from('{{game_store}}')
            ->where('id = :id', array(':id' => $this->gameStoreId))
            ->queryRow();

        $this->render('index', array(
            'pages' => $pager,
            'items' => $items,
            'model' => $model,
            'store' => $store
        ));
    }
}