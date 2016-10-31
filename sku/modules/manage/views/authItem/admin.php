<?php
/* @var $this BrandController */
/* @var $model Brand */

$this->breadcrumbs = array(
    Yii::t('user', '管理员角色 '),
    Yii::t('user', '列表'),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#authItem-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<?php
$this->renderPartial('_search', array(
    'model' => $model,
));
?>
<?php if (Yii::app()->user->checkAccess('Manage.AuthItem.createRole')): ?>
    <a class="regm-sub" href="<?php echo Yii::app()->createAbsoluteUrl('/authItem/createRole') ?>"><?php echo Yii::t('user', '添加角色') ?></a>
<?php endif; ?>
<div class="c10"></div>
<?php
$this->widget('GridView', array(
    'id' => 'authItem-grid',
    'dataProvider' => $model->search(),
    'cssFile' => false,
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
        'name',
        'description',
        array(
            'class' => 'CButtonColumn',
            'template' => '{update}{delete}',
            'updateButtonImageUrl' => false,
            'deleteButtonImageUrl' => false,
            'buttons' => array(
                'update' => array(
                    'label' => Yii::t('user', '编辑'),
                    'visible' => 'Yii::app()->user->checkAccess("Manage.AuthItem.UpdateRole") && $data->name !== "Admin"',
                    'url' => 'Yii::app()->createUrl("/authItem/updateRole", array("name"=>$data->name))'
                ),
                'delete' => array(
                    'label' => Yii::t('user', '删除'),
                    'visible' => 'Yii::app()->user->checkAccess("Manage.AuthItem.Delete") && $data->name !== "Admin"',
                    'url' => 'Yii::app()->createUrl("/authItem/delete", array("name"=>$data->name))'
                ),
            )
        )
    ),
));
?>