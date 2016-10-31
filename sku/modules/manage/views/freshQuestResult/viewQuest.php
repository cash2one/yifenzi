<?php
$this->breadcrumbs=array(
    Yii::t('partner','调查问卷管理'),
    Yii::t('partner','问卷详情'),
);

?>
<style>
    table{
        font-size:14px;
    }

</style>

<link href="<?php echo CSS_DOMAIN; ?>seller.css" rel="stylesheet" type="text/css" />



<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody>
    <tr>
        <th width="10%"><?php echo Yii::t('partner', '申请人'); ?> </th>
        <td width="90%">
            <?php echo $list['name']; ?>
        </td>
    </tr>

    <tr>
        <th width="10%"><?php echo Yii::t('partner', '手机号码'); ?> </th>
        <td width="90%">
            <?php echo $list['mobile']; ?>
        </td>
    </tr>
    <tr>
        <th width="10%"><?php echo Yii::t('partner', '申请类型'); ?> </th>
        <td width="90%">
            <?php echo FreshQuestResult::getType($list['type']); ?>
        </td>
    </tr>
<?php if(isset($list['quest'])){
   foreach($list['quest'] as $k =>$v){
?>

    <tr>
        <th width="10%"><?php echo $k ; ?> </th>
        <td width="90%">

            <?php echo $v ;?>
        </td>
    </tr>
    <?php }
      } ?>



    </tbody>
</table>


