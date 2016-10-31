<header>
    <h2>图文详情</h2>
    <a href="javascript:history.go(-1);" class="goback_btn"></a>
</header>
<div class="container">
<div class="detail_wrap img_detail">
    <?php if($this->beginCache($this->action->id.$model->goods_id,array('duration'=>'3600'))){ ?>
        <div><?php echo $model->goods_desc?></div>
    <?php  $this->endCache(); } ?>
</div>
</div>
<?php
    //$number = empty($orderGoods->goods_number) ? 0 : $orderGoods->goods_number;
    //$total = ceil($model->shop_price / $model->single_price);
?>
<!--<footer class="detail img_detail">
    <p class="spengLeft">已参与<span><?php //echo $number?></span></p>
    <p class="spengRight">剩余<span><?php //echo $total-$number?></span></p>
    <p class="speed"><span class="speedIng" style="width:<?php //echo bcdiv($number, $total,2)?>%"><i></i></span></p>
    <p class="tol">总需人次 <?php //echo $total;?></p>
</footer>-->