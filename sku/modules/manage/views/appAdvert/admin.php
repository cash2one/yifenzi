<?php $this->breadcrumbs = array(Yii::t('appAdvert', '广告位') => array('admin'), Yii::t('appAdvert', '列表')); ?>
<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
    $('#appAdvert-grid').yiiGridView('update', {data: $(this).serialize()});
    return false;
});
");
?>
<?php $this->renderPartial('_search', array('model' => $model)); ?>
<?php if (Yii::app()->user->checkAccess('Manage.AppAdvert.Create')): ?>
    <input id="Btn_Add" type="button" value="<?php echo Yii::t('appAdvert', '添加广告位'); ?>" class="regm-sub" onclick="location.href = '<?php echo Yii::app()->createAbsoluteUrl("/appAdvert/create"); ?>'">
<?php endif; ?>
<div class="c10"></div>
<?php
$this->widget('GridView', array(
    'id' => 'appAdvert-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'cssFile' => false,
    'columns' => array(
        'name',
        'code',
        'content',
        array('name' => 'type', 'value' => 'AppAdvert::getAppAdvertType($data->type)'),
        'width',
        'height',
        array('name' => 'status', 'value' => 'AppAdvert::getAppAdvertStatus($data->status)'),
        array(
            'type' => 'raw',
            'name' => Yii::t('appAdvert', '正在投放广告'),
            'value' => 'Yii::app()->user->checkAccess("Manage.AppAdvertPicture.Admin")?CHtml::link( $data->pictureCount, array("/appAdvertPicture/admin","advert_id"=>"$data->id") ,array("class"=>"reg-sub" )): $data->pictureCount'
        ),
        array(
            'class' => 'CButtonColumn',
            'template' => '{update}{delete}',
            'updateButtonImageUrl' => false,
            'deleteButtonImageUrl' => false,
            'deleteConfirmation' => Yii::t('advert', '删除广告位将连同删除所有所属广告，请谨慎操作！'),
            'buttons' => array(
                'update' => array(
                    'label' => Yii::t('user', '编辑'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.AppAdvert.Update')"
                ),
                'delete' => array(
                    'label' => Yii::t('user', '删除'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.AppAdvert.Delete')"
                ),
            )
        )
    ),
));
?>