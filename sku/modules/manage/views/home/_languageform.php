<div class="border-info clearfix">
    <?php echo CHtml::beginForm(array('home/'.$this->action->id), 'get'); ?>
    <table cellpadding="0" cellspacing="0" class="searchTable">
        <tr>
            <th>
                <?php echo Yii::t('home', '显示文字'); ?>：
            </th>
            <td>
                <input type="text"  name="keyword" placeholder="<?php echo Yii::t('home', '为空则列出语言包目录文件') ?>" value="<?php echo $this->getQuery('keyword') ?>" class="text-input-bj  middle" value="" />
            </td>
        </tr>
    </table>
    <table cellpadding="0" cellspacing="0" class="searchTable">
        <tr>
            <th>
                <?php echo Yii::t('home', '语言选择'); ?>：
            </th>
            <td id="tdLang">
                <select name="languageList" id="languageList">
                    <?php foreach($dir as $k=>$v): ?>
                    <option value="<?php echo $k ?>"  <?php if($languageDir==Tool::authcode($k,'DECODE')) echo 'selected' ?> >
                        <?php echo $v ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <div class="c10">
    </div>
    <input type="submit"  class="reg-sub" value="<?php echo Yii::t('home', '检索'); ?>" />
    <?php echo CHtml::endForm(); ?>
</div>