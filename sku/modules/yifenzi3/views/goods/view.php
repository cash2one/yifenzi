<!--<script src="<?php echo Yii::app()->baseUrl ?>/yifenzi3/js/jquery.lazyload.min.js"></script>-->
<header>
    <h2>商品详情</h2>
    <a href="javascript:history.go(-1);" class="goback_btn"></a>
    <a href="<?php echo $this->createUrl('/yifenzi3/goods/periods', array('id' => $model->goods_id)) ?>" class="periods_show">第<?php echo $oldNper ? $oldNper : $model->current_nper ?>期<i></i></a>
</header>
<div class="container">
<div class="detail_wrap">
    <div class="detail_box">
        <?php if ($oldNper && $oldNper != $model->current_nper): ?>
            <div class="detail_time">
                <p class="time" id="timer" date="<?php echo date('Y-m-d H:i:s', $sumlotterytime) ?>"><span id="time_h">00</span>:<span id="time_m">00</span>:<span id="time_s">00</span></p>
            </div>
            <script type="text/javascript">
                function show_time_detail() {
                    var timer = $("#timer");

                    var time = timer.attr("date");
                    time = time.replace(/-/g,'/');
                    var time_start = new Date().getTime(); //设定当前时间
                    var time_end = new Date(time).getTime(); //设定目标时间
                    var time_distance = time_end - time_start;
                    if (time_distance < 0) {
                        window.location.reload();
                    }
                    else {
                        // 计算时间差
                        var time_distance = time_end - time_start;
                        // 时
                        var int_hour = Math.floor(time_distance / 3600000)
                        time_distance -= int_hour * 3600000;
                        // 分
                        var int_minute = Math.floor(time_distance / 60000)
                        time_distance -= int_minute * 60000;
                        // 秒
                        var int_second = Math.floor(time_distance / 1000)
                        // 时分秒为单数时、前面加零
                        if (int_hour > 99) {
                            int_hour = "99";
                        }
                        if (int_hour < 10) {
                            int_hour = "0" + int_hour;
                        }
                        if (int_minute < 10) {
                            int_minute = "0" + int_minute;
                        }
                        if (int_second < 10) {
                            int_second = "0" + int_second;
                        }
                        // 显示时间
                        timer.find("#time_h").html(int_hour);
                        timer.find("#time_m").html(int_minute);
                        timer.find("#time_s").html(int_second);
                    }


                    // 设置定时器
                    setTimeout("show_time_detail()", 1000);
                }
                show_time_detail();
            </script>
        <?php endif; ?>
        <div class="detail_img">
            <a href="javascript:void(0)">
			<img <?php if(empty($showImage1Exp[0]) && empty($showImage1Exp[1]) && empty($showImage1Exp[2])):?> onerror="this.src='/yifenzi3/images/img-load.png'" <?php endif ?> class="img_big"
			src ="<?php echo ATTR_DOMAIN . '/' . $showImage1Exp[0] ?>" data-src="<?php echo ATTR_DOMAIN . '/' . $showImage1Exp[0] ?>"
			<?php if(!empty($showImage1Exp[1])):?>
			src ="<?php echo ATTR_DOMAIN . '/' . $showImage1Exp[1] ?>" data-src="<?php echo ATTR_DOMAIN . '/' . $showImage1Exp[1] ?>"
			<?php endif ?>
			<?php if(!empty($showImage1Exp[2])):?>
			src ="<?php echo ATTR_DOMAIN . '/' . $showImage1Exp[2] ?>" data-src="<?php echo ATTR_DOMAIN . '/' . $showImage1Exp[2] ?>"
			<?php endif ?>>
			</a>
			<?php if(!empty($showImage1Exp[0])):?>
            <a href="javascript:void(0)" ><img onerror="this.src='/yifenzi3/images/img-load.png'"  class="img_sm" src="<?php echo ATTR_DOMAIN . '/' . $showImage1Exp[0] ?>" data-src="<?php echo ATTR_DOMAIN . '/' . $showImage1Exp[0] ?>"></a>
			<?php endif ?>
            <?php if(!empty($showImage1Exp[1])):?>
            <a href="javascript:void(0)" ><img onerror="this.src='/yifenzi3/images/img-load.png'"  class="img_sm" src="<?php echo ATTR_DOMAIN . '/' . $showImage1Exp[1] ?>" data-src="<?php echo ATTR_DOMAIN . '/' . $showImage1Exp[1] ?>"></a>
            <?php endif ?>
            <?php if(!empty($showImage1Exp[2])):?>
            <a href="javascript:void(0)" ><img onerror="this.src='/yifenzi3/images/img-load.png'"  class="img_sm" src="<?php echo ATTR_DOMAIN . '/' . $showImage1Exp[2] ?>" data-src="<?php echo ATTR_DOMAIN . '/' . $showImage1Exp[2] ?>"></a>
            <?php endif ?>
        </div>
        <div class="detail_text">
            <p class="title">
                <span class="periods_num">[第<?php echo ($oldNper && $oldNper != $model->current_nper) ? $oldNper : $model->current_nper; ?>期]</span>
                <span class="name"><?php echo $model->goods_name; ?></span>
                <span class="subhead"><?php echo $model->after_name; ?></span>
            </p>
            <p class="tips">（产品颜色随机发货）</p>
            <p class="count"><span class="total">价值：&yen; <?php echo number_format($model->shop_price, 2) ?></span><span class="per">&yen; <?php echo number_format($model->single_price, 2) ?>( <?php if ($model->limit_number): ?>限购<?php echo $model->limit_number ?>人份<?php else: ?>无限购<?php endif; ?>)</span></p>
            <p class="spengTop">已参与<span>剩余</span></p>
            <?php
            $total = $model->shop_price / $model->single_price;
            if ($oldNper && $oldNper != $model->current_nper) {
                $number = $total;
            } else {
                $number = empty($orderGoods->goods_number) ? 0 : $orderGoods->goods_number;
            }
            ?>
            <p class="speed"><span class="speedIng" style="width:<?php echo number_format($number / $total, 2) * 100 ?>%"><i></i></span></p>
            <p class="spengBottom"><span class="left"><?php echo $number; ?></span><span class="center">总需人次 <?php echo $total; ?></span><span class="right"><?php echo $total - $number ?></span></p>
        </div>
        <div class="detail_btn">
            <?php
                if (Yii::app()->request->getParam('nper')){
                    $model->current_nper = Yii::app()->request->getParam('nper');
                }
            ?>
            <a href="<?php echo $this->createUrl('/yifenzi3/goods/record', array('id' => $model->goods_id, 'nper' => $model->current_nper)); ?>">参与记录</a>
            <a href="<?php echo $this->createUrl('/yifenzi3/goods/viewDesc', array('id' => $model->goods_id)); ?>">图文详情</a>
        </div>
    </div>
