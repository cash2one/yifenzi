<?php
$this->breadcrumbs = array(
    Yii::t('site', '收入查询'),
);

?>

<div class="search-form" >
    <div class="border-info clearfix">
        <table cellspacing="0" cellpadding="0" class="searchTable" style="width: 100%">
            <tbody><tr>
                    <th align="left" style="width:5%">
                        <b>配送员姓名：
                    </th>
                    <td>
                        <?php echo $d_model->name; ?>
                    </td>
                </tr>     
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" class="searchTable" style="width: 100%">
            <tbody><tr>
                    <th align="right" style="width: 5%">
                        GW号：
                    </th>
                    <td style="width: 20%">
                        <?php echo Member::getMemberById($d_model->member_id, array("gai_number"))->gai_number; ?>
                    </td>

                    <th align="right" style="width: 5%">
                        身份证：
                    </th>
                    <td style="width: 20%">
                        <?php echo $d_model->member_personal->identification; ?>
                    </td>

                    <th align="right"  style="width: 5%">
                        手机号：
                    </th>
                    <td>
                        <?php echo $d_model->mobile; ?>
                    </td>
                </tr>     
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" class="searchTable"style="width: 100%">
            <tbody><tr>
                    <th align="right" style="width:5%">
                        配送收入：
                    </th>
                    <td style="width: 20%">
                        暂无
                    </td>
                    <th align="right" style="width:5%">
                        服务次数：
                    </th>
                    <td>
                        <?php echo $d_model->service_count; ?>
                    </td>         
                    <th align="right"  colspan="4" id="nowStatus" >
                        用户状态：<?php echo Distribution::getStatus($d_model->status); ?>
                    </th>
                    <td style="width:5%">
                        <?php echo $d_model->status == Distribution::STATUS_CLOSE ? CHtml::Button('启用', array('class' => 'reg-sub', 'id' => 'setStatus', 'name' => 'open')) : CHtml::Button('禁用', array('class' => 'reg-sub', 'id' => 'setStatus', 'name' => 'close')) ?>
                    </td>
                </tr>     
            </tbody>
        </table>
    </div>
</div>
<?php
/* @var $this OrderController */
/* @var $model Orders */
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#order-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
});
");
?>

<div class="c10"></div>

<?php
$this->widget('GridView', array(
    'id' => 'personal-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
        array(
            'name' => '订单时间',
            'value' => 'date("Y-m-d H:i:s",$data->orders->create_time)',
            'type' => 'raw'
        ),
        array(
            'name' => '订单编号',
            'value' => '$data->orders->code',
            'type' => 'raw'
        ),
        array(
            'name' => '配送所在城市',
            'value' => 'Region::getName($data->orders->address->province_id, $data->orders->address->city_id)',
            'type' => 'raw'
        ),
        array(
            'name' => '配送店铺',
            'value' => 'Order::findSuper($data->orders->store_id,array("name"))->name',
            'type' => 'raw'
        ),
        array(
            'name' => '联系手机号',
            'value' => '$data->distribution->mobile',
            'type' => 'raw'
        ),
        array(
            'name' => '配送收入',
            'value' => '"暂无"',
            'type' => 'raw'
        ),
    ),
));
?>
<script>
    $('#setStatus').click(function () {
        var name = $('#setStatus').attr('name');
        $.ajax({
            type: 'POST',
            url: '<?php echo $this->createAbsoluteUrl('memberPersonalAuthentication/resetStatus', array('id' => $d_model->id)); ?>',
            data: {'name': name},
            success: function (data) {            
                if (name == 'close') {
                  $('#setStatus').attr('name','open');
                  $('#setStatus').attr('value','启用');
                  $('#nowStatus').html('用户状态：禁用');
                } else {
                   $('#setStatus').attr('name','close');
                  $('#setStatus').attr('value','禁用');
                  $('#nowStatus').html('用户状态：启用');
                }
                alert(data);
            },
            error: function () {
                alert('设置失败')
            }
        });
    });
</script>
