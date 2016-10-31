<?php
/**
 * 游戏模块基础控制器类
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/8/14 17:33
 */
class GameBaseController extends BaseController
{
    protected $_gameKey = GAME_SECRET_KEY;

    /**
     * 接口返回
     * @param array $data
     */
    public function returnResult($resultCode,$data = null)
    {
        $response = array(
            'ResultCode' => $resultCode,
        );
        if($data) $response['msgData'] = $this->encrypt(json_encode($data));
        exit(json_encode($response));
    }

    /**
     * 解密方法--采用XXTEA进行加密和解密
     * @param unknown $data
     * @return array
     */
    public function decrypt($data)
    {
        $data = Xxtea::decrypt($data, $this->_gameKey);
        $postData = json_decode($data, true);
        return $postData;
    }

    /**
     * 加密方法--采用XXTEA进行加密和解密
     * @param unknown $data
     * @return unknown
     */
    public function encrypt($data)
    {
        $data = Xxtea::encrypt($data, $this->_gameKey);
        return $data;
    }
}