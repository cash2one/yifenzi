<?php
//搜索框
$this->renderPartial('_languageform', array('dir' => $dir, 'languageDir' => $languageDir));
?>
<div class="c10">
</div>

<?php
//搜索结果,语言包文件修改
$this->renderPartial('_languageupdatesearch', array(
    'result' => $result,
    'languageDir' => $languageDir,
    'messagesConfig' => $messagesConfig,
));
?>
<div id="tran">
    <?php //生成英文语言包
    $langConfig = Fun::getConfig('languageTranslate');
    if(empty($langConfig) || @$langConfig['tran'] == 'off'){
        echo CHtml::link('开启自动翻译', '', array('onclick' => 'changeConfige(1)', "class" => "regm-sub",'style'=>'float:right'));
    }else{
        echo CHtml::link('关闭自动翻译', '', array('onclick' => 'changeConfige(0)', "class" => "regm-sub",'style'=>'float:right'));
    }
    ?>
</div>

<?php //生成英文语言包
if(strpos($messagesConfig['messagePath'], 'partner') !== FALSE){

    if(Yii::app()->user->checkAccess('Manage.Home.LanguagePartner')){

        $sendUrl = 'createFrontPackFromDb';
        echo CHtml::link('生成英文包', '', array('onclick' => 'createPackage()', "class" => "regm-sub",'style'=>'float:right'));
    }
}elseif(strpos($messagesConfig['messagePath'], 'manage') !== FALSE){
    if(Yii::app()->user->checkAccess('Manage.Home.LanguageBackend')){
        $sendUrl = 'createBackPackFromDb';
        echo CHtml::link('生成英文包', '', array('onclick' => 'createPackage()', "class" => "regm-sub",'style'=>'float:right'));
    }
}elseif(strpos($messagesConfig['messagePath'], 'api') !== FALSE){
    if(Yii::app()->user->checkAccess('Manage.Home.LanguageApi')){
        $sendUrl = 'createApiPackFromDb';
        echo CHtml::link('生成英文包', '', array('onclick' => 'createPackage()', "class" => "regm-sub",'style'=>'float:right'));
    }
}elseif(strpos($messagesConfig['messagePath'], 'sku') !== FALSE){
    if(Yii::app()->user->checkAccess('Manage.Home.LanguageSku')){
        $sendUrl = 'createApiPackFromDb';
        echo CHtml::link('生成英文包', '', array('onclick' => 'createPackage()', "class" => "regm-sub",'style'=>'float:right'));
    }
}
?>
<script type="text/javascript">
function createPackage() {
if(confirm('<?php echo Yii::t('homeLanguage', "此模块的全部英文语言包将被覆盖,并且此操作不可恢复,是否确定生成?!");?>') === true){
    jQuery.ajax({
        url: "<?php echo Yii::app()->createUrl('/home/'.$sendUrl) ?>",
        //dataType: 'json',
        cache: false,
        success: function(data) {
            alert(data);
        }
    });
}
return false;
}
function changeConfige(val) {
if(confirm('<?php echo Yii::t('homeLanguage', "此操作将会影响到前台网页的英文翻译效果,是否确定操作?!");?>') === true){
    var tran = 'off';
    if(val == 1){ tran = 'on'; }

    jQuery.ajax({
        url: "<?php echo Yii::app()->createUrl('/home/languageConfig') ?>",
        cache: false,
        data: {tran:tran},
        type: 'POST',
        success: function(data) {
            if(data == 'on'){
                $('#tran').html('<?php echo CHtml::link("关闭自动翻译", "", array("onclick" => "changeConfige(0)", "class" => "regm-sub","style"=>"float:right"));?>');
            }else{
                $('#tran').html('<?php echo CHtml::link("开启自动翻译", "", array("onclick" => "changeConfige(1)", "class" => "regm-sub","style"=>"float:right"));?>');
            }
        }
    });
}
}
</script>

<?php
//目录文件列表
$this->renderPartial('_languagefileshow', array(
    'languageFiles' => $languageFiles,
    'messagesConfig' => $messagesConfig,
));
?>

<?php
//语言包文件修改
$this->renderPartial('_languageupdate', array(
    'languageArr' => $languageArr,
    'languageName' => $languageName,
    'messagesConfig' => $messagesConfig,
));
?>