<?php
/**
 * 游戏店铺用户信息
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/11/26 13:17
 */
class GameStoreMemberController extends PController
{
    /**
     * 查看游戏店铺用户
     */
    public function actionIndex()
    {
        $this->gameStoreCheck($this->gameStoreId);//检查权限
        $model = new GameStoreMember();
        $model->store_id = $this->gameStoreId;
        $criteria = new CDbCriteria;
        if (isset($_POST['GameStoreMember'])) {
            $model->mobile = $_POST['GameStoreMember']['mobile'];
            $criteria->compare('mobile', $model->mobile,true);
        }
        $criteria->compare('store_id', $model->store_id);
        $criteria->order = 'id DESC';

        // 分页
        $count = $model->count($criteria);
        $pager = new CPagination($count);
        $pager->pageSize = 13;
        $pager->applyLimit($criteria);
        $items = GameStoreMember::model()->findAll($criteria);
        $model->search();

        $this->render('index', array(
            'pages' => $pager,
            'items' => $items,
            'model' => $model
        ));
    }


    /**
     * 修改游戏用户信息
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        // Uncomment the following line if AJAX validation is needed

        $this->gameStoreCheck($model->store_id);//检查权限
        $this->performAjaxValidation($model);
        if (isset($_POST['GameStoreMember'])) {
            $model->real_name = $_POST['GameStoreMember']['real_name'];
            $model->mobile = $_POST['GameStoreMember']['mobile'];
            $model->member_address = $_POST['GameStoreMember']['member_address'];
            //$model->attributes = $this->getPost('GameStoreMember');
            if ($model->save()){
                $this->setFlash('success', Yii::t('GameStoreMember', '修改用户信息')  . Yii::t('GameStoreItems', '成功'));
                ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeUpdate, $model->id, '修改用户信息:' . $model->real_name);
                $this->redirect(array('index'));
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }
}