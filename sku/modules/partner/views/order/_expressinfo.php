 <h3 class="mt15 tableTitle"><?php echo Yii::t('partnerModule.order','物流信息');?>&nbsp;
     <?php if(($orderDetail['delivery_status'] == Order::DELIVERY_STATUS_SEND) && $orderDetail['status'] == Order::STATUS_NEW):?>
         <a onclick="changeExpress()" class="sellerBtn05" href="javascript:void(0)"><span><?php echo Yii::t('partnerModule.order', '修改'); ?></span></a>
     <?php endif;?>
 </h3>
            <table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
                <tbody><tr>
                        <th width="10%"><?php echo Yii::t('partnerModule.order','配送状态');?></th>
                        <td width="90%">
                            <?php echo Order::deliveryStatus($orderDetail['delivery_status']);?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo Yii::t('partnerModule.order','快递公司');?></th>
                       <td><?php echo Yii::t('partnerModule.order',$orderDetail['express'])?></td>
                    </tr>
                    <tr>
                        <th><?php echo Yii::t('partnerModule.order','快递单号');?></th>
                        <td><?php echo $orderDetail['shipping_code']?></td>
                    </tr>
                    <tr>
                        <th><?php echo Yii::t('partnerModule.order','物流动态');?></th>
                       <td id="express_orstatus">
                          <?php echo Yii::t('partnerModule.order','暂无物流动态');?> 
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <?php if(!empty($orderDetail['shipping_code'])):?>
                <script>
                $("#express_orstatus").html('<?php echo Yii::t('partnerModule.order','正在查询物流信息.....');?>');
					$.getJSON("<?php echo $this->createUrl( 'order/getExpressStatus',array('store_name'=>$orderDetail['express'],'code'=>$orderDetail['shipping_code'],'time'=>time())); ?>",function(data){
						if(data.status!=200){
							$("#express_orstatus").html(data.message);
						}else{
							var ex_html = '';

							$.each(data.data, function(i,item){
								
								ex_html += '<p>'+item.time+'&nbsp;&nbsp;&nbsp;&nbsp;'+item.context+'</p>'; 
							  });

							$("#express_orstatus").html(ex_html);

						}
					});

                </script>
                
                <?php endif;?>