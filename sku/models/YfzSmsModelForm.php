<?php

/**
 * 一份子后台短信模板
 * 
 */
class YfzSmsModelForm extends CFormModel {

    public $winner;
  
    public function rules() {
        return array(
            array('winner','required'),
            array('winner', 'safe')
        );
    }

    public function attributeLabels() {
        return array(
        	'winner' => Yii::t('home','中奖信息模板内容'),
        );
    }

}
