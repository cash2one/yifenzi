<header class="normal">
    <h2>参与记录-揭晓结果</h2>
    <a href="javascript:history.go(-1);" class="goback_btn"></a>
    <a href="<?php echo Yii::app()->createUrl('/yifenzi3/goods/periods',array('id'=>$model->goods_id))?>" class="classify">第<?php echo Yii::app()->request->getParam('nper') ? Yii::app()->request->getParam('nper'): $model->current_nper?>期<span></span></a>
</header>
<?php 
    $member = Member::getMemberInfo($winning['member_id']);
    $orderGoods = YfzOrderGoods::getNumberByOrderId($winning['order_id'], $winning['goods_id']);
?>
<div class="container">
    <p class="award-title">获奖者</p>
    <div class="personal-imgBox">
        <div class="userIcon">
            <?php if($member['head_portrait']): ?>
            <?php endif; ?>
        </div>
    </div>
    <p class="award-name"><?php echo ($member && $member['gai_number']) ? substr_replace($member['gai_number'],'****',4,4) : '一份子'?></p>
    <p class="personalAddress"><?php  $adress = Tool::GetIpLookup($member['id']); if(!empty($adress)){echo $adress['province'].' '.$adress['city'];}else{echo '广东 广州';} ?></p>
    <div class="award-info">
        <table>
            <tbody>
                <tr>
                    <td>共参与人次</td>
                    <td>获奖者购买份数</td>
                </tr>
                <tr>
                    <td><span><?php echo ceil($model->shop_price/$model->single_price)?></span>&nbsp;<span class="pIcon"></span></td>
                    <td><span>
                        <?php 
                            //修改了。。。。
                            if($orderGoods){
                                echo $orderGoods->goods_number ? $orderGoods->goods_number : 0;
                            } else
                                echo 0;
                        ?>
                        </span>&nbsp;<span class="fIcon"></span></td>
                </tr>
            </tbody>
        </table>
    </div>
    <p class="award-title">幸运份子</p>
    <div class="award-btnGroup">
        <a href="<?php echo Yii::app()->createUrl('/yifenzi3/goods/winningAll',array('id'=>$model->goods_id,'nper'=>$winning['current_nper'],'winning_code'=>$winning['winning_code']));?>" class="awardBtn award-num"><?php echo $winning['winning_code']?></a>
        <a href="<?php echo Yii::app()->createUrl('/yifenzi3/goods/record',array('id'=>$model->goods_id,'nper'=>$winning['current_nper']));?>" class="awardBtn award-check">参与记录</a>
        <a href="<?php echo Yii::app()->createUrl('/yifenzi3/goods/cloud',array('nper'=>$winning['current_nper'],'id'=>$model->goods_id))?>" class="awardBtn award-check">云计算方式</a>
    </div>
    <!-- <ul class="userInfo">
            <li><a href="#">购买记录<span class="arrowRight"></span></a></li>
            <li><a href="#">获得的奖品<span class="arrowRight"></span></a></li>
            <li>
                    <a href="#">收货地址管理
                            <span class="arrowRight"></span><br/>
                            <span class="AdrInfo">广东省广州市越秀区XX路XX号</span>
                    </a>
            </li>
    </ul> -->
</div>
<div class="h60"></div>
<a href ="<?php echo Yii::app()->createUrl('/yifenzi3/goods/view',array('id'=>$model->goods_id,'nper'=>$model->current_nper))?>">
<footer class="detail ongoing">
    第<?php echo $model["current_nper"]?>期正在进行中...
</footer>
</a>
<script>
    //导航点击切换
    $("#guide").find("a").click(function () {
        $("#guide a").removeClass("active");
        $(this).addClass("active");
    })
</script>