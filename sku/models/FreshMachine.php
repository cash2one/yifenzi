<?php

/**
 * This is the model class for table "{{fresh_machine}}".
 *
 * The followings are the available columns in table '{{fresh_machine}}':
 * @property integer $id
 * @property string $code
 * @property string $thumb
 * @property string $mobile
 * @property integer $partner_id
 * @property integer $category_id
 * @property string $activation_code
 * @property string $name
 * @property string $password
 * @property integer $status
 * @property integer $is_activate
 * @property string $symbol
 * @property integer $country_id
 * @property integer $province_id
 * @property integer $city_id
 * @property integer $district_id
 * @property string $address
 * @property integer $user_id
 * @property string $user_ip
 * @property string $member_id
 * @property string $setup_time
 * @property string $remark
 * @property string $private_key
 * @property string $public_key
 * @property string $create_time
 * @property string $update_time
 * @property string $device_id
 * @property double $lng
 * @property double $lat
 * @property integer $is_recommend
 * @property integer $is_over_amount
 * @property string $max_amount_preday
 * @property string $recommend_side
 */
class FreshMachine extends CActiveRecord {

	public $machine_ids;
    public $isExport;   // 是否导出Excel
    public $exportLimit = 5000; // 导出Excel长度
        
    const TYPE_LINE = 1; //货道

	public $distance;

	
	const CACHE_DIR='freshMachine';
	const CacheMachineLineInfoPrefix = 'partnerMachineLineInfo';
	const CacheMachineListPrefix =  'partnerMachineList';
	
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{fresh_machine}}';
    }

    public $gai_number;

    //状态
    const STATUS_APPLY = 0;   //申请
    const STATUS_ENABLE = 1;  //启用
    const STATUS_DISABLE = 2; //禁用

    public static function getStatus($key = null) {
        $data = array(
            self::STATUS_APPLY => Yii::t('freshMachine', '申请'),
            self::STATUS_ENABLE => Yii::t('freshMachine', '启用'),
            self::STATUS_DISABLE => Yii::t('freshMachine', '禁用'),
        );
        return $key === null ? $data : $data[$key];
    }

    //是否激活
    const IS_ACTIVATE_YES = 1;
    const IS_ACTIVATE_NO = 0;

    public static function getIsActivate($key = null) {
        $data = array(
            self::IS_ACTIVATE_NO => Yii::t('freshMachine', '否'),
            self::IS_ACTIVATE_YES => Yii::t('freshMachine', '是'),
        );
        return $key === null ? $data : $data[$key];
    }
    
    //是否开启防闪退
    const IS_BACK_YES = 1;
    const IS_BACK_NO =0;
    
       public static function getIsBack($key = null) {
        $data = array(
            self::IS_BACK_NO => Yii::t('freshMachine', '未启用'),
            self::IS_BACK_YES => Yii::t('freshMachine', '已启用'),
        );
        return $key === null ? $data : $data[$key];
    }
    
        //生鲜机类型
    const FRESH_MACHINE = 1;    //生鲜机（大）
    const FRESH_MACHINE_SMALL =2;  //小屏生鲜机
    
       public static function getType($key = null) {
        $data = array(
            self::FRESH_MACHINE => Yii::t('freshMachine', '生鲜机'),
            self::FRESH_MACHINE_SMALL => Yii::t('freshMachine', '俊鹏生鲜机'),
        );
        return $key === null ? $data : $data[$key];
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name,partner_id,member_id,country_id,province_id,city_id,address,status,create_time,category_id,gai_number,type', 'required'),
            array('partner_id, category_id, status, is_activate, country_id, province_id, city_id, district_id, user_id, is_recommend, is_over_amount,fee', 'numerical', 'integerOnly' => true),
            array('lng, lat,max_amount_preday', 'numerical'),
            array('code', 'length', 'max' => 24),
            array('name','length','max'=>20),
            array('thumb, password', 'length', 'max' => 128),
            array('mobile', 'length', 'max' => 13),
            array('activation_code', 'length', 'max' => 50),
            array('symbol', 'length', 'max' => 20),
            array('address', 'length', 'max' => 225),
            array('user_ip, member_id', 'length', 'max' => 11),
            array('setup_time, create_time, update_time', 'length', 'max' => 10),
            array('remark', 'length', 'max' => 200),
            array('device_id, recommend_side', 'length', 'max' => 64),
            array('max_amount_preday', 'length', 'max' => 11),
            array('fee','length','max'=>2),
            array('gai_number','checkName','on'=>'create,update'),
            array('mobile', 'match', 'pattern' => '/^(13[0-9]|15[7-9]|153|156|18[7-9])[0-9]{8}$/', 'message' =>Yii::t('freshMachine', '请填写正确的手机号码')),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, code, thumb, mobile, partner_id, category_id, activation_code, name, password, status, is_activate, symbol, country_id, province_id, city_id, district_id, address, user_id, user_ip, member_id, setup_time, remark, private_key, public_key, create_time, update_time, device_id, lng, lat, is_recommend, is_over_amount, max_amount_preday, recommend_side,gai_number,machine_ids,max_amount_preday,distance,fee,type,isExport', 'safe', 'on' => 'search'),
            array('name','checkName'),
            array('gai_number','checkGai'),
        );
    }
