<?php

/**
 * 后台管理控制器父类
 * @author leo8705
 */
class MController extends RController
{

    public $layout = 'main';
    public $menu = array();
    public $breadcrumbs = array();
    public $showExport = false;
    public $exportAction;
    public $flashSessionKey = 'm_flash_key';
    public $flashCachePath = 'm_flash_path';
    
    protected function beforeAction($action) {
    	//设置params
    	$this->_checkLogin($action);
    	return parent::beforeAction($action);
    }
    
    /**
     * 获取左边导航数据
     * @param string $type
     * @return array
     */
    public function getMenu($type)
    {
        $mens = include(Yii::getPathOfAlias('application') . DS . 'config' . DS . 'menu_m.php');
        return $mens[$type];
    }
    
    

    /**
     * 验证是否登录
     * @return boolean
     */
    private function _checkLogin($action) {
    	//swfupload 火狐浏览器不能传递session
    	if (isset($_POST["PHPSESSID"])) {
    		session_id($_POST["PHPSESSID"]);
    	} else if (isset($_GET["PHPSESSID"])) {
    		session_id($_GET["PHPSESSID"]);
    	}
    	if (!Yii::app()->user->isGuest)
    		return true;
    	$nonLogin = $this->params('noLogin');
    	if (!in_array($this->id . '/' . $action->id, $nonLogin)) {
    		 $this->redirect(Yii::app()->createAbsoluteUrl('/manage/site/login'));
    	}
    }
    
    /**
     * 设置cookie值
     * @param string $name
     * @param string $value
     * @param int $life
     */
    public function setCookie($name, $value = null, $life = 0) {
    	$cookie = new CHttpCookie($name, $value);
    	$cookie->expire = $life ? time() + $life : 0;
    	Yii::app()->request->cookies[$name] = $cookie;
    }
    
    /**
     * 读取cookie值
     * @param string $name
     * @return boolean|string
     */
    public function getCookie($name) {
    	$cookie = Yii::app()->request->cookies[$name];
    	if (empty($cookie))
    		return false;
    	return $cookie->value;
    }
    
    /**
     * 设置session值
     * @param string $key
     * @param string|array $value 如果$value为null表示注销session
     */
    public function setSession($key, $value = null) {
    	Yii::app()->user->setState($key, $value, null);
    }
    
    /**
     * 获取session值
     * @param string $key
     * @param string|array $defaultValue
     * @return string|array
     */
    public function getSession($key, $defaultValue = null) {
    	return Yii::app()->user->getState($key, $defaultValue);
    }
    
    /**
     * 获取flash值
     * @param string $key
     * @param string|array $defaultValue
     * @return string|array
     */
    public function getFlash($key, $defaultValue = null, $delete = true) {
    	$key .= $this->user->id;
    	$value = Tool::cache($this->flashCachePath)->get($key);
    	$value =  !empty($value)?$value:$defaultValue;
    	if ($delete==true) {
    		Tool::cache($this->flashCachePath)->set($key,null);
//     		unset($_SESSION[$this->flashSessionKey][$key]);
    	}
    	
    	return $value;
    }
    
    /**
     * 设置flash值
     * @param string $key
     * @param string|array $value
     */
    public function setFlash($key, $value = null) {
//     	$_SESSION[$this->flashSessionKey][$key] = $value;
    	$key .= $this->user->id;
    	Tool::cache($this->flashCachePath)->set($key,$value);
    }
    
    /**
     * 设置flash值
     * @param string $key
     * @param string|array $value
     */
    public function hasFlash($key) {
    	$key .= $this->user->id;
    	$value = Tool::cache($this->flashCachePath)->get($key);
    	return !empty($value)?true:false;
    }
    
    /**
     * 获取配置文件下的参数
     * @param string $field1
     * @param string $field2
     * @return string|array
     */
    public function params($field1, $field2 = null) {
    	return $field2 ? Yii::app()->params[$field1][$field2] : Yii::app()->params[$field1];
    }

    /**
     * 获取当前访问者的ip
     * @return string
     */
    public function clientIp() {
    	return Yii::app()->request->userHostAddress;
    }
    
    /**
     * 通用产生模型实例
     * @param int $id
     * @throws CHttpException
     * @return CModel
     */
    public function loadModel($id) {
    	$object = ucfirst($this->id);
    	$object = '$model=' . $object . '::model()->findByPk((int)' . $id . ');';
    	eval($object);
    	if ($model === null)
    		throw new CHttpException(404, '请求的页面不存在.');
    	return $model;
    }
    
