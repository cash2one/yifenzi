<?php

/**
 * 一份子用户前端控制器 
 * 登陆、注册等相关功能均为直接调用 盖象API 实现  
 * @author xuqiuye <qiuye.xu@g-mall.com>
 * @since 2019-04-21
 */
class MemberController extends YfzController
{

    public $bodyClass = 'register'; //body class 每个body都有个不同的class，定义个变量
    //public $layout = 'member';

//    public function beforeAction($action)
//    {
//        parent::beforeAction($action);
//    }
//    public function actionIndex()
//    {
//        var_dump(Yii::app()->user->getState(WeixinMember::MEMBER_OPENID));exit;
////        var_dump(Yii::app()->user->getState('partnerLoginKey'));
//        echo '用户中心';
//    }

    public function actions()
    {
        return array(
            'captcha' => array(
                'class' => 'CaptchaAction',
                'height' => '30',
                'width' => '70',
                'minLength' => 4,
                'maxLength' => 4,
                'offset' => 3,
                'testLimit' => 30,
            ),
            'captcha2' => array(
                'class' => 'CaptchaAction',
                'height' => '30',
                'width' => '70',
                'minLength' => 4,
                'maxLength' => 4,
                'offset' => 3,
                'testLimit' => 0,
            ),
        );
    }

    /**
     * 用户注册页面
     */
    public function actionRegister()
    {
        if (Yii::app()->user->checkLogin())
            Yii::app()->user->logout();
        $this->pageTitle = '一份子 注册';
		$this->layout = 'register';
        $model = new Member('yifenRegister');
        $this->performAjaxValidation($model);
        if (isset($_POST['Member']) && !empty($_POST['Member'])) {
            $model->attributes = Yii::app()->request->getParam('Member');
            $model->sku_number = $model->generateNumber();
            $model->register_type = Member::REGISTER_TYPE_GW_SYNC;
            $model->register_time = time();
            if ($model->validate()) {
                if($model->autoLogin()){
                    Yii::app()->user->setState($model->mobile,''); //验证不正确，销毁验证码，要求重新获取
                    $this->redirect('/user/index'); //注册完成自动登录
                } else {
                    $this->redirect('/member/login'); //自动登录失败跳到登陆也
                }
            }
        }
        $this->render('register', array('model' => $model));
    }

    /**
     * 通过手机验证进行同步数据到SKu中
     */
    public function actionMobilesync(){
        if(Yii::app()->request->isAjaxRequest){
            $mobile = Yii::app()->request->getPost('mobile');
            $bool = Fun::validateMobile($mobile);

            if ($bool == true){
                $model = new Member();
                if ($model->autoMobile($mobile)){

                }
            }
        }
        Yii::app()->end();
    }
    /**
     * 用户登陆页面
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $this->performAjaxValidation($model);
        $this->layout = 'login';
        $this->bodyClass = 'login';
        $this->pageTitle = '一份子 登陆';
        $this->footerPage = 5;
        if (isset($_POST['LoginForm']) && !empty($_POST['LoginForm'])) {
            $model->attributes = Yii::app()->request->getPost('LoginForm');
            if($model->processLogin()){//用户名密码验证通过
                if(isset($_POST['LoginForm']['verifyCode'])) {
                    if(!empty($_POST['LoginForm']['verifyCode'])) {//验证码不为空时
                        if ($this->createAction('captcha')->validate($model->verifyCode, false)) {//如果验证码验证通过
                            $this->redirect('/user/index');
                        } else {//验证码验证不通过
                            $model->addError('verifyCode', '验证码不正确.');
                        }
                    }else{//验证码不填时
                        $model->addError('verifyCode', '不能为空');
                    }
                }else{
                    $this->redirect('/user/index');
                }
            }
        }
        $model->unsetAttributes(array('password')); //消除上次默认登陆密码
        $this->render('login', array('model' => $model));
    }

    /**
     * ajax 获取验证码
     */
    public function actionGetCaptcha(){
        header('Content-Type:text/html;charset=utf-8');
        if(Yii::app()->request->isAjaxRequest){
            $captcha = Yii::app()->getController()->createAction("captcha2");
            $code = $captcha->verifyCode;
            echo $code;
        }
        Yii::app()->end();
    }

