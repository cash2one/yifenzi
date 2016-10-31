<?php
/**
 * @author zhenjun_xu <412530435@qq.com>
 * Date: 2016/1/12 0012
 * Time: 13:41
 * @var $this MController;
 */
$this->breadcrumbs = array('积分批发' => array('admin'), '积分列表');
?>
<style>
    .search-form{ line-height:40px;border-bottom: 1px solid #000000;margin-bottom: 14px }

</style>

<div class="search-form" >
    <strong>积分批发</strong>
    <?php if (Yii::app()->user->checkAccess('Manage.Guadanpifa.Create')): ?>
    <a href="<?php echo Yii::app()->createAbsoluteUrl("/guadanpifa/create"); ?>"  class="regm-sub addCollect" style="float:right">新增政策</a>
    <?php endif; ?>
</div>
<div style="width:100%;height:30px;line-height:30px;float:left;background: #DADAFE"><span style="float:left;font-size:14px;font-weight: 700;color:#6F6F6F;padding-left: 6px">全国</span> <p style="float:right">
        <?php if(!empty($rule)):?>
        <?php if (Yii::app()->user->checkAccess('Manage.Guadanpifa.Delete')): ?>
        <a href="#" id ="del" style="padding-left: 6px;padding-right: 6px;color:#0000FF">删除</a>
        <?php endif; ?>
        <?php if (Yii::app()->user->checkAccess('Manage.Guadanpifa.Update')): ?>
        <a href="<?php echo Yii::app()->createAbsoluteUrl("/guadanpifa/update",array('id'=>$id)); ?>" style="padding-left: 6px;padding-right: 15px;color:#0000FF">编辑</a></p></div>
      <?php endif; ?>
<?php endif; ?>
<div style="width:100%;height:100%;float:left">
    <div class="left" style="float: left;width:25%;font-size:14px;height:auto;background: #FFFFCC">
        <p style="height: 30px;border-bottom: 1px solid #C9C9C9;border-right: 1px solid #C9C9C9;border-left: 1px solid #C9C9C9"><span style="padding-left: 5px;padding-right: 5px;">限额:</span><span><?php echo $limitScore ;?></span></p>
        <p style="height: 30px;border-bottom: 1px solid #C9C9C9;border-right: 1px solid #C9C9C9;border-left: 1px solid #C9C9C9"><span style="padding-left: 5px;padding-right: 5px;">已使用:</span><span><?php echo $saleAmountBind;?>(<?php echo empty($limitScore)?0:bcdiv($saleAmountBind,$limitScore,4)*100;?>%)</span></p>
        <p style="height: 30px;border-bottom: 1px solid #C9C9C9;border-right: 1px solid #C9C9C9;border-left: 1px solid #C9C9C9"><span style="padding-left: 5px;padding-right: 5px;">商家推荐者分配比例:</span><span><?php echo $ratio;?>%</span></p>
        <?php for($i = 0;$i<$num;$i++){ ?>
        <p style="height: 30px;border-bottom: 1px solid #C9C9C9;border-right: 1px solid #C9C9C9;border-left: 1px solid #C9C9C9"><span style="padding-left: 5px;padding-right: 5px;"></p>
        <?php } ?>
    </div>
    <div class="right"style="float: left;width:75%;height:100%;font-size:14px;">
        <table style="width:100%;">
            <tr style="width:100%;height: 30px;line-height:30px;background: #F2F2F2;border-bottom: 1px solid #C9C9C9;border-right: 1px solid #C9C9C9;">
                <th style="width:25%;font-weight: 700;color:#333333">批发金额</th>
                <th style="width:25%;font-weight: 700;color:#333333">折扣</th>
                <th style="width:25%;font-weight: 700;color:#333333">总优惠比例</th>
            </tr>
            <?php if(is_array($rule)) {?>

            <?php foreach($rule as $k=>$v){ ?>

            <tr style="width:100%;height: 30px;line-height:30px;border-bottom: 1px solid #C9C9C9;border-right: 1px solid #C9C9C9;">
                <?php if($v['min_score'] == 0){ ?>
                    <td style="width:25%;text-align: center;border-right: 1px solid #C9C9C9;font-weight: 400;font-size:14px;">n<<?php echo (int)$v['max_score'];?></td>
                    <?php }elseif($v['max_score'] == 0){ ?>
                    <td style="width:25%;text-align: center;border-right: 1px solid #C9C9C9;font-weight: 400;font-size:14px;"><?php echo (int)$v['min_score'];?>≤n</td>
                    <?php }else{ ?>
                    <td style="width:25%;text-align: center;border-right: 1px solid #C9C9C9;font-weight: 400;font-size:14px;"><?php echo (int)$v['min_score'];?>≤n<<?php echo (int)$v['max_score'];?></td>
                    <?php }?>

                <td style="width:25%;text-align: center;border-right: 1px solid #C9C9C9;font-weight: 400;font-size:14px;"><?php echo (int)$v['ratio']; ?></td>
                <td style="width:25%;text-align: center;border-right: 1px solid #C9C9C9;font-weight: 400;font-size:14px;"><?php echo (100-$v['ratio'])+$ratio;?>%</td>
            </tr>

                <?php } ?>
            <?php }?>
        </table>

    </div>

</div>
<script>
    $("#del").click(function(){
        if(confirm("确定要删除？")) {
            var url = '<?php echo Yii::app()->createAbsoluteUrl('/guadanpifa/delete',array('id'=>$id)) ?>';
            window.location.href = url;
        }
    })


</script>

