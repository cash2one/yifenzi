<?php
$this->breadcrumbs = array(
    Yii::t('category', '选择类别'),
);
?>
<script type="text/javascript">
    var btnCancelClick = function() {
        art.dialog.close();
    }
</script>
<ul id="tree"></ul>
<div class="c10"></div>
<?php echo CHtml::button('取消', array('class' => 'reg-sub', 'onclick' => 'btnCancelClick()')); ?>

<?php // echo Yii::app()->createUrl('/category/categoryTree', array('YII_CSRF_TOKEN'=>Yii::app()->request->csrfToken)) ?>
<script src="/js/iframeTools.js" type="text/javascript"></script>
<script type="text/javascript">
    jQuery(function($) {
        $('#tree').tree({
            method: 'get',
            data: <?php echo $data; ?>,
            loadFilter: function(data) {
                if (data.d) {
                    return data.d;
                } else {
                    return data;
                }
            },
            onClick: function(node) {
                if (node) {
                    var p = artDialog.open.origin;
                    if (p && p.onSelectedCat) {
                        p.onSelectedCat(node.id, node.text);
                    }
                } else {
                    alert('<?php echo Yii::t('category', '异常，未选中值！'); ?>');
                }
                p.doClose();
            }
        });

    });
</script>
<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/js/easyui/themes/default/easyui.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/js/easyui/themes/icon.css");
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/easyui/jquery.easyui.min.js");
?>