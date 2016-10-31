
<?php
$this->breadcrumbs = array('商品' => array('onepartGoods/admin'), '购买详情');
?>
<!--这里是中奖人信息-->

<!--这里是搜索信息-->
<?php
$form = $this->beginWidget('ActiveForm', array(
    'id' => 'lottery-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
));
?>
<!--下面是列表-->
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come" id="sec">
    <tbody>
    <tr>
        <th colspan="2" class="title-th odd">开奖设置</th>
    </tr>
    <tr>
        <td width="5%">商品总价格:</td>
        <td><?php echo $data['shop_price'] ?></td>
    </tr>

    <tr>
        <td width="5%">每人次数:</td>
        <td><?php echo $data['single_price'] ?></td>
    </tr>
    <tr>
        <td width="5%">库存:</td>
        <td><?php echo $data['goods_number'] ?></td>
    </tr>
    <tr>
        <td width="5%">当前期数:</td>
        <td><?php echo $data['current_nper'] ?></td>
    </tr>
    <tr>
        <td>奖品:<input type="text" name="goods_num" value="0"/></td>
        <td></td>
    </tr>

    <tr>
        <td>数:<input type="text" name="sum" value="0"/></td>
        <td></td>
    </tr>

    <tr>
        <td></td>
        <td>
            <?php if ($this->getUser()->checkAccess('onepartOrderGoods.Lottery')) : ?>
            <?php echo CHtml::submitButton(Yii::t('goods', '开门'), array('class' => 'reg-sub')); ?>
            <?php endif?>
        </td>
    </tr>
    </tbody>
</table>
<?php $this->endWidget();?>