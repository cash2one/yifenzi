<?php
$baseUrl = Yii::app()->baseUrl;
Yii::app()->clientScript->registerScriptFile($baseUrl . "/js/swf/js/artDialog.js?skin=blue");
?>
<?php if ($this->hasFlash('success')): ?>
    <script>
        //成功样式的dialog弹窗提示
        art.dialog({
            icon: 'succeed',
            content: '<?php echo $this->getFlash('success'); ?>',
            ok: true
        });
    </script>
<?php endif; ?>
<?php if ($this->hasFlash('error')): ?>
    <script>
        //错误样式的dialog弹窗提示
        art.dialog({
            icon: 'error',
            content: <?php echo json_encode($this->getFlash('error')); ?>,
            ok: true
        });
    </script>
<?php endif; ?>
<?php if ($this->hasFlash('warning')): ?>
    <script>
        //警告样式的dialog弹窗提示
        art.dialog({
            icon: 'warning',
            content: '<?php echo $this->getFlash('warning'); ?>',
            ok: true
        });
    </script>
<?php endif; ?>