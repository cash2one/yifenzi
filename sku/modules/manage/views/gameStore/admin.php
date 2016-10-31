<?php $this->breadcrumbs = array(Yii::t('gameStore', '游戏配置管理') => array('admin'), Yii::t('gameStore', '游戏店铺列表')); ?>
<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
    $('#gameStore-grid').yiiGridView('update', {data: $(this).serialize()});
    return false;
});
");
?>
<?php $this->renderPartial('_search', array('model' => $model)); ?>

<?php if (Yii::app()->user->checkAccess('Manage.GameStore.Create')): ?>
    <a class="regm-sub" href="<?php echo Yii::app()->createAbsoluteUrl('/GameStore/Create') ?>"><?php echo Yii::t('gameStore', '添加店铺') ?></a>
    <div class="c10"></div>
<?php endif; ?>

<?php
$this->widget('GridView', array(
    'id' => 'gameStore-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'cssFile' => false,
    'columns' => array(
        'gai_number',
        'store_name',
        'store_phone',
        'store_address',
        'limit_time_hour',
        'limit_time_minute',
        array(
            'name' => 'store_status',
            'value' => 'GameStore::status($data->store_status)'
        ),
        array(
            'name' => 'create_time',
            'value' => 'date("Y/m/d H:i:s", $data->create_time)'
        ),
        array(
            'name' => 'update_time',
            'value' => 'date("Y/m/d H:i:s", $data->update_time)'
        ),
        array(
            'class' => 'CButtonColumn',
            'template' => '{update}{items}',
            'header' => Yii::t('home', '操作'),
            'updateButtonImageUrl' => false,
            'buttons' => array(
                'update' => array(
                    'label' => Yii::t('gameStore', '编辑'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.GameStore.Update')"
                ),
                'items' => array(
                    'label' => Yii::t('gameStore', '商品'),
                    'imageUrl' => false,
                    'url' => 'Yii::app()->createUrl("/gameStoreItems/admin",array("storeId" => $data->id))',
                    'visible' => "Yii::app()->user->checkAccess('Manage.GameStoreItems.Admin')"
                ),
            )
        ),
    ),
));
?>