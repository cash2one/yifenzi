<?php
/**
 * 盖付通接口控制器
 * 
 * @author leo8705
 *
 */

class CMemberController extends CAPIController {

	protected $verifyCodeCacheKey = 'CMemberVerifyCode';
	
	
    /*
     * 地址查询接口
     * memberId 会员id
     */
    public function actionAddressList() {
		$tag=$this->action->id;
        $cri = new CDbCriteria();
        $cri->select = 't.*';
        $cri->compare('member_id',$this->member);
        $cri->order = ' t.default DESC, t.id DESC';

        $list = Address::model()->findAll($cri);
        $list_arr = array();
        if(!empty($list)){
            foreach($list as $key => $v){
                $list_arr[$key] = $v->attributes;
                $list_arr[$key]['province_name'] = Region::getName($v['province_id']);
                $list_arr[$key]['city_name'] = Region::getName($v['city_id']);
                $list_arr[$key]['district_name'] = Region::getName($v['district_id']);
            }
            $this->_success($list_arr,$tag);
        }else{
            $this->_error(Yii::t('apiModule.member','你目前没有保存地址'),null,$tag);
        }

    }


    /*
     * 用户地址添加接口
     * data为加密过一维数组，元素如下
     * real_name  收货人姓名
     * mobile  手机号码
     * province_id  省份id
     * city_id  城市id
     * district_id  区/县id
     * street  详细地址
     * zip_code  邮编
     * default  默认地址（0否，1是)
     */
    public function actionAddressAdd() {
    	$tag=$this->action->id;
        $add = str_replace("\\\"", "\"",  $this->getParam('data'));
        $data = CJSON::decode($add);       
       if(empty($data['real_name'])){
           $this->_error(Yii::t('apiModule.member','收货人姓名不能为空'),null,$tag);
       }
       if(empty($data['mobile'])){
           $this->_error(Yii::t('apiModule.member','手机号码不能为空'),null,$tag);
       }
       if(empty($data['province_id'])){
           $this->_error(Yii::t('apiModule.member','省份不能为空'),null,$tag);
       }
       if(empty($data['city_id'])){
           $this->_error(Yii::t('apiModule.member','城市不能为空'),null,$tag);
       }
       if(empty($data['district_id'])){
           $this->_error(Yii::t('apiModule.member','区/县不能为空'),null,$tag);
       }       
       if(empty($data['street'])){
           $this->_error(Yii::t('apiModule.member','地址不能为空'),null,$tag);
       }
        $data['member_id'] = $this->member;
        $model = new Address;
        $model->attributes = $data;

        if($model->save()){
            if($model->default == Address::DEFAULT_IS){
                Address::model()->updateAll(array('default'=>Address::DEFAULT_NO),"member_id = :member_id AND id <> :id",array(':member_id'=>$this->member,':id'=>$model->id));
            }
            $this->_success(Yii::t('apiModule.member','保存成功'),$tag);
        }else{
            $this->_error(Yii::t('apiModule.member','保存失败'),null,$tag);
        }
    }


    /*
     * 用户地址编辑接口
     * data为加密过一维数组，元素如下
     * id 主键，地址id
     * real_name  收货人姓名
     * mobile  手机号码
     * province_id  省份id
     * city_id  城市id
     * district_id  区/县id
     * street  详细地址
     * zip_code  邮编
     * default  默认地址（0否，1是)
     */
    public function actionAddressUpdate() {
    	$tag=$this->action->id;
//         $add = $this->getPost('data');
        $add = str_replace("\\\"", "\"",  $this->getParam('data'));
        $data = CJSON::decode($add);

        if(empty($data))
            $this->_error(Yii::t('apiModule.member','地址不能为空'));

        $model = Address::model()->findByPk($data['id'],"member_id = :member_id",array(':member_id'=>$this->member));
        if(empty($model)){
            $this->_error(Yii::t('apiModule.member','不可修改会员id'));
        }
//        $this->_chenck($model->member_id);
        
        if(!empty($model)){
            $model->attributes = $data;
            $model->member_id = $this->member;
            if($model->save()){
                if($model->default == Address::DEFAULT_IS){
                    Address::model()->updateAll(array('default'=>Address::DEFAULT_NO),"member_id = :member_id AND id <> :id",array(':member_id'=>$this->member,':id'=>$model->id));
                }
                $this->_success(Yii::t('apiModule.member','保存成功'),$tag);
            }else{
                $this->_error(Yii::t('apiModule.member','保存失败'),null,$tag);
            }
        }else{
            $this->_error(Yii::t('apiModule.member','地址信息不存在'),null,$tag);
        }

    }

