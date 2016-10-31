<?php
Yii::import ( 'zii.widgets.CWidget' );
class CUploadPicFull extends CWidget {
	public $num = 1;					//允许选中的图片的个数
	public $upload_height = 0;			//规定上传图片的高度
	public $upload_width = 0;			//规定上传图片的宽度
	public $model = NULL;				//模型对象
	public $attribute = "";				//模型属性
	public $img_area = 1;				//图片域名（文件图片:1	附件图片:2）
	public $form = NULL;				//表单类
	public $btn_class = "regm-sub";		//按钮样式
	public $btn_value = "设置列表图片";	//按钮文字 
	public $explode_str = "|";			//分隔符
	public $img_format = "";			//图片格式（默认为空，表示所有正常图片格式）
	public $folder_name = "";			//文件夹名称			只要名称即可，不需要前后加上/
	public $isdate = 1;					//是否更加日期创建文件夹
	
	public function run() {
                
            switch ($this->img_area){
            case 1:
                $area = IMG_DOMAIN;
                break;
            case 2:
                $area = ATTR_DOMAIN;
                break;
            }
		$this->render('uploadFull',array(
			'num'=>$this->num,
			'height'=>$this->upload_height,
			'width'=>$this->upload_width,
			'model'=>$this->model,
			'attribute'=>$this->attribute,
			'img_area'=>$this->img_area,
			'form'=>$this->form,
			'btn_class'=>$this->btn_class,
			'btn_value'=>$this->btn_value,
			'explode_str'=>$this->explode_str,
			'img_format'=>$this->img_format,
			'folder_name'=>$this->folder_name,
                        'imgarea' => $area,
			'isdate'=>$this->isdate,
		));
	}
}
