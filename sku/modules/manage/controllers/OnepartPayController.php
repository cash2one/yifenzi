<?php
/**
 * 后台支付管理 控制器
 * @author peiyong.wang<peiyong.wang@g-mall.com>
 * @since 2016-08-23
 */
class OnepartPayController extends MController
{
    /**
     * 支付列表
     */
    public function actionAdmin()
    {
        $model = new YfzPayment('search');
        if(isset($_POST['YfzPayment'])) {
            $PayConfig = $_POST['YfzPayment'];
            Yii::app()->gwpart->createCommand()->update('{{payment}}', array(
                'enabled'=>$PayConfig['jfpaly_enabled'],
            ), 'payment_code=:payment_code', array(':payment_code'=>'JFPALY'));
            Yii::app()->gwpart->createCommand()->update('{{payment}}', array(
                'enabled'=>$PayConfig['ghtpaly_enabled'],
            ), 'payment_code=:payment_code', array(':payment_code'=>'GHTPALY'));
            Yii::app()->gwpart->createCommand()->update('{{payment}}', array(
                'enabled'=>$PayConfig['wxpay_enabled'],
            ), 'payment_code=:payment_code', array(':payment_code'=>'WXPAY'));
        }
        $criteria = $model->search();
        $PayData = $model->findAll($criteria);
        $formConfig = array(
            'id' => $this->id . '-form',
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
            ),
        );
        $this->render('admin',array('model'=>$model,'formConfig'=>$formConfig,'PayData'=>$PayData));
    }
}

