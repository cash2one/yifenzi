<?php
/**
 * @var array $config
 * @var  array $rights
 */
foreach ($config as $k => $v):
    ?>
    <label><?php echo $k ?></label>
    (
    <?php
    foreach ($v as $k2 => $v2):
        $id = str_replace('.', '', $v2);
        echo CHtml::checkBox('rights[]', in_array($v2, $rights), array('value' => $v2, 'id' => $id,))
        ?>
        <label for="<?php echo $id ?>"><?php echo $k2 ?></label>
    <?php endforeach;?>
    )
    <hr/>
<?php endforeach; ?>