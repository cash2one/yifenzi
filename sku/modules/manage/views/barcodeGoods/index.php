<?php
/** @var  CActiveForm $form */
/** @var UploadForm $model */
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'importBarcode-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
));
?>
    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="tab-come">
        <tbody>
        <tr>
            <td>
                <?php echo $form->labelEx($model, 'file') ?>
            </td>
            <td>
                <?php echo $form->fileField($model, 'file') ?>
                <?php echo $form->error($model, 'file', array(), false); ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <?php echo CHtml::submitButton('上传', array('class' => 'reg-sub')) ?>&nbsp;&nbsp;&nbsp;
                <?php echo CHtml::link('模板',Yii::app()->createUrl('barcodeGoods/template'), array('class'=>'reg-sub')) ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                上传文件只支持zip格式压缩包，压缩包里只能放图片和exel文件，不能放文件夹，图片只支持JPG、PNG格式，exel文件支持xlsx、xls格式，
                图片名称对应的是条形码。
            </td>
        </tr>
        </tbody>
    </table>
<?php $this->endWidget(); ?>
<?php if (!empty($result)): ?>
    <br/>
    <hr/>
    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="tab-come">
        <thead>
        执行结果：  <?php //echo CHtml::link('导出excel',Yii::app()->createUrl('barcodeGoods/export',array('data'=>CJSON::encode($result))), array('class'=>'regm-sub')) ?>
        </thead>
        <tr>
            <td>序号</td>
            <td>条形码</td>
            <td>商品名称</td>
            <td>规格</td>
            <td>默认售价</td>
            <td>单位</td>
            <td>是否成功</td>
            <td>备注</td>
        </tr>
        <?php foreach ($result as $k => $v):
            ?>
            <tr>
                <td><?php echo $k+1 ?></td>
                <td><?php echo $v['barcode'] ?></td>
                <td><?php echo $v['name'] ?></td>
                <td><?php echo $v['model'] ?></td>
                <td><?php echo $v['default_price'] ?></td>
                <td><?php echo $v['unit'] ?></td>
                <td><?php echo $v['status']==0 ? '成功':'失败' ?></td>
                <td><?php echo $v['mark'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>