    /*
     * 用户地址删除接口
     * id 主键，地址id
     */
    public function actionAddressDelete() {
    	$tag=$this->action->id;
        $id = $this->getParam('id');
        if(!is_numeric($id) && !is_int($id))
            $this->_error(Yii::t('apiModule.member','非法参数'));

        $model = Address::model()->findByPk($id);

        $this->_chenck($model->member_id);

        if($model){
            if($model->delete()){
                $this->_success(Yii::t('apiModule.member','成功删除'),$tag);
            }else{
                $this->_error(Yii::t('apiModule.member','删除失败'),null,$tag);
            }
        }else{
            $this->_error(Yii::t('apiModule.member','数据不存在'),null,$tag);
        }
    }

    /**
     * 商家登录设置语言
     */
    public function actionSetLanguage(){
        if($_POST['Language']){
            $this->_success(Yii::t('apiModule.member','设置语言成功'));
        }

    }

    /**
     * 个人认证页面, 发送手机短信接口
     * 传递参数
     * 1.mobile 手机号码
     */
    public function actionGetMobileVerifyCode(){
        $return = array('status'=>404, 'msg'=>Yii::t('apiModule.identification','手机号码不正确'));

        $mobile = trim($this->getParam('mobile'));
        if( $mobile != '' && preg_match("#[\d]{11}#", $mobile)){
            $verifyArr = Yii::app()->db->createCommand()->from('{{checkcode}}')->where("phone='{$mobile}'")->queryRow();
            if ($verifyArr && (time() - $verifyArr['create_time'] < 60)) {
                  $this->_error(Yii::t('apiModule.identification',Yii::t('apiModule.identification', '验证码正在发送，请等待{time}秒后重试', array('{time}' => '60'))));
            }
            $verifyCode = mt_rand(10000, 99999);
            $msg = Yii::t('apiModule.identification', '您的验证码是：{0}。该验证码5分钟内有效且只能使用一次，切忌转发。如非本人操作请忽略本短信', array('{0}' => $verifyCode));
            if($verifyArr){
               Yii::app()->db->createCommand()->update('{{checkcode}}', array('checkcode' => $verifyCode, 'create_time' => time()), "phone='{$mobile}'");
            }else{
                Yii::app()->db->createCommand()->insert('{{checkcode}}', array('phone' => $mobile, 'checkcode' => $verifyCode, 'create_time' => time()));
            }
                $apimember=  new ApiMember();
                $rs = $apimember->sendSms($mobile, $msg,  ApiMember::SMS_TYPE_ONLINE_ORDER,0,  ApiMember::SKU_SEND_SMS,array($verifyCode),  ApiMember::CAPTCHA);
                if ($rs['status']==true) {
                	$this->_success(Yii::t('apiModule.identification', '发送成功'));
                }else{
                	$this->_error(Yii::t('apiModule.identification', '发送失败，'.$rs['msg']));
                }
                
        }else{
        	$this->_error(Yii::t('apiModule.identification', '手机号码不正确'));
        }

    }

