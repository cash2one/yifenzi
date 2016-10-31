<?php
/**
 * 游戏配置控制器
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/9/18 9:58
 */
class GameConfigController extends MController{
    public function filters() {
        return array(
            'rights',
        );
    }

    /**
     * 修改配置文件
     * 文件名规则：控制器+Config 后缀，模型+ConfigForm后缀
     *
     * @param string $actionId   $this->action->id  控制器名称
     */
    private function _settingConfig($actionId) {
        $viewFileName = strtolower($actionId);
        $name = substr($viewFileName, 0, -6);
        $formName = ucfirst($this->id);
        $model = GameConfig::model()->findByAttributes(array('config_name' => $name));
        if(empty($model))$model = new GameConfig();

        //ajax表单验证
        $this->performAjaxValidation($model);

        if (isset($_POST[$formName])) {
            $model->attributes = $_POST[$formName];
            if ($model->validate()) {
                $value = $_POST[$formName]['value'];
                $model = GameConfig::model()->findByAttributes(array('config_name' => $name,'app_type' => $_POST[$formName]['app_type']));
                if ($model) {
                    $gameConfig = GameConfig::model();
                    $gameConfig->id = $model->id;
                } else {
                    $gameConfig = new GameConfig();
                }
                $gameConfig->app_type = $_POST[$formName]['app_type'];
                $gameConfig->config_name = $_POST[$formName]['config_name'];
                $gameConfig->value =  $value;

                if ($gameConfig->save()) { //向得到的文件路劲指定的文件里面插入数据
                    Yii::app()->redis->set($viewFileName,$value,86400);
                    $this->setFlash('success', Yii::t('home', '数据保存成功'));
                    //@SystemLog::record(Yii::app()->user->name . "修改游戏配置文件：" . $this->action->id);
                } else {
                    $this->setFlash('error', Yii::t('home', '数据保存失败，请检查相关目录权限'));
                }
            }
        }

        //CActiveForm widget 参数
        $formConfig = array(
            'id' => $this->id . '-form',
            'enableAjaxValidation' => true,
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
            ),
        );
        $this->render(strtolower($actionId), array('model' => $model, 'formConfig' => $formConfig));
    }

    /**
     * 三国跑跑概率表配置
     */
    public function actionMultipleConfig() {
        $this->breadcrumbs = array(Yii::t('home', '游戏配置管理'), Yii::t('home', '三国跑跑概率表配置'));
        $this->_settingConfig($this->action->id);
    }

    /**
     * 三国跑跑房间表配置
     */
    public function actionRoomConfig() {
        $this->breadcrumbs = array(Yii::t('home', '游戏配置管理'), Yii::t('home', '三国跑跑房间表配置'));
        $this->_settingConfig($this->action->id);
    }

    /**
     * 啪啪萌僵尸游戏配置
     */
    public function actionPaipaimengConfig() {
        $this->breadcrumbs = array(Yii::t('home', '游戏配置管理'), Yii::t('home', '啪啪萌僵尸游戏配置'));
        $this->_settingConfig($this->action->id);
    }

    /**
     * 盖付通黄金矿工游戏配置(物品装备及价格)
     */
    public function actionMinerConfig() {
        $this->breadcrumbs = array(Yii::t('home', '游戏配置管理'), Yii::t('home', '黄金矿工游戏配置'));
        $this->_settingConfig($this->action->id);
    }

    /**
     * 盖付通黄金矿工游戏配置(物品装备及价格)
     */
    public function actionGoldenConfig() {
        $this->breadcrumbs = array(Yii::t('home', '游戏配置管理'), Yii::t('home', '黄金矿工游戏配置'));
        $this->_settingConfig($this->action->id);
    }

    /**
     * 神偷莉莉游戏配置
     */
    public function actionShentouliliConfig() {
        $this->breadcrumbs = array(Yii::t('home', '游戏配置管理'), Yii::t('home', '神偷莉莉游戏配置'));
        $this->_settingConfig($this->action->id);
    }

    /**
     * 弹跳公主游戏配置
     */
    public function actionTantiaogongzhuConfig() {
        $this->breadcrumbs = array(Yii::t('home', '游戏配置管理'), Yii::t('home', '弹跳公主游戏配置'));
        $this->_settingConfig($this->action->id);
    }

}