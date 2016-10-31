<?php

/**
 * This is the model class for table "{{guadan_jifen_goods}}".
 *
 * The followings are the available columns in table '{{guadan_jifen_goods}}':
 * @property integer $id
 * @property integer $rule_id
 * @property string $partner_id
 * @property integer $member_id
 * @property integer $status
 */
class GuadanJifenGoods extends CActiveRecord
{
	
	const STATUS_ENABLE=1;
	const STATUS_DISABLE=2;
	const STATUS_DELETE=3;
	
	const  CACHE_DIR_RULES = 'GuadanJifenGoodsRules';
	
	public static function getStatus($k = null) {
		$arr = array(
				self::STATUS_ENABLE => Yii::t('order', '上架'),
				self::STATUS_DISABLE => Yii::t('order', '下架'),
				self::STATUS_DELETE => Yii::t('order', '已删除'),
		);
		return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{guadan_jifen_goods}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('rule_id, member_id, status', 'numerical', 'integerOnly'=>true),
			array('partner_id', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, rule_id, partner_id, member_id, status', 'safe', 'on'=>'search'),
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
			'rule_id' => '规则id',
			'partner_id' => '商家合作id',
			'member_id' => '商家id',
			'status' => '状态  1位上架  2为下架',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('rule_id',$this->rule_id);
		$criteria->compare('partner_id',$this->partner_id,true);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GuadanJifenGoods the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * 添加商家的积分商品
	 */
	public static function updateRuleData($member_id){
		$time = time();
		$rules = Yii::app()->db->createCommand()
		->select('t.id')
		->from(GuadanRule::model()->tableName().' as t')
		->leftJoin(GuadanCollect::model()->tableName().' as c', 't.collect_id=c.id')
		->where('t.status='.GuadanRule::STATUS_ENABLE.' AND c.status='.GuadanCollect::STATUS_ENABLE.' AND c.time_start<='.$time.' AND c.time_end>='.$time)
		->queryAll();
		
		if (empty($rules)) {
			return ;
		}

		$partner = Partners::model()->find('member_id = :member_id',array(':member_id'=>$member_id));
		if (empty($partner)) return false;
		
		$enable_rule_ids = array();
		foreach ($rules as $r){
			$enable_rule_ids[] = $r['id'];
		}
		
		//禁用规则的商品标记为已删除
		Yii::app()->db->createCommand()
		->update(self::model()->tableName(), array('status'=>self::STATUS_DELETE),'member_id='.$member_id.' AND rule_id NOT IN ('.implode(',', $enable_rule_ids).') ');
		
		
		$oldGoodsList = Yii::app()->db->createCommand()
		->select('id,rule_id')
		->from(GuadanJifenGoods::model()->tableName())
		->where('member_id='.$member_id.' AND status !='.self::STATUS_DELETE)
		->queryAll();
		
		$old_rule_ids = array();
		foreach ($oldGoodsList as $val){
			$old_rule_ids[] = $val['rule_id'];
		}
		
		//添加新规则的商品
		foreach ($rules as $r){
			if (!in_array($r['id'], $old_rule_ids)) {
				$record = new self();
				$record->member_id = $member_id;
				$record->partner_id = $partner->id;
				$record->rule_id = $r['id'];
				$record->status = self::STATUS_DISABLE;
				$record->save();
			}
			
		}
		return true;
	}
	
	/**
	 * 获取商家的积分商品 
	 * 
	 * 使用缓存
	 * 
	 */
	public static function getListByMemberId($member_id,$flag= true){
		
		$cache_key = 'getListByMemberId_'.$member_id;
		
		if ($flag) {
			$list = Tool::cache(self::CACHE_DIR_RULES)->get($cache_key);
		}else{
			$list = array();
		}
		
		if (empty($list)) {
			self::updateRuleData($member_id);				//自动补回缺少的商品
			
			$time = time();
			
			$list = Yii::app()->db->createCommand()
			->from(self::model()->tableName().' as t')
			->select('r.*,t.status,t.id as id')
			->leftJoin(GuadanRule::model()->tableName().' as r', 't.rule_id=r.id')
			->leftJoin(GuadanCollect::model()->tableName().' as c', 'r.collect_id=c.id')
			->where('member_id=:member_id AND t.status='.self::STATUS_ENABLE.' AND r.status='.GuadanRule::STATUS_ENABLE.' AND c.status='.GuadanCollect::STATUS_ENABLE .' AND c.time_start<='.$time.' AND c.time_end>='.$time ,array(':member_id'=>$member_id))
			->queryAll();
			
			Tool::cache(self::CACHE_DIR_RULES)->set($cache_key,$list,900);
			
		}
		
		return $list;
		
	}
	
	/**
	 * 获取商家的积分商品  
	 * 用于盖掌柜端
	 * 
	 * 用户缓存
	 * 
	 */
	public static function getPartnerListByMemberId($member_id){
		self::updateRuleData($member_id);				//自动补回缺少的商品
	
		$list = Yii::app()->db->createCommand()
		->from(self::model()->tableName().' as t')
		->select('r.*,t.status,t.id as id')
		->leftJoin(GuadanRule::model()->tableName().' as r', 't.rule_id=r.id')
		->leftJoin(GuadanCollect::model()->tableName().' as c', 'r.collect_id=c.id')
		->where('member_id=:member_id AND t.status!='.self::STATUS_DELETE.'  AND c.status='.GuadanCollect::STATUS_ENABLE,array(':member_id'=>$member_id))
		->queryAll();
		return $list;
	
	}
	
	/**
	 * 获取处理过后的积分商品
	 */
	static function getFromatListByMemberId($partner_member_id){
		$list = self::getListByMemberId($partner_member_id);
		if (empty($list)) {
			return array();
		}
		//获取商家折扣
		$discount = Yii::app()->db->createCommand()
		->select("selling_discount")
		->from("{{guadan_partner_setting}}")
		->where("member_id =".$partner_member_id)
		->queryRow();
		$data = array();
		$data['selling_discount'] = !empty($discount['selling_discount'])?$discount['selling_discount']:100;
		if (!empty($list)) {
			foreach ($list as $k=>$v){
				$v['status_name'] = GuadanJifenGoods::getStatus($v['status']);
				$v['amount_pay'] = floor($v['amount_pay']*$data['selling_discount'])/100;
				
				$v['point'] = CashHistory::getPoint($v['amount']);
				$v['point_give'] = CashHistory::getPoint($v['amount_give']);
				
				if($v['type'] == GuadanRule::NEW_MEMBER){
					$data['new'][] = $v;
				}else{
					$data['old'][] = $v;
				}
			}
		}
		
		return $data;
	}
	
	
	/**
	 * 清楚缓存
	 * @return boolean
	 */
	static function clearCache($member_id=null){
		if (!empty($member_id)) {
			$cache_key1 = 'getListByMemberId_'.$member_id;
			Tool::cache(self::CACHE_DIR_RULES)->set($cache_key1,array());
		}else{
			Tool::cache(self::CACHE_DIR_RULES)->flush();
		}
		
		return true;
	}
	
	/**
	 * 获取官方积分列表
	 * @return boolean
	 */
	static function getOfficleGoods($falg=true){
		
		$cache_key = 'getOfficleGoods';
		
		$list = $falg?Tool::cache(self::CACHE_DIR_RULES)->get($cache_key):array();
		$time = time();
		if (empty($list)) {
			$list = Yii::app()->db->createCommand()
	            ->select("t.*")
	            ->from(GuadanRule::model()->tableName().' as t')
	            ->leftJoin(GuadanCollect::model()->tableName().' as c', 't.collect_id=c.id')
	            ->where("t.status =".GuadanRule::STATUS_ENABLE.' AND c.status='.GuadanCollect::STATUS_ENABLE.' AND c.time_start<='.$time.' AND c.time_end>='.$time)
	            ->queryAll();
			
			Tool::cache(self::CACHE_DIR_RULES)->set($cache_key,$list,900);
		}
		return $list;
	}
	

}
