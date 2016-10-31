<?php
/**
 * 日期时间选择js 控件
 * @example 
   $this->widget('comext.timepicker.timepicker', array(
                'model' => $model,
                'name' => 'creat_time',
                'options' => array(
                   // 'value' => '2013-10-45 12:12:10',
                ),
            ));
 * 
 * default options:
 * array(
            'dateFormat'=>'yy-mm-dd',
            'timeFormat'=>'hh:mm:ss',
            'showOn'=>'focus',
            'showSecond'=>true,
            'changeMonth'=>true,
            'changeYear'=>true,
            'value'=>'',
            'tabularLevel'=>null,
    );
 * @see http://www.yiiframework.com/extension/timepicker
 */
Yii::import('zii.widgets.jui.CJuiInputWidget');
class timepicker extends CJuiInputWidget {

	public $assets = '';
	public $options = array();
	public $skin = 'default';
	
	public $model;
	public $name;
	public $language='';
	public $select = 'datetime'; # also avail 'time' and 'date'
	public $cssClass = 'datefield text-input-bj middle';

	public function init() {
		$this->assets = Yii::app()->assetManager->publish(dirname(__FILE__).DIRECTORY_SEPARATOR.'assets');
		
		Yii::app()->clientScript
		->registerCoreScript( 'jquery' )
		->registerCoreScript( 'jquery.ui' )
		
        
		->registerScriptFile( $this->assets.'/js/jquery.ui.timepicker.js' )

		->registerCssFile( $this->assets.'/css/timepicker.css' );
		
		//language support
		if (empty($this->language))
			$this->language = Yii::app()->language;
 
		if(!empty($this->language)){
			$path = dirname(__FILE__).DIRECTORY_SEPARATOR.'assets';
			$langFile = '/js/jquery.ui.timepicker.'.$this->language.'.js';

			if (is_file($path.DIRECTORY_SEPARATOR.$langFile))
				Yii::app()->clientScript->registerScriptFile($this->assets.$langFile);
		}
                $cs=Yii::app()->getClientScript();
                Yii::app()->clientScript->registerScriptFile($cs->getCoreScriptUrl().'/jui/js/jquery-ui-i18n.min.js');
		$default = array(
			'dateFormat'=>'yy-mm-dd',
			'timeFormat'=>'hh:mm:ss',
			'showOn'=>'focus',
			'showSecond'=>true,
			'changeMonth'=>true,
			'changeYear'=>true,
			'value'=>'',
			'tabularLevel'=>null,
            'yearSuffix'=>'',
		);

		$this->options = array_merge($default, $this->options);
        
		$options=empty($this->options) ? '' : CJavaScript::encode($this->options);

		Yii::app()->getClientScript()->registerScript(__CLASS__.'#'.$this->id,"
			jQuery('#{$this->id}').".$this->select."picker($options);
		");

		parent::init();
	}

	public function run(){
		$this->render($this->skin,array('css'=>$this->cssClass));		
	}
}
?>