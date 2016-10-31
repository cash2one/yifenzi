<?php
    if(is_null($winning)) echo '中奖码 飞走了';
    $winningCode = json_decode($winning->winning_code);
?>

<div style="text-align: center;font-size: 20px"> 购买号码 </div>
<div style="line-height: 37px;width:291px;text-align: center;">
    <?php foreach ($winningCode as $k=>$w):?>
    
	<span <?php if($w==$win_codes){ echo 'style="background:red;padding: 10px;font-size: 14px;font-weight: bold;"';}?> style="padding: 10px;font-size: 14px;font-weight: bold;">
        <?php echo $w?>
    </span>
    <?php endforeach;?>
</div>