<?php
/**
 * @author ling.wu <ling.wu@gatewang.com>
 * 继承 CActiveForm
 * 增加前台js防止 重复提交，仍然要在后台检查重复提交 (Controller 的方法 checkPostRequest())
 * 使用注意：id，enableAjaxValidation，enableClientValidation 必须要配置
 * 新增属性 $enableRepeatSubmit 开启防重复提交,默认true
 * 例：
 * $form = $this->beginWidget('ActiveForm', array(
        'id' => $this->id . '-form',
        'method' => 'post',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'enableRepeatSubmit' => true,  //开启检测
        'clientOptions' => array(
            'validateOnSubmit' => true, //客户端验证
            'shadeContent' => '正在提交，请稍等.....',   //遮罩显示提示内容,如果不为空，则显示遮罩
            ),
        ));
 *
 *      如果需要自定义 afterValidate ，并且要 验证重复提交，则在需用判断的地方，调用重复提交js(用{validJs})
 *       列：js:function(form, data, hasError){
 *                  if(!hasError){
                        {validJs}
                    }
                }";
 */

class ActiveForm extends CActiveForm {

    /**
     * @var bool
     * 开启防重复提交
     */
    public $enableRepeatSubmit = true;

    /**
     * 返回js变量名
     * @param $id
     * @return string
     */
    private function repeatSubmitVariable($id){
        $arr = explode('-',$id);
        foreach ($arr as $k=>$v) {
            $arr[$k] = ucfirst($v);
        }
        $id = implode($arr);
        $var = $id.'RepeatSubmit';
        return $var;
    }

    /**
     * 返回 检测 重复提交 js 代码
     * @param $id
     * @return string
     */
   private  function repeatSubmitJs($id){
        $repeatSubmitVarName = $this->repeatSubmitVariable($id);
        Yii::app()->clientScript->registerScript($repeatSubmitVarName,'var '.$repeatSubmitVarName.' = false;');     //注册js变量
        $shadeContent = null;
        if(isset($this->clientOptions['shadeContent']) && $this->clientOptions['shadeContent'] ){
            $shadeContent = "new Dialog('<div class=\"pay-tip\"><p>" .$this->clientOptions['shadeContent']."</p></div>',{showTitle:false}).show();";
        }
        $js = "js:function(form, data, hasError){
                if(!hasError){
                    {validJs}
                }
        }";
        $js = isset($this->clientOptions['afterValidate'])?$this->clientOptions['afterValidate']:$js;
        $validJs = "if($repeatSubmitVarName){
                        return false;
                    }else{
                        $repeatSubmitVarName = true;
                        $shadeContent
                        return true;
                    }";
        $js = str_replace('{validJs}',$validJs,$js);
        return $js;
   }

    /**
     * 重写父类run()方法
     */
    public function run()
    {
        if(isset($this->htmlOptions['id']) && $this->enableRepeatSubmit == true){
            $id = $this->htmlOptions['id'];
            // 为afterValidate 赋值
            $this->clientOptions['afterValidate'] = $this->repeatSubmitJs($id);
        }
        unset($this->clientOptions['shadeContent']);
        parent::run();
    }

} 