<style>
    #tbRoles tr th, #tbRoles tr td { padding: 5px; }
    #tbRoles hr{color:#ccc;margin:2px 0;border:1px  #dfdfdf;}
</style>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'authItem-form',
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true
    ),
));
?>
<?php // echo $form->errorSummary($model);?>
<table width="100%" border="0" cellspacing="1" cellpadding="0" class="tab-come">
    <tbody><tr><td colspan="2" class="title-th even" align="center">管理员角色</td></tr></tbody>
    <tbody>
        <tr>
            <th style="width: 120px" class="odd"><?php echo $form->labelEx($model, 'name'); ?></th>
            <td class="odd">
                <?php if ($model->isNewRecord): ?>
                    <?php echo $form->textField($model, 'name', array('maxlength' => 255, 'class' => 'text-input-bj long')); ?>
                <?php else : ?>
                    <?php echo $model->name; ?>
                <?php endif; ?>

                <?php echo $form->error($model, 'name'); ?>
            </td>
        </tr>
        <tr>
            <th class="even"><?php echo $form->labelEx($model, 'description'); ?></th>
            <td class="even">
                <?php echo $form->textField($model, 'description', array('maxlength' => 255, 'class' => 'text-input-bj long')); ?>
                <?php echo $form->error($model, 'description'); ?>
            </td>
        </tr>
        <tr>
            <th class="odd"></th>
            <td class="odd">
                <?php if ($this->action->id == 'createRole'): ?>
                    <?php echo CHtml::submitButton(Yii::t('user', '添加'), array('class' => 'reg-sub')); ?>
                <?php else: ?>
                    <?php echo CHtml::submitButton(Yii::t('user', '编辑'), array('class' => 'reg-sub')); ?>
                <?php endif; ?>
            </td>
        </tr>
    </tbody>
</table>
<table width="100%" border="1" cellspacing="1" cellpadding="0" id="tbRoles">
    <tbody>
        <tr class="tab-reg-title">
            <th style="width: 100px; color: #fff;">一级菜单</th>
            <th style="color: #fff;">二级菜单（页面）</th>
        </tr>
        <tr>
            <td>
                <input type="checkbox" id="checkAll" onchange="chooseAll()"><label for="checkAll">全选</label>
                <script type="text/javascript">
                    var chooseAll = function() {
                        var checkValue = $("#checkAll").attr("checked")
                        $("#tbRoles :input").each(function() {
                            if (checkValue == "checked") {
                                $(this).attr("checked", checkValue);
                            } else {
                                $(this).removeAttr("checked");
                            }
                        });
                    };
                </script>
            </td>
            <td colspan="2"></td>
        </tr>
        <?php $this->renderPartial('_admin', array('rights' => $rights)); ?>
        <?php $this->renderPartial('_config', array('rights' => $rights)); ?>
        <?php $this->renderPartial('_cateconfig', array('rights' => $rights)); ?>
		<?php $this->renderPartial('_partner', array('rights' => $rights)); ?>
		<?php $this->renderPartial('_goods', array('rights' => $rights)); ?>
        <?php $this->renderPartial('_advert', array('rights' => $rights)); ?>
        <?php $this->renderPartial('_webData', array('rights' => $rights)); ?>
        <?php $this->renderPartial('_questResult', array('rights' => $rights)); ?>
        <?php $this->renderPartial('_cash', array('rights' => $rights)); ?>
        <?php $this->renderPartial('_gamemanage', array('rights' => $rights)) ?>
        <?php $this->renderPartial('_guadan', array('rights' => $rights)) ?>
        <?php $this->renderPartial('_jiaoyi', array('rights' => $rights)) ?>
        <?php $this->renderPartial('_yifenzi', array('rights' => $rights)) ?>
    </tbody> 
</table>
<?php $this->endWidget(); ?>