    /**
     * 通用的ajax表单验证
     * @param array CModel $model
     * @return void
     */
    public function performAjaxValidation($model) {
    	if (isset($_POST['ajax']) && $_POST['ajax'] === $this->id . '-form') {
    		echo CActiveForm::validate($model);
    		Yii::app()->end();
    	}
    }
    
    /**
     * @return CFormatter  数据格式化
     */
    public function format() {
    	return Yii::app()->format;
    }
    
    /**
     * 获取应用用户实例
     * @return CWebUser
     */
    public function getUser() {
    	return Yii::app()->user;
    }
    
    /**
     * 获取post提交参数
     * @param string $name
     * @param string $defaultValue
     * @param boolean $filter
     * @return string|array
     */
    public function getPost($name, $defaultValue = null, $filter = true) {
        $data = Yii::app()->request->getPost($name, $defaultValue);
        if (!$filter)
            return $data;
        return $this->magicQuotes($data);
    }

    /**
     * 获取get提交参数
     * @param string $name
     * @param string $defaultValue
     * @param boolean $filter
     * @return string|array
     */
    public function getQuery($name, $defaultValue = null, $filter = true) {
        $data = Yii::app()->request->getQuery($name, $defaultValue);
        if (!$filter)
            return $data;
        return $this->magicQuotes($data);
    }

    /**
     * 获取post,get提交参数
     * @param string $name
     * @param string $defaultValue
     * @param boolea  $filter
     * @return string|array
     */
    public function getParam($name, $defaultValue = null, $filter = true) {
        $params = Yii::app()->request->getParam($name, $defaultValue);
        if (!$filter)
            return $params;
        return $this->magicQuotes($params);
    }
    

    /**
     * 转义数据
     * @param string|array $var
     * @return string|array
     */
    public function magicQuotes(&$var) {
    	if (!get_magic_quotes_gpc()) {
    		if (is_array($var)) {
    			foreach ($var as $k => $v)
    				$var[$k] = $this->magicQuotes($v);
    		}
    		else
    			$var = addslashes($var);
    	}
    	return $var;
    }
    
    /**
     * 反转义数据
     * @param $var
     * @return array|string
     */
    public function delSlashes(&$var) {
    	if (is_array($var)) {
    		foreach ($var as $k => $v) {
    			$var[$k] = $this->delSlashes($v);
    		}
    	} else {
    		$var = stripslashes($var);
    	}
    	return $var;
    }
    
    /**
     * 判断是否post请求
     * @return boolean
     */
    public function isPost() {
    	return Yii::app()->request->isPostRequest;
    }
    
    /**
     * 判断是否ajax请求
     * @return boolean
     */
    public function isAjax() {
    	return Yii::app()->request->isAjaxRequest;
    }
    /**
     * 检测重复提交
     * @param string $url 跳转地址
     * @throws CHttpException
     */
    public function checkPostRequest($url = null) {
        if ($this->isPost() && !$this->isAjax()) {
            $session = Yii::app()->session;
            $sessionKey = 'is_sending'; //action的路径
            //第一次点击确认按钮时执行
            if (!isset($session[$sessionKey])) {
                $session[$sessionKey] = time();
            } else {
                $first_submit_time = $session[$sessionKey];
                $current_time = time();
                $session[$sessionKey] = $current_time;
                if ($current_time - $first_submit_time < 5) {
                    if ($url) {
                        $this->redirect($url);
                    } else {
                        throw new CHttpException(400, '请不要频繁提交数据！');
                    }
                }
            }
        }
    }
    
    /**
     * 获取后台配置的常用参数数据
     * @param string $name  文件名称，例如site.config.inc,$name = 'site'
     * @param string $key 该配置项的键名
     * @return string
     */
    public function getConfig($name, $key = null) {
    	$val = Tool::cache($name . 'config')->get($name);
    	if ($val) {
    		$array = unserialize($val);
    	} else {
    		$value = WebConfig::model()->findByAttributes(array('name' => $name));
    		if ($value) {
    			Tool::cache($name . 'config')->add($name, $value->value);
    			$array = unserialize($value->value);
    		} else {
    			$file = Yii::getPathOfAlias('common') . DS . 'webConfig' . DS . $name . '.config.inc';
    			if (!file_exists($file)) {
    				return array();
    			}
    			$content = file_get_contents($file);
    			$array = unserialize(base64_decode($content));
    		}
    	}
    
    	return $key ? (isset($array[$key]) ? $array[$key] : '') : $array;
    }
    

}