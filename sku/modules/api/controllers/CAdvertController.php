<?php

/**
 * 广告控制器
 * 
 * @author leo8705
 *
 */
class CAdvertController extends CAPIController {

    /**
     * 获取广告列表
     * @param string $code_list 广告代码列表  json格式
     * 
     */
    public function actionGetList($code_list) {
    	
    }

    /**
     * 获取广告列表
     * @param string $code 广告代码
     * 
     */
    public function actionGetOne() {
        $tag=$this->id.ucfirst($this->action->id);
        $code = $this->getParam('code');
        
        $city = $this->getParam('cityId');								//城市
        $city_name = $this->getParam('cityName');		//城市名称
        
  
        if (empty($code)) {
            $this->_error(Yii::t('apiModule.advert','广告代码不能为空'),null,$tag);
        }
        $datas= array();
        $data = AppAdvert::getConventionalAdCache($code);
        
        $ad = Yii::app()->db->createCommand()
        ->select('width,height')
        ->from(AppAdvert::model()->tableName())
        ->where('code=:code',array(':code'=>$code))
        ->queryRow();
        
        $region = null;
        if (!empty($city)) {
        	$region = Yii::app()->db->createCommand()
        	->select('id,parent_id')
        	->from(Region::model()->tableName())
        	->where('id = :id',array(':id'=>$city))
        	->order('id')
        	->limit(1)
        	->queryRow();
        }elseif (!empty($city_name)) {
        	$region = Yii::app()->db->createCommand()
        	->select('id,parent_id')
        	->from(Region::model()->tableName())
        	->where('depth=2 AND name LIKE :name',array(':name'=>'%'.$city_name.'%'))
        	->order('id')
        	->limit(1)
        	->queryRow();
        }
        
        if (!empty($data)) {
            foreach($data as $k=>$v){

            	if (isset($region['id'])  && ( ($v['city_id']==0 && $v['province_id']!=0 && $v['province_id']!=$region['parent_id'] ) ||  ($v['city_id']!=0 && $v['city_id']!=$region['id']) )) {
            		continue;
            	}
            	
                $datas[$k] = $v;
                $datas[$k]['type'] = AppAdvert::getAppAdvertType($v['type']);
                $datas[$k]['target_type'] = $v['target_type'];
            }
            $rs['list'] = array_values($datas);
            $rs['sever_time'] = time();
            
            if (!empty($ad)) {
            	$rs['width'] = $ad['width'];
            	$rs['height'] = $ad['height'];
            }
            
            $this->_success($rs,$tag);
        } else {
            $this->_error(Yii::t('apiModule.advert','广告不存在或已失效'),null,$tag);
        }
    }

    /**
     * 首页广告列表
     * @param string $code 广告代码
     *
     */
    public function actionHomePage() {
        
    }

    /**
     * 首页广告列表
     * @param string $code 广告代码
     *
     */
    public function actionPayPage() {
        
    }

}
