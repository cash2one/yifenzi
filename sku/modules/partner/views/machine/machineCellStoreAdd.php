<?php
/* @var $this FranchiseeArtileController */
/* @var $model FranchiseeArtile */

$this->breadcrumbs=array(
    Yii::t('partnerModule.machine','格子铺管理'),
    Yii::t('partnerModule.machine','添加格子铺商品'),
);
?>

    <div class="toolbar">
        <h3><?php echo Yii::t('partnerModule.machine','添加格子铺商品'); ?></h3>
    </div>


<?php $this->renderPartial('_machine_cellStore_form', array('model'=>$model,'m_model' => $m_model,'mid'=>$mid,'name'=>isset($name)?$name:'')); ?>