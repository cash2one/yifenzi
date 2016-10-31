<?php
/* 导出excel块 */
/* 只在要导出表格的地方加载 */
/* @var $exportPage CPagination */
?>

    <div  id="export-all" class="pager">
        <?php if ($exportPage->getItemCount() > 0): ?>
        <div class="pager">
        
            <link href="/css/reg.css" rel="stylesheet" type="text/css">
            导出excel（每份<?php echo $exportPage->pageSize; ?>条记录）:
            <?php
            $this->widget('CLinkPager', array(
                'pages' => $exportPage,
                'cssFile' => false,
                'maxButtonCount' => 10000,
                'header' => false,
                'prevPageLabel' => false,
                'nextPageLabel' => false,
                'firstPageLabel' => false,
                'lastPageLabel' => false,
            ))
            ?>  
            <?php if ($totalCount <= $exportPage->pageSize): ?>
                <a href="<?php echo $this->createAbsoluteUrl($exportPage->route, $exportPage->params) ?>"><?php echo Yii::t('main', '导出全部') ?></a>
            <?php endif; ?>
        </div>
        <?php else: ?>
            <?php echo Yii::t('main','没有数据') ?>
        <?php endif; ?>
    </div>

<script type="text/javascript" language="javascript" src="/js/iframeTools.source.js"></script>
<script type="text/javascript">
    function showExport() {
        art.dialog({
            content: $("#export-all").html(),
            title: '<?php echo Yii::t('main', '导出到excel') ?>'
        });
    }
</script>