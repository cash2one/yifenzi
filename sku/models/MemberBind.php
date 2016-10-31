<?php

/**
 * This is the model class for table "{{member_bind}}".
 *
 * The followings are the available columns in table '{{member_bind}}':
 * @property string $id
 * @property integer $type
 * @property string $create_time
 */
class MemberBind extends CActiveRecord
{
	const BIND_TYPE_AUTO = 1; //自动绑定
	const BIND_TYPE_MANUA = 2; //手动绑定
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{member_bind}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('create_time', 'required'),
			array('type', 'numerical', 'integerOnly'=>true),
			array('create_time', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, type, create_time', 'safe', 'on'=>'search'),
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
			'type' => '类型（1自动 2手动）',
			'create_time' => '绑定时间',
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
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->select = "id,create_time,type, 
				case type when 1 then '自动' when 2 then '手动' else '其他' end as type";
		$criteria->order='create_time DESC';
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MemberBind the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public static function GetNotBindMem(){
		$count = Yii::app()->db->Createcommand()
		         ->select("count(id)")
		         ->from(Member::model()->tableName())
		         ->where("referrals_id = ''")
		         ->queryScalar();
		return $count;
	}
	
	/*
	 * 绑定
	 */
	public static function Bind($BindGWnumber,$number,$type="",$collect_id=""){
		try {
			$type = $type == "" ? MemberBind::BIND_TYPE_AUTO : $type;
			$SysType = $type == MemberBind::BIND_TYPE_AUTO ? SystemLog::LOG_TYPE_ZHANGHAO_AUTO : SystemLog::LOG_TYPE_ZHANGHAO;
			//待绑定的新用户
			$number = Yii::app()->db->createCommand()->select("id")->from(Member::model()->tableName())->limit($number)->where("referrals_id = ''")->queryColumn();
			if(count($number) != 0){
				//绑定的新用户数
				$countNumber = count($number);
				//被绑定的盖网号
				$GwMemberId = Yii::app()->db->createCommand()->select("id")->from(Member::model()->tableName())->where("sku_number = '{$BindGWnumber}' or gai_number = '{$BindGWnumber}'" )->queryScalar();
				$time = time();
				$InsertBindTable = "insert into ".MemberBind::model()->tableName()."(create_time,type,guandan_collect_id) VALUES('{$time}','{$type}','{$collect_id}')";
				$connection=Yii::app()->db;
				$transaction = $connection->beginTransaction();
				$connection->createCommand($InsertBindTable)->execute();
				$bind_id = $connection->getLastInsertID();
				
				$InsertBindDetail = "insert into ".MemberBindDetail::model()->tablename()."(bind_id,gai_fun_member_id,bind_member_id,create_time) VALUES";
				
					foreach ($number as $val){
						$BindDetailTime = time();
						$InsertBindDetail .= "('{$bind_id}','{$GwMemberId}','{$val}','{$BindDetailTime}')," ;
					}
				$InsertBindDetail=substr($InsertBindDetail,0,-1);
				$connection->createCommand($InsertBindDetail)->execute();
				$number = implode(",", $number);
				$UpMemberTable = "UPDATE ".Member::model()->tableName()." SET referrals_id = '{$GwMemberId}' WHERE id in({$number})";
				$connection->createCommand($UpMemberTable)->execute();
				if($type == MemberBind::BIND_TYPE_AUTO){
					$guadanSql = "UPDATE ".GuadanCollect::model()->tableName()." SET new_member_count = new_member_count + {$countNumber} WHERE id = '{$collect_id}'";
					$connection->createCommand($guadanSql)->execute();
				}
				$transaction->commit();
				}
			SystemLog::record("账号绑定",$SysType);
			return true;
		} catch (Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
		
		
	}

    //获取SKU号ID编号
    public static function getIDByGW($gaiNumber){
        $result = Yii::app()->db->createCommand()->from("{{member}}")
            ->where('gai_number=:gai_number',array(':gai_number'=>$gaiNumber))->queryScalar();
        return $result;
    }
}
