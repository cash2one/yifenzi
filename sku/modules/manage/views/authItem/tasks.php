<?php
$this->breadcrumbs = array(
    'Rights' => Rights::getBaseUrl(),
    Rights::t('core', 'Tasks'),
);
?>

<div id="tasks">
    <p>
        <?php
        echo CHtml::link('创建任务组', array('authItem/create', 'type' => CAuthItem::TYPE_ROLE), array(
            'class' => 'regm-sub',
            'style' => 'color:#fff;'
        ));
        ?>
    </p>
    <?php
    $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $dataProvider,
        'template' => '{items}',
        'emptyText' => Rights::t('core', 'No tasks found.'),
        'htmlOptions' => array('class' => 'grid-view task-table'),
        'columns' => array(
            array(
                'name' => 'name',
                'header' => Rights::t('core', 'Name'),
                'type' => 'raw',
                'htmlOptions' => array('class' => 'name-column'),
                'value' => '$data->getGridNameLink()',
            ),
            array(
                'name' => 'description',
                'header' => Rights::t('core', 'Description'),
                'type' => 'raw',
                'htmlOptions' => array('class' => 'description-column'),
            ),
            array(
                'name' => 'bizRule',
                'header' => Rights::t('core', 'Business rule'),
                'type' => 'raw',
                'htmlOptions' => array('class' => 'bizrule-column'),
                'visible' => Rights::module()->enableBizRule === true,
            ),
            array(
                'name' => 'data',
                'header' => Rights::t('core', 'Data'),
                'type' => 'raw',
                'htmlOptions' => array('class' => 'data-column'),
                'visible' => Rights::module()->enableBizRuleData === true,
            ),
            array(
                'header' => '&nbsp;',
                'type' => 'raw',
                'htmlOptions' => array('class' => 'actions-column'),
                'value' => '$data->getDeleteTaskLink()',
            ),
        )
    ));
    ?>

    <p class="info"><?php echo Rights::t('core', 'Values within square brackets tell how many children each item has.'); ?></p>

</div>