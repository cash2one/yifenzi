<?php

/**
 *  物流公司管理模型
 *  @author qinghao.ye <qinghaoye@sina.com>
 */
class YfzExpress extends CActiveRecord {

    public function tableName() {
        return '{{express}}';
    }

    #数据库连接

    public function getDbConnection()
    {
        return Yii::app()->gw;
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('name', 'required'),
            array('name', 'unique'),
            array('name', 'length', 'max' => 128),
            array('url', 'length', 'max' => 30),
            array('url', 'url'),
            array('code', 'length', 'max' => 64),
            array('id, name, url, code', 'safe', 'on' => 'search'),
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
            'name' => Yii::t('express', '公司名称'),
            'url' => Yii::t('express', '官方网址'),
            'code' => Yii::t('express', '物流编码'),
        );
    }

    public function search() {

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('url', $this->url, true);
        $criteria->compare('code', $this->code, true);

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
    
    /**
     * 物流下拉菜单
     * @return array
     */
    public static function getExpress() {
        $data = self::model()->findAll();
        $list=CHtml::listData($data, 'name', 'name');
        $newList=array();
        foreach ($list as $k=>$v ){
        	$newList[$k]=Yii::t('express',$v);
        }
        return $newList;
    }
    
	/**
	* 物流跳转地址
	* @return array
	*/
	public static function getExpressUrl(){
		$data = self::model()->findAll();
		
		$newList = array();
		foreach($data as $v){
			$newList[$v['name']] = $v['url'];
		}
		return $newList;
	}
}
