<?php
/**
 *一份子 个人中心控制器
 * deng
 * Date: 2016/4/15
 */
class UserController extends YfzController
{
    public $layout = 'user';
    public $footerPage = 5;
    public $footerDisplay = false;  //默认不显示底部导航
    public $topActionName;   //顶部操作的名称
    public $title;
    public $itemsPerPage = 10;  //下拉的界面每页显示的条数

    /*
     * 个人中心验证登录
     */
    public function beforeAction($action)
    {
        $this->checkLogin();
        return parent::beforeAction($action);
    }

    /*
     * 个人中心首页
     */
    public function actionIndex()
    {
        $this->parentPage = 'site/index';
        $this->footerDisplay = true;
        $this->pageTitle = Yii::t('user', '个人中心');
        $this->title = Yii::t('user', '个人中心');
        $id = Yii::app()->user->id;
        $model = Region::model();
        $data = Member::model()->findByPk($id);
        $accountBalance = AccountBalance::getTodayAmountByGaiNumber($data['sku_number']);

        $this->render('index', array('model' => $model, 'data' => $data,'accountBalance'=>$accountBalance));
    }
    /*
     * 购买记录
     */
    public function actionBuyRecord()
    {
        $this->parentPage = 'user/index';
        $this->pageTitle = Yii::t('user', '购买记录');
        $this->title = Yii::t('user', '购买记录');
        $this->footerDisplay = true;
        $model = new Member();
        $memberId = Yii::app()->user->id;
//         $data = $model->getMemberBuyRecord($memberId, 1, $this->itemsPerPage);
//         Tool::pr($data);die;
        if($this->isAjax())
        {
            $page = $this->getPost('page');
            $data = $model->getMemberBuyRecord($memberId, $page, $this->itemsPerPage);
            if($data) {
                exit(CJSON::encode(array('result' => true, 'data' => $data)));
            }else{
                exit(CJSON::encode(array('result' => false)));
            }
            Yii::app()->end();
        }

        $this->render('buyrecord');

    }
    /*
     * 个人购买详情
     */
    public function actionBugDetailEnd($id)
    {
        $this->parentPage = 'user/buyRecord';
        $this->pageTitle = Yii::t('user', '个人购买详情');
        $this->title = Yii::t('user', '个人购买详情');
        $this->footerDisplay = true;
        list($order_id,$goods_id) = explode('_', $id);

        if (!$order_id && !$goods_id)
            throw new CHttpException('404', '找不到商品');


        $sql = "SELECT
                	o.order_id,o.member_id as omember_id,go.goods_name,go.goods_number,go.winning_code,go.goods_price,go.goods_image,
                	go.single_price,go.goods_id,go.current_nper,ogn.status,ogn.sumlotterytime,ogn.winning_code as w_code,o.addtime,
                	ogn.member_id 
                FROM
                	{{order}} AS o
                LEFT JOIN {{order_goods}} AS go ON o.order_id = go.order_id
                LEFT JOIN {{order_goods_nper}} AS ogn ON go.order_id = ogn.order_id AND go.current_nper = ogn.current_nper
                WHERE
                	go.goods_id = $goods_id
                AND o.order_id = $order_id
                AND o.member_id = ".Yii::app()->user->id;
        $data = Yii::app()->gwpart->createCommand($sql)->queryRow();

        if (!$data)
            throw new CHttpException('404', '找不到商品');

        //获得当前订单商品数据时，同时检验此期是否有其它用户购买过。
//         $sql = "select winning_code from {{order_goods}} where goods_id={$data['goods_id']} and current_nper = {$data['current_nper']}";
//         $oldData = Yii::app()->gwpart->createCommand($sql)->queryAll(); 

//         if (!$oldData)
//             throw new CHttpException('404', '请联系管理人员');

        $winning_code = array();
        foreach (json_decode($data['winning_code']) as $key=>$val){
            array_push($winning_code, $val);
        }
        sort($winning_code);
        $data['winning_code'] = $winning_code;

        //时间
        list($year,$sec) = explode('.', $data['addtime']);
        $data['addtime'] = date("Y-m-d H:i:s",$year).'.'.$sec;

        //图片
        $data['goods_image'] = ATTR_DOMAIN . '/' . $data['goods_image'];

        if (!$data['status'] && !$data['sumlotterytime'] && !$data['member_id']){
            $sql = "select * from {{order_goods_nper}} where goods_id={$goods_id} and current_nper={$data['current_nper']}";
            $winningData = Yii::app()->gwpart->createCommand($sql)->queryRow();

            if ( $winningData ){
                $data['status'] = $winningData['status'];
                $data['sumlotterytime'] = date('Y-m-d H:i:s',$winningData['sumlotterytime']);
            }else{
                $data['count_nper'] = $data['goods_price'] / $data['single_price'];
                $yfzGoodsData = YfzGoods::model()->find(array("condition"=>"goods_id={$goods_id} and current_nper={$data['current_nper']}"));
                $yfzGoodsData = json_decode(CJSON::encode($yfzGoodsData),true);
                $data['percentage'] = (($data['count_nper'] - $yfzGoodsData['goods_number']) / $data['count_nper']) * 100;
                $data['inventory']  =   $yfzGoodsData['goods_number'];
            }
        }

        if ($data['status'] == YfzOrderGoodsNpers::STATUS_FALSE){

            //如果自己不是中奖用户那么要进行sql查询
            $sql = "select * from {{order_goods_nper}} where goods_id={$data['goods_id']} and current_nper = {$data['current_nper']}";
            $nperdata = Yii::app()->gwpart->createCommand($sql)->queryRow();

            $data['member_id'] = $nperdata['member_id'];
            $data['sumlotterytime'] = $nperdata['sumlotterytime'];
            $member = Member::getMemberInfo($data['member_id']);
            $data['username'] =  !empty($member['mobile']) ? substr_replace($member['mobile'],'****',3,4) : '';
            if($data['username'] === '') {
                $data['username'] = !empty($member['gai_number'])?substr_replace($member['gai_number'],'****',4,4):'';
            }
            $data['sumlotterytime'] = date('Y-m-d H:i:s',$data['sumlotterytime']);
            $sql = "select current_nper from {{yfzgoods}} where goods_id = $goods_id";

            $current_nper = Yii::app()->gwpart->createCommand($sql)->queryRow();
            $data['new_current_nper'] = $current_nper['current_nper'];
        }
        $this->render('bugdetailend',array('data'=>$data));
    }
    /*
     * 获得的奖品
     */
    public function actionGetProduct()
    {
        $this->parentPage = 'user/index';
        $this->pageTitle = Yii::t('user', '获得的奖品');
        $this->title = Yii::t('user', '获得的奖品');
        $this->footerDisplay = true;
        $model = new Member();
        $data = $model->getMemberGetProduct();
        if(!$data && $this->isAjax()){
            exit(json_encode(array('result'=>false)));
        }
        if($this->isAjax()){
            exit(json_encode(array('result'=>true,'data'=>$data)));
        }

        $this->render('getproduct',array('data'=>$data));
    }
    /*
     * 收获地址管理
     */
    public function actionAddressSet()
    {
        $this->parentPage = 'user/index';
        $this->pageTitle = Yii::t('user', '收货地址管理');
        $this->title = Yii::t('user', '收货地址管理');
        $model = Region::model();
        $memberId = Yii::app()->user->id;
        $data = Address::model()->findAllByAttributes(array('member_id' => $memberId));
        $this->render('addressset',array('model'=>$model,'data'=>$data));
    }
    /*
     * 添加地址
     */
    public function actionAddress()
    {
        $this->parentPage = 'user/addressSet';
        $this->topActionName = '保存';
        $model = new Address('create');
        $this->footerDisplay = true;
        $model->member_id =  Yii::app()->user->id;
        $this->performAjaxValidation($model,'address-form');
        if(isset($_POST['Address']))
        {
            $model->attributes = $this->getPost('Address');
            if ($model->save()) {
                $this->redirect('/user/addressSet');
            }
        }
        $this->render('address',array('model'=>$model));
    }

