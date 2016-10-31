<div class="container">
    <p class="personal-title"><?php echo $data['nickname'];?></p>
    <div class="personal-imgBox">
        <a href="javascript:;" class="userIcon"></a>
    </div>
    <p class="personalId"><?php echo $data['gai_number'];?></p>
    <p class="personalAddress"><?php
        $address = Address::model()->find('member_id=:id AND `default`=:default',array(':id'=>$data->id,':default'=>Address::DEFAULT_IS));
        $address_new = Member::getMemberAddressNew($data->id);
        if(!$address){
            if(!empty($address_new)){
                $address_new_tool = Region::getName($address_new["province_id"],$address_new["city_id"]);
                echo $address_new_tool;
            }else {
                $adress = Tool::GetIpLookup($data->id);
                if (!empty($adress)) {
                    $address = $adress['province'] . ' ' . $adress['city'];
                } else {
                    $address = '广东 广州';
                }
                echo $address;
            }
        }
        else{
            echo Region::getName($address->province_id,$address->city_id);
        }
        ?></p>
    <p class="accountRemain">账户余额：<em><?php echo $accountBalance;?></em></p>
    <ul class="userInfo">
        <li><a href="<?php echo $this->createUrl("buyRecord"); ?>">购买记录<span class="arrowRight"><span></span></span></a></li>
        <li><a href="<?php echo $this->createUrl("getProduct"); ?>">获得的奖品<span class="arrowRight"><span></span></span></a></li>
        <li>
            <a href="<?php echo $this->createUrl("addressSet"); ?>">
                <p><span>收货地址管理</span><br/><span class="AdrInfo">请添加收货地址</span></p>
                <span class="arrowRight"><span></span></span>

            </a>
        </li>
        <div class="clearfix"></div>
    </ul>
    <?php if(!Yii::app()->user->getState('infosource')):?>
        <div class="unLoginBtn">
            <a href="javascript:;">退出登录</a>
        </div>
    <?php endif;?>
</div>
<div class="h60"></div>
<div class="floatLayout"></div>
<div class="unLoginConfirm">
    <a href="javascript:;" id="out">确定退出</a>
    <a href="javascript:void(0)" class="cancel">取消</a>
</div>
<script>
    $('#out').click(function(){
        window.location.href = "<?php echo $this->createUrl("/yifenzi2/member/logout");?>";
    });
</script>