    /**
     * 个人认证接口
     * 传递的参数有六个
     * 1.memberId SKU的会员ID
     * 2.identification 身份证号码
     * 3.bankCardNumber 银行卡号码
     * 4.realName 真实姓名
     * 5.verificationCode 手机验证码
     * 6.mobile 手机号
     */
    public function actionPersonalIdentification(){
        $return = array('status'=>404, 'msg'=>Yii::t('apiModule.identification','参数不正确'));

//         $memberId         = intval($this->getParam('memberId'));//会员ID (gw_sku_member表的ID,非盖象的会员ID)
        $identification   = trim($this->getParam('identification'));//身份证号
        $bankCardNumber   = trim($this->getParam('bankCardNumber'));//银行卡号
        $realName         = trim($this->getParam('realName'));//真实姓名
        $verificationCode = trim($this->getParam('verificationCode'));//手机验证码
        $mobile           = trim($this->getParam('mobile'));//手机号
        $bank_name           = trim($this->getParam('bankName'));//银行
        $this->checkCheckCode($mobile,$verificationCode);
        //三个参数必须全不为空
        if( $identification !='' && $bankCardNumber !='' ){
            //验证身份证是否合法
            if( !((strlen($identification)==15 && preg_match('/^\d{14}(\d|x)$/i', $identification)) || (strlen($identification)==18 && preg_match('/^\d{17}(\d|x)$/i', $identification))) || ( strlen($identification)!=18 && strlen($identification)!=15) ) {
            	$this->_error(Yii::t('apiModule.identification','身份证号码不合法'));
            }
            //验证银行卡是否合法(由于不知道每个银行的规则,目前只限位数)
            if( !preg_match('/^\d{18,22}$/i', $bankCardNumber) ){
               $this->_error(Yii::t('apiModule.identification','银行卡不合法'));
            }
            //将数据保存到数据库
            $id = Yii::app()->db->createCommand()
                ->select('id')
                ->from('{{member_personal_authentication}}')
                ->where('member_id=:id', array(':id'=>$this->member))
                ->queryScalar();
            $data = array(
                'member_id' => $this->member,
            	'identification' => $identification,
                'real_name' => $realName,
            	'mobile' => $mobile,
                'identification' => $identification,
                'bank_card_number' => $bankCardNumber,
            	'bank_name' => $bank_name,
            	'status'=>MemberPersonalAuthentication::STATUS_NO,
            	'auto_status'=>MemberPersonalAuthentication::AUTO_STATUS_NOT_PASS,
            );
            //接口验证银行卡
            $bank_code = ApiBank::getBankCodeByName($bank_name);
            if (!empty($bank_code)) {
            	$apiBank = new ApiBank();
            	$check_rs = $apiBank->auth($bank_code, $bankCardNumber, $realName, $mobile, $identification);
            
            	if ($check_rs['status']==true) {
            		$data['status'] =  MemberPersonalAuthentication::STATUS_PASS;
            		$data['auto_status'] =  MemberPersonalAuthentication::AUTO_STATUS_PASS;
            	}
            }
            if( $id ){//若存在则更新
                Yii::app()->db->createCommand()->update('{{member_personal_authentication}}', $data, 'id=:id', array(':id' =>$id));
            }else{//否则插入
                Yii::app()->db->createCommand()->insert('{{member_personal_authentication}}', $data);
            }
            $this->_success(Yii::t('apiModule.identification','资料保存成功,请耐心等待审核'));
        }else{
        	$this->_error(Yii::t('apiModule.identification','数据缺失'));
        }


    }

    //验证身份证号码是否合法
    protected function checkIdentification($identification){
        if( empty($identification) ){
            return false;
        }

        $city = array(
            11=>"北京", 12=>"天津", 13=>"河北", 14=>"山西",
            15=>"内蒙古", 21=>"辽宁", 22=>"吉林", 23=>"黑龙江",
            31=>"上海", 32=>"江苏", 33=>"浙江", 34=>"安徽",
            35=>"福建", 36=>"江西", 37=>"山东", 41=>"河南",
            42=>"湖北", 43=>"湖南", 44=>"广东", 45=>"广西",
            46=>"海南", 50=>"重庆", 51=>"四川", 52=>"贵州",
            53=>"云南", 54=>"西藏", 61=>"陕西", 62=>"甘肃",
            63=>"青海", 64=>"宁夏", 65=>"新疆", 71=>"台湾",
            81=>"香港", 82=>"澳门", 91=>"国外"
        );

        $iSum = 0;
        $idCardLength = strlen($identification);
        //长度验证
        if(!preg_match('/^\d{17}(\d|x)$/i',$identification) && !preg_match('/^\d{15}$/i',$identification))
        {
            return false;
        }
        //地区验证
        if(!array_key_exists(intval(substr($identification,0,2)),$city))
        {
            return false;
        }
        // 15位身份证验证生日，转换为18位
        if ($idCardLength == 15)
        {
            $sBirthday = '19'.substr($identification,6,2).'-'.substr($identification,8,2).'-'.substr($identification,10,2);
            $d = new DateTime($sBirthday);
            $dd = $d->format('Y-m-d');
            if($sBirthday != $dd)
            {
                return false;
            }
            $identification = substr($identification,0,6)."19".substr($identification,6,9);//15to18
            $Bit18 = $this->getVerifyBit($identification);//算出第18位校验码
            $identification = $identification.$Bit18;
        }
        // 判断是否大于2038年，小于1900年
        $year = substr($identification,6,4);
        if ($year<1900)// || $year>2038
        {
            return false;
        }

        //18位身份证处理
        $sBirthday = substr($identification,6,4).'-'.substr($identification,10,2).'-'.substr($identification,12,2);
        $d = new DateTime($sBirthday);
        $dd = $d->format('Y-m-d');
        if($sBirthday != $dd)
        {
            return false;
        }
        //身份证编码规范验证
        $idcardBase = substr($identification,0,17);
        if(strtoupper(substr($identification,17,1)) != $this->getVerifyBit($idcardBase))
        {
            return false;
        }
        return $identification;
    }

