<?php

/**
 *  物流公司管理模型
 *  @author qinghao.ye <qinghaoye@sina.com>
 */
class YfzMemberIp extends CActiveRecord {

    public function tableName() {
        return '{{member_ip}}';
    }
    #数据库连接
    public function getDbConnection()
    {
        return Yii::app()->gwpart;
    }
    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('id, member_id, ip_address, addtime', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
        );
    }

    public function search() {

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 20, //分页
            ),
            'sort' => array(
                //'defaultOrder'=>' DESC', //设置默认排序
            ),
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    /*
 * 设置用户ip到表
 * @param type $member_id
 *
 * */
    public static function setMemberIp($member_id){
        $ip_address = Tool::getClientIP();
        if(!empty($member_id)){
            $data = array(
                'member_id' => $member_id,
                'ip_address' => $ip_address,
                'addtime' => time(),
            );
            $memberInfo = Yii::app()->gwpart->createCommand()
                ->select("id")
                ->from('{{member_ip}}')
                ->where("member_id = ".$member_id)
                ->queryScalar();
            if(empty($memberInfo)){
                Yii::app()->gwpart->createCommand()->insert(self::model()->tableName(), $data);
            }else{
                Yii::app()->gwpart->createCommand()->update(self::model()->tableName(), array(
                    "ip_address" => $ip_address,
                    "addtime" =>time(),
                ), "id=:id and member_id=:member_id", array(
                    "id" => $memberInfo,
                    "member_id"=>$member_id
                ));
            }
        }
    }
}
