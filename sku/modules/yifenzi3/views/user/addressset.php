<div class="container">
		<ul class="addressMan">
			<?php foreach ($data as $row):?>
			<li>
				<a href="<?php echo $this->createUrl('user/addressUpdate',array('id'=>$row['id']))?>">
					<span class="loca<?php if($row['default'] == Address::DEFAULT_IS):?> active<?php endif;?>"></span>
					<div class="userDetail">
						<p><span class="name"><?php echo $row['real_name']?></span><span class="tel"><?php echo $row['mobile']?></span></p>
						<p><small><?php echo $model->getName($row['province_id'],$row['city_id'],$row['district_id'])?></small></p>
					</div>
				</a>
				
			</li>
			<?php endforeach;?>
		</ul>
</div>
<div style="height:44px;"></div>
<footer class="detail ongoing">
<?php if(count($data)<5):?>
    <a href="<?php echo $this->createUrl('user/address')?>">添加地址</a>
<?php else :?>
    <a href="javascript:void(0)" onclick="addressCount()">添加地址</a>
<?php endif ?>
</footer>
	
<script type="text/javascript">

    function addressCount(){
		alert('最多添加5个收货地址');
	}
</script>