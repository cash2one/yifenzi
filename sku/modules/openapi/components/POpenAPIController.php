<?php

/**
 * openapi模块控制器总父类
 *
 * 参数使用RSA加密
 *
 * @author leo8705
 */
class POpenAPIController extends BaseController {
    public $member;					//当前用户id
    public $partner;					//当前商家
    public $freshMachine;   //当前盖鲜机
    protected $key_path;
    protected $rsaObj;
    protected $gaiNumber;
    protected $userInfo;
    protected $partnerInfo;
    protected $rsaKey = '';
    protected $private_key;
    protected $primaryKey = 'token';
    protected $params;
    protected $member_cache_path = 'OPENAPICACHE_MEMBER';
    protected $partner_cache_path = 'OPENAPICACHE_PARTNER';
    protected $curr_super_key = 'curr_super_store_id';
    protected  $super_cache_path = 'OPENAPI_SUPER_CACHE';
    public $super_id;

    protected $token;
    protected $store;


	function beforeAction($action){

		//设置params
		$params = require(ConfigDir . DS . 'params_open.php');

		Yii::app()->setParams($params);
		parent::beforeAction($action);
        $this->_setRsa();
        $token_val = $this->getParam('token');

        $no_token = $this->params('noTokenPartner')?$this->params('noTokenPartner'):array();

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

        $token_info = OpenPartnerToken::getInfoByToken($this->token);

        if (empty($token_info)) {
            $this->_error('token 无效',ErrorCode::CLIENT_NO_MEMBER);
        }
        $this->member = $token_info['member_id'];
        //设置rsa key
        $this->rsaObj->privateKey = $token_info['private_key'];
        $this->rsaKey = $token_info['private_key'];

        if (!empty($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
            $this->super_id = $this->getParam('sid');
        }else{

            $this->super_id = $this->_decrypt($this->getParam('sid'));
        }
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

        $this->partnerInfo = Tool::cache($this->partner_cache_path)->get($this->member);
//        var_dump($this->partnerInfo);exit;
        if (empty($this->partnerInfo)) {
            $this->partnerInfo = Partners::model()->find('member_id=:member_id',array(':member_id'=>$this->member));
            if (empty($this->partnerInfo)) {
                $this->_error('非合作商家');

                if ($this->partnerInfo['status']!=Partners::STATUS_ENABLE) {
                    $this->_error('合作商家正在审核或已禁用！',ErrorCode::PARTNER_ACCOUNT_UNPASS);
                }

            }else{
                Tool::cache($this->partner_cache_path)->set($this->member,$this->partnerInfo);
            }
        }else if($this->partnerInfo['status']!=Partners::STATUS_ENABLE){
            $this->_error('合作商家正在审核或已禁用！',ErrorCode::PARTNER_ACCOUNT_UNPASS);
        }

        $this->member = $this->userInfo['id'];
        $this->partner = $this->partnerInfo['id'];

//        $this->super_id = Tool::cache($this->super_cache_path)->get($this->curr_super_key.$this->member);

        $unSelectActions = array('member/login','pStore/superCreate');

        if ( in_array($this->id . '/' . $this->action->id, $unSelectActions)) {
            return true;
        }

        if (empty($this->super_id)){


            $super = Supermarkets::getFirstSuperByMemberId($this->member);

            if (!empty($super)){
                $this->store = $super;
                if (empty($this->super_id)) $this->super_id = $super->id;
            }else{
                $this->_error('请先添加门店');
            }

        }else{
            $this->store = Supermarkets::model()->findByPk($this->super_id);			//查询门店


            $this->_checkAccess($this->store);						//检查权限

            if (empty($this->store)) {
                $this->_error('请先添加门店');
            }

        }
        if(isset($_POST['Language'])){
            $lang = $this->getParam('Language');
            switch($lang){
                case HtmlHelper::LANG_ZH_CN : $lang= 'zh_cn';break;
                case HtmlHelper::LANG_ZH_TW : $lang= 'zh_tw';break;
                case HtmlHelper::LANG_EN : $lang= 'en';break;
            }
            $sql = "UPDATE {{open_partner_token}} SET lang = '$lang' WHERE token ='".$this->token."'";
            Yii::app()->db->createCommand($sql)->execute();
        }else{
            $result = Yii::app()->db->createCommand()
                ->select('lang')
                ->from('{{open_partner_token}}')
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

//        $this->_getStore();

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


    protected function _getStore(){
        $sid =  $this->_decrypt($this->getParam('sid'))*1;
        if ($this->getParam('onlyTest')==1) {
            $sid = $this->getParam('sid')*1;
        }
        if (!empty($sid)) {
            $this->store = Supermarkets::model()->findByPk($sid);

            //检查店铺
            $this->_checkStore();
        }else{
            $this->store = new Supermarkets();
        }

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

    /**
     * 检查当前门店是否属于当前商家
     * @param unknown $model
     */
    protected function _checkAccess($store){

        if (empty($store->member_id) || $store->member_id != $this->member) {
            throw new CHttpException(403,'你没有权限修改别人的数据！');
        }
    }

    /**
     * 设置当前超市门店
     * @param unknown $id
     */
    protected function _setSuper($id){
        $this->_check($id);
        Tool::cache($this->super_cache_path)->set($this->curr_super_key.$this->member,$id);
        $this->super_id = Tool::cache($this->super_cache_path)->get($this->curr_super_key.$this->member);
    }



    /**
     * 检查操作的超市门店是否属于当前用户
     * Enter description here ...
     */
    protected function _check($super_id){
        if (empty($super_id)) return false;

        $members = Member::getAllMembers($this->member);
        $instr = ' member_id IN (0';
        foreach ($members as $m){
            $instr .= ','.$m->id;
        }

        $instr .= ') ';

        if( !Supermarkets::model()->count(" {$instr} AND id={$super_id}")){
            $this->_error(Yii::t('sellersuper', '没有权限！'));
            exit();
        }

    }


    protected function _checkStore(){
        if (empty($this->store['id'])) {
            $this->_error('门店不存在');
        }

        if ($this->store['member_id']!=$this->member) {
            $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
        }

        if ($this->store['status']!=Supermarkets::STATUS_ENABLE) {
            $this->_error('当前门店状态为禁用或者未审核，禁止使用。');
        }

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

    protected function _getFreshMachine(){
        $sid =  $this->_decrypt($this->getParam('fmid'))*1;

        if ($this->getParam('onlyTest')==1) {
            $sid = $this->getParam('fmid')*1;
        }
        if (!empty($sid)) {
            $this->freshMachine = FreshMachine::model()->findByPk($sid);

            //检查店铺
            $this->_checkFreshMachine();
        }else{
            $this->freshMachine = new FreshMachine();
        }

    }
    protected function _checkFreshMachine(){
        if (empty($this->freshMachine['id'])) {
            $this->_error('生鲜机不存在');
        }

        if ($this->freshMachine['member_id']!=$this->member) {
            $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
        }

        if ($this->freshMachine['status']!=FreshMachine::STATUS_ENABLE) {
            $this->_error('当前生鲜机状态为禁用或者未审核，禁止使用。');
        }

    }



}