<?php if($this->footerDisplay):?>
<footer>
    <nav id="guide">
        <a href="/" class="home<?php if($this->footerPage == 1):?> active<?php endif;?>">首页</a>
        <a href="<?php echo $this->createUrl('goods/list');?>" class="product<?php if($this->footerPage == 2):?> active<?php endif;?>">所有商品</a>
        <a href="<?php echo $this->createUrl('goods/announced');?>" class="announce<?php if($this->footerPage == 3):?> active<?php endif;?>">最新揭晓</a>
        <a href="<?php echo $this->createUrl('carts/index');?>" class="cart<?php if($this->footerPage == 4):?> active<?php endif;?>"><i class="print">0</i>购物车</a>
        <a href="<?php echo $this->createUrl('user/index');?>" class="user<?php if($this->footerPage == 5):?> active<?php endif;?>">个人中心</a>
    </nav>
</footer>
<script>
function lodingCartNum(){
	$.ajax({
		type:"post",
		url:'<?php echo $this->createUrl("carts/getcartnums"); ?>',
// 		dataType:"json",
		data:{'YII_CSRF_TOKEN':"<?php echo Yii::app()->request->csrfToken?>"},
		success:function(data){
			console.info(data);
			if (data){
				$(".print").html(data);
			}else{
				$(".print").html(0);
			}
				
		}
	});
}
lodingCartNum();
</script>
<?php endif;?>