 <div class="border-info clearfix search-form">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'GET',
    ));
    ?>
	
    <table cellpadding="0" cellspacing="0" class="searchTable">
        <tbody>
            <tr>
                <th><?php echo '订单号：'; ?></th>
                <td><input type="text" name="order_sn" id="val1" value="<?php echo Yii::app()->request->getParam('order_sn')?>" class="text-input-bj least"></td>
				<th><?php echo '发货状态：'; ?></th>
                <td>
				<input type="radio" name="is_invoice" value="0" <?php if(Yii::app()->request->getParam('is_invoice')==0) echo("checked");?> >未发货
				<input type="radio" name="is_invoice" value="1" <?php if(Yii::app()->request->getParam('is_invoice')==1) echo("checked"); ?> >已发货
				<input type="radio" name="is_invoice" value="2" <?php if(Yii::app()->request->getParam('is_invoice')==null || Yii::app()->request->getParam('is_invoice')==2  ) echo("checked");?> >全部
				</td>
				<th><?php echo '收货状态：'; ?></th>
				<td>
				<input type="radio" name="is_delivery" value="0" <?php if(Yii::app()->request->getParam('is_delivery')==0) echo("checked");?> >未收货
				<input type="radio" name="is_delivery" value="1" <?php if(Yii::app()->request->getParam('is_delivery')==1) echo("checked"); ?> >已收货
				<input type="radio" name="is_delivery" value="2" <?php if(Yii::app()->request->getParam('is_delivery')==null || Yii::app()->request->getParam('is_delivery')==2) echo("checked");?> >全部
				</td>
				<th><?php echo '支付状态：'; ?></th>
                <td>
				<input type="radio" name="order_status" value="0" <?php if(Yii::app()->request->getParam('order_status')==0) echo("checked");?> >未支付
				<input type="radio" name="order_status" value="1" <?php if(Yii::app()->request->getParam('order_status')==1) echo("checked"); ?> >支付成功
				<input type="radio" name="order_status" value="2" <?php if(Yii::app()->request->getParam('order_status')==2) echo("checked");?> >支付失败
				<input type="radio" name="order_status" value="3" <?php if(Yii::app()->request->getParam('order_status')==null || Yii::app()->request->getParam('order_status')==3) echo("checked");?> >全部
				</td>
				
                <th><?php echo '中奖用户ID：'; ?></th>
                <td><input type="text" name="member_id" id="val1" value="<?php echo Yii::app()->request->getParam('member_id')?>" class="text-input-bj least"></td>
                <th><?php echo '商品ID：'; ?></th>
                <td><input type="text" name="goods_id" id="val1" value="<?php echo Yii::app()->request->getParam('goods_id')?>" class="text-input-bj least"></td>
                <td><select id="test" name="select_order" class="text-input-bj least" onclick="fun()"> 
				<option value ="0" <?php if(Yii::app()->request->getParam('select_order')==0) echo("selected");?>>默认</option>
                <option value ="1" <?php if(Yii::app()->request->getParam('select_order')==1) echo("selected");?>>按购买时间顺序</option>
				<option value ="2" <?php if(Yii::app()->request->getParam('select_order')==2) echo("selected");?>>按购买总价顺序</option>
				<option value ="3" <?php if(Yii::app()->request->getParam('select_order')==3) echo("selected");?>>按购买次数顺序</option>
                </select></td>
            </tr>
        </tbody>
    </table>

    <?php echo CHtml::submitButton(Yii::t('user', '搜索'), array('class' => 'reg-sub')); ?>
    <?php $this->endWidget(); ?>
</div>
