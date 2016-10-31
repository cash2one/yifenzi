<?php

/**
 * This is the model class for table "{{partner_join_auditing}}".
 *
 * The followings are the available columns in table '{{partner_join_auditing}}':
 * @property string $id
 * @property string $name
 * @property string $mobile
 * @property string $gai_number
 * @property string $referrals_gai_number
 * @property string $id_name
 * @property string $id_card
 * @property integer $id_card_to_time
 * @property string $id_card_font_img
 * @property string $id_card_back_img
 * @property string $store_mobile
 * @property integer $store_province_id
 * @property integer $store_city_id
 * @property integer $store_district_id
 * @property string $store_address
 * @property string $license_img
 * @property integer $license_to_time
 * @property string $bank
 * @property integer $bank_account
 * @property integer $bank_province_id
 * @property integer $bank_city_id
 * @property integer $bank_district_id
 * @property string $bank_branch
 * @property string $bank_img
 * @property integer $status
 * @property string $remark
 * @property integer $create_time
 * @property integer $update_time
 */
class PartnerJoinAuditing extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{partner_join_auditing}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('store_province_id, store_city_id, store_district_id, bank_account, bank_province_id, bank_city_id, bank_district_id, status, create_time, update_time', 'numerical', 'integerOnly'=>true),
			array('id_name', 'length', 'max'=>24),
			array('name,store_name', 'length', 'max'=>20),
			array('bank_account_name', 'length', 'max'=>32),
			array('store_name','CheckName'),
			array('mobile, store_mobile', 'length', 'max'=>14),
			array('mobile', 'match', 'pattern' => '/^13[0-9]{1}[0-9]{8}$|^15[0-9]{1}[0-9]{8}$|^18[0-9]{1}[0-9]{8}$|^14[0-9]{1}[0-9]{8}$|^17[0-9]{1}[0-9]{8}$|^(852){0,1}[0-9]{8}$/', 'message' => Yii::t('partner', '请填写正确的联系人手机号码.')),
			array('store_mobile', 'match', 'pattern' => '/^13[0-9]{1}[0-9]{8}$|^15[0-9]{1}[0-9]{8}$|^18[0-9]{1}[0-9]{8}$|^14[0-9]{1}[0-9]{8}$|^17[0-9]{1}[0-9]{8}$|^(852){0,1}[0-9]{8}$/', 'message' => Yii::t('partner', '请填写正确的店铺联系手机号码.')),
			array('gai_number, referrals_gai_number,bank_account', 'length', 'max'=>22),
			array('id_card', 'length', 'max'=>18),
			array('id_card_font_img, id_card_back_img, store_address, license_img, bank, bank_branch, bank_img,head', 'length', 'max'=>128),
			array('remark', 'length', 'max'=>256),
			array('bank_province_id,bank_city_id,bank_district_id,store_province_id,store_city_id,store_district_id,bank,bank_account,bank_account_name,bank_branch,id_name,name,store_name,mobile,store_mobile,store_address,id_card,bank_img,id_card_font_img,id_card_back_img,license_img,head ,license_to_time, id_card_to_time', 'required'),
			array('referrals_gai_number,gai_number', 'CheckGW'),
			//array('id_card_font_img', 'Check'),
			array('id_card_font_img,id_card_back_img,license_img,bank_img,head', 'file', 'types' => 'jpg,gif,png,jpeg', 'maxSize' => 1024 * 1024 * 3, 'tooLarge' => Yii::t('partner', '文件大于3M，上传失败！请上传小于3M的文件！'), 'allowEmpty' => true, 'safe' => true),
			array('id_card','match', 'pattern'=> '/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X|x)$/'),
			array('mobile','checkMobile'),
                                                    array('mobile','checkEnMobile'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, mobile, gai_number, referrals_gai_number, id_name, id_card, id_card_to_time, id_card_font_img, id_card_back_img, store_mobile, store_province_id, store_city_id, store_district_id, store_address, license_img, license_to_time, bank, bank_account, bank_province_id, bank_city_id, bank_district_id, bank_branch, bank_img, status, remark, create_time, update_time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => '申请人姓名',
			'mobile' => '联系电话',
			'gai_number' => '运营方GW号',
			'referrals_gai_number' => '推荐人GW号',
			'id_name' => '身份证姓名',
			'id_card' => '身份证号',
			'id_card_to_time' => '身份证有效期',
			'id_card_font_img' => '身份证正面照',
			'id_card_back_img' => '身份证反面照',
			'store_mobile' => '电话',
			'store_province_id' => '省份',
			'store_city_id' => '城市',
			'store_district_id' => '县区',
			'store_address' => '店铺地址',
			'license_img' => '个体工商户执照/企业法人营业执照',
			'license_to_time' => '执照到期限期',
			'bank' => '开户银行',
			'bank_account' => '银行卡号',
			'bank_province_id' => '银行所属省份',
			'bank_city_id' => '银行所属城市',
			'bank_district_id' => '银行所属区县',
			'bank_branch' => '开户支行',
			'bank_img' => '银行卡/开户许可证图片',
			'status' => '状态 ',
			'remark' => '备注',
			'create_time' => '申请时间',
			'update_time' => '更新时间',
			'store_name'=>'店铺名称',
			'bank_account_name'=>'银行卡账户名',
			'head'=>'商家头像',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('store_name',$this->store_name,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('gai_number',$this->gai_number,true);
		$criteria->compare('status',$this->status,true);


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PartnerJoinAuditing1 the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	
	//状态
	const STATUS_APPLY = 0;   //申请中
	const STATUS_ENABLE = 1;  //审核通过
	const STATUS_UNPASS = 2;  //审核不通过
	
	public static function getStatus($key = null) {
		$data = array(
				self::STATUS_APPLY => Yii::t('partner', '申请中'),
				self::STATUS_ENABLE => Yii::t('partner', '审核通过'),
				self::STATUS_UNPASS => Yii::t('partner', '审核不通过'),
		);
		return $key === null ? $data : $data[$key];
	}
	
	public function CheckGW($attribute,$params){
		if($this->gai_number != ""){
			$result = Member::getByGwNumber($this->gai_number);
                                                   
                        if($result){
                            $mid = $result['id'];
                            $partner = Partners::model()->find('member_id=:mid and status=:status',array(':mid'=>$mid,':status'=>  Partners::STATUS_ENABLE));
                       
                            if(empty($partner)){
                                $this->addError('gai_number',"请输入有效的商家GW号或留空.");
                            }
                        }else{
			 $this->addError('gai_number',"请输入有效的商家GW号或留空.");
                        }
		}
		if($this->referrals_gai_number != ""){
			$result = Member::getByGwNumber($this->referrals_gai_number);
			if(!$result) $this->addError('referrals_gai_number',"请输入有效的商家GW号或留空.");
		}
	}
	
	public function CheckName($attribute,$params){
		if($this->store_name != ""){
			$sql = "SELECT * FROM gw_sku_partner_join_auditing where store_name = '{$this->store_name}' and `status` in (0,1)";
			$result = Yii::app()->db->createCommand($sql)->queryRow();
			if($result) $this->addError('store_name',"店铺名称不能重复");
		}
	}
	
	/**
	 * 验证手机是否已注册
	 */
	public function checkMobile($attribute, $params){
		$partner = Partners::model()->find('mobile=:mobile',array(':mobile'=>$this->$attribute));
		if($partner){
			$this->addError($attribute, Yii::t('partner', '该号码已申请过商家！'));
		}
                                    $mobile = PartnerJoinAuditing::model()->find('mobile=:mobile and status !=:status ',array(':mobile'=>$this->$attribute,':status'=>self::STATUS_UNPASS));
                                    if($mobile){
                                        $this->addError($attribute, Yii::t('partner', '该号码已申请商家，请勿重复申请！'));
                                    }
	}
        
        /*
         * 查询手机号是否有可用GW
         */
        public function checkEnMobile($attribute, $params){
            $Api = new ApiMember();
           if(empty($this->$attribute)){
               return false;
           }
            $member = $Api->getInfo($this->$attribute);
             if (isset($member['0'])) {
                 $total = count($member);
                 $count = 0;
                   foreach($member as $v){
                       if($v['status']== 2||$v['status']==3 ){
                           $count++;
                       }
                   }
                   if($count ==$total){
                        $this->addError($attribute, Yii::t('partner', '该手机号码绑定的GW号已除名或禁用！请重新选择手机号或联系商城客服！'));
                   }
                }
                elseif($member['status'] == 2 || $member['status'] == 3) {
                    $this->addError($attribute, Yii::t('partner', '该手机号码绑定的GW号已除名或禁用！请重新选择手机号或联系商城客服！'));
                }
        }
}
