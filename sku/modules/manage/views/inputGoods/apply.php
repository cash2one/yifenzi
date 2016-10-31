<?php
/* @var $this InputGoodsController */
/* @var $model InputGoods */
$this->breadcrumbs = array(Yii::t('inputGoods', '返回') => '/inputGoods/admin');
?>

<style type="text/css">
    body {font-size:12px;}
    a {color:#000; text-decoration:none;}
    a:hover {color:#F00;}/*主菜单伪类*/
    #menu {width:100%; border:1px solid #ccc;text-align: center;}
    #menu ul {list-style:none; margin:0px; padding:0px;width: 20%;}
    #menu ul li {background:#eee; padding:0px 10px; height:60px; line-height:60px;/*字体在上下边框的剧中位置*/ border-bottom:1px solid #ccc; }

</style>
<div>
    <span style="font-size: 18px; font-weight: bold" onclick="aa()">商品审核</span> 
    <?php if($barcode->status!=BarcodeGoods::STATUS_PASS){?>
    <span style="float: right"> <?php echo CHtml::Button(Yii::t('inputGoods', '保存至产品库'), array('class' => 'regm-sub', 'id' => 'inputGoods')); ?></span>
    <span style="float: right"> <?php echo CHtml::Button(Yii::t('inputGoods', '保存至草案'), array('class' => 'regm-sub', 'id' => 'temp')); ?></span>
    <?php }?>
    <a onClick="return confirm('是否确定当前商品重新开放录入？最多3个用户可提交录入结果')" style=" float: right"  class="regm-sub" href="<?php echo Yii::app()->createAbsoluteUrl('/inputGoods/openGoods', array('id' => $id)) ?>"><?php echo Yii::t('inputGoods', '重新开放') ?></a>
</div>
<div style="height:20px"></div>
<div id="menu">
    <ul style="float: left">
        <li>项目</li>
        <li><?php echo EnGoodsRule::getName(EnGoodsRule::RULE_NAME)?></li>
        <li><?php echo EnGoodsRule::getName(EnGoodsRule::RULE_BARCODE)?></li>
        <li><?php echo EnGoodsRule::getName(EnGoodsRule::RULE_CATE_NAME)?></li>
        <li><?php echo EnGoodsRule::getName(EnGoodsRule::RULE_MODEL)?></li>
        <li><?php echo EnGoodsRule::getName(EnGoodsRule::RULE_UNIT)?></li>  
        <li><?php echo EnGoodsRule::getName(EnGoodsRule::RULE_PRICE)?></li>
        <li><?php echo EnGoodsRule::getName(EnGoodsRule::RULE_DESCRIBE)?></li>
        <li><?php echo EnGoodsRule::getName(EnGoodsRule::RULE_THUMB)?></li>
    </ul>
    <?php foreach ($applyGoods as $k => $v1): ?>
        <ul style="float: left;background-color:  #66F4DF"  onmouseover="change(this)" onmouseout="clearBgc(this)"id="<?php echo $v1->id ?>">          
            <li> 
                <input type="checkbox" id="cbxAll<?php echo$k + 1?>" name="<?php echo $v1->id ?>" onclick="getData(this)">
                选项<?php echo $k + 1 ?></li>
            <?php if(!empty($v1->name)){?>
            <li onclick="getOne(this,1,'name',<?php echo $v1->id;?>)" id="name_and_<?php echo $v1->id;?>" class="name" style="color:red;cursor: pointer;"><?php echo $v1->name; ?></li>
            <?php }else{?>
            <li><?php echo $barcode->name?></li>
            <?php }?>
              <?php if(!empty($v1->barcode)){?>
            <li onclick="getOne(this,2,'barcode',<?php echo $v1->id;?>)" id="barcode_and_<?php echo $v1->id;?>" style="color:red;cursor: pointer;"><?php echo $v1->barcode ?></li>
              <?php }else{?>
            <li><?php echo $barcode->barcode?></li>
              <?php }?>
            <?php if(!empty($v1->cate_name)){?>
            <li onclick="getOne(this,3,'cate_name',<?php echo $v1->id;?>)" id="cate_name_and_<?php echo $v1->id;?>" style="color:red;cursor: pointer;"><?php echo $v1->cate_name; ?></li>
               <?php }else{?>
            <li><?php echo $barcode->cate_name?></li>
              <?php }?>
            <?php if(!empty($v1->model)){?>
            <li onclick="getOne(this,4,'model',<?php echo $v1->id;?>)" id="model_and_<?php echo $v1->id;?>" style="color:red;cursor: pointer;"><?php echo $v1->model; ?></li>
               <?php }else{?>
            <li><?php echo $barcode->model?></li>
              <?php }?>
            <?php if(!empty($v1->unit)){?>
            <li onclick="getOne(this,5,'unit',<?php echo $v1->id;?>)" id="unit_and_<?php echo $v1->id;?>" style="color:red;cursor: pointer;"><?php echo $v1->unit; ?></li>
               <?php }else{?>
            <li><?php echo $barcode->unit?></li>
              <?php }?>
            <?php if($v1->default_price!='0.0'){?>
            <li onclick="getOne(this,6,'default_price',<?php echo $v1->id;?>)" id="default_price_and_<?php echo $v1->id;?>"style="color:red;cursor: pointer;"><?php echo $v1->default_price; ?></li>  
                <?php }else{?>
                <li><?php echo $barcode->default_price;?></li>
              <?php }?>
            <?php if(!empty($v1->describe)){?>
            <li onclick="getOne(this,7,'describe',<?php echo $v1->id;?>)" id="describe_and_<?php echo $v1->id;?>"style="color:red;cursor: pointer;"><?php echo $v1->describe; ?></li>  
              <?php }else{?>
            <li><?php echo $barcode->describe?></li>
              <?php }?>
            <?php if(!empty($v1->thumb)){?>
            <li onclick="getOne(this,8,'thumb',<?php echo $v1->id;?>)" id="thumb_and_<?php echo $v1->id;?>" ><a style="cursor: pointer" onMouseOver="picBig(this);" onmouseout="picClose(this);"id='<?php echo $k ?>' ><?php echo CHtml::image(ATTR_DOMAIN . "/" . $v1->thumb, '点击显示大图', array("width" => 80, "height" => 60, "style" => "display: inline-block")) ?></a></li>  
               <?php }else{?>
               <li><a style="cursor: pointer" onMouseOver="picBig(this);" onmouseout="picClose(this);" id='<?php echo $k ?>' ><?php echo CHtml::image(ATTR_DOMAIN . "/" . $barcode->thumb, '点击显示大图', array("width" => 80, "height" => 60, "style" => "display: inline-block")) ?></a></li>
               <?php }?>
        </ul>
        <div id="divCenter<?php echo $k ?>" align="center" style="position: absolute;display: none;  width:100%;margin-top:-40px ">
            <?php if(!empty($v1->thumb)){?>
            <?php echo CHtml::image(ATTR_DOMAIN . "/" . $v1->thumb,'', array( "style" => "cursor: pointer;border: white  solid 5px;")) ?>
            <?php }else{?>
            <?php echo CHtml::image(ATTR_DOMAIN . "/" . $barcode->thumb, '',array( "style" => "cursor: pointer;border: white  solid 5px;")) ?>
            <?php }?>
        </div>
    <?php endforeach; ?>
    <?php if (count($applyGoods) < ApplyBarcodeGoods::APPLY_COUNT): ?>
        <?php for ($i = count($applyGoods); $i < ApplyBarcodeGoods::APPLY_COUNT; $i++): ?>
            <?php echo '<ul style="float: left;" ><li>选项' . ($i + 1) . '</li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ul>' ?>
        <?php endfor; ?><?php endif; ?>

    <?php if (empty($tempGoods['id'])) { ?>
        <ul style="float: left;" id = "apply">
            <li>审核选择</li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
    <?php } else { ?> 
        <ul style="float: left;" id = "apply">
            <li>审核选择</li>
            <?php if(isset( $tempGoods['name'])&&!empty( $tempGoods['name'])){?>
            <li style="color: red"><?php echo $tempGoods['name'] ?></li>
              <?php }else{?>
             <li><?php echo $barcode['name'] ?></li>
              <?php }?>
             <?php if(isset( $tempGoods['barcode'])&&!empty( $tempGoods['barcode'])){?>
            <li style="color: red"><?php echo $tempGoods['barcode'] ?></li>
              <?php }else{?>
             <li><?php echo $barcode['barcode'] ?></li>
              <?php }?>
            <?php if(isset( $tempGoods['cate_name'])&&!empty( $tempGoods['cate_name'])){?>
            <li style="color: red"><?php echo $tempGoods['cate_name'] ?></li>
              <?php }else{?>
             <li ><?php echo $barcode['cate_name'] ?></li>
              <?php }?>
            <?php if(isset( $tempGoods['model'])&&!empty( $tempGoods['model'])){?>
            <li style="color: red"><?php echo $tempGoods['model'] ?></li>
              <?php }else{?>
             <li><?php echo $barcode['model'] ?></li>
              <?php }?>
            <?php if(isset( $tempGoods['unit'])&&!empty( $tempGoods['unit'])){?>
             <li style="color: red" ><?php echo $tempGoods['unit'] ?></li>
              <?php }else{?>
             <li ><?php echo $barcode['unit'] ?></li>
              <?php }?>
            <?php if(isset( $tempGoods['default_price'])&&!empty( $tempGoods['default_price'])){?>
            <li style="color: red"><?php echo $tempGoods['default_price'] ?></li>
              <?php }else{?>
             <li><?php echo $barcode['default_price'] ?></li>
              <?php }?>
            <?php if(isset( $tempGoods['describe'])&&!empty( $tempGoods['describe'])){?>
            <li style="color: red"><?php echo $tempGoods['describe'] ?></li>
              <?php }else{?>
             <li><?php echo $barcode['describe'] ?></li>
              <?php }?>
             <?php if(isset($tempGoods['thumb'])&&!empty($tempGoods['thumb'])){?>
            <li><a><?php echo CHtml::image(ATTR_DOMAIN . "/" . $tempGoods['thumb'], '',array("width" => 80, "height" => 60, "style" => "display: inline-block")) ?></a></li>
             <?php }else{?>
             <li><a><?php echo CHtml::image(ATTR_DOMAIN . "/" . $barcode['thumb'], '', array("width" => 80, "height" => 60, "style" => "display: inline-block")) ?></a></li>
            <?php }?>
        </ul>
    <?php } ?>
</div>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'action' => Yii::app()->createUrl('inputGoods/tempGoods'),
    'method' => 'post',
    'id' => 'inputGoods-temp-form',
        ));
