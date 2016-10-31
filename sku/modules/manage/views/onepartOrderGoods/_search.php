<div class="border-info clearfix search-form">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        //'action' => Yii::app()->createUrl("/onepartOrderGoods/view",array("id"=>$data->goods_id,"nper"=>$data->current_nper)),
		'action' => Yii::app()->createUrl($this->route,array('id'=>$result["goods_id"],'nper'=>$result["current_nper"])),
        'method' => 'get',
        
    ));
    ?>
    <table cellpadding="0" cellspacing="0" class="searchTable">
        <tbody>
            <tr>
               <td><input type="text" name="order_sn" id="val1" value="<?php echo Yii::app()->request->getParam('order_sn')?>" placeholder="请输入订单号" class="text-input-bj least"> </td>
               <td><input type="text" name="member_id" id="val2" value="<?php echo Yii::app()->request->getParam('member_id')?>" placeholder="请输入购买人用户ID"  class="text-input-bj middle"> </td>
            </tr>
        </tbody>
    </table>
    <input type="submit" value="搜索" class="reg-sub">
    <?php $this->endWidget(); ?>
</div>
<script>
    function fun(){
        var val = document.getElementById("test");
        if(val.value==0){
           $("#val1").show();
           $("#val2").show();
        }else if(val.value==1){
           $("#val1").show();
           $("#val2").hide();
        }else{
           $("#val1").hide();
           $("#val2").show();
        }
    }

  </script>