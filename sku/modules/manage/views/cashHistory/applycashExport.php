<?php

/* @var $this CashHistoryController */
/* @var $model CashHistory */
?>
<?php

$this->widget('comext.PHPExcel.EExcelView', array(
    'id' => 'prepaid-card-grid',
    'dataProvider' => $model->search(true),
    'itemsCssClass' => 'tab-reg',
    'title' => '提现申请单',
    'cssFile' => false,
    'columns' => array(
        array(
            'name' => Yii::t('cashHistory', '申请会员'),
            'value' => '$data->applyer',
            'type' => 'raw'
        ),
       'member_id',
        array(
            'name' => Yii::t('cashHistory', '申请时间'),
            'value' => 'date("Y-m-d G:i:s",$data->apply_time)',
            'type' => 'raw'
        ),
        array(
            'name' => Yii::t('cashHistory', '联系方式'),
            'value' => '$data->mobile',
            'type' => 'raw'
        ),
        array(
            'name' => Yii::t('cashHistory', '申请金额'),
            'value' => '$data->money',
            'type' => 'raw'
        ),
        array(
            'name' => Yii::t('cashHistory', '手续费'),
            'value' => 'sprintf("%0.2f", $data->money * $data->factorage / 100)',
            'type' => 'raw'
        ),
        array(
            'name' => Yii::t('cashHistory', '手续费率'),
            'value' => '$data->factorage',
            'type' => 'raw'
        ),
        array(
            'name' => Yii::t('cashHistory', '实扣金额'),
            'value' => '$data->money + sprintf("%0.2f", $data->money * $data->factorage / 100)',
            'type' => 'raw'
        ),
        array(
            'name' => Yii::t('cashHistory', '状态'),
            'value' => 'CashHistory::status($data->status)',
            'type' => 'raw'
        ),
        
        array(
            'name' => Yii::t('cashHistory', '开户银行'),
            'value' => '$data->bank_name',
            'type' => 'raw'
        ),
        
        array(
            'name' => Yii::t('cashHistory', '银行地址'),
            'value' => '$data->bank_address',
            'type' => 'raw'
        ),
        
        array(
            'name' => Yii::t('cashHistory', '银行帐户名'),
            'value' => '$data->account_name',
            'type' => 'raw'
        ),
        
        array(
            'name' => Yii::t('cashHistory', '银行卡号'),
            'value' => '" ".$data->account',
            'type' => 'raw'
        ),
        
        
    ),
));
?>