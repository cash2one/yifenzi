<?php
/**
 * 改写 CHttpRequest，让部分 action 不需要 csrf 验证
 *
 * @author zhenjun_xu <412530435@qq.com>
 */

class CustomHttpRequest extends CHttpRequest
{
    public $noCsrfValidationRoutes = array();
    public $disableModules = array();

    protected function normalizeRequest()
    {
        //attach event handlers for CSRFin the parent
        parent::normalizeRequest();
        //remove the event handler CSRF if this is a route we want skipped
        if ($this->enableCsrfValidation) {
            $url = Yii::app()->getUrlManager()->parseUrl($this);
            $route_arr = explode('/', $url);
            
            if (count($route_arr)>2 && in_array($route_arr[0],$this->disableModules)) {
            	Yii::app()->detachEventHandler('onBeginRequest', array($this, 'validateCsrfToken'));
            }
            
            foreach ($this->noCsrfValidationRoutes as $route) {
                if (strpos($url, $route) === 0)
                    Yii::app()->detachEventHandler('onBeginRequest', array($this, 'validateCsrfToken'));
            }
        }
    }
}

