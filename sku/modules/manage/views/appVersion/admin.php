<?php $this->breadcrumbs = array(Yii::t('appVersion', '客户端') => array('admin'), Yii::t('appVersion', '列表')); ?>
<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
    $('#appVersion-grid').yiiGridView('update', {data: $(this).serialize()});
    return false;
});
");
?>
<?php $this->renderPartial('_search', array('model' => $model)); ?>
<?php if (Yii::app()->user->checkAccess('Manage.AppVersion.Create')): ?>
    <input id="Btn_Add" type="button" value="<?php echo Yii::t('appVersion', '添加客户端'); ?>" class="regm-sub" onclick="location.href = '<?php echo Yii::app()->createAbsoluteUrl("/appVersion/create"); ?>'">
<?php endif; ?>
<div class="c10"></div>
<?php
$this->widget('GridView', array(
    'id' => 'appVersion-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'cssFile' => false,
    'columns' => array(
//        array(
//            'selectableRows' => 2,
//            'footer' => '<button type="button" onclick="GetCheckbox();" class="regm-sub">' . Yii::t('appVersion', '批量删除') . '</button>',
//            'class' => 'CCheckBoxColumn',
//            'headerHtmlOptions' => array('width' => '33px'),
//            'checkBoxHtmlOptions' => array('name' => 'selectdel[]'),
//        ),
        array('name' => 'type', 'value' => 'AppVersion::getFlag($data->type)'),
        array('name' => 'system_type', 'value' => 'AppVersion::getSystemType($data->system_type)'),
        'name',
        'url',
        'size',
        'version',
        'version_name',
        array('name' => 'user_id', 'value' => 'AppVersion::getUserNameById($data->user_id)'),
        array('name' => 'create_time', 'value' => 'date("Y-m-d H:i:s", $data->create_time)'),
        array('name' => 'update_time', 'value' => 'date("Y-m-d H:i:s", $data->update_time)'),
        'apk_name',
        array('name' => 'is_auto_download', 'value' => 'AppVersion::getAutoDownload($data->is_auto_download)'),
        array('name' => 'is_visible', 'value' => 'AppVersion::getVisible($data->is_visible)'),
        array('name' => 'is_published', 'value' => 'AppVersion::getPublished($data->is_published)'),
        array(
            'class' => 'CButtonColumn',
            'header' => Yii::t('home', '操作'),
            'template' => '{update}{delete}',
            'updateButtonImageUrl' => false,
            'deleteButtonImageUrl' => false,
            'buttons' => array(
                'update' => array(
                    'label' => Yii::t('user', '编辑'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.AppVersion.Update')"
                ),
                'delete' => array(
                    'label' => Yii::t('user', '删除'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.AppVersion.Delete')"
                ),
            )
        )
    ),
));
?>
<script type='text/javascript'>
    /*<![CDATA[*/
    var GetCheckbox = function() {
        var data = new Array();
        $("input:checkbox[name='selectdel[]']").each(function() {
            if ($(this).attr("checked") == 'checked') {
                data.push($(this).val());
            }
        });
        if (!confirm('<?php echo Yii::t('appVersion', '确定要删除这些数据吗?'); ?>'))
            return false;
        if (data.length > 0) {
            $.post('<?php echo Yii::app()->createAbsoluteUrl('/appVersion/delall'); ?>', {'selectdel[]': data, 'YII_CSRF_TOKEN': '<?php echo Yii::app()->request->csrfToken; ?>'}, function(data) {
                if (data != null && data.success != null && data.success) {
                    $.fn.yiiGridView.update('appVersion-grid');
                }
            }, 'json');
        } else {
            alert("<?php echo Yii::t('appVersion', '请选择要删除的数据!'); ?>");
        }
    }
    /*]]>*/
</script>