<?php
$this->breadcrumbs = array(
    Yii::t('store', '录入记录'),
);
?>
<style>
    .a li{float:left;list-style:none;padding-left: 10px;padding-top: 10px}
</style>
<div class="web-content fl">
    <div class="web-record">
        <table width="100%" border="0">
            <tr class="table-top">
                <td>商品名称</td>
                <td>商品条码</td>
                <td>商品归属</td>
                <td>所属店铺</td>
                 <td>创建时间</td>
                 <td>审核时间</td>
                <td>状态</td>
            </tr>

            <?php foreach ($data as $k => $v): ?>
                <tr>
                    <td><?php echo $v->name ?></td>
                    <td><?php echo $v->barcode ?></td>
                    <td>系统商品</td>
                    <td> - </td>
                     <td><?php echo date('Y-m-d H:i:s',$v->create_time)?></td>
                     <td><?php echo empty($v->apply_time)?'-':date('Y-m-d H:i:s',$v->apply_time)?></td>
                    <td><?php echo $v->status == ApplyBarcodeGoods::STATUS_PASS ? ApplyBarcodeGoods::getInput($v->status) . '<span style="color:red">(' . $v->reward_money . ')</span>' : ApplyBarcodeGoods::getInput($v->status) ?></td>
                </tr>
            <?php endforeach; ?>                      
        </table>
        <span style="float: right">
            <tr>
                <td>本日获利：</td>
                <td><span style="color: red"><?php echo $today[0]['sum(reward_money)']?></span>元；</td>
                <td>本周获利：</td>
                <td><span style="color: red"><?php echo $week[0]['sum(reward_money)']?></span>元；</td>
                <td>总获利：</td>
                 <td><span style="color: red"><?php echo $total[0]['sum(reward_money)']?></span>元</td>
            </tr>
        </span>
 <div>
          <?php $this->widget('CLinkPager',array(
               'header'=>'',
               'firstPageLabel' => '首页',
               'lastPageLabel' => '末页',
               'prevPageLabel' => '上一页',
               'nextPageLabel' => '下一页',
               'pages' => $pages,
               'maxButtonCount'=>8,
              'htmlOptions' => array('class' =>'a'), 
         )
         );           
 ?> 
        </div>

    </div>