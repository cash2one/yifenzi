	<div class="container">
     <div class="warpbg">
		<?php if($data['status'] == 2){?>
        <div class="newList">
            <ul>
                <li>
                    <a href="#"><img src="<?php echo $data['goods_image']?>"><span class="title">已揭晓</span></a>
                    <div class="right">
                        <p class="name"><span>[第<?php echo $data['current_nper']?>期]</span>
                        <a href="<?php echo Yii::app()->createUrl('/yifenzi3/goods/view',array('id'=>$data['goods_id'],'nper'=>$data['current_nper']))?>"><?php echo $data['goods_name']?></a></p>
                        <p class="overTime max">
                            <span>获奖者：<i><?php echo $data['username']?></i></span>
                            <span>揭晓时间：<?php echo $data['sumlotterytime']?></span>
                        </p>
                        <p class="conduct"><a style="color:#fff;" href="<?php echo Yii::app()->createUrl('/yifenzi3/goods/view',array('id'=>$data['goods_id'],'nper'=>$data['current_nper']))?>">第<?php echo $data['new_current_nper']?>期正在进行...</a></p>
                    </div>
                </li>
            </ul>
        </div>
        <div class="buyRecord">
            <p><?php echo $data['addtime']?> <?php echo $data['goods_number']?>人次</p>
            <ul>
            	<?php foreach($data['winning_code'] as $k=>$v){?>
            		<?php if ($v == $data['w_code']){?>
            			<li class="on"><?php echo $v?></li>
            		<?php }else{?>
            			<li><?php echo $v?></li>
            		<?php }?>
            	<?php }?>
            </ul>
        </div>
		<?php }elseif ($data['status'] == 1){?>
        <div class="newList">
            <ul>
                <li>
                    <a href="#"><img src="<?php echo $data['goods_image']?>"><span class="title on">揭晓中</span></a>
                    <div class="right">
                        <p class="name"><span>[第<?php echo $data['current_nper']?>期]</span>
                        	<a href="<?php echo Yii::app()->createUrl('/yifenzi3/goods/view',array('id'=>$data['goods_id'],'nper'=>$data['current_nper']))?>"><?php echo $data['goods_name']?></a>
                        </p>
                        <p class="count"></p>
                        <p class="conduct">第<?php echo $data['current_nper']?>期揭晓中...</p>
                    </div>
                </li>
            </ul>
        </div>
        <div class="buyRecord">
            <p><?php echo $data['addtime']?> <?php echo $data['goods_number']?>人次</p>
            <ul>
                <?php foreach($data['winning_code'] as $k=>$v){?>
            		<li><?php echo $v?></li>
            	<?php }?>
            </ul>
        </div>		
		<?php }else{?>
        <div class="newList">
                <ul>
                    <li>
                        <a href="#"><img src="<?php echo $data['goods_image']?>"><span class="title being">进行中</span></a>
                        <div class="right">
                            <p class="name min"><span>[第<?php echo $data['current_nper']?>期]</span>
                            <a href="<?php echo Yii::app()->createUrl('/yifenzi3/goods/view',array('id'=>$data['goods_id'],'nper'=>$data['current_nper']))?>"><?php echo $data['goods_name']?></a></p>
                            <p class="count min">价值：￥<?php echo $data['goods_price']?></p>
                            <p class="speedbg"><span class="speed"><span class="speedIng" style="width:<?php echo $data['percentage']?>%"><i></i></span></span></p>
                        <p class="spengBottom"><?php echo ($data['count_nper'] - $data['inventory'])?><span><?php echo $data['count_nper']?></span></p>
                        <p class="conduct"><a href="<?php echo Yii::app()->createUrl('/yifenzi3/goods/view',array('id'=>$data['goods_id'],'nper'=>$data['current_nper']))?>" style="color: #fff">继续购买</a></p>
                    </div>
                </li>
            </ul>
        </div>
        <div class="buyRecord">
            <p><?php echo $data['addtime']?> <?php echo $data['goods_number']?>人次</p>
            <ul>
            	<?php foreach($data['winning_code'] as $k=>$v){?>
            		<li><?php echo $v?></li>
            	<?php }?>
            </ul>
        </div>
		<?php }?>
	    <div class="h60"></div>
	</div>
	</div>