<?php
$this->breadcrumbs = array(
    Yii::t('appVersion', '客户端') => array('admin'),
    $model->isNewRecord ? Yii::t('appVersion', '新增') : Yii::t('appVersion', '修改')
);
?>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'appVersion-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
));
?>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#android').show();
            $('#ios').hide();
            $('#IOS_SELECT_URL').show();
            $('#IOS_SELECT_IPD').hide();
            $('#IOS_SELECT_IPD_IMG').hide();
            //编辑页面用到。
            var a = $('#AppVersion_system_type').val();
            if (a == <?php echo AppVersion::SYSTEM_TYPE_ANDROID?>) {
                $('#android').show();
                $('#ios').hide();
                $('#IOS_SELECT_IPD_IMG').hide();
            }
            else if (a == <?php echo AppVersion::SYSTEM_TYPE_IOS?>) {
                $('#android').hide();
                $('#ios').show();
            } else;

            var IOSSelectVal = $('#AppVersion_ios_select').val();
            if (IOSSelectVal == <?php echo AppVersion::IOS_SELECT_URL?>){
                $('#IOS_SELECT_URL').show();
                $('#IOS_SELECT_IPD').hide();
                $('#IOS_SELECT_IPD_IMG').hide();
                $('#imageUpload').text('请上传图片');
            }else if(IOSSelectVal == <?php echo AppVersion::IOS_SELECT_IPD?>){
                $('#IOS_SELECT_URL').hide();
                $('#IOS_SELECT_IPD').show();
                $('#IOS_SELECT_IPD_IMG').show();
                $('#imageUpload').text('请上传Full Size Image图片');
            }
        });
    </script>
    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="tab-come">
        <tbody>
        <tr>
            <td colspan="2" class="title-th even"
                align="center"><?php echo $model->isNewRecord ? Yii::t('appVersion', '添加客户端') : Yii::t('appVersion', '修改客户端'); ?></td>
        </tr>
        </tbody>
        <tbody>
        <tr>
            <th style="width: 220px" class="odd">
                <?php echo $form->labelEx($model, 'name'); ?>
            </th>
            <td class="odd">
                <?php echo $form->textField($model, 'name', array('class' => 'text-input-bj  middle')); ?>
                <?php echo $form->error($model, 'name'); ?>
            </td>
        </tr>
        <tr>
            <th class="even">
                <?php echo $form->labelEx($model, 'version'); ?>
            </th>
            <td class="even">
                <?php echo $form->textField($model, 'version', array('class' => 'text-input-bj  middle')); ?>
                <?php echo $form->error($model, 'version'); ?>
            </td>
        </tr>
        <tr>
            <th class="odd">
                <?php echo $form->labelEx($model, 'version_name'); ?>
            </th>
            <td class="odd">
                <?php echo $form->textField($model, 'version_name', array('class' => 'text-input-bj  middle')); ?>
                <?php echo $form->error($model, 'version_name'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 220px" class="even">
                <?php echo $form->labelEx($model, 'system_type'); ?>
            </th>
            <?php
            /**
             * 增加、编辑app的表单页面去掉获取的“其他”类型
             * @param unknown $array
             * @return multitype:unknown
             */
            function getType_form($array)
            {
                $system = array();
                foreach ($array as $key => $value) {
                    if ($key != 0) {
                        $system[$key] = $value;
                    }
                }
                return $system;
            }

            ?>
            <td class="even">
                <?php echo $form->dropDownList($model, 'system_type', getType_form(AppVersion::getSystemType()), array('prompt' => '请选择', 'class' => 'text-input-bj  middle')); ?>
                <?php echo $form->error($model, 'system_type'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 220px" class="even">
                <?php echo $form->labelEx($model, 'type'); ?>
            </th>
            <td class="even">
                <?php echo $form->dropDownList($model, 'type', getType_form(AppVersion::getFlag()), array('prompt' => '请选择', 'class' => 'text-input-bj  middle')); ?>
                <?php echo $form->error($model, 'type'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 220px" class="even">
                <?php echo $form->labelEx($model, 'app_type'); ?>
            </th>
            <td class="even">
                <?php if ($model->type == AppVersion::FLAG_TYPE_SOFTWARE): ?>
                    <?php echo $form->dropDownList($model, 'app_type', getType_form(AppVersion::getAppList(AppVersion::FLAG_TYPE_SOFTWARE)), array('prompt' => '请选择', 'class' => 'text-input-bj  middle')); ?>
                    <?php echo $form->error($model, 'app_type'); ?>
                <?php elseif ($model->type == AppVersion::FLAG_TYPE_GAME): ?>
                    <?php echo $form->dropDownList($model, 'app_type', getType_form(AppVersion::getAppList(AppVersion::FLAG_TYPE_GAME)), array('prompt' => '请选择', 'class' => 'text-input-bj  middle')); ?>
                    <?php echo $form->error($model, 'app_type'); ?>
                <?php else: ?>
                    <?php echo $form->dropDownList($model, 'app_type', getType_form(AppVersion::getAppList()), array('prompt' => '请选择', 'class' => 'text-input-bj  middle')); ?>
                    <?php echo $form->error($model, 'app_type'); ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th class="odd">
                <?php echo $form->labelEx($model, 'mobile_log'); ?>
            </th>
            <td class="odd">
                <?php echo $form->textArea($model, 'mobile_log', array('class' => 'text-input-bj  middle')); ?>
                <?php echo $form->error($model, 'mobile_log'); ?>
            </td>
        </tr>
        <tr>
            <th class="even">
                <?php echo $form->labelEx($model, 'web_log'); ?>
            </th>
            <td class="even">
                <?php echo $form->textArea($model, 'web_log', array('class' => 'text-input-bj  middle')); ?>
                <?php echo $form->error($model, 'web_log'); ?>
            </td>
        </tr>
        <tr>
            <th class="odd">
                <?php echo $form->labelEx($model, 'remark'); ?>
            </th>
            <td class="odd">
                <?php echo $form->textArea($model, 'remark', array('class' => 'text-input-bj  middle')); ?>
                <?php echo $form->error($model, 'remark'); ?>
            </td>
        </tr>
        <tr>
            <th class="odd">
                <?php echo $form->labelEx($model, 'is_visible'); ?>
            </th>
            <td class="odd">
                <?php echo $form->radioButtonList($model, 'is_visible', AppVersion::getVisible(), array('separator' => '')); ?>
                <?php echo $form->error($model, 'is_visible'); ?>
            </td>
        </tr>
        <tr>
            <th class="odd">
                <?php echo $form->labelEx($model, 'is_published'); ?>
            </th>
            <td class="odd">
                <?php echo $form->radioButtonList($model, 'is_published', AppVersion::getPublished(), array('separator' => '')); ?>
                <?php echo $form->error($model, 'is_published'); ?>
            </td>
        </tr>
        <tr id="android">
            <th class="odd">
                <?php echo $form->labelEx($model, '请上传APK'); ?>
            </th>
            <td class="odd">
                <?php echo $form->fileField($model, 'apk_name') ?>
                <?php echo $form->error($model, 'apk_name'); ?>
                <span style="color: Red;">* </span><?php echo Yii::t('appVersion', '请上传APK'); ?>
                <?php if ($model->apk_name): ?>
                    <?php echo CHtml::hiddenField('oldFile', $model->apk_name); ?>
                    <?php echo ATTR_DOMAIN . '/' . $model->apk_name; ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr id="ios">
            <th class="odd" rowspan='2'>
                <?php echo $form->dropDownList($model, 'ios_select', AppVersion::getIosSelect(), array('class' => 'text-input-bj  middle')); ?>
            </th>
            <td class="odd" id="IOS_SELECT_URL">
                <?php echo $form->textField($model, 'url', array('class' => 'text-input-bj  middle')) ?>
                <?php echo $form->error($model, 'url'); ?>
                <span style="color: Red;">* </span><?php echo Yii::t('appVersion', '请输入IOS版安装文件连接'); ?>
            </td>
            <td class="odd" id="IOS_SELECT_IPD">
                <?php echo $form->fileField($model, 'ios_ipd') ?>
                <?php echo $form->error($model, 'ios_ipd'); ?>
                <span style="color: Red;">* </span><?php echo Yii::t('appVersion', '请上传IPA安装文件'); ?>
                <?php if ($model->ios_ipd): ?>
                    <?php echo CHtml::hiddenField('oldIOSFile', $model->ios_ipd); ?>
                    <?php echo ATTR_DOMAIN . '/' . $model->ios_ipd; ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td class="even" id="IOS_SELECT_IPD_IMG">
                <?php echo $form->fileField($model, 'ios_img_url') ?>
                <?php echo $form->error($model, 'ios_img_url'); ?>
                <span style="color: Red;">* </span><?php echo Yii::t('appVersion', '请上传Display Image图片'); ?>
                <?php if ($model->img_url): ?>
                    <?php echo CHtml::hiddenField('oldIOSImg', $model->ios_img_url); ?>
                    <?php echo ATTR_DOMAIN . '/' . $model->ios_img_url; ?>
                    <?php if ($this->action->id == 'update'): ?>
                        <img alt="" width="80" height="80" src="<?php echo ATTR_DOMAIN . '/' . $model->ios_img_url ?>">
                    <?php endif; ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th class="odd">
                <?php echo $form->labelEx($model, 'img_url'); ?>
            </th>
            <td class="odd">
                <?php echo $form->fileField($model, 'img_url') ?>
                <?php echo $form->error($model, 'img_url'); ?>
                <span style="color: Red;">* </span><span id="imageUpload"><?php echo Yii::t('appVersion', '请上传图片'); ?></span>
                <?php if ($model->img_url): ?>
                    <?php echo CHtml::hiddenField('oldImg', $model->img_url); ?>
                    <?php echo ATTR_DOMAIN . '/' . $model->img_url; ?>
                    <?php if ($this->action->id == 'update'): ?>
                        <img alt="" width="80" height="80" src="<?php echo ATTR_DOMAIN . '/' . $model->img_url ?>">
                    <?php endif; ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th class="odd">
                <?php echo $form->labelEx($model, 'is_auto_download'); ?>
            </th>
            <td class="odd">
                <?php echo $form->radioButtonList($model, 'is_auto_download', AppVersion::getAutoDownload(), array('separator' => '')); ?>
                <?php echo $form->error($model, 'is_auto_download'); ?>
            </td>
        </tr>
        <tr>
            <th class="odd"></th>
            <td colspan="2" class="odd">
                <?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('appVersion', '新增') : Yii::t('appVersion', '保存'), array('class' => 'reg-sub')); ?>
            </td>
        </tr>
        </tbody>
    </table>
<?php $this->endWidget(); ?>
    <script type="text/javascript">

        /**
         * 选择系统类型  Android/IOS
         */
        $("#AppVersion_system_type").change(function () {
            var a = $(AppVersion_system_type).val();
            if (a == <?php echo AppVersion::SYSTEM_TYPE_ANDROID?>) {
                $('#android').show();
                $('#ios').hide();
                $('imageUpload').text('请上传图片');
                $('#IOS_SELECT_IPD_IMG').hide();
            }
            else if (a == <?php echo AppVersion::SYSTEM_TYPE_IOS?>) {
                $('#android').hide();
                $('#ios').show();
            } else;
        });

        /**
         * IOS安装文件选择从后台上传/填写appStore URL
         */
        $('#AppVersion_ios_select').change(function(){
            var IOSSelectVal = $('#AppVersion_ios_select').val();
            if (IOSSelectVal == <?php echo AppVersion::IOS_SELECT_URL?>){
                $('#IOS_SELECT_URL').show();
                $('#IOS_SELECT_IPD').hide();
                $('#IOS_SELECT_IPD_IMG').hide();
                $('#imageUpload').text('请上传图片');
            }else if(IOSSelectVal == <?php echo AppVersion::IOS_SELECT_IPD?>){
                $('#IOS_SELECT_URL').hide();
                $('#IOS_SELECT_IPD').show();
                $('#IOS_SELECT_IPD_IMG').show();
                $('#imageUpload').text('请上传Full Size Image图片');
            }
        });

        /**
         * 选择标识  软件/游戏
         */
        $("#AppVersion_type").change(function () {
            var type = $('#AppVersion_type').val();
            var url = "<?php echo $this->createAbsoluteUrl('/appVersion/list') ?>";

            $.post(url, {YII_CSRF_TOKEN: '<?php echo Yii::app()->request->csrfToken ?>', type: type}, function (msg) {
                var obj = $.parseJSON(msg);
                var myData = [];       //定义一个数组变量
                $.each(obj, function (key, value) {
                    myData[key] = value;
                });
                var m = myData.length;
                var options = "<option value=''>请选择</option>";
                for (var i = 0; i < m; i++) {
                    if(myData[i] !== undefined){
                        options += '<option value=' + i + '>' + myData[i] + '</option>';
                    }
                }
                $('#AppVersion_app_type').html(options);
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            toggleTr($('input[name="AppVersion[direction]"]:checked').val());
        });
        function toggleTr(value) {
            $("#cityTr").hide();
            $("#categoryTr").hide();
            if (value === '1') {
                $("#cityTr").show();
            } else if (value === '2') {
                $("#categoryTr").show();
            }
        }
    </script>
    <script src="/js/iframeTools.js" type="text/javascript"></script>
<?php
Yii::app()->clientScript->registerScript('categoryTree', "
var dialog = null;
jQuery(function($) {
        var url = '" . $this->createUrl('/category/categoryTree') . "';
        $('#getTree').click(function() {
            dialog = art.dialog.open(url, {'id': 'SearchCat', title: '搜索类别', width: '640px', height: '600px', lock: true});
        })
})
var onSelectedCat = function(Id, Name) {
    $('#AppVersion_category_id').val(Id);
    $('#category_name').val(Name);
};
var doClose = function() {
    if (null != dialog) {
        dialog.close();
    }
};
", CClientScript::POS_HEAD);
?>