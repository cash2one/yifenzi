<?php
/**
 * 扩展 zii.widgets.jui.CJuiSliderInput
 * 重写 run() 方法
 */

Yii::import('zii.widgets.jui.CJuiSliderInput');

class SliderInput extends CJuiSliderInput
{
    /**
     * 显示滑块当前值
     * @var bool
     */
    public $showValue = true;

    public $jsfun;

	/**
	 * Run this widget.
	 * This method registers necessary javascript and renders the needed HTML code.
	 */
	public function run()
	{
		list($name,$id)=$this->resolveNameID();
		if(isset($this->htmlOptions['id']))
			$id=$this->htmlOptions['id'];
		else
			$this->htmlOptions['id']=$id;

		$isRange=isset($this->options['range']) && $this->options['range'] &&
			$this->options['range']!=='max' && $this->options['range']!=='min';

		if($this->hasModel())
		{
			$attribute=$this->attribute;
			if($isRange)
			{
				$options=$this->htmlOptions;
				echo CHtml::activeHiddenField($this->model,$this->attribute,$options);
				$options['id'].=$this->maxIdSuffix;
				echo CHtml::activeHiddenField($this->model,$this->maxAttribute,$options);
				$maxAttribute=$this->maxAttribute;
				$this->options['values']=array($this->model->$attribute,$this->model->$maxAttribute);
			}
			else
			{
				echo CHtml::activeHiddenField($this->model,$this->attribute,$this->htmlOptions);
				$this->options['value']=$this->model->$attribute;
			}
		}
		else
		{
			if($isRange)
			{
				list($maxName,$maxId)=$this->resolveNameID('maxName','maxAttribute');
				$options=$this->htmlOptions;
				echo CHtml::hiddenField($name,$this->value,$options);
				$options['id'].=$this->maxIdSuffix;
				echo CHtml::hiddenField($maxName,$this->maxValue,$options);
				$this->options['values']=array($this->value,$this->maxValue);
			}
			else
			{
				echo CHtml::hiddenField($name,$this->value,$this->htmlOptions);
				if($this->value!==null)
					$this->options['value']=$this->value;
			}
		}

		$idHidden=$this->htmlOptions['id'];
		$this->htmlOptions['id']=$idHidden.'_slider';
		echo CHtml::tag($this->tagName,$this->htmlOptions,'');

        if ($this->showValue === true) {
            echo CHtml::tag('span', array('id' => $id . '_value'), $this->options['value']);
        }

        $jsfun = !$this->jsfun ? '' : $this->jsfun;

		$this->options[$this->event]=$isRange
			? new CJavaScriptExpression("function(e,ui){ v=ui.values; jQuery('#{$idHidden}').val(v[0]); jQuery('#{$idHidden}{$this->maxIdSuffix}').val(v[1]); }")
			: new CJavaScriptExpression("function(event, ui) { jQuery('#{$idHidden}').val(ui.value); jQuery('#{$id}_value').html(ui.value); {$jsfun} }");

		$options=CJavaScript::encode($this->options);
		Yii::app()->getClientScript()->registerScript(__CLASS__.'#'.$id,"jQuery('#{$id}_slider').slider($options);");
	}
}
