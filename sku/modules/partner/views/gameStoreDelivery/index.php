<?php
$title = Yii::t('gameStore', '游戏店铺');
$this->pageTitle = $title . '-' . $this->pageTitle;
$this->breadcrumbs = array(
    Yii::t('gameStoreDelivery', '游戏店铺发货管理') => array('index'),
    $title,
);
?>
<div class="main clearfix">
    <div class="workground">
        <div class="toolbar">
            <b><?php echo Yii::t('gameStoreDelivery', '发货列表') ?></b>
            <span><?php echo Yii::t('gameStoreDelivery', '游戏商品发货列表展示。') ?></span>
        </div>

        <?php
        $this->renderPartial('_search', array(
            'model' => $model,
        ));
        ?>

        <table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
            <tbody>
            <tr>
                <th class="bgBlack" width="15%"><?php echo Yii::t('gameStoreDelivery', '收货人名称') ?></th>
                <th class="bgBlack" width="15%"><?php echo Yii::t('gameStoreDelivery', '收货人手机号') ?></th>
                <th class="bgBlack" width="25%"><?php echo Yii::t('gameStoreDelivery', '收货人地址') ?></th>
                <th class="bgBlack" width="15%"><?php echo Yii::t('gameStoreDelivery', '货品') ?></th>
                <th class="bgBlack" width="20%"><?php echo Yii::t('gameStoreDelivery', '发货时间') ?></th>
                <th class="bgBlack" width="10%"><?php echo Yii::t('gameStoreDelivery', '操作') ?></th>
            </tr>
            <?php foreach ($items as $v): ?>
                <tr class="even">
                    <td class="ta_c"><?php echo $v->info->real_name ?></td>
                    <td class="ta_c"><?php echo $v->info->mobile ?></td>
                    <td class="ta_c"><?php echo $v->info->member_address ?></td>
                    <td class="ta_c"><?php echo $v['delivery_items'] ?></td>
                    <td class="ta_c"><?php echo date('Y-m-d H:i:s',$v['delivery_time']); ?></td>
                    <td class="ta_c">
                        <?php echo CHtml::link('<span>' . Yii::t('gameStoreDelivery', '编辑') . '</span>', $this->createAbsoluteUrl('/partner/gameStoreDelivery/update', array('id' => $v->id)), array('class' => 'sellerBtn03')) ?>
                        &nbsp;&nbsp;
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="page_bottom clearfix">
            <div class="pagination">
                <?php
                $this->widget('CLinkPager', array(
                        'header' => '',
                        'cssFile' => false,
                        'firstPageLabel' => Yii::t('page', '首页'),
                        'lastPageLabel' => Yii::t('page', '末页'),
                        'prevPageLabel' => Yii::t('page', '上一页'),
                        'nextPageLabel' => Yii::t('page', '下一页'),
                        'pages' => $pages,
                        'maxButtonCount' => 13
                    )
                );
                ?>
            </div>
        </div>
    </div>
    <!--</div>-->
    <!--</div>-->
</div>
