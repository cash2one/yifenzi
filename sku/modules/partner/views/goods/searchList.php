<?php
$this->breadcrumbs = array(
    Yii::t('partnerModule.superGoods', '商品搜索'),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
        if (!$('#Goods_name').val()) {
            alert('" . Yii::t('partnerModule.superGoods', '请输入商品名') . "');
            return false;
        }
	$('#goods-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<script type="text/javascript" language="javascript" src="<?php echo DOMAIN ?>/js/iframeTools.source.js"></script>
<script type="text/javascript">
    var btnOKClick = function(obj) {
        var keyword = obj.hash.replace('#', '');

var id = keyword.match(/.+\$\*1\$\*/g).toString();
id = id.replace('\$\*1\$\*','');
id = id.replace('1\$\*','');
var name = keyword.match(/\$\*1\$\*.+\$\*2\$\*/g).toString();
name = name.replace('\$\*1\$\*','');
name = name.replace('\$\*2\$\*','');

        if (!id) {
            alert(<?php echo Yii::t('partnerModule.superGoods', "请选择商品"); ?>);
            return false;
        }
        var p = artDialog.open.origin;
        if (p && p.onSelectGoods) {
            p.onSelectGoods(id,name);
        }
        p.doClose();
    }

    var btnCancelClick = function() {
        art.dialog.close();
    }
</script>

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    ));
    ?>

<div class="seachToolbar">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="sellerT5">
        <tbody>
        <tr>	
          <th width="7%" class="ta_r"><?php echo Yii::t('partnerModule.superGoods', '请输入商品名'); ?>：</th>
            <td width="18%">
           			<?php echo $form->textField($model, 'name', array('class' => 'inputtxt1')); ?>
                    <?php echo CHtml::submitButton(Yii::t('partnerModule.superGoods', '搜索'), array('class' => 'reg-sub')); ?>
            </td>
            </tr>
    </table>
    <?php $this->endWidget(); ?>
</div>

<?php
$this->widget('GridView', array(
    'id' => 'goods-grid',
    'dataProvider' => $model->superGoodsSearch($type,$sid,$all),
    'itemsCssClass' => 'mt15 sellerT3 goodsIndex',
    'cssFile'=>false,
    'pager'=>array(
        'class'=>'LinkPager',
        'htmlOptions'=>array('class'=>'pagination'),
    ),
    'pagerCssClass'=>'page_bottom clearfix',
     'columns' => array(
        array(
            'class' => 'CButtonColumn',
            'template' => '{select}',
            'buttons' => array(
                'select' => array(
                    'label' => Yii::t('partnerModule.superGoods','选择'),
					'url' => '"#".$data->id."$*1$*".$data->name."$*2$*"',
                    'options' => array(
                        'class' => 'reg-sub',
                        'onclick' => "btnOKClick(this)",
                    ),
                ),
            ),
        ),
        array(
			'name'=>Yii::t('partnerModule.superGoods','商品名'),
			'value'=>'$data->name',
		),
		array(
			'name'=>Yii::t('partnerModule.superGoods','价格'),
			'value'=>'$data->price',
		),

    ),
));


?>