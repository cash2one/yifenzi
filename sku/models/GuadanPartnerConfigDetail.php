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
class GuadanPartnerConfigDetail extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{guadan_partner_config_detail}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
//            array('partner_config_id', 'integerOnly'=>true),

            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, min_score, max_score, ratio,partner_config_id', 'safe'),
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
            'min_score' => '最小积分',
            'max_score' => '最大积分',
            'partner_config_id' => '商家积分批发政策表id',
            'ratio' => '出售的折扣',

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
