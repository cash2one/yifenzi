<div class="appwapper">
    <div class="site404Box">
        <dl class="clearfix">
            <dt class="site404Pic"></dt>
            <dd>
                <p class="bigTxt"><?php echo empty($message) ? Yii::t('site', '很抱歉，页面出现错误了!') : CHtml::encode($message) ?></p>
                <p class="samllTxt"><?php echo $code; ?></p>
                <p class="topmargin"><?php echo Yii::t('site','建议您');?>：</p>
                <p class="backHome">
                    <?php echo Yii::t('site','看看输入的文字是否有误，点击');?>
                    <?php echo CHtml::link(Yii::t('site','返回首页'), DOMAIN); ?>
                    <a href="javascript:history.back();"><?php echo Yii::t('site','返回上页') ?></a>
                </p>
            </dd>
        </dl>

    </div>
</div>
<div style="display: none">
    <?php echo Tool::authcode($trace) ?>
</div>