<?php $this->breadcrumbs = array(Yii::t('user', '管理员') => array('admin'), Yii::t('user', '列表')); ?>
<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
    $('#user-grid').yiiGridView('update', {data: $(this).serialize()});
    return false;
});
");
?>
<?php $this->renderPartial('_search', array('model' => $model, 'roles' => $roles)); ?>
<?php if ($this->getUser()->checkAccess('Manage.User.Create')): ?>
    <input id="Btn_Add" type="button" value="<?php echo Yii::t('user', '添加管理员'); ?>" class="regm-sub" onclick="location.href = '<?php echo Yii::app()->createAbsoluteUrl("/user/create"); ?>'">
<?php endif; ?>
<div class="c10"></div>
<?php
$this->widget('GridView', array(
    'id' => 'user-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'cssFile' => false,
    'columns' => array(
        'username',
        'real_name',
        'mobile',
        'email',
        array(
            'name' => 'status',
            'value' => 'User::showStatus($data->status)'
        ),
        array(
            'class' => 'CButtonColumn',
            'header' => Yii::t('home', '操作'),
            'template' => '{update}{delete}{reset}',
            'updateButtonImageUrl' => false,
            'deleteButtonImageUrl' => false,
            'buttons' => array(
                'update' => array(
                    'visible' => '$data->username != "admin" && Yii::app()->user->checkAccess("Manage.User.Update")'
                ),
                'delete' => array(
                    'visible' => '$data->username != "admin" && Yii::app()->user->checkAccess("Manage.User.Delete")'
                ),
                'reset' => array(
                    'label' => Yii::t('user', '重置密码'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.User.Reset')",
                    'url' => 'Yii::app()->createUrl("/user/reset", array("id"=>$data->id))',
                    'click' => "function(){
                        if(!confirm('确定要重置密码吗？')) return false;
                         $.fn.yiiGridView.update('user-grid', {
                            type:'GET',
                            url:$(this).attr('href'),
                            success:function(data) {
                                  if(data){alert('重置密码成功')}
                            }
                        })
                        return false;
                    }",
                    'options' => array(
                        'class' => 'regm-sub-a',
                    )
                ),
            )
        ),
    ),
));
?>