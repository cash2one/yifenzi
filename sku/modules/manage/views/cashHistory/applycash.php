<?php
/** @var $this CashHistoryController */
?>

<?php $this->renderPartial('_searchcash', array('model' => $model)); ?>
    <div class="c10">
    </div>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-reg2">
        <thead>
        <tr class="tab-reg-title">
            <th>
                <input type="checkbox" id="checkAll" /><label for="checkAll"><?php echo Yii::t('cashHistory', '全选'); ?></label>
            </th>
            <th>id</th>
            <th>申请人</th>
            <th>
                <?php echo Yii::t('cashHistory', '盖网编号'); ?>
            </th>
            <th>
                <?php echo Yii::t('cashHistory', '申请时间'); ?>
            </th>
            <th>
                <?php echo Yii::t('cashHistory', '联系方式'); ?>
            </th>
            <th>
                <?php echo Yii::t('cashHistory', '提现类型'); ?>
            </th>
            <th>
                <?php echo Yii::t('cashHistory', '申请金额'); ?>
            </th>
            <th>
                <?php echo Yii::t('cashHistory', '手续费'); ?>
            </th>
            <th>
                <?php echo Yii::t('cashHistory', '手续费率'); ?>
            </th>
            <th>
                <?php echo Yii::t('cashHistory', '实扣'); ?>
            </th>
            <th>
                <?php echo Yii::t('cashHistory', '状态'); ?>
            </th>
            <th>
                <?php echo Yii::t('cashHistory', '审阅');  ?>
            </th>
            <th>
                <?php echo Yii::t('cashHistory', '操作'); ?>
            </th>
        </tr>
        </thead>
        <tbody id="TabLst">
        <?php /** @var $v CashHistory */ ?>
        <?php foreach ($log as $v): ?>
            <tr class="info">
                <td>
                    <?php if (in_array($v->status, array($v::STATUS_APPLYING,$v::STATUS_CHECKED,$v::STATUS_TRANSFERING)) || $v->is_check==$v::CHECK_NO ): ?>
                        <?php echo CHtml::checkBox('CacheKey',false,array('value'=>$v->id,'data-status'=>$v->status,'data-check'=>$v->is_check)) ?>
                        <span style="display: none">
                            <?php echo $v->member_id ?>&nbsp;&nbsp;
                            <?php echo $v->account_name ?> &nbsp;&nbsp;
                            <?php echo $v->account ?> &nbsp;&nbsp;
                            ¥ <?php echo $v->money ?>
                        </span>
                    <?php endif; ?>
                </td>
                <td><?php echo $v->id ?></td>
                <td><?php echo $v->applyer ?></td>
                <td class="hy">
                    <?php echo $v->member_id ?>
                </td>
                <td>
                    <?php echo $this->format()->formatDatetime($v->apply_time) ?>
                </td>
                <td>
                    <?php echo $v->mobile ?>
                </td>
                <td><?php echo $v::getType($v->type) ?></td>
                <td style="color: Red; font-weight: bold">
                    <span class="jf">&#165; <?php echo $v->money ?></span>
                </td>

                <td>
                    <span class="jf">¥ <?php echo $fee = sprintf('%0.2f', $v->money * $v->factorage / 100) ?></span>
                </td>
                <td>
                    <?php echo $v->factorage ?>%
                </td>
                <td>
                    <span class="jf">¥ <?php echo $v->money + $fee ?></span>
                </td>

                <td>
                    <?php echo $v::status($v->status).'|'.$v->is_check($v->is_check) ?>
                </td>
                <td>
                    <?php if(Yii::app()->user->checkAccess('Manage.CashHistory.SetReview')): ?>
                        <?php echo $v::showReviewStatus($v->id,$v->is_review) ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (Yii::app()->user->checkAccess('Manage.CashHistory.ApplyCashDetail')): ?>
                        <?php echo CHtml::link(Yii::t('cashHistory', '【查看】'), $this->createUrl('cashHistory/applyCashDetail', array('id' => $v->id))); ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr class="user">
                <td colspan="14" style="text-align: left">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <b class="b-neme"><?php echo Yii::t('cashHistory', '开户银行'); ?>:</b>
                    <span class="span-content"><?php echo $v->bank_name ?></span>
                    <b class="b-neme"><?php echo Yii::t('cashHistory', '银行地址'); ?>:</b>
                    <span class="span-content"><?php echo $v->bank_address ?> </span>
                    <b class="b-neme"><?php echo Yii::t('cashHistory', '账户名'); ?>:</b>
                    <span class="span-content"><?php echo $v->account_name ?></span>
                    <b class="b-neme"><?php echo Yii::t('cashHistory', '银行帐号'); ?>:</b>
                    <span class="span-content"><?php echo $v->account ?></span>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="14" align="center">
                <?php if (Yii::app()->user->checkAccess('Manage.CashHistory.CheckedBatch')): ?>
                    <?php echo CHtml::button(Yii::t('cashHistory', '批量审核申请'), array('data-status' => CashHistory::CHECK_YES, 'class' => 'regm-sub checkedBatch')) ?>
                <?php endif; ?>

                <?php if (Yii::app()->user->checkAccess('Manage.CashHistory.CashBatchUpdate')): ?>
                    <?php echo CHtml::button(Yii::t('cashHistory', '批量转账中'), array('data-status' => 'transfering', 'class' => 'regm-sub updateApply')) ?>
                    <?php echo CHtml::button(Yii::t('cashHistory', '批量转账失败'), array('data-status' => 'fail', 'class' => 'regm-sub updateApply')) ?>
                    <?php echo CHtml::button(Yii::t('cashHistory', '批量转账成功'), array('data-status' => 'transfered', 'class' => 'regm-sub updateApply')) ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td colspan="14">
                <div class="pager">
                    <?php
                    /** @var $pages CPagination */
                    $summaryText = '第 {start}-{end} 条 / 共 {count} 条 / {pages} 页';
                    $summaryText = strtr($summaryText, array(
                        '{start}' => $pages->offset + 1,
                        '{end}' => ($pages->currentPage + 1) * $pages->limit,
                        '{count}' => $pages->itemCount,
                        '{pages}' => $pages->pageCount,
                    ));
                    ?>
                    <?php echo $summaryText ?>
                    <?php
                    $this->widget('LinkPager', array(
                        'pages' => $pages,
                        'jump' => false,
                    ))
                    ?>

                </div>
            </td>
        </tr>
        </tbody>
    </table>
    <!--批量操作弹窗格式-->
    <div style="display: none;" id="confirmArea">
        <table width="100%" cellspacing="0" cellpadding="0" border="0" class="tab-come">
            <tbody>
            <tr>
                <th style="text-align: center" id="confimTitle" class="title-th even" colspan="2"></th>
            </tr>
            <tr>
                <td id="confirmDetail" colspan="2" class="odd">

                </td>
            </tr>
            <tr id="confirmTR" >
                <th class="even">
                    <?php echo Yii::t('cashHistory', '原因'); ?>：
                </th>
                <td class="even">
                    <textarea name="confirmReason" id="confirmReason" cols="50" rows="3" style="width: 95%" class="text-input-bj  text-area"></textarea>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <script>
        $("#checkAll").click(function() {
            if (this.checked) {
                $(":input[name='CacheKey']").attr('checked', 'checked');
            } else {
                $(":input[name='CacheKey']").removeAttr('checked');
            }
        });
        //批量审核操作
        $("input.checkedBatch").click(function(){
            var ids = [];
            var details = [];
            $(":input[name='CacheKey']:checked").each(function() {
                //筛选符合条件的
                if($(this).attr('data-check')=='<?php echo CashHistory::CHECK_NO ?>'){
                    ids.push($(this).val());
                    details.push($(this).next().text());
                }
            });
            if (ids.length == 0) {
                art.dialog({
                    icon: 'error',
                    content: '<?php echo Yii::t('cashHistory', '请选择符合条件的兑现记录'); ?>',
                    lock: true
                });
            }else{
                var updateTitle = $(this).val();
                var check = $(this).attr("data-status");
                $("#confimTitle").html("<?php echo Yii::t('cashHistory', '确认操作'); ?> \"" + updateTitle + "\"？");
                $("#confirmDetail").html(details.join("<br/>"));
                $("#confirmTR").hide();
                var content = $("#confirmArea").html();
                $("#confirmTR").show();
                art.dialog({
                    icon: 'question',
                    content: content,
                    lock: true,
                    cancel: true,
                    ok: function() {
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->createUrl('cashHistory/checkedBatch') ?>",
                            data: {
                                idArr: ids.join(','),
                                YII_CSRF_TOKEN: '<?php echo Yii::app()->request->csrfToken ?>',
                                check:check
                            },
                            success: function(data) {
                                art.dialog({
                                    icon: 'succeed',
                                    content: updateTitle + data,
                                    ok:function(){
                                        location.reload();
                                    }
                                });

                            }
                        });
                    }
                })
            }
        });
        //批量操作
        $("input.updateApply").click(function() {
            var ids = [];
            var details = [];
            var status = $(this).attr("data-status");
            $(":input[name='CacheKey']:checked").each(function() {
                //筛选符合条件的
                if($(this).attr('data-check')!='<?php echo CashHistory::CHECK_NO ?>'){
                    if(status=='transfering' && $(this).attr('data-status')=='<?php echo CashHistory::STATUS_TRANSFERING ?>'){
                        //nothing to to
                    }else{
                        ids.push($(this).val());
                        details.push($(this).next().text());
                    }
                }
            });
            if (ids.length == 0) {
                art.dialog({
                    icon: 'error',
                    content: '<?php echo Yii::t('cashHistory', '请选择符合条件的兑现记录,批量转账需要先操作：批量审核申请'); ?>',
                    lock: true
                });
            } else {
                var updateTitle = $(this).val();
                $("#confimTitle").html("<?php echo Yii::t('cashHistory', '确认操作'); ?> \"" + updateTitle + "\"？");
                $("#confirmDetail").html(details.join("<br/>"));
                art.dialog({
                    icon: 'question',
                    content: $("#confirmArea").html(),
                    lock: true,
                    cancel: true,
                    ok: function() {
                        var reason = $("#confirmReason").val();
                        if (reason.length == 0 && status != 'transfering') {
                            alert("<?php echo Yii::t('cashHistory', '请填写原因信息，若成功填写转账人等信息，若失败填写失败原因'); ?>");
                            return false;
                        }
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->createUrl('cashHistory/cashBatchUpdate') ?>",
                            data: {
                                idArr: ids.join(','),
                                reason: reason,
                                YII_CSRF_TOKEN: '<?php echo Yii::app()->request->csrfToken ?>',
                                status: status},
                            success: function(data) {
                                art.dialog({
                                    icon: 'succeed',
                                    content: updateTitle + data,
                                    ok:function(){
                                        location.reload();
                                    }
                                });

                            }
                        });

                    }
                })
            }
        });
    </script>
    <?php if ($this->showExport==true):?>
    <?php $this->renderPartial('/layouts/_export', array('exportPage' => $exportPage, 'totalCount' => $totalCount)); ?>
    <?php endif;?>
