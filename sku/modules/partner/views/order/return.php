<?php
/* 协商退货 */
/* @ var $this OrderController */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="Keywords" content="" />
        <meta name="Description" content="" />
        <title><?php echo CHtml::encode($this->pageTitle) ?></title>
        <link href="<?php echo CSS_DOMAIN; ?>global.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo CSS_DOMAIN; ?>seller.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo DOMAIN ?>/css/custom.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo DOMAIN ?>/css/custom-seller.css" rel="stylesheet" type="text/css"/>
        <script src="<?php echo DOMAIN ?>/js/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="<?php echo DOMAIN ?>/js/artDialog/jquery.artDialog.js?skin=aero"></script>
        <script src="<?php echo DOMAIN ?>/js/artDialog/plugins/iframeTools.source.js" type="text/javascript"></script>
    </head>
    <body>
        <div style="padding: 25px 10px;">
            <?php echo Yii::t('partnerModule.order', '经与会员协商同意，扣除运费 {a}元后退回订单款项，会员进行退货，订单关闭。',array('{a}'=>$returnInfo['freight'])); ?>
            <div style="width:500px; text-align: center; margin-top: 10px;"> 
                <input type="button" value="<?php echo Yii::t('partnerModule.order', '同意'); ?>" class="button_red1" onclick="RePurcharse('agree', '<?php echo $returnInfo['orderId']; ?>')"/>
                <input type="button" value="<?php echo Yii::t('partnerModule.order', '不同意'); ?>" class="button_red1" onclick="RePurcharse('disagree', '<?php echo $returnInfo['orderId']; ?>')"/>
            </div>
        </div>
    </body>
</html>
<script type='text/javascript'>
                    var RePurcharse = function(repit, orderId) {
                        location.href = '/order/return/orderId/' + orderId + '/repit/' + repit;
                    };
                    if (typeof success != 'undefined') {
//                        alert(success);
                        art.dialog.opener.goodsReturn(success);
                        art.dialog.close();
                    }
</script>

