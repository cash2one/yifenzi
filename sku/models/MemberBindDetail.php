<?php

/**
 * This is the model class for table "{{member_bind_detail}}".
 *
 * The followings are the available columns in table '{{member_bind_detail}}':
 * @property string $id
 * @property string $bind_id
 * @property string $gai_fun_member_id
 * @property string $bind_member_id
 * @property string $create_time
 */
class MemberBindDetail extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public $sku_number;
	public $gai_number;
	public function tableName()
	{
		return '{{member_bind_detail}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('bind_id, gai_fun_member_id, bind_member_id, create_time', 'required'),
			array('bind_id, gai_fun_member_id, bind_member_id, create_time', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, bind_id, gai_fun_member_id, bind_member_id, create_time', 'safe', 'on'=>'search'),
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
			'bind_id' => '绑定编号id',
			'gai_fun_member_id' => '盖粉会员id（被绑定人的会员id）',
			'bind_member_id' => '绑定的会员id',
			'create_time' => '创建时间',
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
	public function search($id)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
		$criteria=new CDbCriteria;
		$this->bind_id = $id;
		$criteria->select = "bind_id,gai_fun_member_id,m.sku_number,m.gai_number";
		$criteria->join = "left join ".Member::model()->tableName()." as m on m.id = gai_fun_member_id";
		$criteria->order = "create_time DESC";
		$criteria->compare('id',$this->id,true);
		$criteria->compare('bind_id',$this->bind_id,false);
		$criteria->compare('gai_fun_member_id',$this->gai_fun_member_id,true);
		$criteria->compare('bind_member_id',$this->bind_member_id,true);
		$criteria->compare('create_time',$this->create_time,true);
		
		$criteria->group = "gai_fun_member_id";
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MemberBindDetail the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/*
	 * 获取绑定的盖网号和被绑定的盖网号
	 */
	public static function BindNumber($id,$type){
		if($type){
			$count = Yii::app()->db->Createcommand()
					->select("count(id)")
					->from(MemberBindDetail::model()->tableName())
					->where("bind_id = '".$id."'")
					->queryScalar();
		}else{
			$count = Yii::app()->db->Createcommand()
					->select("gai_fun_member_id")
					->from(MemberBindDetail::model()->tableName())
					->where("bind_id = '".$id."'")
					->queryColumn();
			$count = count(array_unique($count));
		}
		return $count;
	}
	
	/**
	 * @param  $bind_id
	 * @param  $gai_fun_member_id 
	 * @return $count 一个绑定动作中某个GW号绑定了多少GW号
	 */
	public static function GetCount($bind_id,$gai_fun_member_id){
		$count = Yii::app()->db->Createcommand()
			->select("count(id)")
			->from(MemberBindDetail::model()->tableName())
			->where("bind_id = '".$bind_id."' and gai_fun_member_id = '".$gai_fun_member_id."'")
			->queryScalar();
		return $count;
	}
	
	/**
	 * View 调用形成Button
	 * @param unknown $bind_id
	 * @param unknown $gai_fun_member_id
	 * @param unknown $sku_number
	 * @return string
	 */
	public static function CreateButton($bind_id,$gai_fun_member_id,$sku_number){
		$string = "";
		if (Yii::app()->user->checkAccess('Manage.MemberBind.CheckBindGW'))
			$string .= "<a class=\"regm-sub\" href=\"javascript:CheckGWnumber({$bind_id},{$gai_fun_member_id},'{$sku_number}')\">查看名单</a>";
		return $string;
	}
	
	/*
	 * 通过时间统计一段时间内绑定的账号
	 * $type ture=>绑定新账号数  false=>被绑定账号数
	 * $Bindtype : 绑定的类型 自动=>1|手动=>2
	 */
	public static function GetTimeCount($time,$type,$Bindtype){
		$start = strtotime($time);
		$end = strtotime ("+1 day", strtotime($time));
		$bindIdSql = "SELECT id FROM ".MemberBind::model()->tableName()." WHERE type = {$Bindtype} and create_time BETWEEN  {$start} and {$end}";
		$bindId = Yii::app()->db->createcommand($bindIdSql)->queryColumn();
		$bindId = empty($bindId) == true ? "''" : implode(',',$bindId);
		if($type){
			$sql = "SELECT count(id) FROM ".MemberBindDetail::model()->tableName()." WHERE bind_id in ({$bindId})";
			$count = Yii::app()->db->createcommand($sql)->queryScalar();
		}else{
			$sql = "SELECT gai_fun_member_id FROM ".MemberBindDetail::model()->tableName()." WHERE bind_id in ({$bindId})";
			$count = Yii::app()->db->createcommand($sql)->queryColumn();
			$count = count(array_unique($count));
		}
		return $count;
	}
	
}