?>
<?php echo CHtml::hiddenField('tempid',  empty($tempGoods['id'])?'':$tempGoods['id']) ?>
<?php echo CHtml::hiddenField('name', empty($tempGoods['name'])?'':$tempGoods['name'])?>
<?php echo CHtml::hiddenField('barcode', empty($tempGoods['barcode'])?'':$tempGoods['barcode']) ?>
<?php echo CHtml::hiddenField('cate_name', empty($tempGoods['cate_name'])?'':$tempGoods['cate_name']) ?>
<?php echo CHtml::hiddenField('unit', empty($tempGoods['unit'])?'':$tempGoods['unit']) ?>
<?php echo CHtml::hiddenField('default_price', empty($tempGoods['default_price'])?'':$tempGoods['default_price']) ?>
<?php echo CHtml::hiddenField('model', empty($tempGoods['model'])?'':$tempGoods['model']) ?>
<?php echo CHtml::hiddenField('describe', empty($tempGoods['describe'])?'':$tempGoods['describe']) ?>
<?php echo CHtml::hiddenField('thumb', empty($tempGoods['thumb'])?'':$tempGoods['thumb']) ?>
<?php echo CHtml::hiddenField('arrId') ?>
<?php $this->endWidget(); ?>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'action' => Yii::app()->createUrl('inputGoods/inputGoods'),
    'method' => 'post',
    'id' => 'inputGoods-input-form',
        ));
