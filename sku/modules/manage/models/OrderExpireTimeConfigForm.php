<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OrderExpireTimeConfigForm
 *
 * @author Administrator
 */
class OrderExpireTimeConfigForm extends CFormModel{	
    public $orderExpireTime;            //订单未支付超时时间
    public $orderUnsendRefundTime;  //订单支付后未发货可以手动申请退款的时间
    public $orderUnsendAutoRefundTime;  //订单支付后未发货超时退款的时间
    public $machineAutoCancelTime;  //售货机订单自动取消
    public $machineUnTakeAutoCancelTime;    //售货机订单用户不取货自动取消时间  在线下单支付
    public $machineScanOrderUnTakeAutoCancelTime;    //售货机订单用户不取货自动取消时间  扫码下单支付
    
     public function rules(){
        return array(
            array('orderExpireTime,machineAutoCancelTime, orderUnsendAutoRefundTime,machineUnTakeAutoCancelTime,machineScanOrderUnTakeAutoCancelTime','required'),
            array('orderExpireTime,orderUnsendRefundTime,machineAutoCancelTime, orderUnsendAutoRefundTime,machineUnTakeAutoCancelTime,machineScanOrderUnTakeAutoCancelTime', 'numerical'),
        );
    }
    
     public function attributeLabels() {
        return array(
            'orderExpireTime' => Yii::t('home','订单未支付超时时间'),
            'orderUnsendRefundTime' => Yii::t('home','订单支付后未发货可以手动申请退款的时间'),
            'machineAutoCancelTime' => Yii::t('home','售货机订单自动取消'),
            'orderUnsendAutoRefundTime' => Yii::t('home','订单支付后未发货超时退款的时间'),
            'machineUnTakeAutoCancelTime' => Yii::t('home','售货机在线下单订单用户不取货自动取消时间'),
        	'machineScanOrderUnTakeAutoCancelTime' => Yii::t('home','售货机扫码下单订单用户不取货自动取消时间'),
        );
    }
}
