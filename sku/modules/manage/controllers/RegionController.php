<?php

/**
 * 省份，城市，区县控制器类
 * 操作（实现省市区三级联动数据调用）
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class RegionController extends MController {


    /**
     * 不作权限控制的action
     * @return string
     */
    public function allowedActions() {
        return 'updateProvince, updateCity, updateArea, regionTree';
    }

    /**
     * 三级联动之获取省份
     */
    public function actionUpdateProvince() {
        if ($this->isAjax() && $this->isPost()) {
            $countriesId = $this->getPost('countries_id');
            $countriesId = $countriesId ? (int) $countriesId : "1000000000";
            $province = Region::model()->findAll(array(
                'select' => 'id, name',
                'condition' => 'parent_id=:pid',
                'params' => array(':pid' => $countriesId)));
            $cities = CHtml::listData($province, 'id', 'name');
            $dropDownProvinces = "<option value=''>" . Yii::t('region', '选择省份') . "</option>";
            if (!empty($cities)) {
                foreach ($cities as $id => $name)
                    $dropDownProvinces .= CHtml::tag('option', array('value' => $id), $name, true);
            }
            $dropDownCities = "<option value=''>" . Yii::t('region', '选择城市') . "</option>";
            $dropDownCounties = "<option value=''>" . Yii::t('region', '选择区/县') . "</option>";
            echo CJSON::encode(array(
                'dropDownProvinces' => $dropDownProvinces,
                'dropDownCities' => $dropDownCities,
                'dropDownCounties' => $dropDownCounties,
            ));
        }
    }

    /**
     * 三级联动之获取城市
     */
    public function actionUpdateCity() {
        if ($this->isAjax() && $this->isPost()) {             
            $provinceId = $this->getPost('province_id');       
            $provinceId = $provinceId ? (int) $provinceId : "1000000000";
            $regions = Region::model()->findAll(array(
                'select' => 'id, name',
                'condition' => 'parent_id=:pid',
                'params' => array(':pid' => $provinceId)));
            $cities = CHtml::listData($regions, 'id', 'name');
            $dropDownCities = "<option value=''>" . Yii::t('region', '选择城市') . "</option>";
            if (!empty($cities)) {
                foreach ($cities as $id => $name)
                    $dropDownCities .= CHtml::tag('option', array('value' => $id), $name, true);
            }
            $dropDownCounties = "<option value=''>" . Yii::t('region', '选择区/县') . "</option>";
            echo CJSON::encode(array(
                'dropDownCities' => $dropDownCities,
                'dropDownCounties' => $dropDownCounties
            ));
        }
    }

    /**
     * 三级联动之获取区、县
     */
    public function actionUpdateArea() {
        if ($this->isAjax() && $this->isPost()) {
            $cityId = $this->getPost('city_id');
            $cityId = $cityId ? (int) $cityId : "1000000000";
            $regions = Region::model()->findAll(array(
                'select' => 'id, name',
                'condition' => 'parent_id=:pid',
                'params' => array(':pid' => $cityId)));
            $districts = CHtml::listData($regions, 'id', 'name');
            echo "<option value=''>" . Yii::t('region', '选择区/县') . "</option>";
            if (!empty($districts)) {
                foreach ($districts as $id => $name)
                    echo CHtml::tag('option', array('value' => $id), $name, true);
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
