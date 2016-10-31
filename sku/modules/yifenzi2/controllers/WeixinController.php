<?php

/**
 * 微信登陆控制器
 * @author qiuye.xu<qiuye.xu@g-mall.com>
 * @since 2016-04-28
 */
class WeixinController extends YfzController
{

    const APPID = 'wx2d18dd88febcec82';
    const APPSECRET = 'b39168d5b185c454fe14e8a347d34c10';
    const WEBCAT_HOST_AUTH = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
    const WEBCAT_HOST_TOKEN = 'https://api.weixin.qq.com/sns/oauth2/access_token?';

    public function actionAppLogin()
    {
        $code = Yii::app()->request->getParam('code');
        if ($code) {
            $params = array(
                'appid' => self::APPID,
                'secret' => self::APPSECRET,
                'code' => $code,
                'grant_type' => 'authorization_code'
            );
            $url = self::WEBCAT_HOST_AUTH . http_build_query($params);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $back = curl_exec($ch);
            curl_close($ch);
            $userInfo = json_decode($url);
            if ($userInfo && isset($userInfo['openid'])) { //获取成功 登陆
                $model = new WeixinMember;
                $model = $model->find('openid=:oid', array(':oid' => $userInfo['openid']));
                if ($model) {
                    //如果该微信用户已经在商城注册 则默认登陆
                    $member = Member::model()->find(
                            array(
                                'select' => 'id,username,sku_number,gai_number',
                                'condition' => 'id=:id',
                                'params' => array(':id' => $model->member_id)
                            ));
                    if ($member) {
                        $attribute = $member->attributes;
                        Yii::app()->user->login($attribute['id'], $attribute);
                        $model->last_login_time = $model->login_time;
                        $model->login_time = time();
                        $model->save();
                    }
                } else {
                    Yii::app()->user->setState(WeixinMember::MEMBER_OPENID,$userInfo['openid']);
                }
            }
        }
        $this->redirect('/site');
    }

    /**
     * 微信登陆入口 //
     */
    public function actionIndex()
    {
        //拼接登陆链接
        $url = Yii::app()->createAbsoluteUrl('/yifenzi2/weixin/applogin');
        $params = array(
            'appid' => self::APPID,
            'redirect_uri' => $url,
            'response_type' => 'code',
            'scope' => 'snsapi_base',
            'state' => 1
        );
        $url = self::WEBCAT_HOST_AUTH . http_build_query($params);
        header('Location:' . $url);
    }

}
