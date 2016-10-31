<?php
/**
 * Created by PhpStorm.
 * User: zsj
 * Date: 2015/11/4
 * Time: 10:55
 */

class FreshQuestResult extends CActiveRecord
{
    public $isExport; //是否导出excel
    public $start_time;
    public $end_time;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{fresh_quest_result}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name,type,mobile,data', 'required'),
            array('id,name,type,mobile,data,create_time,start_time,end_time', 'safe'),
            array('mobile,name', 'length', 'max' => 20),
//            array('mobile', 'match', 'pattern' => '/(^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$)|(^((\(\d{3}\))|(\d{3}\-))?(1[358]\d{9})$)/', 'message' => Yii::t('partner', '请填写正确的手机号码或电话号码')),
//            array('mobile', 'unique'),

        );
    }
    /**
     * 获取问卷问题类型
     * @param int $key
     * @return string|array
     */
    const TYPE_ZHAOSHANG = 0;
    const TYPE_ZHUANGJI = 1;

    public static function getType($key = false) {
        $type = array(
            self::TYPE_ZHAOSHANG => Yii::t('appAdvert', '入驻申请'),
            self::TYPE_ZHUANGJI => Yii::t('appAdvert', '装机申请'),
        );
        if ($key === false)
            return $type;
        return $type[$key];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'type'=>'申请类型',
            'name' => '姓名',
            'mobile'=>'手机号码',
            'data' => '答案',
            'create_time' => '提交时间',
            'start_time'=>'start_time'
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
        $criteria->compare('mobile',$this->mobile,true);
        $criteria->compare('type',$this->type,true);
        if ($this->start_time)$criteria->compare('create_time', ">=" .strtotime($this->start_time) );
        if ($this->end_time)$criteria->compare('create_time', "<" . strtotime($this->end_time));
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'sort' => array(
                'defaultOrder' => 'create_time DESC',
            ),
        ));
    }
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Address the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    public function resultSearch(){
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;
        $criteria->compare('id',$this->id,true);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('mobile',$this->mobile,true);
        $criteria->compare('type',$this->type,true);
        if ($this->start_time)$criteria->compare('create_time', ">=" .strtotime($this->start_time) );
        if ($this->end_time)$criteria->compare('create_time', "<" . strtotime($this->end_time));
        $dataProvider = self::model()->findAll($criteria);
        $list = array();
        foreach($dataProvider as $k =>$v){
            $listData = array();
            foreach($v->attributes as $key => $val){

                if($key == 'data'){
                    $quest = unserialize($val);
                    foreach($quest as $k3=>$v3){
                        if(is_array($v3)){
                            $str ="";
                            foreach($v3 as $k4 =>$v4){
                                $str .= $v4."、";
                            }
                            $str = mb_substr($str, 0, -1, 'utf-8');
                            $listData[$k3] = $str;


                        }else{
                            $listData[$k3] = $v3;
                        }
                    }
                }else{
                    $listData[$key] = $val;
                }
            }
            $list[] = $listData;
        }
        return $list;
    }

    /**
     *获取所在城市
     */
    public static function getCity($data){
        $data = unserialize($data);
        $data = array_values($data);
      
        return $city = !empty($data[0])?$data[0]:'';

    }
}
