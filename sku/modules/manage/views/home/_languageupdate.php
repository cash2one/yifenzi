<?php if (!empty($languageArr)): //语言包数组     ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-reg">
        <caption>语言包文件：<?php
            echo $updateFile = basename(Tool::authcode($this->getQuery('languageFile'), 'DECODE'));
            ?>,
            文件说明：<?php echo isset($messagesConfig['languageInfoMap'][$updateFile]) ? $messagesConfig['languageInfoMap'][$updateFile] :null; ?>
        </caption>
        <thead>
            <tr class="tab-reg-title">
                <th><?php echo Yii::t('home', '简体中文'); ?></th>
                <th><?php echo Yii::t('home', $languageName); ?></th>
                <th><?php echo Yii::t('home', '操作'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($languageArr as $key => $v):
                $size = strlen($v) <= 140 ? 140 : 0;
                ?>
                <tr name="tr_<?php echo md5($key); ?>">
                    <td><?php echo htmlspecialchars($key); ?></td>
                    <td>
                        <?php
                        if ($size) {
                            echo CHtml::textField(md5($key), $v, array(
                                'class' => 'text-input-bj  longest'
                            ));
                        } else {
                            echo CHtml::textArea(md5($key), $v, array(
                                'class' => 'text-input-bj  longest'
                            ));
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        echo CHtml::ajaxLink(Yii::t('home', '【修改】'), '', array(
                            'type' => 'post',
                            'success' => 'js:function(msg){
                                 art.dialog({content:msg,ok:true});
                           }  
                        ',
                            'data' => array(
                                'do' => 'update',
                                'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken,
                                'languageFile' => $this->getQuery('languageFile'),
                                'key' => $key,
                                'value' => 'js:$("#' . md5($key) . '").val()',
                            ),
                        ));
                        echo CHtml::ajaxLink(Yii::t('home', '【删除】'), '', array(
                            'type' => 'post',
                            'data' => array(
                                'do' => 'del',
                                'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken,
                                'languageFile' => $this->getQuery('languageFile'),
                                'key' => $key,
                            ),
                            'success' => 'js:function(msg){
                                    if(msg.length>0){
                                        art.dialog({content:msg,ok:true});
                                    }else{
                                       $("tr[name=\'tr_' . md5($key) . '\']").remove();
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
