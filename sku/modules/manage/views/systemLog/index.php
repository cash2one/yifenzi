<?php
/* @var $this SystemLogController */

$this->breadcrumbs=array(
	    Yii::t('SystemLog', '系统日志'),
        Yii::t('SystemLog', '系统日志'),
);
?>
<div class="us-log clearfix">
    <div class="us-log-title"><span class="us-ico-convenient-time">系统日志</span></div>
        <ul>
            <?php 
            $LastTime = '';
            foreach ($dataProvider->getData() as $log): 
            	$tempTime = date('Y-m-d', $log->create_time) ;
                if($tempTime != $LastTime){
                	if($LastTime != ''){
                		?>
                		<h1>--------------------------------------------------------------------------------------------</h1>
                		<?php 
                	}
                	?>
                	<font size="5px" color="red"><?php echo $tempTime;?></font>
                	<li>
                            <?php 
                            //自动绑定
                                $nowTime = SystemLog::autoBindNumber($tempTime,SystemLog::LOG_TYPE_ZHANGHAO_AUTO);
                                if($nowTime){
                                    echo "<span>[".date('H:i:s',$nowTime->create_time)."]</span>";
                                } else {
                                    echo "<span>[00:00:00]</span>";
                                }
                            ?>
		                
		                <span style="color: red;">[账号绑定]</span>
		                系统完成<span><?php echo MemberBindDetail::GetTimeCount($tempTime, true,MemberBind::BIND_TYPE_AUTO); ?></span>个新用户对<span><?php echo MemberBindDetail::GetTimeCount($tempTime, false,MemberBind::BIND_TYPE_AUTO);?></span>个GW号的绑定
                    </li>
                    
                     	<li>
                            <?php 
                            //手动绑定
                                $nowTime = SystemLog::autoBindNumber($tempTime,SystemLog::LOG_TYPE_ZHANGHAO);
                                if($nowTime){
                                	$userName = $nowTime->username;
                                    echo "<span>[".date('H:i:s',$nowTime->create_time)."]</span>";
                                } else {
                                	$userName = "admin";
                                    echo "<span>[00:00:00]</span>";
                                }
                            ?>
		                
		                <span style="color: red;">[账号绑定]</span>
		                管理员(<?php echo $userName; ?>)完成<span><?php echo MemberBindDetail::GetTimeCount($tempTime, true,MemberBind::BIND_TYPE_MANUA); ?></span>个新用户对<span><?php echo MemberBindDetail::GetTimeCount($tempTime, false,MemberBind::BIND_TYPE_MANUA);?></span>个GW号的绑定
                    </li>
                	<?php
                }
               if($log->type != "账号绑定"){
            ?>
                <li>
	                <span>[<?php echo date('H:i:s', $log->create_time); ?>]</span>
	                <span style="color: red;">[<?php echo $log->type; ?>]</span>
	                <?php echo Tool::truncateUtf8String(CHtml::encode($log->info),'100'); ?>
               </li>
            <?php 
                }
            $LastTime = $tempTime;
              
            endforeach; ?>
        </ul>
        <style>
            .us-log .pag li { display: inline; padding:0 10px; }
            .us-log .pag li.selected a { color: #000; font-weight: bold; }
            .pag { padding-bottom: 10px; }
        </style>
        <div style="text-align: center" class="pag">
            <?php
            $this->widget('CLinkPager', array(
                'header' => '',
                'cssFile' => false,
                'firstPageLabel' => Yii::t('comment', '首页'),
                'lastPageLabel' => Yii::t('comment', '末页'),
                'prevPageLabel' => Yii::t('comment', '上一页'),
                'nextPageLabel' => Yii::t('comment', '下一页'),
                'maxButtonCount' => 13,
                'pages' => $dataProvider->pagination
            ));
            ?>  
        </div>
</div>
