 <h3 class="mt15 tableTitle"><?php echo Yii::t('partnerModule.order','物流信息');?></h3>
            <table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
                <tbody><tr>
                        <th width="10%"><?php echo Yii::t('partnerModule.order','配送状态');?></th>
                        <td width="90%">
                            <?php  if($orderDetail['delivery_status']==  Order::DELIVERY_STATUS_WAIT||$orderDetail['refund_status']==Order::RETURN_STATUS_FAILURE){?>未发货&nbsp;&nbsp;<a href="javascript:void(0)" class="sellerBtn05" onClick="consignment()"><span>发货</span><?php  }?></a>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo Yii::t('partnerModule.order','运送方式');?></th>
                        <td></td>
                    </tr>
                    <tr>
                        <th><?php echo Yii::t('partnerModule.order','物流动态');?></th>
                        <td>
                              <?php echo Yii::t('partnerModule.order','暂无物流动态');?>
                        </td>
                    </tr>
                </tbody>
            </table>