    // 计算身份证校验码，根据国家标准GB 11643-1999
    protected function getVerifyBit($idcardBase)
    {
        if(strlen($idcardBase) != 17)
        {
            return false;
        }
        //加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码对应值
        $verifyArray = array('1', '0', 'X', '9', '8', '7', '6', '5', '4','3', '2');
        $checksum = 0;

        for ($i = 0; $i < strlen($idcardBase); $i++)
        {
            $checksum += substr($idcardBase, $i, 1) * $factor[$i];
        }

        $mod = $checksum % 11;
        $verifyNumber = $verifyArray[$mod];

        return $verifyNumber;
    }
    
    /*
     * 查询积分余额
    */
    public function actionAmount() {
    	$tag = 'amount';
    	//消费
    	$xiaofei_amount = AccountBalance::getMemberXiaofeiAmount($this->member)*1;
    	$xiaofei_point = CashHistory::getPoint($xiaofei_amount);
    	
    	//待返还
    	$return_amount = AccountBalance::getMemberGuadanReturnAmount($this->member)*1;
    	$return_point = CashHistory::getPoint($return_amount);
    	
    	//现金
    	$cash_amount = AccountBalance::getMemberBalance($this->member)*1;
    	$cash_point = CashHistory::getPoint($cash_amount);
    	
    	
    	$data['jiaoyi_amount'] = $xiaofei_amount;
    	$data['jiaoyi_point'] = $xiaofei_point;
    	$data['xiaofei_amount'] = $xiaofei_amount;
    	$data['xiaofei_point'] = $xiaofei_point;
    	$data['return_amount'] = $return_amount;
    	$data['return_point'] = $return_point;
    	$data['cash_amount'] = $cash_amount;
    	$data['cash_point'] = $cash_point;
    	$this->_success($data,$tag);
    
    }
    
    

    /*
     * 查询待返还积分记录
    */
    public function actionReturnAmountList() {
    	$tag = 'returnAmount';
    	
    	$pageSize = $this->getParam('pageSize',10);
    	$page = $this->getParam('page',1);
    	
    	//消费
    	$list = Yii::app()->db->createCommand()
    	->select('t.*')
    	->from(GuadanJifenOrderDetail::model()->tableName().' as t')
    	->leftJoin(GuadanJifenOrder::model()->tableName().' as o', 't.order_id=o.id')
    	->where('o.member_id=:member_id',array(':member_id'=>$this->member))
    	->order('t.to_time DESC')
    	->limit($pageSize)
    	->offset($pageSize*($page-1))
    	->queryAll();
    	
    	foreach ($list as $k=>$v){
    		$list[$k]['status_name'] = GuadanJifenOrderDetail::getStatus($v['status']);
    	}
    	
    	$data['list'] = $list;
    	$data['server_time'] = time();
    	
    	$this->_success($data,$tag);
    
    }

    public function actionTest() {
    	$card = $this->getParam('card');
    	$info = BankAccount::getBankCardInfo($card);
    if ($info) {
    		$this->_success($info);
    	}else {
    		$this->_error('银行卡无效');
    	}
    
    }
    
