<?php

/**
 * This is the model class for table "{{guadan_partner_setting}}".
 *
 * The followings are the available columns in table '{{guadan_partner_setting}}':
 * @property integer $id
 * @property string $partner_id
 * @property integer $member_id
 * @property integer $selling_discount
 */
class GuadanPartnerConfig extends CActiveRecord
{
     const STATUS_ENABLE = 1; //启用
    const STATUS_END = 2;//终止
     /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{guadan_partner_config}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, limit_score, status, distribution_ratio,create_time,update_time', 'safe'),
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
            'limit_score' => '全国商家限额的积分',
            'status' => '是否启用',
            'distribution_ratio' => '商家推荐折扣百分比',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
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

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return GuadanPartnerSetting the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
