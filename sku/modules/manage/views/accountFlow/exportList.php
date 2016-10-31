<?php

/* @var $this AccountFlowController */
/* @var $model AccountFlow */

$this->breadcrumbs = array(
    '流水导出' => array('exportMonth'),
    '下载',
);
?>
<style type="text/css">
    .log{
        width: 90%;border:1px solid #ccc;padding:20px; margin:auto 0;text-align: center;
    }
    .log table{
        width: 100%;
    }
    .log_title{
        border:1px solid #ccc;width:100%;height:38px;padding:3px;line-height:20px;text-align:center; background-color: #f5f5f5;
    }
    .log_val{
        line-height: 28px;
    }
    .log_val td{
        border:1px solid #ccc;
    }
</style>
<div class="log">
    <table>
        <tr class="log_title">
            <th>批号</th>
            <th>最后导出时间</th>
            <th>导出次数</th>
            <th>文件</th>
        </tr>
        <?php if(!empty($log)){ ?>
        <?php foreach($log as $val){ ?>
        <tr class="log_val">
            <td><?= $val['export_batch']; ?></td>
            <td><?= date('Y-m-d H:i:s',$val['last_time']); ?></td>
            <td><?= $val['export_times']; ?></td>
            <td><a target="_blank" href="http://att.e-gatenet.cn/<?= $val['file_name'];/*ATTR_DOMAIN.'/'.*/ ?>">
                    <?= str_replace('/','',str_replace(dirname($val['file_name']),'',$val['file_name'])); ?></a></td>
        </tr>
        <?php } ?>
        <?php } ?>
    </table>

</div>