    /**
     * 查询
     */
    public function actionPersonalIdentificationInfo(){
    	$info = Yii::app()->db->createCommand()
    	->from(MemberPersonalAuthentication::model()->tableName())
    	->where('member_id=:member_id',array(':member_id'=>$this->member))
    	->queryRow();
    	
    	if (!empty($info)) {
    		$info['status_name'] = MemberPersonalAuthentication::status($info['status']);
    		$this->_success($info);
    	}else {
    		$this->_error('未提交过个人认证申请',ErrorCode::PREASON_AUTH_NOT_EXCITES);
    	}
    	
    	
    }

    /*
     * 配送人员信息接口
     * return json
     */

    public function actionGetDcInfo()
    {
        $memberId = $this->member;

        $info = Distribution::model()->getDcInfo($memberId);

        //获取当前配送员完成订单
        $result = Order::model()->getDealWithGoodsList($memberId,DistributionOrder::STATUS_OK,0,0,true);

        //获取商户信息
        if(!empty($info['bind_store'])) {
            $sinfo = Supermarkets::model()->findByPk(intval($info['bind_store']))->attributes;
            $info['bind_store'] = array('store_id' => $sinfo['id'],'store_name' => $sinfo['name']);
        }

        if(empty($info))
        {
            $this->_error('没有相关的记录!',ErrorCode::GOOD_STOCK_NOT_EXIST);
        }
        $info['bind_store_id'] = !empty($info['bind_store_id']) ? json_decode($info['bind_store_id']) : array();
        $info['finish_order_counts'] = !empty($result) ?  count($result) : 0;
        return $this->_success($info);
    }

    /*
     * 配送人员修改信息接口
     * @param memberId 配送员的账户id
     * @param mobile 手机号码
     * @param bind_store绑定的商户
     */

    public function actionEditDcInfo()
    {
        $memberId     = $this->member;
        $mobile       = $this->getParam('mobile');
        //$bind_store   = $this->getParam('bind_store');

        $tag_name = 'EditDcInfo';

        if(empty($memberId))
        {
            $this->_error('缺少参数',ErrorCode::COMMON_PARAMS_LESS);
        }

        //获取配送员的基本信息
        $info = Distribution::model()->getDcInfo($memberId);

        if(empty($info))
        {
            $this->_error('配送员信息记录不存在',ErrorCode::GOOD_STOCK_NOT_EXIST);
        }

        $param = array(
            'memberId' => intval($memberId),
        );

        if(!empty($mobile))
        {
            $param['mobile'] = $mobile;
        }
       /* if(!empty($bind_store))
        {
            $param['bind_store'] = json_encode($bind_store);
        }*/

        $result = Distribution::model()->updateDcInfo($param);

        if(isset($result['result']) && $result['result'] == false)
        {
            $this->_error($result['msg'],-1,$tag_name);
        }
        else
        {
            $this->_success($result['result'],$tag_name);
        }

    }

    /*
     * 在线状态修改接口
     * @param int memberId 配送员的账户id
     * @param int status 修改的状态
     */

    public function actionChangeOnOffOnline()
    {
        $memberId = $this->member;
        $status   = $this->getParam('status');

        $tag_name = 'ChangeOnOffOnline';
        if(empty($memberId))
        {
            $this->_error('缺少参数',ErrorCode::COMMON_PARAMS_LESS);
        }

        $param = array(
            'memberId' => $memberId,
            'status'   => $status
        );

        $result = Distribution::model()->changeOnline($param);


        if(isset($result['result']) && $result['result'] == false)
        {
            $this->_error($result['msg'],-1,$tag_name);
        }
        else
        {
            $this->_success($result['result'],$tag_name);
        }
    }


    /*
     * 驻店
     * @param int store_id 门店id
     * @param int is_scan 是否是扫码  扫码只支持驻店
     */

