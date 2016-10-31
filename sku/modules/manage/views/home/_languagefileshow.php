<?php if (!empty($languageFiles)): //显示语言文件  ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-reg">
        <thead>
            <tr class="tab-reg-title">
                <th><?php echo Yii::t('home', '语言包'); ?></th>
                <th><?php echo Yii::t('home', '文件说明'); ?></th>
                <th><?php echo Yii::t('home', '操作'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($languageFiles as $key => $v): ?>
                <tr id="tr_<?php echo $v;?>">
                    <td><?php echo $v; ?></td>
                    <td><?php echo isset($messagesConfig['languageInfoMap'][$v]) ? $messagesConfig['languageInfoMap'][$v] : null; ?></td>
                    <td>
                        <?php
                        echo CHtml::link(Yii::t('home', '【编辑】'), array('home/'.$this->action->id,
                            'languageFile' => Tool::authcode($key),
                            'languageList' => $this->getQuery('languageList'),
                        ))
                        ?>
                        <?php
                        echo CHtml::ajaxLink(Yii::t('home', '【删除】'), '', array(
                                'type' => 'post',
                                'data' => array(
                                    'do' => 'delFile',
                                    'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken,
                                    'languageFile' => Tool::authcode($key),
                                ),
                                'success' => 'js:function(msg){
                                    if(msg.length>0){
                                        art.dialog({content:msg,ok:true});
                                    }else{
                                       $("tr[id=\'tr_' . $v . '\']").remove();
                                    }
                                }',
                            ), array(
                                'onclick' => 'if(!confirm("' . Yii::t('home', '真的要删除？') . '") ) return;',
                            )
                        );
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>