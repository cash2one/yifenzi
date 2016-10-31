<?php
// 切换加盟商视图
$this->breadcrumbs = array(
    Yii::t('partnerModule.freshMachine', '生鲜机管理'),
    Yii::t('partnerModule.freshMachine', '盖网生鲜机列表')
);
?>
<div class="toolbar">
    <b> <?php echo Yii::t('partnerModule.freshMachine', '盖网生鲜机列表'); ?></b>
</div>
<a href="javascript:;" class="regm-sub mt15 btnSellerAdd" id="Export" onclick="showExport()"><?php echo Yii::t('main', '导出excel'); ?></a>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody><tr>
            <th class="bgBlack" width="15%"><?php echo Yii::t('partnerModule.freshMachine', '名称'); ?></th>
            <th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.freshMachine', '机器编号'); ?></th>
            <th class="bgBlack" width="5%"><?php echo Yii::t('partnerModule.freshMachine', '类型'); ?></th>
            <th class="bgBlack" width="5%"><?php echo Yii::t('partnerModule.freshMachine', '状态'); ?></th>
            <th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.freshMachine', '所在地区'); ?></th>
            <th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.freshMachine', '软件版本'); ?></th>
            <th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.freshMachine', '防闪退启用状态'); ?></th>
            <th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.freshMachine', '操作'); ?></th>
        </tr>

        <?php foreach ($machine_data as $data): ?>

            <tr class="even">
                <td class="ta_c"><?php echo $data->name; ?></td>
                <td class="ta_c"><?php echo $data->code; ?></td>
                <td class="ta_c"><?php echo StoreCategory::getCategoryName($data['category_id']); ?></td>
                <td class="ta_c"><b class="red"><?php echo FreshMachine::getStatus($data->status); ?></b></td>
                <td class="ta_c"><?php echo Region::getName($data->province_id, $data->city_id, $data->district_id) ?></td>
                <td class="ta_c"><?php echo empty($data->version)?'-':$data->version?></td>
                <td class="ta_c"><?php echo ($data->flash_back_status==FreshMachine::IS_BACK_NO)?'<b class="red">'.  FreshMachine::getIsBack($data->flash_back_status):FreshMachine::getIsBack($data->flash_back_status) ?></td>
                <td class="ta_c">

                    <a href="<?php echo Yii::app()->createUrl('/partner/freshMachine/freshGoods/', array('mid' => $data->id)); ?>"><?php echo Yii::t('partnerModule.freshMachine', '商品管理') ?></a>
                    
                    <?php if ($data['partner_id']==$this->curr_act_partner_id) { ?>
                    	| <a href="<?php echo Yii::app()->createUrl('/partner/freshMachine/freshLine/', array('mid' => $data->id)); ?>"><?php echo Yii::t('partnerModule.freshMachine', '货道管理') ?></a>
                    <?php  }?>
                    
                    
                </td>
            </tr>

        <?php endforeach; ?>

    </tbody></table>




<div class="page_bottom clearfix">
    <div class="pagination">
        <?php
        $this->widget('CLinkPager', array(//此处Yii内置的是CLinkPager，我继承了CLinkPager并重写了相关方法
            'header' => '',
            'prevPageLabel' => Yii::t('partnerModule.page', '上一页'),
            'nextPageLabel' => Yii::t('partnerModule.page', '下一页'),
            'pages' => $pager,
            'maxButtonCount' => 15, //分页数目
            'htmlOptions' => array(
                'class' => 'paging', //包含分页链接的div的class
            )
                )
        );
        ?>
    </div>
</div>
<div  id="export-all" class="pager" style="display:none;">

</div>
<script type="text/javascript" language="javascript" src="/js/iframeTools.source.js"></script>
<script type="text/javascript">
    function showExport() {
        var html = '';
        $.post('<?=$this->createAbsoluteUrl('/partner/freshMachine/export')?>',{export:1,YII_CSRF_TOKEN:"<?php echo Yii::app()->request->csrfToken?>"},function(data){
            if(data.success == true){
                $("#export-all").html('');
                var i = 1;
                html = '<div class="pager"> ';
                html += '（每份最多'+data.totalCount+'条记录）:<ul class=" " id="yw1">';
                if(data.pagecount > 1){
                    for(i = 1; i <= data.pagecount; i++){
                        html += '<li class="page"><a href="http://'+window.location.host+'/freshMachine/export?page='+i+'" style="color: rgb(51, 102, 204);">'+i+'</a></li>';
                    }
                    html += '</ul>';
                }else{
                    html += '<a href="http://'+window.location.host+'/freshMachine/export?page='+i+'" style="color: rgb(51, 102, 204);">导出数据  </a>';

                }
                html += '</div>';
                $("#export-all").append(html);
                art.dialog({
                    content: $("#export-all").html(),
                    title: '<?php echo !empty($title)?$title: Yii::t('main', '导出到excel') ?>'
                });

           }

        },'json');

    }
</script>
<style>
    /*调整后的分页样式*/
    .pager {
        display: inline-table;
        padding: 30px 0 10px 0;
        height: 25px;
        line-height: 25px;
        font-family: "微软雅黑";
        font-size: 16px;
        text-align: center;
        color: #FF3C3C;
    }
    .pager { padding-left: 20px; }
    .pager ul, .pager ul li { display: inline; padding: 0 5px; border: none; }
    .pager li form { display: inline; }
    .pager a {
        color: #FF3C3C;
        padding: 0 2px;
        font-family: "微软雅黑";
        font-size: 16px;
        display: inline-block;
        font-weight: normal;
        border: none;
    }
    .pager ul li.selected a { color: black; font-size: 15px; font-weight: bold; }
</style>