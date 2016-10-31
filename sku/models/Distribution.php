<?php

/**
 * This is the model class for table "{{distribution}}".
 *
 * The followings are the available columns in table '{{distribution}}':
 * @property integer $id
 * @property integer $member_id
 * @property string $name
 * @property string $deposit
 * @property integer $deposit_status
 * @property integer $service_count
 * @property integer $status
 * @property integer $create_time
 */
class Distribution extends CActiveRecord
{
    /*
     * 配送员状态
     */
    public $gai_number; //gw号

    const STATUS_OPEN = 1; //启用
    const STATUS_CLOSE = 0; //禁用

    public static function getStatus($key = null)
    {
        $arr = array(
            self::STATUS_CLOSE => Yii::t('distribution', '禁用'),
            self::STATUS_OPEN => Yii::t('distribution', '启用')
        );
        return is_numeric($key) ? (isset($arr[$key]) ? $arr[$key] : null) : $arr;
    }

    /*
     * 押金状态
     */

    const DEPOSIT_YES = 1; // 已交
    const DEPOSIT_NO = 0; //未交

    public static function getDeposit($key = null)
    {
        $arr = array(
            self::DEPOSIT_NO => Yii::t('distribution', '否'),
            self::DEPOSIT_YES => Yii::t('distribution', '是')
        );
        return is_numeric($key) ? (isset($arr[$key]) ? $arr[$key] : null) : $arr;
    }
    
    /*
     * 是否选择当前位置2公里内店铺
     */
    const  RANGE_STATUS_NO = 0;  //否
    const  RANGE_STATUS_YES = 1; //是
    public static function getRange($key = null)
    {
        $arr = array(
            self::RANGE_STATUS_NO => Yii::t('distribution', '否'),
            self::RANGE_STATUS_YES => Yii::t('distribution', '是')
        );
        return is_numeric($key) ? (isset($arr[$key]) ? $arr[$key] : null) : $arr;
    }
    
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{distribution}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('member_id, name, mobile, deposit_status, service_count, status, create_time', 'required'),
            array('member_id, deposit_status, service_count, status, create_time', 'numerical', 'integerOnly' => true),
            array('name, bind_store_id', 'length', 'max' => 100),
            array('range_store_id', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, member_id, name, mobile, deposit_status, service_count, status, create_time', 'safe', 'on' => 'search'),
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
            'member_personal' => array(self::BELONGS_TO, 'MemberPersonalAuthentication', 'member_personal_id'),
//              'member' => array(self::BELONGS_TO, 'MemberPersonalAuthentication', 'member_personal_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'member_id' => '会员id',
            'mobile' => '手机号码',
            'name' => '配送员姓名',
            'deposit_status' => '押金状态',
            'service_count' => '服务次数',
            'status' => '状态',
            'create_time' => '注册时间',
            'bind_store' => '驻店id',
            'bind_store_id' => '驻点信息',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);

        $criteria->compare('name', $this->name, true);
        $criteria->compare('deposit_status', $this->deposit_status);
        $criteria->compare('service_count', $this->service_count);
        $criteria->compare('status', $this->status);
        $criteria->compare('create_time', $this->create_time);
        if ($this->gai_number) {
            $member = Member::getByGwNumber($this->gai_number);
            $criteria->compare('member_id', $member->member_id);
        } else {
            $criteria->compare('member_id', $this->member_id);
        }

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Distribution the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /*
     * 获取配送员的信息
     * @param $memberId 用户id
     * renturn obiect
     */
    public function getDcInfo($memberId)
    {
        if (empty($memberId)) return false;

        $cri = new CDbCriteria();
        $cri->addCondition('member_id=' . intval($memberId));

        return self::model()->find($cri)->attributes;
    }

    /*
     * 配送人员修改信息接口
     * @param array $param
     * renturn obiect
     * @author yuanmei.chen
     */

