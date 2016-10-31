<?php
$this->breadcrumbs=array(
    Yii::t('MemberBind','绑定管理'),
    Yii::t('MemberBind','绑定详情'),
);
?>
    <input id="Btn_Return" type="button" value="<?php echo Yii::t('MemberBind', '返回'); ?>" class="regm-sub" onclick="location.href = '<?php echo $this->createAbsoluteUrl("/operatorBinding/admin"); ?>'" />
    <h></h>
    <div class="border-info clearfix search-form">
        <table class="searchTable">
            <tr>
                <td>
                    <span>绑定时间：<font color="red"><?php echo date("Y-m-d H:i:s",$result['create_time']); ?>   </font></span>
                </td>
                <td>
                    <span>绑定状态：<font color="red"><?php echo OperatorRelation::getStaus($result['status']); ?>   </font></span>
                </td>
            </tr>
            <tr>

                <td>
                    <span>商家GW号：<font color="red"><?php echo $result['pr_gai_number']; ?>   </font></span>
                </td>

                <td>
                    <span>运营方商家GW号：<font color="red"><?php echo $result['ps_gai_number']; ?>   </font></span>
                </td>
            </tr>

        </table>
    </div>

    <div class="coypyright"  style="display: none;" id="confirmArea">

    </div>
<?php
?>