<?php
// 切换加盟商视图
$this->breadcrumbs = array(
    Yii::t('partner', '操作日志'),
    Yii::t('partner', '操作记录')
);
?>
<div class="toolbar">
    <b> <?php echo Yii::t('partner', '操作记录'); ?></b>
</div>

<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody><tr>
            <th class="bgBlack" ><?php echo Yii::t('partner', '操作内容'); ?></th>
            <th class="bgBlack" width="10%"><?php echo Yii::t('partner', '操作ip'); ?></th>
            <th class="bgBlack" width="10%"><?php echo Yii::t('partner', '操作人'); ?></th>
            <th class="bgBlack" width="10%"><?php echo Yii::t('partner', '操作时间'); ?></th>         
        </tr>

        <?php foreach ($logdata as $data): ?>

            <tr class="even">
                <td class="ta_c"><?php echo $data->title; ?></td>
                <td class="ta_c"><?php echo $data->ip; ?></td>
                <td class="ta_c"><?php echo $data->member_name; ?></td>
                <td class="ta_c"><?php echo date('Y-m-d H:i:s',$data->create_time); ?></b></td>
               
            </tr>

        <?php endforeach; ?>

    </tbody></table>




<div class="page_bottom clearfix">
    <div class="pagination">
        <?php
        $this->widget('CLinkPager', array(//此处Yii内置的是CLinkPager，我继承了CLinkPager并重写了相关方法
            'header' => '',
            'prevPageLabel' => Yii::t('partnerModule.page', '上一页'),
            'nextPageLabel' => Yii::t('partnerModule.page', '下一页'),
            'pages' => $pager,
            'maxButtonCount' => 15, //分页数目
            'htmlOptions' => array(
                'class' => 'paging', //包含分页链接的div的class
            )
                )
        );
        ?>
    </div>
</div>