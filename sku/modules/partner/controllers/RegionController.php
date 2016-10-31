<?php

/**
 * 省份，城市，区县控制器类
 * 操作（实现省市区三级联动数据调用）
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class RegionController extends Controller {

    public function actionUpdateCity() {
        
        if ($this->isAjax() && $this->isPost()) {
            $this->layout = FALSE;
            $province_id = isset($_POST['province_id']) ? (int) $_POST['province_id'] : "9999999";
          
            if ($province_id) {
                $data = Region::model()->findAll('parent_id=:pid', array(':pid' => $province_id));
                $data = CHtml::listData($data, 'id', 'name');
            }
            $dropDownCities = "<option value=''>".Yii::t('partnerModule.region', '选择城市')."</option>";
            if (isset($data)) {
                foreach ($data as $value => $name)
                    $dropDownCities .= CHtml::tag('option', array('value' => $value), CHtml::encode(Yii::t('partnerModule.region', $name)), true);
            }
            $dropDownCounties = "<option value='null'>".Yii::t('partnerModule.region', '选择区/县')."</option>";
            echo CJSON::encode(array(
                'dropDownCities' => $dropDownCities,
                'dropDownCounties' => $dropDownCounties
            ));
            Yii::app()->end();
        }
    }

    public function actionUpdateArea() {
        if ($this->isPost()) {
            $city_id = isset($_POST['city_id']) ? (int) $_POST['city_id'] : "9999999";
            if ($city_id) {
                $data = Region::model()->findAll('parent_id=:pid', array(':pid' => $city_id));
                $data = CHtml::listData($data, 'id', 'name');
            }
            echo "<option value=''>".Yii::t('partnerModule.region', '选择区/县')."</option>";
            if ($city_id) {
                foreach ($data as $value => $name)
                    echo CHtml::tag('option', array('value' => $value), CHtml::encode(Yii::t('partnerModule.region', $name)), true);
            }
        }
    }
    
    
    
    /*
     * 查询坐标  根据地质
    *
    */
    public function actionSearchLocation($address) {
    
    	$url = BAIDU_MAP_LOCATION_API_URL.'?output=json&ak='.BAIDU_MAP_API_AK.'&address='.$address;
    
    	$rs = file_get_contents($url);
    
    	echo $rs;
    
    }

}
