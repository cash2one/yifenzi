<?php

/**
 * 单个文件上传表单，用excel 导入会员
 * @author zhenjun_xu <412530435@qq.com>
 */
class UploadForm extends CFormModel
{

    public $file;

    public function rules()
    {
        return array(
            array('file', 'required', 'on' => 'zip'),
            array('file', 'file', 'types' => array('zip'), 'on' => 'zip', 'allowEmpty' => true,'wrongType'=>'文件格式不正确，文件 "{file}" 无法被上传',),
        	array('file', 'required', 'on' => 'excel'),
        	array('file', 'file',  'on' => 'excel', 'allowEmpty' => true,'wrongType'=>'文件格式不正确，文件 "{file}" 无法被上传',),
        );
    }

    public function attributeLabels()
    {
        return array(
            'file' => '上传文件'
        );
    }
} 