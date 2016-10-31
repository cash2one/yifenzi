<?php
$title = Yii::t('gameStore', '查看游戏店铺');
$this->pageTitle = $title . '-' . $this->pageTitle;
$this->breadcrumbs = array(
    Yii::t('gameStore', '游戏店铺管理') => array('view'),
    $title,
);
?>
<div class="toolbar">
    <b><?php echo $title ?></b>
</div>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt15 sellerT3">
    <tbody>
    <tr>
        <td>
            您还没有创建游戏店铺！
        </td>
    </tr>
    </tbody>
</table>