    /**
     * 找回密码
     */
    public function actionresetPassword()
    {
        $this->pageTitle = '一份子-忘记密码';
		$this->layout = 'findPassword';
        $step = 1;
        $model = new Member('findpw');
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'findpw-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        if (isset($_POST['Member'])) {
            if ($_POST['step'] == 1) {
                $model->attributes = $_POST['Member'];
                if ($model->validate()) {
                    $step = 2; //跳到修改密码页
                    $model->setScenario('changepw');
                }
            } else {
                $model->setScenario('changepw'); //修改验证场景
                $model->attributes = $_POST['Member'];
                if ($model->validate()) {
                    if ($model->resetPassword()) {
                        $this->setFlash('success', '密码修改成功，请登录!');
                        $this->redirect('/member/login');
                    } else {
                        $this->setFlash('error', '修改失败');
                        $this->redirect('/member/resetPassword');
                    }
                }
            }
        }
        //$this->performAjaxValidation($model);
        $this->render('findPassword', array('model' => $model, 'step' => $step));
    }

    /**
     * 获取手机验证码
     */
    public function actionGetVerifyCode()
    {
        if (!Yii::app()->request->isAjaxRequest) {
            exit;
        }
        $model = new Member('yifenVerify');
        /**
         * 如果是登录状态没有传递手机号码，则查询手机号
         */
        if (isset($_POST['mobile']) && !empty($_POST['mobile'])) {
            $mobile = $_POST['mobile'];
            $model->mobile = $mobile;
        } else {
            $model = $model->find(array('select' => 'mobile', 'condition' => 'id=' . $this->getUser()->id));
        }
        if (!$model->validate())
            exit($mobile);
        $verifyCodeCheck = $this->getSession($mobile);
        $apiMember = new ApiMember;
        if ($verifyCodeCheck) {
            $verifyArr = unserialize(Tool::authcode($verifyCodeCheck, 'DECODE'));
            if($verifyArr['overtime'] - time() > 2*60) {
                //在3分钟失效时间内，没有使用验证码，重新发送相同的验证码
                $content =  Tool::getConfigGW('smsmodel', 'phoneVerifyContent');
                $tmpId = Tool::getConfigGW('smsmodel', 'phoneVerifyContentId');
                $msg = str_replace('{0}', $verifyArr['code'],  $content);
                $apiMember->sendSms($mobile, $msg, ApiMember::SMS_TYPE_CAPTCHA, 0, true, array($verifyArr['code']), $tmpId);
                exit;
            }
        }

        //$verifyCode = '000000';
        $data = $apiMember->captcha($mobile);
        //验证码同时写cookie\session 防止丢失
        $this->setCookie($mobile, Tool::authcode(serialize($data), 'ENCODE', '', 60 * 5), 60 * 5);
        $this->setSession($mobile, Tool::authcode(serialize($data), 'ENCODE', '', 60 * 5));
        $this->setCookie('verifyCode_times', 0, 20); //过期时间
        $this->setSession('verifyCode_times', 0, 20); //过期时间

        if (!(Yii::app()->request->cookies[$mobile] && $data)) {
            echo Yii::t('memberHome', '操作失败,请重试');
        }
        Yii::app()->end();

    }

    /**
     * 退出登陆
     */
    public function actionLogout()
    {
        WeixinMember::DeleteProcessMember();
        $logout = Yii::app()->user->logout();
        $this->redirect('/member/login');
    }

    /**
     * 盖付通 模拟登陆
     */
    public function actionAppLogin()
    {
        $member = new Member();
        $accessToken = Yii::app()->request->getParam('accessToken');
		$type = Yii::app()->request->getParam('type');
		Yii::app()->session['type'] = $type;
        $loginInfo = $member->appLogin($accessToken); //返回登陆信息
        if ($loginInfo){
            Yii::app()->user->setState('infosource',"gw_gft");
            $this->redirect('/user');
        }

    }

    public function actionTestLogin()
    {
        $member = array(
            'tips' => '用户注册成功',
            'memberId' => '1666884',
            'memberInfo' =>
            array(
                'id' => '1666884',
                'gai_number' => 'GW74638411',
                'mobile' => '13751527133',
            ),
            'success' => true,
        );
        $model = new Member();
        $model->autoLogin($member);
    }

    //关于用户设置支付密码
    public function actionRestPayPass(){
        $this->pageTitle = '设置支付密码';
        $this->layout = 'restpaypass';
	  // $this->bodyClass = 'restpaypass';
		$step = 1;
		$member_id = Yii::app()->user->id;
        $model = Member::model()->findByPk($member_id);
        $model->setScenario('yifenpaypwss');
        //var_dump($member_id);exit;
       /* if(isset($_POST['ajax']) && $_POST['ajax']==='findpw-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
       */
        $this->performAjaxValidation($model);
        if ( isset($_POST['Member']) && !empty($_POST['Member'] ))
        {
			if ($_POST['step'] == 1) {
                $model->attributes = Yii::app()->request->getParam('Member');
                if ($model->validate()) {
                    $step = 2; //跳到设置支付密码页
                    $model->setScenario('yifenzipaypwss1');
                    Yii::app()->user->setState($model->mobile,''); //验证不正确，销毁验证码，要求重新获取
                }
            }else{
                $model = new Member();
				$model->setScenario('yifenzipaypwss1');
                $m_model  = $model->find(array("condition"=>"id={$member_id}","select"=>array('gai_number')));
                Member::syncPassword($m_model->gai_number);
                $m_model  = $model->find(array("condition"=>"id={$member_id}"));
               // if (!$m_model->validatePassword($_POST['Member']['password'])){
                 //   $this->setFlash('error', '密码错误');
              //  }else{
                    if ( $_POST['Member']['password3'] == $_POST['Member']['confirmpassword3'] ){
                        $password3 = $m_model->hashPassword($_POST['Member']['password3']);

                        $res = Yii::app()->gw->createCommand()->update('gw_member', array(
                            'password3'=>$password3,
                             ), 'gai_number=:gai_number', array(':gai_number'=>$m_model->gai_number));

                        //同步密码、支付密码、秘钥
                        if($res)
                        {
                            Member::syncPassword($m_model->gai_number);
                            $this->setFlash('yes',"操作成功");
                            if(isset($_GET['retUrl'])) $this->redirect($_GET['retUrl']);

                        }else{
                            $this->setFlash('error',"操作失败");
                        }
                    }
               // }
			}
        }
        $model->password3 = '';//清空默认支付密码
        $this->render('restpaypass', array('model'=>$model, 'step' => $step));
    }
    /*
     * 用户协议
     * */
    public function actionLookProtocol(){
        $model = new Member("yifenpaypwss");
        $this->footerDisplay = false;
        $this->render('lookprotocol', array('model'=>$model));
    }

}