/**
 * 同一合作商家生鲜机名称不能重复
 */
    public function checkName($attribute, $params){
        $partenr = Partners::model()->find('gai_number=:gw',array(':gw'=>$this->gai_number));
        if(!empty($partenr)){
	        if ($this->scenario=='create') {
	    		$count = self::model()->count('partner_id=:pid AND name=:name ',array(':pid'=>$partenr->id,':name'=>$this->name));
	    		if ($count>0) {
	    			$this->addError($attribute, Yii::t('freshMachine', '该GW号下已存在名为（{name}）的生鲜机'),array('{name}'=>$this->name));
	    		}
	    	}
	    	
	    	if ($this->scenario=='update') {
	    		$rs =	Yii::app()->db->createCommand()
	    			->select('id')
			    	->from(self::model()->tableName())
			    	->where('partner_id=:pid  AND name=:name ',array(':pid'=>$partenr->id,':name'=>$this->name))
			    	->queryRow();
	    		if (!empty($rs) && $rs['id']!=$this->id) {
	    			$this->addError($attribute, Yii::t('freshMachine', '该GW号下已存在名为（{name}）的生鲜机'),array('{name}'=>$this->name));
	    		}
	    	}
        }

    	return true;
    	
    }
    /**
     * 检查生鲜机机盖网号是否正确
     * @param type $attribute
     * @param type $params
     */
    public function checkGai($attribute, $params) {

        $gai = Partners::model()->find('gai_number=:gw', array(':gw' => $this->$attribute));
        if (empty($gai)) {
            $this->addError($attribute, Yii::t('freshMachine', '该GW号不是合作商家'));
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'partner' => array(self::BELONGS_TO, 'Partners', 'partner_id'),
            'freshMachineGoods' => array(self::HAS_MANY, 'FreshMachineGoods', 'machine_id'),
            'freshMachineLine' => array(self::HAS_MANY, 'FreshMachineLine', 'machine_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => Yii::t('freshMachine', 'ID'),
            'code' => Yii::t('freshMachine', '装机编码，由系统自动生成,12位数字组成'),
            'thumb' => Yii::t('freshMachine', '缩略图'),
            'mobile' =>Yii::t('freshMachine', '管理员手机'),
            'partner_id' =>Yii::t('freshMachine', '拥有者商家id'),
            'category_id' => Yii::t('freshMachine', '店铺分类'),
            'activation_code' =>Yii::t('freshMachine', '系统生成的激活码'),
            'name' =>Yii::t('freshMachine', '名称'),
            'password' => Yii::t('freshMachine', '管理密码'),
            'status' =>Yii::t('freshMachine', '状态'),
            'is_activate' =>Yii::t('freshMachine', '是否激活（0未激活、1已激活）'),
            'symbol' => Yii::t('freshMachine', '币种(RMB、HKD)'),
            'country_id' =>Yii::t('freshMachine', '国家id'),
            'province_id' => Yii::t('freshMachine', '省份id'),
            'city_id' => Yii::t('freshMachine', '城市id'),
            'district_id' =>Yii::t('freshMachine', '区县id'),
            'address' =>Yii::t('freshMachine', '地址'),
            'user_id' => Yii::t('freshMachine', '管理员id'),
            'user_ip' =>Yii::t('freshMachine', '管理员ip'),
            'member_id' =>Yii::t('freshMachine', '商家id'),
            'setup_time' =>Yii::t('freshMachine', '安装时间'),
            'remark' =>Yii::t('freshMachine', '备注'),
            'private_key' => Yii::t('freshMachine', '私钥'),
            'public_key' =>Yii::t('freshMachine', '公钥'),
            'create_time' =>Yii::t('freshMachine', '创建时间'),
            'update_time' =>Yii::t('freshMachine', '修改时间'),
            'device_id' =>Yii::t('freshMachine', '设备id'),
            'lng' =>Yii::t('freshMachine', '经度'),
            'lat' =>Yii::t('freshMachine', '纬度'),
            'is_recommend' =>Yii::t('freshMachine', '是否推荐'),
            'is_over_amount' =>Yii::t('freshMachine', '是否超过每日最大营业额'),
            'max_amount_preday' => Yii::t('freshMachine', '每日最大营业额'),
            'recommend_side' => Yii::t('freshMachine', '推荐位置'),
            'gai_number' => Yii::t('freshMachine', '商家盖网号'),
        	'fee' => Yii::t('freshMachine', '服务费百分比'),
            'type'=> Yii::t('freshMachine', '生鲜机类型'),
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('t.id', $this->id);
        $criteria->compare('t.code', $this->code, true);
        $criteria->compare('t.thumb', $this->thumb, true);
        $criteria->compare('t.mobile', $this->mobile, true);
        $criteria->compare('t.partner_id', $this->partner_id);
        $criteria->compare('t.category_id', $this->category_id);
        $criteria->compare('t.activation_code', $this->activation_code, true);
        $criteria->compare('t.name', $this->name, true);
        $criteria->compare('t.password', $this->password, true);
        $criteria->compare('t.status', $this->status);
        $criteria->compare('t.is_activate', $this->is_activate);
        $criteria->compare('t.symbol', $this->symbol, true);
        $criteria->compare('t.country_id', $this->country_id);
        $criteria->compare('t.province_id', $this->province_id);
        $criteria->compare('t.city_id', $this->city_id);
        $criteria->compare('t.district_id', $this->district_id);
        $criteria->compare('t.address', $this->address, true);
        $criteria->compare('t.user_id', $this->user_id);
        $criteria->compare('t.user_ip', $this->user_ip, true);
        $criteria->compare('t.member_id', $this->member_id);
        $criteria->compare('t.setup_time', $this->setup_time, true);
        $criteria->compare('t.remark', $this->remark, true);
        $criteria->compare('t.private_key', $this->private_key, true);
        $criteria->compare('t.public_key', $this->public_key, true);
        $criteria->compare('t.create_time', $this->create_time, true);
        $criteria->compare('t.update_time', $this->update_time, true);
        $criteria->compare('t.device_id', $this->device_id, true);
        $criteria->compare('t.lng', $this->lng);
        $criteria->compare('t.lat', $this->lat);
        $criteria->compare('t.is_recommend', $this->is_recommend);
        $criteria->compare('t.is_over_amount', $this->is_over_amount);
        $criteria->compare('t.max_amount_preday', $this->max_amount_preday, true);
        $criteria->compare('t.recommend_side', $this->recommend_side, true);
        $criteria->with = 'partner';
        
        if (!empty($this->machine_ids)) {
        	$criteria->addCondition('t.id IN('.implode(',', $this->machine_ids).')');
        }
        
        
        //根据gw查找
        $criteria->join = ' LEFT JOIN  '.Partners::model()->tableName().' AS p ON t.partner_id=p.id ';
        $criteria->compare('p.gai_number', $this->gai_number,true);
        $criteria->order = 't.create_time DESC';

        // 导出 Excel
        $pagination = array();
        if (!empty($this->isExport)) {
            $pagination['pageVar'] = 'page';
            $pagination['pageSize'] = $this->exportLimit;
        }

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => $pagination,
        ));
    }

    /**
     * @return CDbConnection the database connection used for this class
     */
    public function getDbConnection() {
        return Yii::app()->db;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return FreshMachine the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }


      public function afterSave(){
    	if ($this->isNewRecord) {
    		$stores = new Stores();
    		$stores->stype = Stores::FRESH_MACHINE;
    		$stores->target_id = $this->id;
    		$stores->create_time = $this->create_time;
    		$stores->lat = $this->lat;
    		$stores->lng = $this->lng;
    		$stores->status = $this->status;
    		$stores->is_recommend = $this->is_recommend;
    		$stores->save();
    	}else {
	    	$sql  =  ' UPDATE  '.Stores::model()->tableName().' SET lat='.$this->lat .' , lng= '.$this->lng.' , status= '.$this->status.' , is_recommend= '.$this->is_recommend.' WHERE stype = '.Stores::FRESH_MACHINE.' AND target_id= '.$this->id;
	    	$rs  =Yii::app()->db->createCommand($sql)->execute();
	    	
	    	//清空货道缓存
	    	FreshMachine::clearLineInfo($this->partner_id);
	    	FreshMachine::clearListInfo($this->partner_id);
    	}
    	
    	
    	
    	parent::afterSave();
    	return true;
    	
    }
    
    public static function getMachineLineInfoCacheKey($key){
    	$cache_prefix = self::CacheMachineLineInfoPrefix;
    	return $cache_prefix.'_'.$key;
    }
    
    /**
     * 通过member_id获取货道信息  保存缓存
     * @param unknown $member_id
     */
    public static function getLineByPartnerId($partner_id){
    	$partner_id = $partner_id*1;
    	$cache_key = self::getMachineLineInfoCacheKey($partner_id);
    	$data = Tool::cache(self::CACHE_DIR)->get($cache_key);
    	
    	if (!empty($data)) return $data;
    	
    	$list = Yii::app()->db->createCommand()
    	->from(FreshMachineLine::model()->tableName().' as t')
    	->select('t.*')
    	->leftJoin(FreshMachine::model()->tableName().' as m', 't.machine_id=m.id')
    	->where('t.rent_partner_id=:rent_partner_id OR m.partner_id=:partner_id',array(':rent_partner_id'=>$partner_id,':partner_id'=>$partner_id))
    	->queryAll();

    	Tool::cache(self::CACHE_DIR)->set($cache_key,$list);
    	
    	return $list;
    	
    }
    
    
    public static function clearLineInfo($partner_id){
    	$cache_key = self::getMachineLineInfoCacheKey($partner_id);
    	return Tool::cache(self::CACHE_DIR)->set($cache_key,null);
    }
    
    public static function getMachineListCacheKey($key){
    	$cache_prefix = self::CacheMachineListPrefix;
    	return $cache_prefix.'_'.$key;
    }
    
    /**
     * 通过member_id获取货道信息  保存缓存
     * @param unknown $member_id
     */
    public static function getListByPartnerId($partner_id){
    	$partner_id = $partner_id*1;
    	$cache_key = self::getMachineListCacheKey($partner_id);
    	$data = Tool::cache(self::CACHE_DIR)->get($cache_key);
    	 
    	if (!empty($data)) return $data;
    	 
    	$list = Yii::app()->db->createCommand()
    	->from(FreshMachine::model()->tableName().' as t')
    	->select('t.id,t.name,t.code')
    	->where('t.partner_id=:partner_id',array(':partner_id'=>$partner_id))
    	->queryAll();
    
    	Tool::cache(self::CACHE_DIR)->set($cache_key,$list);
    	
    	return $list;
    	
    }
    
    
    public static function clearListInfo($partner_id){
    	$cache_key = self::getMachineListCacheKey($partner_id);
    	return Tool::cache(self::CACHE_DIR)->set($cache_key,null);
    }

    /*
     * 自动生成货道
     * @$params array
     * @return boolean
     * @author yuanmei.chen
     */
    public static function autoGenerateGoodsLine($params){
        if(empty($params)) return false;
        try{
            //每台生鲜机最多添加36个货道 并且判断是否已经有生成货道
            $freshMachineLineCount = FreshMachineLine::model()->count('machine_id=:machine_id', array(':machine_id' => $params['machine_id']));
            if($freshMachineLineCount > 36 && FreshMachineLine::model()->find('where machine_id ='.intval($params['machine_id']))) throw new Exception('此生鲜机已经有货道或者货道已经达到36个');
            $model = new FreshMachineLine();
            $partner = Partners::model()->find('gai_number=:gw', array(':gw' => $params['gai_number']));
            if (!empty($partner)) {
                $model->rent_member_id = $partner->member_id;
                $model->rent_partner_id = $partner->id;
            }else{
                throw new Exception('找不到合作伙伴记录!');
            }
            $class_name = get_class();
            $lineData = call_user_func_array(array($class_name,'generateLine'),array('L','R'));
            if(count($lineData) <= 36){
                foreach($lineData as $columArray){
                    $columArray['machine_id'] = $params['machine_id'];
                    $columArray['rent_member_id'] = $model->rent_member_id;
                    $columArray['rent_partner_id'] = $model->rent_partner_id;
                    $columArray['create_time'] = time();
                    $columArray['status'] = FreshMachineLine::STATUS_ENABLE;
                    Yii::app()->db->createCommand()->insert(FreshMachineLine::model()->tableName(),$columArray);
                }
            }else{
                throw new Exception('货道不能超过36!');
            }

            return true;

        }catch (Exception $e){
            return $e->getMessage();
        }

    }

    /*
     * 生成36个货道
     * @return array
     * @author yuanmei.chen
     */
    public static function generateLine(){
        $num = 7;
        $count = 4;
        $data = array();
        $insertData = array();
        $args = func_get_args();
        foreach($args as $k => $char){
            for($i = 1; $i < $num;$i++){
                for($j = 1; $j < $count; $j++){
                    $insertData[$char.$i.'-'.$char.$j]['name'] = $char.$i.$j;
                    $insertData[$char.$i.'-'.$char.$j]['code'] = $char.$i.$j;
                }
                $data = $insertData;
            }
        }
        return $data;
    }

    /*
     * 导出机器信息Excel列表
     */
    public function  getFreshMachineExport($machine_model){
        try{
            $machine_ids = array_unique($machine_model['machine_ids']);
            $machine_data = $machine_model->search()->getData();
            $data = array();
            foreach($machine_data as $key=>$val){
                if(empty($val)){
                    continue;
                }
                $freshMachineLine = $val->freshMachineLine;
                $freshMachineGoods = $val->freshMachineGoods;
                for($i = 0; $i < count($freshMachineGoods); $i++){
                    //获取商品信息
                    if(isset($freshMachineGoods[$i]) && !empty($freshMachineGoods[$i])){
                        //导出
                        $data[$key][$i]['exportTime'] = date('Ymd H:i:s',time());
                        //网站名称
                        $data[$key][$i]['machineName'] = isset($val['name']) ? $val['name'] : '';
                        $goods = $freshMachineGoods[$i]->goods;
                      // $freshMachineGoods[$i]['line_id']['goodsName']
                        $data[$key][$i]['goodsName'] = isset($goods['name']) ? $goods['name'] : '';
                        $data[$key][$i]['barcode'] = isset($goods['barcode']) ? (string)$goods['barcode'] : '';
                        $data[$key][$i]['goodsPrice'] = isset($goods['price']) ? '￥'.$goods['price'] : '￥0';
                        //获取货道名称
                        $line = FreshMachineLine::model()->findByPk($freshMachineGoods[$i]['line_id']);
                        $data[$key][$i]['lineName'] = isset($line['name']) ?$line['name'] : '';

                        //获取库存
                        $goodsStock = GoodsStock::model()->findAll('target=:target',array(':target'=>$freshMachineGoods[$i]['line_id']));
                        foreach($goodsStock as $v){
                            if(in_array($v['outlets'],$machine_ids) ){
                                $goodsStock = $v;
                            }else{
                                continue;
                            }
                        }
                        $data[$key][$i]['frozen_stock'] = isset($goodsStock['frozen_stock'])  ? $goodsStock['frozen_stock'] : 0;
                        $data[$key][$i]['stock'] = isset($goodsStock['stock'])  ? $goodsStock['stock'] : 0;

                        /*if(isset($freshMachineLine[$i]->goodsStock)){

                            $goodsStock = $freshMachineLine[$i]->goodsStock;
                            foreach($goodsStock as $v){
                                if(in_array($v['outlets'],$machine_ids) ){
                                    $goodsStock = $v;
                                }else{
                                    continue;
                                }
                            }
                            var_dump($goodsStock);
                            $data[$key][$i]['frozen_stock'] = isset($goodsStock['frozen_stock'])  ? $goodsStock['frozen_stock'] : 0;
                            $data[$key][$i]['stock'] = isset($goodsStock['stock'])  ? $goodsStock['stock'] : 0;

                        }else{
                            $data[$key][$i]['frozen_stock'] =  0;
                            $data[$key][$i]['stock'] =  0;
                        }*/
                    }else{
                        continue;
                    }
                }

            }

            $item = array();
            foreach($data as $key => $val){
                for($i = 0; $i < count($val);$i++){
                    $insertData[$key.'-'.$i]['exportTime']   = $val[$i]['exportTime'];
                    $insertData[$key.'-'.$i]['machineName']  = $val[$i]['machineName'];
                    $insertData[$key.'-'.$i]['goodsName']    = $val[$i]['goodsName'];
                    $insertData[$key.'-'.$i]['barcode']      = $val[$i]['barcode'];
                    $insertData[$key.'-'.$i]['goodsPrice']   = $val[$i]['goodsPrice'];
                    $insertData[$key.'-'.$i]['lineName']     = $val[$i]['lineName'];
                    $insertData[$key.'-'.$i]['frozen_stock'] = $val[$i]['frozen_stock'];
                    $insertData[$key.'-'.$i]['stock']        = $val[$i]['stock'];
                }
                $item = $insertData;

            }

            $result['data'] =$item;
        }catch (Exception $e){
            $result['success'] = false;
            $result['message'] = $e->getMessage();
        }
        return $result;

    }


    
    
}
