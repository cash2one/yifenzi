<?php

/**
 */
class OrderapiBaseController extends BaseController {

    public $encryptCode;				//接收的校验码
    public $project;						//访问项目
    public $data;							//数据
    public $jsonData;					//json数据
    public $no_filter = array(
        'balance/index',
        'balance/sign',
        'balance/ab',
        'balance/cancelOrder',
    );

    function beforeAction($action){
        Yii::log('orderapi-log'.PHP_EOL.'url:'.$action->controller->id.'/'.$action->id.PHP_EOL.var_export($_POST,true));
        parent::beforeAction($action);
        if(in_array($action->controller->id.'/'.$action->id,$this->no_filter)){
            return true;
        }
        $this->project = $this->getParam('project');
        $this->jsonData = str_replace("\\\"", "\"",  $this->getParam('data'));//处理转义
        $this->data = CJSON::decode($this->jsonData);
        $this->encryptCode = $this->getParam('encryptCode');
        if ($this->getParam('onlyTest')==1) {
            //测试动作
        }else{
            $this->_checkEncryption(stripcslashes($this->jsonData));
        }


        return true;
    }


    /**
     * 接口参数的验证
     * @param array $requireParams 必填的参数
     * @return mixed
     */
    public function getValidateData($requireParams)
    {
        $data = ($_POST['data']);
        $sign = $this->getPost('sign');
        if (substr(md5($data . ORDER_API_SIGN_KEY), 5, 20) != $sign) {
//var_dump($data,ORDER_API_SIGN_KEY,substr(md5($data . ORDER_API_SIGN_KEY), 5, 20),$sign);exit;
            $response = array(
                'status' => 401,
                'msg' => 'Validation errors',
            );
            exit(json_encode($response));
        }

        $postData = json_decode($data, true);
        $arr = array_keys($postData);
        foreach ($requireParams as $param) {
            if (!in_array($param, $arr)) {
                $response = array(
                    'status' => 402,
                    'msg' => 'params errors',
                );
                exit(json_encode($response));
            }
        }
        return $postData;
    }

    public function requestSku($params,$url,$project = '105',$api = DOMAIN_API)
    {
        $json = json_encode($params);
        $private_key = $this->_getApiKeys('gw_project',$project);
        $code = md5($json.$private_key);//校验
        $url = $api.'/'.$url;
        $data = array(
            'project'=>$project,
            'data'=>$json,
            'encryptCode'=>$code
        );
        $ch = curl_init() ;
        curl_setopt($ch, CURLOPT_URL,$url) ;
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data); // 在HTTP中的“POST”操作。如果要传送一个文件，需要一个@开头的文件名
        ob_start();
        curl_exec($ch);
        $response = ob_get_contents() ;
        ob_end_clean();
        curl_close($ch) ;
        $res = json_decode($response,true);
        if($res == null)
            throw new Exception($response);
        return $res;
    }
    /**
     * 运行成功返回json
     * @param string|array $data
     */
    protected function _success($data)
    {
        header("Content-type:text/html;charset=utf-8");
        $array['result'] = $data;
        $array['resultCode'] = 1;
        echo CJSON::encode($array);
        Yii::app()->end();
    }


    /**
     * 运行错误返回json
     * @param $data
     * @param null $code
     */
    protected function _error($data,$code=null)
    {
        header("Content-type:text/html;charset=utf-8");
        $array = array('resultCode' =>  !empty($code)?$code:ErrorCode::COMMOM_ERROR);
        $array['resultDesc'] = $data;
        echo CJSON::encode($array);
        Yii::app()->end();
    }

    protected function _output($array)
    {
        header("Content-type:text/html;charset=utf-8");
        echo CJSON::encode($array);
        Yii::app()->end();
    }




    /**
     * 检验加密串
     *
     * 检验规则是各个参数按规定顺序排列，连成字符串，加上密文私钥，生成md5
     *
     */
    protected function _checkEncryption($json_data){
        if (empty($json_data)) {
            $this->_error('数据字段不能为空！',ErrorCode::COMMON_PARAMS_LESS);
        }
        $private_key = $this->_getPrivateKey($this->project);
        if ($this->encryptCode!==md5($json_data.$private_key)) {
            Yii::log($json_data);
            $this->_error('校验码错误！',ErrorCode::COMMOM_ENCRYPT_CODE_ERROR);
        }
    }


    /**
     * 获取项目秘钥
     *
     */
    protected function _getPrivateKey($project){
        $key = $this->_getApiKeys('gw_project',$project);
        return $key;
    }


}