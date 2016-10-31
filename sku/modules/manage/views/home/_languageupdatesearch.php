<?php if (!empty($result)): //语言包数组         ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-reg">
        <thead>
            <tr class="tab-reg-title">
                <th><?php echo Yii::t('home', '语言包'); ?></th>
                <th><?php echo Yii::t('home', '文件说明'); ?></th>
                <th><?php echo Yii::t('home', '简体中文'); ?></th>
                <th><?php echo $messagesConfig['languageName'][basename($languageDir)]; ?></th>
                <th><?php echo Yii::t('home', '操作'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $langArr): ?>
                <?php
                foreach ($langArr['result'] as $k => $v):
                    $size = strlen($v) <= 140 ? 140 : 0;
                    ?>
                    <tr name="tr_<?php echo md5($langArr['file'].$k); ?>" >
                        <td>
                            <?php
                            $langFile = Tool::authcode($langArr['file'], 'DECODE');
                            $baseLangFile = basename($langFile);
                            echo CHtml::link("【{$baseLangFile}】", array('home/'.$this->action->id,
                                'languageFile' => $langArr['file'],
                                'languageList'=>$this->getQuery('languageList'),
                                    ));
                            ?></td>
                        <td><?php echo Yii::t('home', isset($messagesConfig['languageInfoMap'][$baseLangFile]) ? $messagesConfig['languageInfoMap'][$baseLangFile] : null)  ?></td>
                        <td><?php echo htmlspecialchars($k) ?></td>
                        <td>
                            <?php
                            if ($size) {
                                echo CHtml::textField(md5($langArr['file'].$k), $v, array(
                                    'class' => 'text-input-bj  longest'
                                ));
                            } else {
                                echo CHtml::textArea(md5($langArr['file'].$k), $v, array(
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
                                'languageFile' => $langArr['file'],
                                'key' => $k,
                                'value' => 'js:$("#' . md5($langArr['file'].$k) . '").val()',
                            ),
                        ));
                        echo CHtml::ajaxLink(Yii::t('home', '【删除】'), '', array(
                            'type' => 'post',
                            'data' => array(
                                'do' => 'del',
                                'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken,
                                'languageFile' => $langArr['file'],
                                'key' => $k,
                            ),
                            'success' => 'js:function(msg){
                                    if(msg.length>0){
                                        art.dialog({content:msg,ok:true});
                                    }else{
                                       $("tr[name=\'tr_' . md5($langArr['file'].$k) . '\']").remove();
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
    <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
