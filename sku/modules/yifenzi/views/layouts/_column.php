<?php
$sql = "select column_name,id,column_logo from {{column}} where parent_id=0 and is_show=1 order by sort_order desc, addtime desc";
$column = Yii::app()->gwpart->createCommand($sql)->queryAll();
$column_id = Yii::app()->request->getParam('column_id', 0);
?>
<ul class="list-group">
    <?php if ($this->action->id == 'announced'): ?>
        <li onclick="javascript:window.location.href='<?php echo Yii::app()->createUrl("/yifenzi/goods/list")?>'">
            <a href="<?php echo Yii::app()->createUrl('/yifenzi/goods/list') ?>">
                <i class="listIcon allItem"></i>
                <span class="<?php if (!$column_id) echo 'active' ?>">全部分类</span>
                <?php if (!$column_id): ?>
                    <i class="listIcon choose"></i>
                <?php endif; ?>
            </a>
        </li>
        <?php foreach ($column as $c): ?>
        <li onclick="javascript:window.location.href='<?php echo Yii::app()->createUrl("/yifenzi/goods/announced", array("column_id" => $c["id"])) ?>'">
            <a href="<?php echo Yii::app()->createUrl('/yifenzi/goods/announced', array('column_id' => $c['id'])) ?>">
                <i class="listIcon Item<?php echo $c['id'] ?>"></i>
                <span class="<?php if($column_id == $c['id']) echo 'active' ?>"><?php echo $c['column_name'] ?></span>
                <?php if($column_id == $c['id']): ?>
                <i class="listIcon choose"></i>
                <?php endif; ?>
            </a>
        </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li onclick="javascript:window.location.href='<?php echo Yii::app()->createUrl("/yifenzi/goods/list")?>'">
            <a href="<?php echo Yii::app()->createUrl('/yifenzi/goods/list') ?>">
                <i class="listIcon allItem"></i>
                <span class="<?php if (!$column_id) echo 'active' ?>">全部分类</span>
                <?php if (!$column_id): ?>
                    <i class="listIcon choose"></i>
                <?php endif; ?>
            </a>
        </li>
        <?php foreach ($column as $c): ?>
            <li onclick="javascript:window.location.href='<?php echo Yii::app()->createUrl("/yifenzi/goods/list", array("column_id" => $c["id"])) ?>'">
                <a href="<?php echo Yii::app()->createUrl('/yifenzi/goods/list', array('column_id' => $c['id'])) ?>">
                    <!--<i class="listIcon Item<?php //echo $c['id'] ?>"></i>-->
					<i><img src="<?php echo ATTR_DOMAIN .'/'. $c['column_logo']?>"></i>
                    <span class="<?php if ($column_id == $c['id']) echo 'active' ?>"><?php echo $c['column_name'] ?></span>
                    <?php if ($column_id == $c['id']): ?>
                        <i class="listIcon choose"></i>
                    <?php endif; ?>
                </a>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>