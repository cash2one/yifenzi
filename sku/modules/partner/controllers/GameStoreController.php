<?php
/**
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/11/20 13:53
 */
class GameStoreController extends PController{

    public function init()
    {
        $this->pageTitle = Yii::t('partnerModule.partner', '小微企业联盟') . Yii::app()->name;
    }

    /**
     * 查看游戏店铺
     */
    public function actionView() {
        $model = GameStore::model()->findByPk($this->gameStoreId);
        if(!$model){
            $this->redirect(array('/partner/GameStore/invalid'));
        }
        $this->render('view', array(
            'model' => $model,
        ));
    }

    /**
     * 编辑游戏店铺
     */
    public function actionUpdate($id) {
        $this->gameStoreCheck($id);//检查权限
        $model = $this->loadModel($id);
        $this->performAjaxValidation($model);
        $model->setScenario('updateGameStore');
        $this->performAjaxValidation($model);
        if (isset($_POST['GameStore'])) {
            $_POST['GameStore']['update_time'] = time();
            $model->attributes = $this->getPost('GameStore');

            if ($model->save()) {
                $this->setFlash('success', Yii::t('partnerGoods', '操作成功'));
                ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeUpdate, $model->id, '更新游戏店铺:' . $model->store_name);
                $this->redirect(array('view'));

            } else {
                $this->setFlash('error', Yii::t('store', '操作失败'));
                $this->redirect(array('view'));
            }
        }
        $this->render('update', array('model' => $model));
    }

    public function actionInvalid(){
        $this->render('invalid');
    }

    public function actionIllegal(){
        $this->render('illegal');
    }
}