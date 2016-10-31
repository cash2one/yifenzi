<?php
/* @var $this InputGoodsController */
/* @var $model EnGoodsRule */
$this->breadcrumbs = array(Yii::t('category', '发布管理'), Yii::t('inputGoods', '条码商品发布'));
?>
<?php 
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#inputGoods-grid').yiiGridView('update', {
		data: $(this).serialize()
	})
});
");
?>
<table border="1" cellspacing="1" cellpadding="0" style="text-align: center;">
    <tr>
        <td width="150px" height="40px"><a href="<?php echo Yii::app()->createAbsoluteUrl('/inputGoods/release') ?>" class="title"><?php echo Yii::t('inputGoods', '条码商品发布') ?></a></td>
<!--        <td width="150px"><?php if ($this->getUser()->checkAccess('Manage.InputGoods.storeActive')): ?><a href="<?php echo Yii::app()->createAbsoluteUrl('/inputGoods/storeActive') ?>"><?php echo Yii::t('inputGoods', '店铺录入活动商品') ?></a><?php endif;?></td>  -->
    </tr>
</table>
<?php if ($this->getUser()->checkAccess('Manage.InputGoods.enCreate')): ?>
<a style=" float: right" class="regm-sub" href="<?php echo Yii::app()->createAbsoluteUrl('/inputGoods/enCreate') ?>"><?php echo Yii::t('FreshMachine', '添加项目') ?></a>
<?php endif;?>
<?php
$this->widget('GridView', array(
    'id' => 'inputGoods-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
        array(
            'name' => 'name',
            'value' => '(isset($data->name))?EnGoodsRule::getName($data->name):""',
            'type' => 'raw',
        ),
        array(
            'name' => 'type',
            'value' => 'isset($data->type)?EnGoodsRule::getType($data->type):""',
            'type' => 'raw',
        ),
         array(
            'name' => 'upload_bonus',
            'value' => '$data->upload_bonus',
            'type' => 'raw',
        ),
        array(
            'name' => 'adopt_bonus',
            'value' => '$data->adopt_bonus',
            'type' => 'raw',
        ),
        array(
            'name' => 'is_input',
            'value' =>'isset($data->is_input)?EnGoodsRule::getInput($data->is_input):""',
            'type' => 'raw',
        ),
        array(
              'header' => '操作',
            'class' => 'CButtonColumn',
            'template' => '{update}{delete}',
            'htmlOptions' => array('style' => 'width:120px', 'class' => 'button-column'),
            'viewButtonImageUrl' => false,
            'buttons' => array(
                'update' => array(
                    'imageUrl' => false,
                    'label' => Yii::t('inputGoods', '编辑'),
                    'url' => 'Yii::app()->createUrl("inputGoods/update",array("id"=>$data->id))',
                    'visible' => "Yii::app()->user->checkAccess('Manage.FreshMachine.update')"
                ),
                 'delete' => array(
                    'imageUrl' => false,
                    'label' => Yii::t('inputGoods', '删除'),
                    'url' => 'Yii::app()->createUrl("inputGoods/deleteRule",array("id"=>$data->id))',
                    'visible' => "Yii::app()->user->checkAccess('Manage.FreshMachine.update')"
                ),
            )
        ),
    ),
));
?>
<script>
    $(document).ready(function () {
        $('.title').css({'font-weight':'bold'});
    });
    </script>