    public function actionBindStore()
    {
        $memberId   = $this->member;
        $sid        = $this->getParam('store_id');
        $is_scan    = $this->getParam('is_scan');
        $bind_store = 0;
        $tag_name   = 'BindStore';

        if(empty($sid)) {
            $this->_error('缺少参数',ErrorCode::COMMON_PARAMS_LESS,$tag_name);
        }

        $info = Distribution::model()->find('member_id = :member_id',array(':member_id' => intval($memberId)))->attributes;
        if(empty($info))
            $this->_error('不存在此配送员的信息!',ErrorCode::GOOD_STOCK_NOT_EXIST,$tag_name);

        if(isset($is_scan) && !empty($is_scan)) {//驻店

            if(!empty($info['bind_store']))
            {
                if($info['bind_store'] == $sid){
                    $this->_error('你已经绑定此店了!',ErrorCode::GOOD_STOCK_EXIST);
                }else{
                    $this->_error('你已经驻店了其他店,请解绑其它点再驻店!',ErrorCode::GOOD_STOCK_EXIST,$tag_name);
                }
            }

            $bind_store = intval($sid);
        }
        //更新数据
        $result = Distribution::model()->updateByPk(intval($info['id']),array('bind_store' => $bind_store));

        if($result < 0){
            $this->_error('操作失败!',ErrorCode::GOOD_STOCK_UPDATE_ERROR,$tag_name);
        }

        $this->_success(null,$tag_name);

    }

    /*
     * 驻点接口
     * @param double lat   纬度
     * @param double lng   经度
     * @param int select_store_id 当前选中的驻点商家id 预留
     * @param int bindType 类型  0解绑 1驻点
     */

    public function actionBundledStore()
    {
        $memberId = $this->member;
        $lat      = (double)$this->getParam('lat');
        $lng      = (double)$this->getParam('lng');
        $bindType = $this->getParam('bindType');

        $data     = array();
        $tag_name = 'BundledStore';

        if(!isset($bindType))
        {
            $this->_error('缺少参数',ErrorCode::COMMON_PARAMS_LESS);
        }

        $info = Distribution::model()->find('member_id = :member_id',array(':member_id' => intval($memberId)))->attributes;
        if(empty($info))
            $this->_error('不存在此配送员的信息!',ErrorCode::GOOD_STOCK_NOT_EXIST,$tag_name);


        if($bindType > 0) { //驻点
            if(empty($lat) || empty($lng)) {
                $this->_error('缺少参数',ErrorCode::COMMON_PARAMS_LESS,$tag_name);
            }
           $bind_store_id = json_decode($info['bind_store_id'],true);
            if(!empty($bind_store_id))
                $this->_error('你已经驻点其他点，请先解绑驻点!',ErrorCode::GOOD_STOCK_EXIST,$tag_name);

            $data = array(
                array('id' => 1, //暂时只有一个驻点
                    'position' =>
                        array(
                    'lat' => is_double($lat) ? $lat : 0,
                    'lng' => is_double($lng) ? $lng : 0,
                    )
                ),
            );
        }

        //更新数据
        $result = Distribution::model()->updateByPk(intval($info['id']),array('bind_store_id' => json_encode($data)));

        if($result < 0){
            $this->_error('操作失败!',ErrorCode::GOOD_STOCK_UPDATE_ERROR,$tag_name);
        }

        $this->_success(null,$tag_name);


    }

    /*
     * 工作汇报接口
     * @param lastId int 当前页面列表最后的订单id
     * @param int pageSize 当前页面显示的数量
     * @param int type 1:完成 0：取消
     */
    public function actionWorkReport()
    {
        $memberId   = $this->member;
        $lastId     = $this->getParam('lastId') ? $this->getParam('lastId')*1 : -1;
        $pageSize   = $this->getParam('pageSize') ? $this->getParam('pageSize') : 10;
        $type       = $this->getParam('type') > 0 ? DistributionOrder::STATUS_OK : DistributionOrder::STATUS_CANCEL;

        if(empty($lastId))
            $this->_error('缺少参数',ErrorCode::COMMON_PARAMS_LESS);

        $tag_name = 'WorkReport';
        $result = Order::model()->getDealWithGoodsList($memberId,$type,$pageSize,$lastId,true);
        if(isset($result['result']) && $result['result'] == false)
        {
            $this->_error($result['msg'],-1,$tag_name);
        }
        else
        {
            $this->_success($result['result'],$tag_name);
        }

    }

}