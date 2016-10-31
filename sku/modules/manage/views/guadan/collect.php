<?php
/**
 * @author zhenjun_xu <412530435@qq.com>
 * Date: 2016/1/12 0012
 * Time: 13:41
 * @var $this MController;
 */
$this->breadcrumbs = array('挂单管理' => array('admin'), '积分出售');
?>
<style>
    .search-form{ line-height:40px; }
    .tab-reg{
        margin-bottom:20px;
    }
    .tab-reg thead th{
        font-weight:bold;
        background:#D0D0F0;
        color:#333;
    }
    .tab-reg a, .tab-reg th a{
        color:blue;
    }
    .tab-reg thead th{
        padding:0 10px;
    }

    .rule thead th{
        background:#DBDBFF;
        color:#333;
    }
</style>
<div class="search-form" >
    <div class="border-info clearfix search-form">
        <table class="searchTable">
            <tr>
                <td>可使用非绑定积分总额: <strong><?php echo HtmlHelper::formatPrice($amount2) ?></strong></td>
            </tr>
            <tr>
                <td>提取的待绑定积分总额: <strong><?php echo HtmlHelper::formatPrice($amount1) ?></strong></td>
            </tr>
        </table>
    </div>
</div>
<div class="search-form" >

<?php if(Yii::app()->user->checkAccess('Manage.Guadan.Collect')):?>

    <strong>出售政策</strong>
    <button type="button"  class="regm-sub addCollect" style="float:right">新增政策</button>
    <?php endif;?>
</div>
<div class="grid-view" id="guadan-grid">
    <?php foreach($data as $k => $v): ?>
    <table class="tab-reg <?php echo ($k>0 || $v['id']!=$this->getSession('currentGuadan')) ? 'moreTab':'' ?>" style="display: <?php echo $v['id']==$this->getSession('currentGuadan') ? 'block':'none'; ?>;">
        <thead>
        <tr style="width:100%;">
            <th colspan="2" style="text-align:left;">挂单提取编号：<?php echo $v['code'] ?></th>
            <th colspan="2" style="text-align:right;">
            	<?php if(Yii::app()->user->checkAccess('Manage.Guadan.DelCollect')):?>
                <?php echo CHtml::link('删除',array('guadan/delCollect','id'=>$v['id']),array('class'=>'collectDel')); ?>&nbsp;
                <?php endif;?>
                <?php //echo CHtml::link('编辑','#',array('class'=>'edit')); ?>
            </th>
        </tr>
        </thead>
        <tr >
            <td width="200">待绑定积分额度:</td>
            <td style="text-align:left"><strong><?php echo $v['amount_bind'] ?></strong></td>
            <td width="200">非绑定积分额度:</td>
            <td style="text-align:left"><strong><?php echo $v['amount_unbind'] ?></strong></td>
        </tr>
        <tr >
            <td width="200">绑定粒度:</td>
            <td style="text-align:left"><strong><?php echo $v['bind_size'] ?></strong>积分/人</td>
            <td width="200">会员推荐者分配比例:</td>
            <td style="text-align:left"><strong><?php echo $v['distribution_ratio'] ?></strong>%</td>
        </tr>
        <tr>
            <td colspan="2">
                新用户政策 
                
                <?php if(Yii::app()->user->checkAccess('Manage.GuadanRule.Add')):?>
                <?php echo CHtml::link('+',array(
                    'guadanRule/add','collect_id'=>$v['id'],
                    'type'=>GuadanRule::NEW_MEMBER,
                    'amount_bind'=>$v['amount_bind'],
                    'amount_unbind'=>$v['amount_unbind'],
                ),
                    array('style'=>'font-size:30px;','class'=>'addNew','title'=>'添加新用户政策')); ?>
                    
                    <?php endif;?>
            </td>
            <td colspan="2">
                老用户政策 
                
                <?php if(Yii::app()->user->checkAccess('Manage.GuadanRule.Add')):?>
                <?php echo CHtml::link('+',array(
                    'guadanRule/add','collect_id'=>$v['id'],
                    'type'=>GuadanRule::OLD_MEMBER,
                    'amount_bind'=>$v['amount_bind'],
                    'amount_unbind'=>$v['amount_unbind'],
                ),
                    array('style'=>'font-size:30px;','class'=>'addOld','title'=>'添加老用户政策')); ?>
                    <?php endif;?>
            </td>
        </tr>
        <tr>
            <td colspan="2" width="50%" >
                <?php
                    $this->renderPartial('_ruleTable',array('collect'=>$v,'type'=>GuadanRule::NEW_MEMBER));
                ?>
            </td>
            <td colspan="2" width="50%" >
                <?php
                $this->renderPartial('_ruleTable',array('collect'=>$v,'type'=>GuadanRule::OLD_MEMBER));
                ?>
            </td>
        </tr>
    </table>
    <?php endforeach; ?>
    <?php
        if(count($data)>1 || (count($data)==1 && $this->getSession('currentGuadan')!=$data[0]['id'])){
            echo CHtml::link('显示更多未启用政策','#',array('id'=>'showMore'));
        }
    ?>
</div>
<style>
    #showMore{
        background: #E40808;
        border: 0 none;
        border-radius:5px;
        color: #fff;
        display: inline-block;
        font-family: "微软雅黑";
        height: 27px;
        line-height: 27px;
        text-align: center;
        width: 166px;
    }
</style>

<script src="<?php echo DOMAIN_M ?>/js/swf/js/artDialog.iframeTools.js"></script>
<script>
    $(function(){
        //显示更多
        $("#showMore").click(function(){
            if($(this).html()=='显示更多未启用政策'){
                $(".moreTab").show();
                $(this).html("隐藏更多未启用政策")
            }else{
                $(".moreTab").hide();
                $(this).html("显示更多未启用政策")
            }

        });
        //打开积分提取弹窗
        $('.addCollect').click(function(){
            art.dialog.open("<?php echo $this->createAbsoluteUrl('guadan/collect',array('form'=>1,'ids'=>$this->getParam('ids'))) ?>",
                {
                    width:610,height:600,title:"新增政策",
                    yesFn:function(data){
                        console.log(data);
                    }
                }
            );
        });
        //删除积分提取
        $(".collectDel").click(function(){
            if(!confirm("确定要删除这条数据？")) return false;
            $.post(this.href,{},function(data){
                if(typeof data.msg!='undefined') alert(data.msg);
                if(data.flag) document.location.reload();
            },'json');
            return false;
        });
        //添加新用户政策
        $(".addNew").click(function(){
            art.dialog.open(this.href,
                {width:610,height:650,title:"添加新用户政策"}
            );
            return false;
        });
        //添加旧用户政策
        $(".addOld").click(function(){
            art.dialog.open(this.href,
                {width:610,height:650,title:"添加老用户政策"}
            );
            return false;
        });
        //删除用户政策
        $(".ruleDel").click(function(){
            if(!confirm("确定要删除这条数据？")) return false;
            $.post(this.href,{},function(data){
                if(typeof data.msg!='undefined') alert(data.msg);
                if(data.flag) document.location.reload();
            },'json');
            return false;
        });
        //编辑用户政策
        $(".ruleEdit").click(function(){
            art.dialog.open(this.href,
                {width:610,height:650,title:"修改用户政策"}
            );
            return false;
        });
    })
</script>