<?php

/**
 * openapi模块控制器总父类
 *
 * 用户端
 *
 * 参数使用RSA加密
 *
 * @author leo8705
 */
class COpenAPIController extends BaseController {
    public $member;					//当前用户id
    public $partner;					//当前商家

    protected $key_path;
    protected $rsaObj;

    protected $gaiNumber;
    protected $userInfo;
    protected $rsaKey = '';
    protected $primaryKey = 'token';
    protected $params;
    protected $member_cache_path = 'OPENAPI_CACHE_MEMBER';
    protected $partner_cache_path = 'OPENAPI_CACHE_PARTNER';

    protected $token;


    function beforeAction($action){

        //设置params
        $params = require(ConfigDir . DS . 'params_open.php');
        Yii::app()->setParams($params);
        parent::beforeAction($action);

        $this->_setRsa();
        $token_val = $this->getParam('token');

        $no_token = $this->params('noToken')?$this->params('noToken'):array();
        $this->params = array('token');


        if(in_array($this->id . '/' . $this->action->id, $no_token)){
            return true;
        }


        if (!empty($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
            $this->token = $token_val;
        }else{
            $this->token = $this->_decrypt($this->getParam('token'));
        }


        if (empty($token_val)) {
            $this->_error('token不能为空',ErrorCode::CLIENT_TOKEN_ERROR);
        }


        $token_info = OpenClientToken::getInfoByToken($this->token);
        if (empty($token_info)) {
            $this->_error('token无效',ErrorCode::CLIENT_TOKEN_ERROR);
        }
        $this->member = $token_info['member_id'];


        //设置rsa key
        $this->rsaObj->privateKey = $token_info['private_key'];
        $this->rsaKey = $token_info['private_key'];
//      var_dump($this->rsaKey);exit;

        $this->userInfo = Tool::cache($this->member_cache_path)->get($this->member);
        if (empty($this->userInfo)) {
            $mApi = new ApiMember();
            //$this->userInfo = $mApi->getInfo($this->member);
			$this->userInfo = Member::model()->findByPk($this->member);
            if (empty($this->userInfo)) {
                $this->_error('获取用户信息失败');
            }else{
                Tool::cache($this->member_cache_path)->set($this->member,$this->userInfo);
            }
        }

        $this->member = $this->userInfo['id'];


        if(isset($_POST['Language'])){
            $lang = $this->getParam('Language');
            switch($lang){
                case HtmlHelper::LANG_ZH_CN : $lang= 'zh_cn';break;
                case HtmlHelper::LANG_ZH_TW : $lang= 'zh_tw';break;
                case HtmlHelper::LANG_EN : $lang= 'en';break;
            }
            $sql = "UPDATE {{open_client_token}} SET lang = '$lang' WHERE token ='".$this->token."'";
            Yii::app()->db->createCommand($sql)->execute();
        }else{
            $result = Yii::app()->db->createCommand()
                ->select('lang')
                ->from('{{open_client_token}}')
                ->where('token = :token ', array(':token' => $this->token))
                ->queryRow();
            if(empty($result['lang'])){
                $lang = HtmlHelper::LANG_ZH_CN;
                switch($lang){
                    case HtmlHelper::LANG_ZH_CN : $lang= 'zh_cn';break;
                    case HtmlHelper::LANG_ZH_TW : $lang= 'zh_tw';break;
                    case HtmlHelper::LANG_EN : $lang= 'en';break;
                }
            }else{
                $lang = $result['lang'];
            }
        }
        Yii::app()->language = $lang;
        return true;
    }

    protected function _setRsa(){
        $key_path = Yii::getPathOfAlias('keyPath') . DS . 'rsa_openapi_private_key.pem';
        $fp = fopen($key_path, "r");
        $this->rsaKey  = fread($fp, 8192);
        fclose($fp);

        $public_key_path = Yii::getPathOfAlias('keyPath') . DS . 'rsa_openapi_public_key.pem';
        $fp = fopen($key_path, "r");
        $public_rsaKey  = fread($fp, 8192);
        fclose($fp);

        $this->rsaObj = new RSA();
        $this->rsaObj->privateKey = $this->rsaKey;
        $this->rsaObj->publicKey = $public_rsaKey;
    }



    /**
     * 解密方法
     * @param array $request 请求的数据数组
     * @param array $requiredFields 必填字段数组
     * @param array $decryptFields 需解密字段数组
     * @return array
     * @throws Exception
     * @author wanyun.liu <wanyun_liu@163.com>
     */
    public function decrypt($request, $requiredFields = array(), $decryptFields = array())
    {
        $result = array();
        $rsa = new RSA();


        foreach ($this->params as $field) {

            if (isset($request[$field])) {
                if($field == $this->primaryKey && !empty($this->token)){
                    $result[$this->primaryKey] = $this->token;//解密完毕之后，将shopId重新赋值为之前已经解密好的值
                    continue;
                }
                // 解密字段值
                if ($decryptFields && in_array($field, $decryptFields)) {

                    $result[$field] = $this->_decrypt($request[$field]);
                    if ($result[$field] == null || $result[$field] == ''){
                        throw new Exception('数据解密失败');
                    }

                } else
                    $result[$field] = $request[$field];
            }elseif(in_array($field, $requiredFields)){
                throw new Exception($field.'是必填字段！');
            }
        }
        return $this->magicQuotes($result);
    }
    /**
     * 解密方法
     * @param string $value
     * @return string|null
     * @author wanyun.liu <wanyun_liu@163.com>
     */
    public function _decrypt($value) {
        $len = strlen($value);
        $string = pack("H" . $len, $value);
        $res = openssl_get_privatekey($this->rsaKey);
        openssl_private_decrypt($string, $result, $res);
        return $result;
    }

    /**
     * 访问接口,以post方式传递参数
     * @param $host            域名
     * @param $url            地址
     * @param $data            数据
     * @param $type            传递方式
     * return string
     */
    public function visitHttp($host, $url, $data = array(), $type = 'post')
    {
        $http = new HttpClient($host);
        return $http::quickPost($url, $data);
    }


    /**
     * 将对象类型里面的字符串强制转化为字符串
     */
    public function _objtostring($string)
    {
        return (String)$string;
    }

    /**
     * 写APP日志
     * @param 需要转的数据
     */
    public function _writeLog($msg, $step = '')
    {
        $step = $step == '' ? '' : '-' . $step;
        $msg = json_encode($msg);
        @Yii::log($msg, 'info', $this->getId() . '/' . $this->action->getId() . $step);
    }


    /**
     * 运行成功返回json
     * @param type $data
     * $resultDesc type 说明
     * $actionType type $data
     *
     */
    protected function _success($data,$actionType='',$resultDesc='成功')
    {
        header("Content-type:text/html;charset=utf-8");
        // 		$array['result'] = $data;
        // 		$array['resultCode'] = 1;

        $array = array();
        $array['actionType'] = $actionType;
        $array['Response']['resultDesc'] = $resultDesc;
        if($data!=null){
            $array['Response']['resultData'] = $data;
        }
        $array['Response']['resultCode'] = 1;

        echo CJSON::encode($array);
        Yii::app()->end();
    }


    /**
     * 运行错误返回json
     * @param type $error
     */
    protected function _error($data,$code=null,$actionType='',$resultDesc='失败')
    {
        header("Content-type:text/html;charset=utf-8");
        $array = array();
        $array['actionType'] = $actionType;
        $array['Response']['resultDesc'] = is_string($data)?$data:$resultDesc;
        $array['Response']['resultData'] = is_string($data)?null:$data;
        $array['Response']['resultCode'] = !empty($code)?$code:ErrorCode::COMMOM_ERROR;

        echo CJSON::encode($array);
        Yii::app()->end();
    }

    public static function getStoreClass($stype){
        $class='';
        switch ($stype){
            case Stores::SUPERMARKETS:
                $class = 'Supermarkets';
                break;
            case Stores::MACHINE:
                $class = 'VendingMachine';
                break;
            case Stores::FRESH_MACHINE:
                $class = 'FreshMachine';
                break;
        }

        return $class;

    }

    public static function getStoreGoodsClass($stype){
        $class='';
        switch ($stype){
            case Stores::SUPERMARKETS:
                $class = 'SupermarketsGoods';
                break;
            case Stores::MACHINE:
                $class = 'VendingMachineGoods';
                break;
            case Stores::FRESH_MACHINE:
                $class = 'FreshMachineGoods';
                break;
        }

        return $class;
    }

    public static function getStoreProjectId($stype){
        $project_id='';
        switch ($stype){
            case Stores::SUPERMARKETS:
                $project_id =API_PARTNER_SUPER_MODULES_PROJECT_ID;
                break;
            case Stores::MACHINE:
                $project_id =API_PARTNER_VENDING_MACHINE_MODULES_PROJECT_ID;
                break;
            case Stores::FRESH_MACHINE:
                $project_id =API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID;
                break;
        }

        return $project_id;
    }
    /*
 * 权限检查
 */
    public function _chenck($memberId){
        if($memberId != $this->member)
            $this->_error(Yii::t('api','无权操作'),ErrorCode::CLIENT_TOKEN_ERROR);
    }






}