    public function updateDcInfo($param, $trans = true)
    {
        $msg = array();
        if ($trans) {
            $transaction = Yii::app()->db->beginTransaction();
        }
        try {

            $sql = 'SELECT * FROM {{distribution}} WHERE member_id = :member_id  FOR UPDATE';

            $info = self::model()->findAllBySql($sql, array(':member_id' => intval($param['memberId'])));
            if (empty($info)) {
                throw new CException('没有对应信息记录!');
            }
            $data = array();
            if(!empty($param['mobile']))
            {
                $data['mobile'] = $param['mobile'];
            }

           /* if(!empty($param['bind_store']))
            {
                $data['bind_store_id'] = $param['bind_store_id'];
            }*/

            if(!empty($data))
            {
                $rs = self::model()->updateByPk($info[0]['id'], $data);

                if ($rs < 0) {
                    throw new CException('修改信息失败!');
                }
            }

            //查询修改后的信息
            $dc_info = self::model()->findByPk($info[0]['id'])->attributes;
            $sdata = array();
            if(!empty($dc_info['bind_store']))
            {
                $sinfo = Supermarkets::model()->findByPk(intval($dc_info['bind_store']))->attributes;
                $sdata = array(
                    'store_id'   => $sinfo['id'],
                    'store_name' => $sinfo['name']
                );
            }
            //驻店信息
            $msg['result'] = array('mobile' => $dc_info['mobile'],'bind_store' => $sdata);
            $msg['msg'] = '';

            $transaction->commit();

        } catch (Exception $e) {
            $transaction->rollBack();
            $msg['result'] = false;
            $msg['msg'] = $e->getMessage();

        }
        return $msg;
    }

    /*
     * 在线状态修改接口
     * @param array $param
     * renturn obiect
     * @author yuanmei.chen
     */

    public function changeOnline($param, $trans = true)
    {
        if ($trans) {
            $transaction = Yii::app()->db->beginTransaction();
        }

        $msg = array();
        try {
            if (empty($param)) throw new Exception('参数缺失!');

            $sql = 'SELECT * FROM {{distribution}} WHERE member_id = :member_id  FOR UPDATE';

            $info = self::model()->findAllBySql($sql, array(':member_id' => intval($param['memberId'])));

            if (empty($info)) {
                throw new CException('没有对应信息记录!');
            }

            $msg['result'] = array();
            if (intval($param['status']) != $info[0]['status']) {
                $status = intval($info[0]['status']) == 0 ? 1 : 0;
                $rs = self::model()->updateByPk($info[0]['id'], array('status' => $status));
                if($rs)
                   $msg['result'] = array('status' => $status);
            }
            else {
                throw new CException('切换状态失败!');
            }

            $transaction->commit();
            $msg['msg'] = '';

        } catch (Exception $e) {

            $transaction->rollBack();
            $msg['result'] = false;
            $msg['msg'] = $e->getMessage();

        }

        return $msg;

    }


    /**
     * @desc 根据两点间的经纬度计算距离
     * @param float $lat 纬度值
     * @param float $lng 经度值
     */
    public static function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6367000; //approximate radius of earth in meters

        /*
        *Convert these degrees to  radians
        *to work with the formula
        */

        $lat1 = ($lat1 * pi()) / 180;
        $lng1 = ($lng1 * pi()) / 180;

        $lat2 = ($lat2 * pi()) / 180;
        $lng2 = ($lng2 * pi()) / 180;

        /*
        *Using the
        *Haversine formula
        */

        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;

        return round($calculatedDistance);
    }

    /**
     * 2      * 计算某个经纬度的周围某段距离的正方形的四个点
     * 3      *
     * 4      * @param
     * 5      *            radius 地球半径 平均6371km
     * 6      * @param
     * 7      *            lng float 经度
     * 8      * @param
     * 9      *            lat float 纬度
     * 10      * @param
     * 11      *            distance float 该点所在圆的半径，该圆与此正方形内切，默认值为1千米
     * 12      * @return array 正方形的四个点的经纬度坐标
     * 13      */
    public function returnSquarePoint($lng, $lat, $distance = 2, $radius = 6371)
    {
        $dlng = 2 * asin(sin($distance / (2 * $radius)) / cos(deg2rad($lat)));
        $dlng = rad2deg($dlng);

        $dlat = $distance / $radius;
        $dlat = rad2deg($dlat);

        return array(
            'left-top' => array(
                'lat' => $lat + $dlat,
                'lng' => $lng - $dlng
            ),
            'right-top' => array(
                'lat' => $lat + $dlat,
                'lng' => $lng + $dlng
            ),
            'left-bottom' => array(
                'lat' => $lat - $dlat,
                'lng' => $lng - $dlng
            ),
            'right-bottom' => array(
                'lat' => $lat - $dlat,
                'lng' => $lng + $dlng
            )
        );
    }


}
