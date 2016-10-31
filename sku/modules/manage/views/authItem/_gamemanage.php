<tr>
    <td rowspan="1">
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Main.GameConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Main.GameConfig" id="GameConfig"><label for="GameConfig">游戏管理</label>
    </td>
    <td>
        <?php $this->renderPartial('_gameconfig', array('rights' => $rights)); ?>
    </td>
</tr>