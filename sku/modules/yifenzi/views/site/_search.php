<style type="text/css">
.search_btn_group .btsearch{display: inline-block;background-color: #fff;width:41px;height: 30px;line-height: 30px;text-align: center;border-radius: 5px;margin-left:-3px;}
.search_btn_group a.btcancel{display: inline-block;background-color: #fff;width:41px;height: 30px;line-height: 30px;text-align: center;border-radius: 5px;margin-left:-3px;}
</style>
<?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => $this->createUrl('goods/list'),
        'method' => 'get',
        ));
?>
    <div class="search_bar">
        <div class="search_input">
            <input type="text" name="goods_name" value="<?php echo Yii::app()->request->getParam('goods_name')?>" placeholder="请输入商品名称"> 
		</div>
        <div class="search_btn_group">
            <input type="submit" value="搜索" class="btsearch">
            <a href="javascript:;" id="search_cancel" class ="btcancel">取消</a>
        </div>
    </div>
<?php $this->endWidget(); ?>

    