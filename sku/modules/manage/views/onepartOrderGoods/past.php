
<?php
$this->breadcrumbs = array('商品列表' => array('onepartGoods/admin'), '往期管理');
//$dataProvider = $model->search();

//Yii::app()->clientScript->registerScript('search', "
//$('.search-form form').submit(function(){
//	$('#goods-grid').yiiGridView('update', {
//		data: $(this).serialize()
//	});
////	return false;
//});
//");
?>
<div class="c10"></div>
<div id="yifenGoods-grid" class="grid-view">
    <table class="tab-reg">
        <thead>
        <tr>
            <th>期数</th>
            <th>商品标题</th>
            <th>所属栏目</th>
            <th>已参与/总需</th>
            <th>单价/元</th>
            <th>期数/最大期数</th>
			<th>人气商品</th>
            <th>限时</th>
            <th>揭晓状态</th>
			<th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($data as $v):?>
	    <tr>
            <td><?php echo $v['current_nper'];?></td>
            <td><?php echo $v['goods_name'];?></td>
            <td><?php 
			    if($v["column_id"]){
				    echo $v["column_id"];
			    }else{
					echo "无栏目名称";
				}
				?>
			</td>
            <td><?php echo $v["goods_number"]. "/".ceil($v["shop_price"]/$v["single_price"]);?></td>
            <td><?php echo $v["single_price"];?></td>
            <td><?php echo $v["current_nper"]. "/".$v["max_nper"];?></td>
			<td><?php 
			    if ($v["recommended"]){
				    echo "是";
			    }else{
					echo "否";
				}?></td>
            </td>
			<td><?php ?></td>
			<td><?php 
			    if($v["status"]==1){
					echo "揭晓中";
				}else{
					echo "揭晓";
				}
			?></td>
            <td>
            <?php if (Yii::app()->user->checkAccess('Manage.OnepartOrderGoods.Update')) : ?><!--检查编辑权限 begin-->
            <a href ="<?php echo Yii::app()->createUrl("/onepartGoods/update",array("id"=>$v["goods_id"],"nper"=>$v["current_nper"]))?>" class ="reg-sub" >修改</a>
            <?php endif?>
            <?php if (Yii::app()->user->checkAccess('Manage.OnepartOrderGoods.BeforeGoodsView')) : ?><!--检查权限 begin-->
            <a href ="<?php echo Yii::app()->createUrl("/onepartOrderGoods/orderGoodsView",array("id"=>$v["goods_id"],"nper"=>$v["current_nper"]))?>" class ="reg-sub" >详情</a>
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

