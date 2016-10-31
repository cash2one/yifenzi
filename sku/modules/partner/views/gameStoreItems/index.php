<?php
$title = Yii::t('gameStore', '游戏店铺');
$this->pageTitle = $title . '-' . $this->pageTitle;
$this->breadcrumbs = array(
    Yii::t('gameStoreItems', '游戏店铺商品管理') => array('index'),
    $title,
);
?>
<div class="main clearfix">
    <div class="workground">
        <div class="toolbar">
            <b><?php echo Yii::t('gameStoreItems', '商品列表') ?></b>
            <span><?php echo Yii::t('gameStoreItems', '游戏商品页面列表展示。') ?></span>
        </div>

        <?php
        $this->renderPartial('_search', array(
            'model' => $model,
        ));
        ?>
        <a href="/gameStoreItems/create" class="mt15 btnSellerAdd"><?php echo Yii::t('gameStoreItems', '添加商品') ?></a>
        <?php if($store['franchise_stores'] == GameStore::FRANCHISE_STORES_IS):?>
            <a href="/gameStoreItems/createflag" class="mt15 btnSellerAdd"><?php echo Yii::t('gameStoreItems', '添加特殊商品') ?></a>
        <?php endif ?>

        <table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
            <tbody>
            <tr>
                <th class="bgBlack" width="10%"><?php echo Yii::t('gameStoreItems', '商品名称') ?></th>
                <th class="bgBlack" width="10%"><?php echo Yii::t('gameStoreItems', '每日提供数量') ?></th>
                <th class="bgBlack" width="10%"><?php echo Yii::t('gameStoreItems', '状态') ?></th>
                <th class="bgBlack" width="10%"><?php echo Yii::t('gameStoreItems', '活动开始日期') ?></th>
                <th class="bgBlack" width="10%"><?php echo Yii::t('gameStoreItems', '活动结束日期') ?></th>
                <th class="bgBlack" width="10%"><?php echo Yii::t('gameStoreItems', '每日开抢时间') ?></th>
                <th class="bgBlack" width="10%"><?php echo Yii::t('gameStoreItems', '每日结束时间') ?></th>
                <th class="bgBlack" width="10%"><?php echo Yii::t('gameStoreItems', '单次可获数量') ?></th>
                <th class="bgBlack" width="10%"><?php echo Yii::t('gameStoreItems', '特殊商品') ?></th>
                <th class="bgBlack" width="10%"><?php echo Yii::t('gameStoreItems', '操作') ?></th>
            </tr>
            <?php foreach ($items as $v): ?>
                <tr class="even">
                    <td class="ta_c"><?php echo $v['item_name'] ?></td>
                    <td class="ta_c"><?php echo $v['item_number'] ?></td>
                    <td class="ta_c"><?php echo GameStoreItems::status($v['item_status']); ?></td>
                    <td class="ta_c"><?php echo $v['start_date'] ?></td>
                    <td class="ta_c"><?php echo $v['end_date'] ?></td>
                    <td class="ta_c"><?php echo $v['start_time'] ?></td>
                    <td class="ta_c"><?php echo $v['end_time'] ?></td>
                    <td class="ta_c"><?php echo $v['limit_per_time']; ?></td>
                    <td class="ta_c"><?php echo GameStoreItems::flagItems($v['flag']); ?></td>
                    <td class="ta_c">
                        <?php echo CHtml::link('<span>' . Yii::t('gameStoreItems', '编辑') . '</span>', $this->createAbsoluteUrl($v['flag'] == GameStoreItems::SPECIAL_ITEM_FLAG ? '/partner/gameStoreItems/updateFlag' : '/partner/gameStoreItems/update', array('id' => $v->id)), array('class' => 'sellerBtn03')) ?>
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
