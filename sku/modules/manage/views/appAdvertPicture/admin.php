<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
$('#appAdvertPicture-grid').yiiGridView('update', {
data: $(this).serialize()
});
//	return false;
});
");
?>

<?php $this->breadcrumbs = array(Yii::t('appAdvertPicture', '广告位图片') => array('appAdvert/admin'), Yii::t('appAdvertPicture', '列表')); ?>
<div class="search-form" >
    <?php $this->renderPartial('_search', array('model' => $model,'id'=>$id)); ?>
</div>
<?php if (Yii::app()->user->checkAccess('Manage.AppAdvertPicture.Create')): ?>
    <input id="Btn_Add" type="button" value="<?php echo Yii::t('appAdvertPicture', '添加广告'); ?>" class="regm-sub" onclick="location.href = '<?php echo Yii::app()->createAbsoluteUrl("/appAdvertPicture/create" ,array('advert_id'=>Yii::app()->request->getParam('advert_id'))); ?>'">
<?php endif; ?>

<?php echo CHtml::link('返回','javascript:void(0)',array('onclick'=>'javascript:history.go(-1)','class'=>'regm-sub'))?>

<div class="c10"></div>
<?php
$this->widget('GridView', array(
    'id' => 'appAdvertPicture-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'cssFile' => false,
    'columns' => array(
//        array(
//            'selectableRows' => 2,
//            'footer' => '<button type="button" onclick="GetCheckbox();" class="regm-sub">' . Yii::t('appAdvertPicture', '批量删除') . '</button>',
//            'class' => 'CCheckBoxColumn',
//            'headerHtmlOptions' => array('width' => '33px'),
//            'checkBoxHtmlOptions' => array('name' => 'selectdel[]'),
//        ),
        array(
            'name' => 'advert_id',
            'value' => '$data->appAdvert->name'
        ),
        'name',
        array(
            'type' => 'datetime',
            'name' => 'start_time',
            'value' => '$data->start_time'
        ),
        array(
            'type' => 'raw',
            'name' => 'end_time',
            'value' => 'empty($data->end_time) ? "<font color=\'green\'>永不过期</font>" : date("Y-m-d H:i:s", $data->end_time)'
        ),
        'sort',
        array(
            'type' => 'raw',
            'name' => 'status',
            'value' => '!empty($data->end_time) && $data->end_time < time() ? "<font color=\'red\'>过期</font>" : AppAdvertPicture::getStatus($data->status)',
        ),
        array(
            'name' => 'target',
            'value' => 'AppAdvertPicture::getTargetType($data->target_type)'
        ),
        'target',
        'group',
        'seat',
        'text',
        array(
            'name' => 'picture',
            'value' => '$data->picture ? CHtml::image(ATTR_DOMAIN."/".$data->picture, $data->name, array("width" => "22px", "height" => "22px")) : ""',
            'type' => 'raw'
        ),
        array(
            'name' => '所属地区',
            'value' => 'Region::getName($data->province_id, $data->city_id)',
            'type' => 'raw'
        ),
        array(
            'class' => 'CButtonColumn',
            'header' => Yii::t('home', '操作'),
            'template' => '{update}{delete}',
            'updateButtonImageUrl' => false,
            'deleteButtonImageUrl' => false,
            'buttons' => array(
                'update' => array(
                    'label' => Yii::t('user', '编辑'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.AppAdvertPicture.Update')"
                ),
                'delete' => array(
                    'label' => Yii::t('user', '删除'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.AppAdvertPicture.Delete')"
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
        if (!confirm('<?php echo Yii::t('appAdvertPicture', '确定要删除这些数据吗?'); ?>'))
            return false;
        if (data.length > 0) {
            $.post('<?php echo Yii::app()->createAbsoluteUrl('/appAdvertPicture/delall'); ?>', {'selectdel[]': data, 'YII_CSRF_TOKEN': '<?php echo Yii::app()->request->csrfToken; ?>'}, function(data) {
                if (data != null && data.success != null && data.success) {
                    $.fn.yiiGridView.update('appAdvertPicture-grid');
                }
            }, 'json');
        } else {
            alert("<?php echo Yii::t('appAdvertPicture', '请选择要删除的数据!'); ?>");
        }
    }
    /*]]>*/
</script>