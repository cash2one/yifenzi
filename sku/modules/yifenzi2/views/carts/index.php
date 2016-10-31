<style type="text/css">
.liSwipeLeft{
	animation:swLeft 0.5s ease-in-out 1;
	-webkit-animation:swLeft 0.5s ease-in-out 1;
	-webkit-animation-fill-mode:forwards; 
    animation-fill-mode:forwards; 
}
.liSwipeRight{
	animation:swRight 0.5s ease-in-out 1;
	-webkit-animation:swRight 0.5s ease-in-out 1; 
	-webkit-animation-fill-mode:forwards;
	animation-fill-mode:forwards;
}
@keyframes swLeft{
	from{
		transform:translateX(0px);
	}
	to{
		transform:translateX(-60px);
	}
}

@-webkit-keyframes swLeft{
	from{
		-webkit-transform:translateX(0px);
	}
	to{
		-webkit-transform:translateX(-60px);
	}
}
@keyframes swRight{
	from{
		transform:translateX(-60px);
	}
	to{
		transform:translateX(0px);
	}
}

@-webkit-keyframes swRight{
	from{
		-webkit-transform:translateX(-60px);
	}
	to{
		-webkit-transform:translateX(0px);
	}
}
</style>
<header class="normal">
		<h2>购物车</h2>
		<a href="javascript:history.go(-1);" class="goback_btn"></a>
	</header>
	<div class="container">
		<form>
			<ul class="carList">
			<?php if($goodsdata):?>
				<?php foreach ( $goodsdata as $k=>$v ):?>
				<li max="<?php echo $v['goods_number'];?>">
					<div class="circle"><span data="<?php echo $k;?>" class="active"></span></div>
					<?php if(isset($v['goods_thumb'])){?>
					<a href="<?php echo Yii::app()->createUrl('/yifenzi2/goods/view',array('id'=>$v['goods_id'],'nper'=>$v['current_nper']))?>"><img width=88 height=88 src="<?php echo $v['goods_thumb']?>"></a>
					<?php }else{?>
					<a href="<?php echo Yii::app()->createUrl('/yifenzi2/goods/view',array('id'=>$v['goods_id'],'nper'=>$v['current_nper']))?>"><img width=88 height=88  src="<?php echo $v['goods_image']?>"></a>
					<?php }?>
					
					<div class="proDetail">
						<p class="number"><?php echo $v['current_nper_desc'];?></p>
						<p class="goodsInfo"><?php echo $v['goods_name'];?></p>
						<p class="remain">剩余<span><?php echo $v['goods_number'];?></span>人次</p>
						<p class="calculate">
							<span class="act subtract subtract_<?php echo $k;?>" data="<?php echo $k;?>">-</span>
							<span class="calBoard" data="<?php echo $k;?>"><?php echo $v['num'] ? $v['num'] : 1;?>
							</span>
							<span class="act plus plus_<?php echo $k;?>" data="<?php echo $k;?>">+</span>
							<span class="price">&yen <span><?php echo $v['single_price'];?><!-- </span>.00</span> -->
						</p>
					</div>
					<div class="proDel proDel_<?php echo $k;?>" data="<?php echo $k;?>"></div>
				</li>
				<?php endforeach;?>
				<script>
					var index = $('.circle').find('span.active').length;
					var maxLen = $('.circle').length;
					$(function(){
						var sum = 0;//总价格
						if(index==0){

							$('.settlement').addClass('notActive').parent().attr('href','javascript:void(0)');
						}
						else if(index>=maxLen){
							$('.circle2').find('span').addClass('active');
							calSum(index);
						}
						else{
							calSum(index)
							$('.totalPrice').html(sum);
						}
						$('.totalAmount').html(index);

					});

					function calSum(total){
						var val = 0;
						for(var i=0;i<total;i++){
							var num = $('.circle').eq(i).siblings('.proDetail').find('.calBoard').text();
							var count = $('.circle').eq(i).siblings('.proDetail').find('.price>span').text();
							val += num*count;
						}
						sum = val;
						$('.totalAmount').html(total);
						$('.totalPrice').html(sum);
						return false;
					}
				</script>
			<?php endif;?>
			</ul>
		</form>
		<div class="tips" <?php if(!$goodsdata){?>style="display:block"<?php }?>>
			<div class="carEmpty"></div>
			<span>你的购物车还没有任何商品</span>
			<a href="<?php echo $this->createUrl("site/index"); ?>">马上去逛逛</a>
			</div>
		<div class="h100"></div>
	</div>
<div class="totalAmountBox">
		<form>
			<div>
				<div class="circle2"><span class="active"></span></div><span>全选</span>
				<p>共<span class="totalAmount">0</span>个商品,合计<span><span class="totalPrice"></span>.00</span></p>
				<a href="javascript:;"><span class="settlement<?php if(!$goodsdata){?> notActive<?php }?>"  onclick="checkGoodsNum();">去结算</span></a>
			</div>
		</form>