    /**
     * 编辑地址
     */
    public function actionAddressUpdate()
    {
        $this->parentPage = 'user/addressSet';
        $this->pageTitle = Yii::t('user', '收货地址');
        $this->title = Yii::t('user', '收货地址');
        //$this->topActionName = '修改';
        $id = $this->getQuery('id');
        $model = Address::model()->findByPk($id);
        $model->setScenario('create');
        $this->performAjaxValidation($model,'address-form');
        if(isset($_POST['Address']))
        {
            $model->attributes = $this->getPost('Address');
            if ($model->save()) {
                echo $this->redirect('/user/addressSet');
            }
        }
        $this->render('addressupdate',array('model'=>$model));
    }

    /**
     *删除地址
     **/
    public function actionAddressDel($id){
        $del = Address::model()->deleteByPk($id);
        if($del){
            $this->redirect(array('addressset'));
        }
    }

    /**
     *设置默认地址
     **/
    public function actionAddressSetting($id){
        $memberId = Yii::app()->user->id;
        Address::model()->updateAll(array('default'=>Address::DEFAULT_NO),'member_id = :memberId',array(':memberId'=>$memberId));
        Address::model()->updateByPk($id, array('default'=>Address::DEFAULT_IS));
        $this->redirect(array('addressset'));
    }

    /**
     *ajax处理地址
     */
    public function actionAddressHandel()
    {
        if($this->isAjax())
        {
            $id = $this->getPost('id');
            $action = $this->getPost('action');
            $memberId = Yii::app()->user->id;
            $return = 0;
            switch ($action)
            {
                case 'del':
                    Address::model()->deleteByPk($id);
                    $return = 1;
                    break;
                case 'set':
                    Address::model()->updateAll(array('default'=>Address::DEFAULT_NO),'member_id = :memberId',array(':memberId'=>$memberId));
                    Address::model()->updateByPk($id, array('default'=>Address::DEFAULT_IS));
                    break;
                default:
                    $return = 0;
            }
            echo $return;
        }
    }
}