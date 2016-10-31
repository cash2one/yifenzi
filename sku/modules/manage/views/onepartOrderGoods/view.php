
<?php
$this->breadcrumbs = array('商品' => array('onepartGoods/admin'), '购买详情');
?>
<!--这里是中奖人信息-->

<!--这里是搜索信息-->

<!--下面是列表-->
<div class="search-form" >
<?php $this->renderPartial('_search', array('data' => $data,'result'=>$result)); ?>
</div>
<?php if(!empty($data)):?>
<div class="c10"></div>
<div id="yifenGoods-grid" class="grid-view">
    <table class="tab-reg">
        <thead>
        <tr>
            <th>订单编号</th>
			<th>购买人用户ID</th>
            <th>购买时间</th>
            <th>购买次数</th>
            <th>购买手机号码</th>
            <th>来自</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($data as $v):?>
            <?php if(!$v["memberId"]) continue; ?>

	    <tr>
            <td><?php echo $v['orderSn'];?></td>
			<td><?php echo $v['memberId'];?></td>
            <td><?php echo date("Y-m-d H:i:s",$v["addtime"]);?></td>
            <td><?php echo $v['goods_number'];?></td>
            <td><?php echo substr_replace(Member::getMemberById($v["memberId"]),"****",3,4);?></td>
            <td><?php 
                $memberData = Member::getMemberInfo($v->memberId);
				$memberIp = Member::getMemberIp($v->memberId);
				
                 if ( $memberData["province_name"] && $memberData["province_name"] && empty($memberIp["ip"])){
			        echo  $memberData["province_name"].",".$memberData["city_name"];
				}else if( $memberData["province_name"] && $memberData["province_name"] && $memberIp["ip"]){
					echo $memberData["province_name"].",".$memberData["city_name"].",".$memberIp["ip"];
				}else{
                    echo  '未设置';
                }?>
            </td>
            
            <td>
            <?php if ($this->getUser()->checkAccess('onepartOrderGoods.Update')) : ?><!--检查编辑权限 begin-->
			<a href="javascript:;" onclick="javascript:art.dialog.load('<?php echo Yii::app()->createUrl("/onepartOrderGoods/look",array("id"=>$v->goods_id,"nper"=>$v->current_nper,"memberId"=>$v->memberId))?>', {
            title: '购买详情',
        }, false);
        return false;;">查看</a> 
            <?php endif?>
            </td>
            
        </tr>
        <?php endforeach ?>
        </tbody>
    </table>
    
	<div class="pager">
    <?php
	    $this->widget('SLinkPager', array(
		    'header' => '',
		    'cssFile' => Yii::app()->baseUrl."/css/reg.css",
		    'firstPageLabel' => Yii::t('page', '首页'),
		    'lastPageLabel' => Yii::t('page', '末页'),
		    'prevPageLabel' => Yii::t('page', '上一页'),
		    'nextPageLabel' => Yii::t('page', '下一页'),
		    'maxButtonCount' => 10,
		    'pages' => $pages,
		    'htmlOptions' => array(
			'class' => 'yiiPageer'
		    )
	    ));
    ?>  
    </div>
</div>
   
<?php else:?>
<div class="c10"></div>
<div id="second-kill-grid" class="grid-view">
    <table class="tab-reg">
        <thead>
        <tr>
            <th>订单编号</th>
            <th>购买时间</th>
            <th>购买次数</th>
            <th>购买人</th>
            <th>来自</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    <div style ="text-align:center;margin-top:10px;"><?php echo Yii::t('goods','没有找到数据');?></div>
</div>
<?php endif ?>

<div style="display: none" id="confirmArea">
    <style>
        .aui_buttons{
            text-align: center;
        }
    </style>
    <?php 
     $form = $this->beginWidget('ActiveForm', array(
          'id' => $this->id . '-form',
          'enableAjaxValidation' => true,
          'enableClientValidation' => true,
      ));
    ?>
  
<?php $this->endWidget(); ?>

</div>

<script src="<?php echo DOMAIN_M?>/js/swf/js/artDialog.iframeTools.js"></script>
