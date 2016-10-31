<?php
/**
 * @author zhenjun_xu <412530435@qq.com>
 * Date: 2016/1/14 0014
 * Time: 18:54
 */
$rule = GuadanRule::getRule($collect['id'],$type);
?>
<?php if(!empty($rule)): ?>
<table width="100%" class="rule">
    <thead>
    <tr>
        <th>商品名</th>
        <th>面值</th>
        <th>官方售价</th>
        <th>赠送</th>
        <th>总优惠比例</th>
        <th>充值赠送分期</th>
        <th>本金返还分期</th>
        <th>操作</th>
    </tr>
    </thead>
    <?php foreach($rule as $v): ?>
    <tr>
        <td><?php echo CHtml::encode($v['title']) ?></td>
        <td><?php echo $v['amount'] ?></td>
        <td><?php echo $v['amount_pay'] ?></td>
        <td><?php echo $v['amount_give'] ?></td>
        <td><?php
            if($v['amount_pay']>0){
                echo  number_format($v['amount_give']/$v['amount_pay']*100 + $collect['distribution_ratio'],0);
            }else{
                $collect['distribution_ratio'];
            }

            ?>
        </td>
        <td><?php echo $v['give_installment'] ?></td>
        <td><?php echo $v['amount_installment'] ?></td>
        <td>
        	<?php if(Yii::app()->user->checkAccess('Manage.GuadanRule.Del')):?>
            <?php echo CHtml::link('删除',array('guadanRule/del','id'=>$v['id']),array('class'=>'ruleDel')); ?>
            <?php endif;?>
            <?php if(Yii::app()->user->checkAccess('Manage.GuadanRule.Edit')):?>
            <?php echo CHtml::link('编辑',array('guadanRule/edit','id'=>$v['id']) ,array('class'=>'ruleEdit')); ?>
            <?php endif;?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>