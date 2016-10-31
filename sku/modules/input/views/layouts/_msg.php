<?php Yii::app()->clientScript->registerScriptFile(DOMAIN.'/js/artDialog/jquery.artDialog.js?skin=aero') ?>
<?php if (Yii::app()->user->hasFlash('success')): ?>
    <script>
        //成功样式的dialog弹窗提示
        art.dialog({
            icon: 'succeed',
            content: '<?php echo Yii::app()->user->getFlash('success'); ?>',
            ok: true,
            okVal:'<?php echo Yii::t('page','确定') ?>',
            title:'<?php echo Yii::t('page','消息') ?>'
        });
    </script>
<?php endif; ?>
<?php if (Yii::app()->user->hasFlash('error')): ?>
    <script>
        //错误样式的dialog弹窗提示
        art.dialog({
            icon: 'error',
            content: '<?php echo Yii::app()->user->getFlash('error'); ?>',
            ok: true,
            okVal:'<?php echo Yii::t('page','确定') ?>',
            title:'<?php echo Yii::t('page','消息') ?>'
        });
    </script>
<?php endif; ?>
<?php if (Yii::app()->user->hasFlash('warning')): ?>
    <script>
        //警告样式的dialog弹窗提示
        art.dialog({
            icon: 'warning',
            content: '<?php echo Yii::app()->user->getFlash('warning'); ?>',
            ok: true,
            okVal:'<?php echo Yii::t('page','确定') ?>',
            title:'<?php echo Yii::t('page','消息') ?>'
        });
    </script>
<?php endif; ?>