</div>
</div>
<?php if ($oldNper == $nowNper): ?>
    <footer class="detail">
        <a href="javascript:;" class="add_cart addTap" onclick="addGoods(<?php echo $model->goods_id ?>, false);">加入购物车</a>
        <a href="javascript:;" class="goto_buy" onclick="addGoods(<?php echo $model->goods_id ?>, 'link');">马上购</a>
    </footer>
<?php else: ?>
    <footer class="detail ongoing">
        <a class="blue" href="<?php echo Yii::app()->createUrl('/yifenzi3/goods/view', array('id' => $model->goods_id,'nper'=> $nowNper)) ?>">第<?php echo $nowNper ?>期正在进行中...</a>
    </footer>
<?php endif; ?>

<script type="text/javascript">
    $(".detail_img .img_sm").click(function () {
        var imgSrc = $(this).attr("data-src");
        $(".detail_img .img_big").attr("src", imgSrc);
    })

    function addGoods(goods_id, types) {
        var types = types;
        if (!goods_id)
            return false;
        var reg = new RegExp("^[0-9]*$");
        if (!reg.test(goods_id))
            return false;

        $.getJSON("/carts/ajaxadd?goods_id=" + goods_id, function (json) {
//            alert(json.msg);
            if (json.status == 2 || json.status == '2'){
                $('body').addTips();
            }else{
                $('body').addTips({bool:0});
            }
            if (types == 'link' && (json.status == 2 || json.status == '2')) {
                window.location.href = '/carts';
            }
            if(json.status == 1 || json.status == '1'){
                alert(json.msg);
            }
// 			  console.info(json);
        });
    }
</script>