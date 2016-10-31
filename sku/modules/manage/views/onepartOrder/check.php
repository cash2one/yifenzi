<?php
/* @var $this ProductController */
/* @var $model Product */
$this->breadcrumbs = array('公事验证' => array('check'), '列表');
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#goods-grid').yiiGridView('update', { //orderid
		data: $(this).serialize()
	});
//	return false;
});
");
?>
<div class="search-form" >
    <?php $this->renderPartial('_searchorder', array('model'=>$model)); ?>
</div>

<?php if(!empty($retArr)):?>
<div class="search-form" >
    <div class="result" style="font-size:30px;">
        <p>取以下数值结果得</p>
        <p>1. 求和: <?php echo $retArr['formuladata']['h_i_s_sum']?>(下面100条换算数据的时间之和)</p>
        <p>2. 取余: （<?php echo $retArr['formuladata']['h_i_s_sum'].'%'.$retArr['formuladata']['nperall']?>）+ 10000001</p>
        <p>3. 结果 <?php echo ($retArr['formuladata']['winning_code']+10000001).'~~'.($retArr['formuladata']['winning_code']+10001000); ?></p>
    </div>
</div>
    <div class="c10"></div>
    <div id="yifenGoods-grid" class="grid-view">
        <table class="tab-reg">
            <thead>
            <tr>
                <th>时间详情</th>
                <th>换算数据</th>
                <th>会员账号</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($retArr['allusername'] as $k=>$v):?>
                <tr>
                    <td><?php echo $retArr['yffdata'][$k].' '.$retArr['hisdata'][$k];?></td>
                    <td><?php echo $retArr['sumhisdata'][$k];?></td>
                    <td><?php echo $v;?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>

    </div>

<?php else:?>
    <div class="c10"></div>
    <div id="second-kill-grid" class="grid-view">
        <table class="tab-reg">
            <thead>
            <tr>
                <th>时间详情</th>
                <th>换算数据</th>
                <th>会员账号</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
        <div style ="text-align:center;margin-top:10px;"><?php echo Yii::t('goods','没有找到数据');?></div>
    </div>
<?php endif ?>
<!--增加开始-->
<div style="display: none" id="confirmArea">
    <style>
        .aui_buttons{
            text-align: center;
        }
    </style>
    <?php
    $form = $this->beginWidget('ActiveForm', array(
        'id' => $this->id . '-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
    ));
    ?>

    <?php $this->endWidget(); ?>

</div>
<!--增加结束-->
<script src="<?php echo DOMAIN_M?>/js/swf/js/artDialog.iframeTools.js"></script>
<script type="text/javascript">

</script>