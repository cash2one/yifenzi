<div>
<?php
    $this->breadcrumbs = array('售卖管理' => array('admin'), '售卖详情');
    // var_dump($model);exit;
?>
</div>
<div class="grid-view" id="guadan-grid">
    <table class="tab-reg">
        <thead>
        <tr>
            <th colspan="2" style="text-align:left;">挂单提取编号：<?php echo $model->code ?></th>
            <th colspan="2" style="text-align:right;">
                <?php //echo CHtml::link('删除',array('guadan/delCollect','id'=>$v['id']),array('class'=>'collectDel')); ?>&nbsp;
                <?php //echo CHtml::link('编辑','#',array('class'=>'edit')); ?>
            </th>
        </tr>
        </thead>
        <tr >
            <td width="200">待绑定积分额度:</td>
            <td style="text-align:left"><strong><?php echo $model->amount_bind ?></strong></td>
            <td width="200">非绑定积分额度:</td>
            <td style="text-align:left"><strong><?php echo $model->amount_unbind ?></strong></td>
        </tr>
        <tr >
            <td width="200">绑定粒度:</td>
            <td style="text-align:left"><strong><?php echo $model->bind_size ?></strong>积分/人</td>
            <td width="200">会员推荐者分配比例:</td>
            <td style="text-align:left"><strong><?php echo $model->distribution_ratio ?></strong>%</td>
        </tr>
        <tr>
            <td colspan="2">
                <b>新用户政策</b> <?php //exit; echo CHtml::link('+',array(
                //     'guadanRule/add','collect_id'=>$v['id'],
                //     'type'=>GuadanRule::NEW_MEMBER,
                //     'amount_bind'=>$v['amount_bind'],
                //     'amount_unbind'=>$v['amount_unbind'],
                // ),
                //     array('style'=>'font-size:30px;','class'=>'addNew','title'=>'添加新用户政策')); ?>
            </td>
            <td colspan="2">
                <b>老用户政策</b> <?php //echo CHtml::link('+',array(
                //     'guadanRule/add','collect_id'=>$v['id'],
                //     'type'=>GuadanRule::OLD_MEMBER,
                //     'amount_bind'=>$v['amount_bind'],
                //     'amount_unbind'=>$v['amount_unbind'],
                // ),
                //     array('style'=>'font-size:30px;','class'=>'addOld','title'=>'添加老用户政策')); ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" width="50%" >
                <?php
                    $this->renderPartial('_ruleTable',array('collect'=>$model,'type'=>GuadanRule::NEW_MEMBER));
                ?>
            </td>
            <td colspan="2" width="50%" >
                <?php
                $this->renderPartial('_ruleTable',array('collect'=>$model,'type'=>GuadanRule::OLD_MEMBER));
                ?>
            </td>
        </tr>
    </table>
</div>