</div>
<div class="layer"></div>
<div class="shareBox">
	<p>购买份额</p>
	<div class="toolBox">
		<input type="text">
		<a href="javascript:void(0)" class="confirmShare">确认</a>
		<a href="javascript:void(0)" class="cancelShare">取消</a>
	</div>		
</div>
<div class="loading"></div>
<div class="errorTips">
	<p>商品库存不足，或者已变期</p>
	<a href="javascript:void(0)">返回购物车</a>
</div>
<script>
	var tmp_orderid = [];
	var boolear = true;

    function checkGoodsNum(goods_id){
        var jssum = 1;
        var ajaxsum = 1;
        var check = true;
        $(".circle .active").each(function(){
            jssum++;
            var good_id = $(this).attr("data");
            var amount = parseInt($('.plus_'+good_id).siblings('.calBoard').text());
            $.getJSON("/carts/AjaxGoodsnum?goods_id="+good_id+"&carr_goods_num="+amount, function(json){
                ajaxsum++;
                console.log(json);
                if (json.status == 404 || json.status == '404'){
                    alert(json.msg);
                    $('.plus_'+good_id).siblings('.calBoard').html(json.num);
                    $('.plus_'+good_id).parents('.calculate').siblings('.remain').find('span').html(json.num);
                    check = false;
                }
                if((ajaxsum == jssum) && (check)){
                    goodsbuy();
                }

            });
        });
    }

	function goodsbuy(){
		var goodsnum = $(".circle .active").length;
		if( goodsnum >= 1 ){
			var idList = [];
			$(".circle .active").each(function(){
				idList.push($(this).attr("data"));
			});
			$.ajax({
				type:"post",
				url:"/order/queueorder",
				data:{'YII_CSRF_TOKEN':"<?php echo Yii::app()->request->csrfToken?>","idList":idList},
				dataType:"json",
				success:function(data){
					console.log(data);
					if (data.err == 1 || data.err == '1'){
						alert(data.msg);
						return false;
					}

					if (data.err == 3 || data.err == '3'){
						window.location.href="/member/login";
					}
					
					if ( data.err == 2 || data.err == '2' ){
						tmp_orderid = data.data;
					}

				},
				complete:function(data, stauts){
					if ( tmp_orderid && tmp_orderid != '' ){
						//循环一份钟，如果后台一直没有响应那么最后一次直接提示处理数据有误
						//准备传给后台数据
                        $('.layer,.loading').show();
						setInterval(toOrder, 1000); 
					}
					
				}
			});
		}
	}

	var httpOrderNum = 0; //如果轮询三十秒还没有回应则直接提示退出
	//去提交订单
	function toOrder(){
		httpOrderNum++;

		if (httpOrderNum == 60){
		//	alert('商品库存不足，或者已变期');
            $('.errorTips').show();
            httpOrderNum = 0
			return false;
		}
		if (boolear == false) 
			return false;

		if ( !tmp_orderid ) return false;
		var queueData = tmp_orderid;

		//购物车用户选择的商品ID
		var idList = [];
		$(".circle .active").each(function(){
			idList.push($(this).attr("data"));
		});
		
		$.ajax({
			type:"post",
			url:"/order/toorder",
			data:{'YII_CSRF_TOKEN':"<?php echo Yii::app()->request->csrfToken?>","queueID":queueData,"idList":idList},
			dataType:"json",
			success:function(msg){
				console.log(msg);		
				if ( msg.err == 2 || msg.err == '2' ){
					boolear = false;
// 					alert(msg.msg);
					window.location.href=  "<?php echo DOMAIN_YIFENZI; ?>/order/topayment/order_id/" + msg.msg + "/types/2";
				}		
			}
		});
	}

	//导航点击切换
		$("#guide").find("a").click(function(){
			$("#guide a").removeClass("active");
			$(this).addClass("active");
		})
		//减持单位数
		$('.subtract').click(function(){
			var goods_id = $(this).attr("data");

			if (!goods_id) return false;

			var reg = new RegExp("^[0-9]*$");
			if(!reg.test(goods_id)) return false;

			//此用是用户对自己购物商品的数量进行相减操作。在请求的过程中，如果用户请求失败不作硬操作。
			//如果是用户已经登陆，实际购物数量为字段num为准
			$.ajax({type:'get',url:'/carts/ajaxdel?goods_id='+goods_id,success:function(data){
// 				console.log("ajax success:"+data);
				var calBoard = $('.subtract_'+goods_id).siblings('.calBoard');
				var amount = calBoard.text();
				if(amount<=1){
					amount=1;
					return false;
				}
				else{
					calBoard.html(--amount);
				}
				if($('.subtract_'+goods_id).parents('.proDetail').siblings('.circle').find('span').hasClass('active')){
					sum -= parseInt($('.subtract_'+goods_id).siblings('.price').find('span').text());
					$('.totalPrice').html(sum);
				}
			}})
			
		});
		//增加单位数

		$('.plus').click(function(){
			var goods_id = $(this).attr("data");
			var max = $(this).parents('li').attr('max');

			if (!goods_id) return false;

			var reg = new RegExp("^[0-9]*$");
			if(!reg.test(goods_id)) return false;

			$.getJSON("/carts/ajaxadd?goods_id="+goods_id, function(json){
				console.log(json);
				if (json.status == 2 || json.status == '2'){
					var calBoard = $('.plus_'+goods_id).siblings('.calBoard');
					var amount = calBoard.text();
					amount = parseInt(amount);
					if(amount>=max){
						amount=max;
						return false;
					}
					else{
						calBoard.html(++amount);
					}
					if($('.plus_'+goods_id).parents('.proDetail').siblings('.circle').find('span').hasClass('active')){
						sum += parseInt($('.plus_'+goods_id).siblings('.price').find('span').text());
						$('.totalPrice').html(sum);
					}
				}else{
					alert(json.msg);
				}
			});
		});

		var sum = 0;//总价格

		$('.circle').tap(function(){
			var sp = $(this).find('span');
			var num = $(this).siblings('.proDetail').find('.calBoard').text();
			var count = $(this).siblings('.proDetail').find('.price>span').text();
			var sumCache = num*count;
			if(sp.hasClass('active')){//取消选择
				index--;
				if(index<=0){
					$('.settlement').addClass('notActive');
				}
				sum -= sumCache;
				$('.circle2').find('span').removeClass('active');
			}
			else{
				index++;
				if(index==1){
//					$('.settlement').removeClass('notActive').parent().attr('href',link);
					$('.settlement').removeClass('notActive');
				}
				sum += sumCache;
				if(index>=maxLen){
					$('.circle2').find('span').addClass('active');
				}
			}
			sp.toggleClass('active');

			$('.totalAmount').html(index);
			$('.totalPrice').html(sum);
		})
		//全选
		var circle2Click = 0;
		$('.circle2').click(function(){
			var sp = $(this).find('span');
			if(circle2Click){
				circle2Click--;
				sp.removeClass('active');
				$('.circle').find('span').removeClass('active');
				index = 0;
			}
			else{
				circle2Click++;
				sp.addClass('active');
				$('.circle').find('span').addClass('active');
				index = $('.circle').find('span.active').length;
			}
            if(index<=0){
                $('.settlement').addClass('notActive');
            }else{
                $('.settlement').removeClass('notActive');
            }
			calSum(index);
		})

		//计算总额
		
		$('.proDel').click(function(){
			if(confirm('确定要从购物车删除该商品吗？')){
			var goods_id = $(this).attr("data");

			if (!goods_id) return false;

			var reg = new RegExp("^[0-9]*$");
			if(!reg.test(goods_id)) return false;

			$.ajax({type:'get',url:'/carts/ajaxdel?goods_id='+goods_id+'&types=goods',success:function(data){
					if (data == '1' || data == 1 || data == true){
						$(".proDel_"+goods_id).parents('li').remove();
						index--;
						if($('.circle').find('span.active').length==0){
							sum = 0;
							$('.totalAmount').html('0')
							$('.totalPrice').html(sum);
							//$(".tips").css({display:"block"});
							
						}
						else{
							calSum(index);
						}

						if ($(".carList li").length == 0){

							$('.settlement').addClass('notActive');

							$('.circle2').find('span').removeClass('active');
							var newHeight = $(window).height()-144;
							$('.container').css({height:newHeight})
							$(".tips").css({display:"block"});
						};
						//更新通栏中购物车商品数量
						lodingCartNum();
					}			
				}
			});
			}else{
				return false;
			}
		})
		
		//输入购买份数
		$(function(){
			var _this;
            var goods_id;
			$('.calBoard').click(function(){
				$('.layer').show();
				$('.shareBox').show();
				$('.shareBox input').val($(this).text().trim());
				_this = $(this);
                goods_id = $(this).attr("data");
			})
            $('.confirmShare').click(function(){
                $('.layer').hide();
                $('.shareBox').hide();

                var input_num = $('.shareBox input').val();

                if(isNaN(input_num)){
                    alert("购买份额只能为数字");
                    return false;
                }
                if(input_num<=0){
                    alert("购买份额必须大于0");
                    return false;
                }

                //var input_new = _this.html($('.shareBox input').val());
                var index = $('.circle').find('span.active').length;

                $.ajax({
                    type: 'get',
                    dataType: 'json',
                    url: '/carts/inputgoodsnums?goods_id='+goods_id+'&input_num='+input_num,
                    success: function(data) {
                        console.log(data);
                        if(parseInt(data.status)==1){
                            alert(data.msg);
                            return false;
                        } else if(parseInt(data.status)==2){
                            _this.html($('.shareBox input').val());
                            calSum(index);
                            return true;
                        }
                    }
                });
            })
            $('.cancelShare').click(function(){
                $('.layer').hide();
                $('.shareBox').hide();
            })
            $('.errorTips a').click(function(){
                $(this).parent().hide();
                $('.layer').hide();
                window.location.reload();
            })
		})
	</script>