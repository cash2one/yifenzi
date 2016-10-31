<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come" id="tab1">
    <tr>
        <td colspan="6" class="title-th">
            <?php echo Yii::t('site', '个人认证'); ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('member_id') ?>：
        </th>
        <td>
            <?php echo $model->member_id; ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('real_name') ?>：
        </th>
        <td>
            <?php echo $model->real_name ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('identification') ?>：
        </th>
        <td>
            <?php echo $model->identification ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('bank_card_number') ?>：
        </th>
        <td>
            <?php echo $model->bank_card_number; ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('status') ?>：
        </th>
        <td>
            <?php echo MemberPersonalAuthentication::status($model->status); ?>
        </td>
    </tr>

    <tr>
        <th align="right">
            <?php echo Yii::t('site', '操作'); ?>：
        </th>
        <td>
            <input id="Btn_Add" type="button" value="审核通过" class="regm-sub" onclick="location.href = '<?php echo $this->createAbsoluteUrl('/manage/memberPersonalAuthentication/apply',array('id'=>$model->id,'apply'=>'pass'));?>'">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input id="Btn_Add" type="button" value="审核不通过" class="regm-sub" onclick="location.href = '<?php echo $this->createAbsoluteUrl('/manage/memberPersonalAuthentication/apply',array('id'=>$model->id,'apply'=>'unpass'));?>'">
        
        </td>
    </tr>

</table>