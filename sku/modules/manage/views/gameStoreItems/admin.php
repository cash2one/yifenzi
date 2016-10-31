<?php $this->breadcrumbs = array(Yii::t('gameStore', '游戏配置管理') => array('admin'), Yii::t('gameStoreItems', '游戏商品列表')); ?>
<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
    $('#gameStoreItems-grid').yiiGridView('update', {data: $(this).serialize()});
    return false;
});
");
?>
<?php //$this->renderPartial('_search', array('model' => $model));?>

<?php if (Yii::app()->user->checkAccess('Manage.GameStoreItems.Create')): ?>
    <a class="regm-sub" href="<?php echo Yii::app()->createAbsoluteUrl('/GameStoreItems/Create',array('storeId' => $store->id)) ?>"><?php echo Yii::t('gameStoreItems', '添加商品') ?></a>
    <div class="c10"></div>
<?php endif; ?>

<?php
$this->widget('GridView', array(
    'id' => 'gameStoreItems-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'tab-reg',
    'cssFile' => false,
    'columns' => array(
        array(
            'name' => 'flag',
            'value' => 'GameStoreItems::getFlagStatus($data->flag)'
        ),
        array(
            'name' => 'store_id',
            'value' => 'GameStore::name($data->store_id)'
        ),
        'item_name',
        'item_number',
        'item_status',
        //'item_description',
        //'store_description',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'limit_per_time',
        //'bees_number',
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
            'template' => '{update}',
            'header' => Yii::t('home', '操作'),
            'updateButtonImageUrl' => false,
            'buttons' => array(
                'update' => array(
                    'label' => Yii::t('gameStore', '编辑'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.GameStoreItems.Update')"
                ),
            )
        ),
    ),
));
?>