?>
<?php echo CHtml::hiddenField('inputGoodsid') ?>
<?php echo CHtml::hiddenField('inputarrId') ?>
<?php $this->endWidget(); ?>
<script>

    function picBig(o) {
        var id = o.id;
        var v = document.getElementById('divCenter' + id);
        v.style.display = "block";
    }

    function picClose(o) {
        var id = o.id;
        var v = document.getElementById("divCenter"+id);
        v.style.display = "none";
    }

    function getData(o) {
       
        var id = o.name;
        var arr = '';
        $("#tempid").val(id);
        $("#arrId").val(arr);
        var applys = document.getElementById("apply").getElementsByTagName("li");

        var list_cell = document.getElementById(id).getElementsByTagName("li");
        for (var i = 1; i < list_cell.length; i++) {
            if(list_cell[i].id){
                arr =arr+','+list_cell[i].id;   
                $("#arrId").val(arr);
      
                applys[i].style.color='red';
                 applys[i].innerHTML = list_cell[i].innerHTML;
                 applys[i].id = list_cell[i].id;
                 var s = applys[i].id;
                applys[i].onclick=function(){
                 $(this).html('');
                 arr = arr.replace(","+s,"");
                 $("#arrId").val(arr);
                  $("#tempid").val('');
                };
            }else{
//                  applys[i].id = list_cell[i].id;
            applys[i].innerHTML = list_cell[i].innerHTML;
        }       
        }
    }
    function getOne(o,i,e,aid){
        var id = o.id;
        var str = id.replace("_and_"+aid,"");

         var arr = $("#arrId").val();
         if(arr.indexOf(str)> 0){
                 arr = $("#arrId").val();
         }else{
                 arr = $("#arrId").val()+','+id;
         }
        $("#arrId").val(arr);
         $("#tempid").val('');
        $("#"+e).val(o.innerHTML);
        var applys = document.getElementById("apply").getElementsByTagName("li");
         applys[i].innerHTML = o.innerHTML;
         applys[i].style.color='red';
         if(applys[i].id){
             var a =applys[i].id;
              var s = $("#arrId").val();
              s = s.replace(","+a,"");
              $("#arrId").val(s);
         }
         applys[i].onclick=function(){
             applys[i].innerHTML='';
             $("#arrId").val('');
         };
         alert($("#arrId").val());
    }
    function change(o) {
        var id = o.id;
        var list_cell = document.getElementById(id).getElementsByTagName("li");
        for (i = 0; i < list_cell.length; i++) {
            list_cell[i].style.backgroundColor = "#66F4DF";
        }
    }
    function clearBgc(o) {
        var id = o.id;
        var list_cell = document.getElementById(id).getElementsByTagName("li");
        for (i = 0; i < list_cell.length; i++) {
            list_cell[i].style.backgroundColor = "";
        }
    }
    $("#temp").click(function () {
        var id = $("#tempid").val();
          var arr = $("#arrId").val();
//          alert(arr);
        if (id == "" && arr == "") {
            art.dialog({
                content: '没有选中的记录!',
                ok: true
            });
        } else {
            art.dialog({
                content: '是否保存至草案?',
                ok: function () {
                    $("#inputGoods-temp-form").submit();
                },
                cancel: true
            });
        }
    });
    $("#inputGoods").click(function () {
        var id = $("#tempid").val();
         var arr = $("#arrId").val();
        if (id == "" && arr == "") {
            art.dialog({
                content: '没有选中的记录!',
                ok: true
            });
        } else {
            art.dialog({
                content: '是否保存至产品库?',
                ok: function () {
                    $("#inputGoodsid").val(id);
                    $("#inputarrId").val(arr);
                    $("#inputGoods-input-form").submit();
                },
                cancel: true
            });
        }
    });
     $("#cbxAll1").live('change', function() {
            if ($(this).attr("checked"))
            {               
                $("#cbxAll2").removeAttr("checked");
                $("#cbxAll3").removeAttr("checked");
                
            }
        });
         $("#cbxAll2").live('change', function() {
            if ($(this).attr("checked"))
            {
             
                $("#cbxAll1").removeAttr("checked");
                $("#cbxAll3").removeAttr("checked");
            }

        });
        $("#cbxAll3").live('change', function() {
            if ($(this).attr("checked"))
            {
          
                $("#cbxAll1").removeAttr("checked");
                $("#cbxAll2").removeAttr("checked");
            }

        });
</script>