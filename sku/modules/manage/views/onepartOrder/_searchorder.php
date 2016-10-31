 <div class="border-info clearfix search-form">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'GET',
    ));
    ?>
	
    <table cellpadding="0" cellspacing="0" class="searchTable">
        <tbody>
            <tr>
                <th><?php echo '份数：'; ?></th>
                <td><input type="text" name="goods_number" id="val1" value="<?php echo Yii::app()->request->getParam('goods_number')?>" class="text-input-bj least"></td>
				<th><?php echo '截至揭晓时间：'; ?></th>
                <td><?php
                    $this->widget('comext.timepicker.timepicker', array(
                        'model'=>$model,
                        'name'=>'addtime',
                    ));
                    ?></td>
              <!--  <th><?php /*echo '时'; */?></th>
                <td><select id="test" name="i" class="text-input-bj least" onclick="fun()">
                        <?php /*for($i=0;$i<60;$i++):*/?>
                            <option value ="<?php /*echo $i;*/?>" <?php /*if(Yii::app()->request->getParam('i')==$i) echo("selected");*/?>><?php /*echo $i;*/?></option>
                        <?php /*endfor;*/?>
                    </select></td>
                <th><?php /*echo '分'; */?></th>
                <td><select id="test" name="s" class="text-input-bj least" onclick="fun()">
                        <?php /*for($i=0;$i<60;$i++):*/?>
                            <option value ="<?php /*echo $i;*/?>" <?php /*if(Yii::app()->request->getParam('s')==$i) echo("selected");*/?>><?php /*echo $i;*/?></option>
                        <?php /*endfor;*/?>
                    </select></td>
                <th><?php /*echo '秒'; */?></th>-->
            </tr>
        </tbody>
    </table>

    <?php echo CHtml::submitButton(Yii::t('user', '搜索'), array('class' => 'reg-sub')); ?>
    <?php $this->endWidget(); ?>
</div>
