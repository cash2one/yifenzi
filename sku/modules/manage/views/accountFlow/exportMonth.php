<?php

/* @var $this AccountFlowController */
/* @var $model AccountFlow */

$this->breadcrumbs = array(
    '流水导出' => array('exportMonth'),
    '导出',
);
?>
<div style="padding-top: 50px;">
    <a style="visibility: hidden;" id="export_month_hidden" href="#"></a>
    <span style="padding: 0 60px;font-weight:bold;">1.&nbsp;&nbsp;<a id="export_month" href="#">[导出 -> 未被导出的流水]</a></span>
</div>
<div style="padding-top: 50px;">
    <a style="visibility: hidden;" id="export_batch_hidden" href="#"></a>
    <span style="padding: 0 60px;font-weight:bold;">2.&nbsp;&nbsp;批号:<input type="text" name="batch_input" id="batch_input" />&nbsp;&nbsp;<a id="export_batch" href="#">[导出该批号的流水]</a></span>
</div>

<div style="padding-top: 50px;">
    <span style="padding: 0 60px;font-weight:bold;">
        3.&nbsp;&nbsp;<a href="<?php echo $this->createAbsoluteUrl('/accountFlow/downloadList',array('t'=>time()));?>">[查看并下载]</a></span>
</div>
<script type="text/javascript">
    $(function(){
        $("#export_batch").click(function(){
            var batch_input = $("#batch_input").val();
            if(batch_input == undefined || batch_input < 1 || batch_input.length < 10 || isNaN(batch_input)){
                alert('请输入你用导出的正确批号!');return;
            }
            $.ajax({
                dataType: 'json',
                url:'<?php echo $this->createAbsoluteUrl("/accountFlow/getCountBeforeExportBatchCsv",array('t'=>time())); ?>',
                type: "GET",
                data: {
                    batch: batch_input
                },
                success:function(data){
                    if(data == 'success'){
                        $("#export_batch_hidden").attr('href','<?php echo $this->createAbsoluteUrl("/accountFlow/exportCsv",array('t'=>time())); ?>'+'&batch='+batch_input);
                        document.getElementById("export_batch_hidden").click();
                    }else if(data == 'exist'){
                        alert('文件已被导出,请直接下载');
                    }else{
                        alert('没找到该批次的流水');
                    }
                }
            })
        });
        $("#export_month").click(function(){
            $.ajax({
                dataType: 'json',
                url:'<?php echo $this->createAbsoluteUrl("/accountFlow/getCountBeforeExportCsv",array('t'=>time())); ?>',
                type: "GET",
                success:function(data){
                    if(data == 'success'){
                        if(confirm('确定后,文件将在几分钟后生成.\n是否现在导出？') === true){
                            $("#export_month_hidden").attr('href','<?php echo $this->createAbsoluteUrl("/accountFlow/exportCsv",array('t'=>time())); ?>');
                            document.getElementById("export_month_hidden").click();
                        }
                    }else{
                        alert('现在暂时没有未被导出的新流水');
                    }
                }
            })
        });
    })
</script>