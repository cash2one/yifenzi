<?php
/* @var $this InputGoodsController */
/* @var $model StoreActive */
$this->breadcrumbs = array(Yii::t('category', '发布管理'), Yii::t('inputGoods', '店铺录入活动商品'));
?>
<style type="text/css">
    .settingdiv{display: none; position: absolute; z-index: 10; width:120px; height: 100px; background-color: #FFF;}
    li {list-style-type:none;}

</style>

<table border="1" cellspacing="1" cellpadding="0" style="text-align: center;">
    <tr>
        <td width="150px" height="40px"><a href="<?php echo Yii::app()->createAbsoluteUrl('/inputGoods/release') ?>"><?php echo Yii::t('inputGoods', '条码商品发布') ?></a></td>
        <td width="150px"><a href="<?php echo Yii::app()->createAbsoluteUrl('/inputGoods/storeActive') ?>" class="title"><?php echo Yii::t('inputGoods', '店铺录入活动商品') ?></a></td>  
    </tr>
</table>
<?php if ($this->getUser()->checkAccess('Manage.InputGoods.addStore')): ?>
<a style=" float: right"  class="regm-sub" href="<?php echo Yii::app()->createAbsoluteUrl('/inputGoods/addStore') ?>"><?php echo Yii::t('inputGoods', '新增活动店铺') ?></a>
<?php endif;?>
<table   width="100%" cellspacing="0" cellpadding="0" id="storeTable" style="border-collapse:separate; border-spacing:10px;">
    <?php foreach ($data as $k => $val): ?>
        <tr class="storeTr" >
            <td width="100%" style="background-color: #9CD9FF; font-size: 18px; padding-left: 20px;">
                <b><?php echo $val->name; ?></b><span style="color: #707a7c"><?php echo'(' . $val->address . ')' ?></span>         

                <a href="javascript:void(0);" class="show_setting" style="float: right;"><span style="padding-right: 60px;font-size: 12px" >设置</span></a>
                <div class="settingdiv">
                    <?php if ($this->getUser()->checkAccess('Manage.InputGoods.addGoods')): ?>
                    <li><a href="<?php echo Yii::app()->createAbsoluteUrl('/inputGoods/addGoods', array("id" => $val->id)) ?>">新增商品</a></li>
                    <?php endif;?>
                    <?php if ($this->getUser()->checkAccess('Manage.InputGoods.updateStore')): ?>
                    <li><a href="<?php echo Yii::app()->createAbsoluteUrl('/inputGoods/updateStore',array("id"=>$val->id)) ?>" >编辑店铺</a></li>
                    <?php endif;?>
                    <?php if ($this->getUser()->checkAccess('Manage.InputGoods.storeDelete')): ?>
                    <li><a href="<?php echo Yii::app()->createAbsoluteUrl('/inputGoods/storeDelete', array("id" => $val->id)) ?>">删除店铺</a></li>
                    <?php endif;?>
                </div>
            <td>             
        </tr> 

        <?php
        $goods = ActiveGoods::model()->findAll("store_id={$val->id}");
        if (!empty($goods)):
            ?>                
            <tr class="trTable" style="display: none;">
                <td style="padding-top:5px;padding-bottom: 5px" >
                    <table width="70%" style="text-align:center;" align="center" border="1">
                        <tr style="background-color:#95B8E7;">
                            <td><b>商品名称</td>
                            <td><b>待录入项目</td>
                            <td><b>操作</td>
                        </tr>  
                        <?php foreach ($goods as $k2 => $v2): ?>  
                            <tr >
                                <td style="font-size: 14px"><?php echo $v2->name ?></td>
                                <td><?php echo $v2->type_name ?></td>
                                <td>
                                      <?php if ($this->getUser()->checkAccess('Manage.InputGoods.updateGoods')): ?>
                                    <a href="<?php echo Yii::app()->createAbsoluteUrl('/inputGoods/updateGoods', array('id' => $v2->id)) ?>"><?php echo Yii::t('inputGoods', '编辑') ?></a>
                                    <?php endif;?>
                                      <?php if ($this->getUser()->checkAccess('Manage.InputGoods.deleteGoods')): ?>
                                    <a href="<?php echo Yii::app()->createAbsoluteUrl('/inputGoods/deleteGoods', array('id' => $v2->id)) ?>"><?php echo Yii::t('inputGoods', '删除') ?></a>
                                <?php endif;?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </td>
            </tr>

        <?php endif; ?>

    <?php endforeach; ?>
</table>

<?php
$this->widget('CLinkPager', array(
    'header' => '',
    'firstPageLabel' => '首页',
    'lastPageLabel' => '末页',
    'prevPageLabel' => '上一页',
    'nextPageLabel' => '下一页',
    'pages' => $pages,
    'maxButtonCount' => 8,
        )
);
?>

<script>
// 收缩展开效果
    $(document).ready(function () {
        $('.title').css({'font-weight':'bold'});
        $('#storeTable').on('click', '.storeTr', function () {
              var hide = $(this).next('tr').is(':hidden');
              $('#storeTable').find('.trTable').hide();

              if (hide) {
                  $(this).next('tr').show();
              } else {
                  $(this).next('tr').hide();
              }
        });

        $('#storeTable').find('.show_setting').mouseover(
                function () {
                    $('.settingdiv').hide();
                    var left = $(this).offset().left;
                    var top = $(this).offset().top +18;
                    $(this).next('div').css({'display': 'block', 'top': top, 'left': left});
                }).mouseout(
                function () {
                    $('.settingdiv').hide();
                });

        $('#storeTable').find('.settingdiv').mouseover(
                function () {
                    $(this).show();
                }).mouseout(
                function () {
                    $(this).hide();
                });

    });

</script>