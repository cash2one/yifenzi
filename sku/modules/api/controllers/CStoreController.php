<?php

/**
 * 盖付通接口控制器
 * 
 * @author leo8705
 *
 */
class CStoreController extends CAPIController {

    /**
     * 获取超市店铺列表
     *
     *
     */
    public function actionSuperList() {
        $tag_name = 'StoreLists';

        $lat = $this->getParam('lat');   //经度
        $lng = $this->getParam('lng');   //纬度
        $default_distance = Tool::getConfig('site','api_distance')*1;
        $distance = $default_distance>0?$default_distance:($this->getParam('distance') ? $this->getParam('distance') : 1000);   //距离
        $lastId = $this->getParam('lastId') ? $this->getParam('lastId') : -1;   //最新的id
        $page = $this->getParam('page') ? $this->getParam('page') : 1;
        $pageSize = $this->getParam('pageSize') ? $this->getParam('pageSize') : 20;
        $store_cate_id = $this->getParam('store_cate_id'); //分类
        
        $cri = new CDbCriteria();
        $cri->select = 't.id,t.name,t.mobile,CONCAT("' . ATTR_DOMAIN . '/",t.logo) as logo,t.type,t.province_id,t.city_id,t.district_id,t.street,t.zip_code,t.lng,t.lat,t.is_delivery,t.category_id,t.delivery_mini_amount,t.delivery_fee,t.star,t.open_time,t.delivery_time,t.is_recommend,t.max_amount_preday';
        $cri->compare('t.status', Supermarkets::STATUS_ENABLE);
        $cri->join = ' LEFT JOIN  '.  Partners::model()->tableName().' AS p ON t.partner_id=p.id ';
        $cri->compare('p.status',  Partners::STATUS_ENABLE);
         if(!empty($store_cate_id)){
            $cri->compare('category_id',$store_cate_id);
        }
        
        if (!empty($lat) && !empty($lng)) {
            $vicinity_rs = Tool::GetRange($lat, $lng, $distance);
            if ($vicinity_rs['maxLat'] > $vicinity_rs['minLat']) {
                $cri->addBetweenCondition('lat', $vicinity_rs['minLat'], $vicinity_rs['maxLat']);
            } else {
                $cri->addBetweenCondition('lat', $vicinity_rs['maxLat'], $vicinity_rs['minLat']);
            }
            if ($vicinity_rs['maxLng'] > $vicinity_rs['minLng']) {
                $cri->addBetweenCondition('lng', $vicinity_rs['minLng'], $vicinity_rs['maxLng']);
            } else {
                $cri->addBetweenCondition('lng', $vicinity_rs['maxLng'], $vicinity_rs['minLng']);
            }
        }

        if ($lastId > 0) {
            $cri->addCondition('id>' . $lastId);
        }
        //分页
        $cri->limit = $pageSize;
        $cri->offset = ($page - 1) * $pageSize;
        $list = Supermarkets::model()->findAll($cri);
        if (empty($list)) {
            $this->_success(null, $tag_name);
        }
        if (!empty($list)) {
            $list = Supermarkets::model()->findAll($cri);
            $data = array();
            foreach ($list as $k => $v) {
                $data[$k] = $v->attributes;
                $data[$k]['province_name'] = Region::getName($v->province_id);
                $data[$k]['city_name'] = Region::getName($v->city_id);
                $data[$k]['district_name'] = Region::getName($v->district_id);
            }
        }


        $data_list = array();
        $data_list['list'] = $data;
        $cri = new CDbCriteria();
        $cri->compare('status', Supermarkets::STATUS_ENABLE);
        if (!empty($lat) && !empty($lng)) {
            $vicinity_rs = Tool::vicinity($lng, $lat, $distance);
            $cri->addBetweenCondition('lat', $vicinity_rs['lat']['bottom'], $vicinity_rs['lat']['top']);
            $cri->addBetweenCondition('lng', $vicinity_rs['lng']['right'], $vicinity_rs['lng']['left']);
        }
        $total = Supermarkets::model()->count($cri);
        $data_list['listCount'] = $total;
        $data_list['lastId'] = $lastId;
        $this->_success($data_list, $tag_name);
    }

    /**
     * 获取售货机列表
     *
     *
     */
    public function actionMachineList() {
        $tag_name = 'MachineList';
        $lat = $this->getParam('lat');   //经度
        $lng = $this->getParam('lng');   //纬度
        $default_distance = Tool::getConfig('site','api_distance')*1;
        $distance = $default_distance>0?$default_distance:($this->getParam('distance') ? $this->getParam('distance') : 1000);   //距离
        $lastId = $this->getParam('lastId') ? $this->getParam('lastId') : -1;   //最新的id
        $store_cate_id = $this->getParam('store_cate_id'); //分类
        
        $page = $this->getParam('page') ? $this->getParam('page') : 1;
        $pageSize = $this->getParam('pageSize') ? $this->getParam('pageSize') : 20;

        $cri = new CDbCriteria();
        $cri->select = 't.id,t.name,t.province_id,t.city_id,t.district_id,t.address,t.lng,t.lat,CONCAT("' . ATTR_DOMAIN . '/",t.thumb) as thumb';
        $cri->compare('t.status', VendingMachine::STATUS_ENABLE);
        $cri->join = ' LEFT JOIN  '.  Partners::model()->tableName().' AS p ON t.partner_id=p.id ';
        $cri->compare('p.status',  Partners::STATUS_ENABLE);
          if(!empty($store_cate_id)){
            $cri->compare('category_id',$store_cate_id);
        }

        if (!empty($lat) && !empty($lng)) {
            $vicinity_rs = Tool::GetRange($lat, $lng, $distance);
            if ($vicinity_rs['maxLat'] > $vicinity_rs['minLat']) {
                $cri->addBetweenCondition('lat', $vicinity_rs['minLat'], $vicinity_rs['maxLat']);
            } else {
                $cri->addBetweenCondition('lat', $vicinity_rs['maxLat'], $vicinity_rs['minLat']);
            }
            if ($vicinity_rs['maxLng'] > $vicinity_rs['minLng']) {
                $cri->addBetweenCondition('lng', $vicinity_rs['minLng'], $vicinity_rs['maxLng']);
            } else {
                $cri->addBetweenCondition('lng', $vicinity_rs['maxLng'], $vicinity_rs['minLng']);
            }
        }


        if ($lastId > 0) {
            $cri->addCondition('id>' . $lastId);
        }
        //分页
        $cri->limit = $pageSize;
        $cri->offset = ($page - 1) * $pageSize;

        $list = VendingMachine::model()->findAll($cri);
        $data = array();
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $data[$k] = $v->attributes;
                $data[$k]['province_name'] = Region::getName($v->province_id);
                $data[$k]['city_name'] = Region::getName($v->city_id);
                $data[$k]['district_name'] = Region::getName($v->district_id);
            }
        }
        $data_list = array();
        $data_list['list'] = $data;
        $cri = new CDbCriteria();
        $cri->compare('status', VendingMachine::STATUS_ENABLE);
        if (!empty($lat) && !empty($lng)) {
            $vicinity_rs = Tool::vicinity($lng, $lat, $distance);
            $cri->addBetweenCondition('lat', $vicinity_rs['lat']['bottom'], $vicinity_rs['lat']['top']);
            $cri->addBetweenCondition('lng', $vicinity_rs['lng']['left'], $vicinity_rs['lng']['right']);
        }
        $total = VendingMachine::model()->count($cri);
        $data_list['listCount'] = $total;
        $data_list['lastId'] = $lastId;
        $this->_success($data_list, $tag_name);
    }

    /*
     * 超市、售货机列表
     */

    public function actionListV2() {
        $tag_name = 'StoreList';
        $lat = $this->getParam('lat');   //经度
        $lng = $this->getParam('lng');   //纬度
        $store_cate_id = $this->getParam('cateId'); //分类
        $default_distance = Tool::getConfig('site','api_distance')*1;
        $distance = $default_distance>0?$default_distance:($this->getParam('distance') ? $this->getParam('distance') : 1000);   //距离
        $page = $this->getParam('page') ? $this->getParam('page') : 1;
        $pageSize = $this->getParam('pageSize') ? $this->getParam('pageSize') : 20;
        $isRecommend = $this->getParam('isRecommend'); 	//是否推荐
        
        $withFresh = $this->getParam('withFresh');   //	是否查询生鲜机
        $withFresh = true;

        $mysql_select = 't.stype,
        							s.id as s_id,s.name as s_name,s.mobile as s_mobile, CONCAT("' . ATTR_DOMAIN . '/",s.logo) as s_logo,s.province_id as s_province_id,s.city_id as s_city_id,s.district_id as s_district_id,s.street as s_street,s.zip_code as s_zip_code,s.lng as s_lng,s.lat as s_lat,s.is_delivery as s_is_delivery,s.category_id as s_category_id,s.delivery_mini_amount as s_delivery_mini_amount,s.delivery_start_amount as s_delivery_start_amount,s.delivery_fee as s_delivery_fee,s.star as s_star,s.open_time as s_open_time,s.delivery_time as s_delivery_time,s.is_recommend as s_is_recommend,s.max_amount_preday as s_max_amount_preday,s.status as s_status,
        							m.id as m_id,m.name as m_name, CONCAT("' . ATTR_DOMAIN . '/",m.thumb) as m_thumb,m.province_id as m_province_id,m.category_id as m_category_id,m.city_id as m_city_id,m.district_id as m_district_id,m.address as m_address,m.lng as m_lng,m.lat as m_lat,m.status as m_status';

        if (!empty($withFresh)) $mysql_select .=',f.id as f_id,f.name as f_name, CONCAT("' . ATTR_DOMAIN . '/",f.thumb) as f_thumb,f.province_id as f_province_id,f.category_id as f_category_id,f.city_id as f_city_id,f.district_id as f_district_id,f.address as f_address,f.lng as f_lng,f.lat as f_lat,f.status as f_status';
        
        $conditions = ' 1=1 ';
        $order = '';
        if (!empty($lat) && !empty($lng)) {
        
        	$vicinity_rs = Tool::GetRange($lat, $lng, $distance);
        	
        	if ($vicinity_rs['maxLat'] > $vicinity_rs['minLat']) {
        		$conditions .= ' AND  ( t.lat  BETWEEN "'.$vicinity_rs['minLat'].'" AND "'.$vicinity_rs['maxLat'].'") ';
        	} else {
        		$conditions .= ' AND  ( t.lat  BETWEEN "'.$vicinity_rs['maxLat'].'" AND "'.$vicinity_rs['minLat'].'") ';
        	}
        	if ($vicinity_rs['maxLng'] > $vicinity_rs['minLng']) {

        		$conditions .= ' AND  ( t.lng  BETWEEN "'.$vicinity_rs['minLng'].'" AND "'.$vicinity_rs['maxLng'].'") ';
        	} else {
        		$conditions .= ' AND  ( t.lng  BETWEEN "'.$vicinity_rs['maxLng'].'" AND "'.$vicinity_rs['minLng'].'") ';
        	}
        	
        	$mysql_select .= ',getDistance('.$lng.','.$lat.',t.lng,t.lat) as distance  ';
        	
        	$order = 'distance ASC';
        	
        }
        
        $conditions .= ' AND t.status ='.Stores::STATUS_ENABLE;
        
        if (empty($withFresh)) {
        	$conditions .= ' AND t.stype IN ( '.implode(',', array(Stores::SUPERMARKETS,Stores::MACHINE)).') ';
        }
        
        $conditions .= ' AND p.status ='.  Partners::STATUS_ENABLE;
        
        if(!empty($store_cate_id)){
        	$conditions .= ' AND ( s.category_id ='.$store_cate_id.' OR  m.category_id ='.$store_cate_id.' ) ';
        }
        
        if(!empty($isRecommend)){
        	$conditions .= ' AND ( s.is_recommend ='.Supermarkets::RECOMMEND_YES.' OR  m.is_recommend ='.VendingMachine::RECOMMEND_YES.' ) ';
        }
        
        
        $data = Yii::app()->db->createCommand()
        				->select($mysql_select)
        				->where($conditions)
        				->from(Stores::model()->tableName().' as t')
        				->leftJoin(Supermarkets::model()->tableName().' as s', 't.target_id=s.id')
        				->leftJoin(VendingMachine::model()->tableName().' as m', 't.target_id=m.id')
                        ->leftJoin(Partners::model()->tableName().' as p', 'm.partner_id=p.id or s.partner_id=p.id');
//                                                                      ->leftJoin(Partners::model()->tableName().' as p', 's.partner_id=p.id') 
        if (!empty($withFresh)) $data = $data->leftJoin(FreshMachine::model()->tableName().' as f', 't.target_id=f.id');
        
        $data = $data->order($order)
        				->group('t.id')
        				->limit($pageSize)
        				->offset(($page - 1) * $pageSize)
        				->queryAll();

        $list = array();
        foreach ($data as $val){
        	$temp_arr = array();
        	$temp_arr['stype'] = $val['stype'];
        	$temp_arr['distance'] = isset($val['distance'])?round($val['distance']):0;
        	
        	if ($val['stype']==Stores::SUPERMARKETS) {
//         		if ($val['s_status']!=Supermarkets::STATUS_ENABLE) {
//         			continue;
//         		}
        		$temp_arr['id'] = $val['s_id'];
        		$temp_arr['name'] = $val['s_name'];
        		$temp_arr['mobile'] = $val['s_mobile'];
        		$temp_arr['logo'] = $val['s_logo'];
        		$temp_arr['province_id'] = $val['s_province_id'];
        		$temp_arr['city_id'] = $val['s_city_id'];
        		$temp_arr['district_id'] = $val['s_district_id'];
        		$temp_arr['street'] = $val['s_street'];
        		$temp_arr['zip_code'] = $val['s_zip_code'];
        		$temp_arr['lng'] = $val['s_lng'];
        		$temp_arr['lat'] = $val['s_lat'];
        		$temp_arr['is_delivery'] = $val['s_is_delivery'];
        		$temp_arr['category_id'] = $val['s_category_id'];
        		$temp_arr['delivery_mini_amount'] = $val['s_delivery_mini_amount'];
        		$temp_arr['delivery_start_amount'] = $val['s_delivery_start_amount'];
        		$temp_arr['delivery_fee'] = $val['s_delivery_fee'];
        		$temp_arr['star'] = $val['s_star'];
        		$temp_arr['open_time'] = $val['s_open_time'];
        		$temp_arr['delivery_time'] = $val['s_delivery_time'];
        		$temp_arr['is_recommend'] = $val['s_is_recommend'];
        		$temp_arr['max_amount_preday'] = $val['s_max_amount_preday'];
        		$temp_arr['status'] = $val['s_status'];
        		
        	}elseif ($val['stype']==Stores::MACHINE ){
        		$temp_arr['id'] = $val['m_id'];
        		$temp_arr['name'] = $val['m_name'];
        		$temp_arr['thumb'] = $val['m_thumb'];
        		$temp_arr['province_id'] = $val['m_province_id'];
        		$temp_arr['city_id'] = $val['m_city_id'];
        		$temp_arr['district_id'] = $val['m_district_id'];
        		$temp_arr['category_id'] = $val['m_category_id'];
        		$temp_arr['address'] = $val['m_address'];
        		$temp_arr['lng'] = $val['m_lng'];
        		$temp_arr['lat'] = $val['m_lat'];
        		$temp_arr['status'] = $val['m_status'];
        	}elseif ($val['stype']==Stores::FRESH_MACHINE){
        		$temp_arr['id'] = $val['f_id'];
        		$temp_arr['name'] = $val['f_name'];
        		$temp_arr['thumb'] = $val['f_thumb'];
        		$temp_arr['province_id'] = $val['f_province_id'];
        		$temp_arr['city_id'] = $val['f_city_id'];
        		$temp_arr['district_id'] = $val['f_district_id'];
        		$temp_arr['category_id'] = $val['f_category_id'];
        		$temp_arr['address'] = $val['f_address'];
        		$temp_arr['lng'] = $val['f_lng'];
        		$temp_arr['lat'] = $val['f_lat'];
        		$temp_arr['status'] = $val['f_status'];
        	}
        	
        	$temp_arr['province_name'] = Region::getName($temp_arr['province_id']);
        	$temp_arr['city_name'] = Region::getName($temp_arr['city_id']);
        	$temp_arr['district_name'] = Region::getName($temp_arr['district_id']);
        	
        	
        	if (!isset($list[$temp_arr['stype'].'_'.$temp_arr['id']])) $list[$temp_arr['stype'].'_'.$temp_arr['id']] = $temp_arr;
        	
        }
        
        $list = array_values($list);
//         //根据距离排序
//         $sort_list = array();
//         if (!empty($lat) && !empty($lng)) {
//             foreach ($list as $s) {
//                 $s['distance'] = Tool::GetDistance($lat, $lng, $s['lat'], $s['lng']);
//                 if (isset($sort_list[$s['distance']])) {
//                     $sort_list[(string) ($s['distance'] + rand(0.001,0.099))] = $s;
//                 } else {
//                     $sort_list[(string) $s['distance']] = $s;
//                 }
//             }
//             ksort($sort_list);
//             $sort_list = array_values($sort_list);
//         } else {
//             $sort_list = $list;
//         }
        $this->_success($list, $tag_name);
    }
    
    /*
     * 超市、售货机列表
    */
    
public function actionList() {
        $tag_name = 'StoreList';
        $lat = $this->getParam('lat');   //经度
        $lng = $this->getParam('lng');   //纬度
        $store_cate_id = $this->getParam('store_cate_id'); //分类

        $default_distance = Tool::getConfig('site','api_distance')*1;
        $distance = $default_distance>0?$default_distance:($this->getParam('distance') ? $this->getParam('distance') : 1000);   //距离

        $page = $this->getParam('page') ? $this->getParam('page') : 1;
        $pageSize = $this->getParam('pageSize') ? $this->getParam('pageSize') : 999999999;

        $superCri = new CDbCriteria();
        $superCri->select = 't.id,t.name,t.mobile, CONCAT("' . ATTR_DOMAIN . '/",t.logo) as logo,t.type,t.province_id,t.city_id,t.district_id,t.street,t.zip_code,t.lng,t.lat,t.is_delivery,t.category_id,t.delivery_mini_amount,t.delivery_fee,t.star,t.open_time,t.delivery_time,t.is_recommend,t.max_amount_preday';
        $superCri->compare('t.status', Supermarkets::STATUS_ENABLE);
        $superCri->join = ' LEFT JOIN  '.  Partners::model()->tableName().' AS p ON t.partner_id=p.id ';
        $superCri->compare('p.status',  Partners::STATUS_ENABLE);
        if(!empty($store_cate_id)){
            $superCri->compare('category_id',$store_cate_id);
        }
        if (!empty($lat) && !empty($lng)) {

            $vicinity_rs = Tool::GetRange($lat, $lng, $distance);

            if ($vicinity_rs['maxLat'] > $vicinity_rs['minLat']) {
                $superCri->addBetweenCondition('lat', $vicinity_rs['minLat'], $vicinity_rs['maxLat']);
            } else {
                $superCri->addBetweenCondition('lat', $vicinity_rs['maxLat'], $vicinity_rs['minLat']);
            }
            if ($vicinity_rs['maxLng'] > $vicinity_rs['minLng']) {
                $superCri->addBetweenCondition('lng', $vicinity_rs['minLng'], $vicinity_rs['maxLng']);
            } else {
                $superCri->addBetweenCondition('lng', $vicinity_rs['maxLng'], $vicinity_rs['minLng']);
            }
        }
        
        //分页
        $superCri->limit = $pageSize;
        $superCri->offset = ($page - 1) * $pageSize;

        $superList = Supermarkets::model()->findAll($superCri);
        $superArray = array();
        if (!empty($superList)) {
            foreach ($superList as $key => $v) {
                $superArray[$key] = $v->attributes;
                foreach ($superArray[$key] as $k => $val) {
                    if ($val === null) {
                        unset($superArray[$key][$k]);
                    }
                }
                $superArray[$key]['stype'] = Order::TYPE_SUPERMARK;
                $superArray[$key]['province_name'] = Region::getName($v->province_id);
                $superArray[$key]['city_name'] = Region::getName($v->city_id);
                $superArray[$key]['district_name'] = Region::getName($v->district_id);
            }
        }


        $machineCri = new CDbCriteria();
        $machineCri->select = 't.id,t.name, CONCAT("' . ATTR_DOMAIN . '/",t.thumb) as thumb,t.province_id,t.category_id,t.city_id,t.district_id,t.address,t.lng,t.lat';
        $machineCri->compare('t.status', VendingMachine::STATUS_ENABLE);
        $machineCri->join = ' LEFT JOIN  '.  Partners::model()->tableName().' AS p ON t.partner_id=p.id ';
        $machineCri->compare('p.status',  Partners::STATUS_ENABLE);
         if(!empty($store_cate_id)){
            $machineCri->compare('category_id',$store_cate_id);
        }
        if (!empty($lat) && !empty($lng)) {
            if ($vicinity_rs['maxLat'] > $vicinity_rs['minLat']) {
                $machineCri->addBetweenCondition('lat', $vicinity_rs['minLat'], $vicinity_rs['maxLat']);
            } else {
                $machineCri->addBetweenCondition('lat', $vicinity_rs['maxLat'], $vicinity_rs['minLat']);
            }
            if ($vicinity_rs['maxLng'] > $vicinity_rs['minLng']) {
                $machineCri->addBetweenCondition('lng', $vicinity_rs['minLng'], $vicinity_rs['maxLng']);
            } else {
                $machineCri->addBetweenCondition('lng', $vicinity_rs['maxLng'], $vicinity_rs['minLng']);
            }
        }

        //分页
        $machineCri->limit = $pageSize;
        $machineCri->offset = ($page - 1) * $pageSize;
        $machineList = VendingMachine::model()->findAll($machineCri);

        $machineArray = array();
        if (!empty($machineList)) {
            foreach ($machineList as $key => $v) {
                $machineArray[$key] = $v->attributes;
                foreach ($machineArray[$key] as $k => $val) {
                    if ($val === null) {
                        unset($machineArray[$key][$k]);
                    }
                }
                $machineArray[$key]['stype'] = Order::TYPE_MACHINE;
                $machineArray[$key]['province_name'] = Region::getName($v->province_id);
                $machineArray[$key]['city_name'] = Region::getName($v->city_id);
                $machineArray[$key]['district_name'] = Region::getName($v->district_id);
            }
        }

        $list = array_merge($superArray, $machineArray); //合拼超市、售货机数据
        //根据距离排序
        $sort_list = array();
        if (!empty($lat) && !empty($lng)) {
            foreach ($list as $s) {
                $s['distance'] = Tool::GetDistance($lat, $lng, $s['lat'], $s['lng']);
                if (isset($sort_list[$s['distance']])) {
                    $sort_list[(string) ($s['distance'] + rand(0.001,0.099))] = $s;
                } else {
                    $sort_list[(string) $s['distance']] = $s;
                }
            }
            ksort($sort_list);
            $sort_list = array_values($sort_list);
        } else {
            $sort_list = $list;
        }
        $this->_success($sort_list, $tag_name);
    }

    /**
     * 店铺分类
     */
    public function actionStoreCategory() {
        $tag_name = 'StoreCategory';
        $list = StoreCategory::getCategoryList();
        $list = array_values($list);
        $this->_success($list, $tag_name);
    }
    
    
    /**
     * 生鲜机列表
     */
    public function actionFreshMachineList() {
    	$tag_name = 'StoreList';
    	$lat = $this->getParam('lat');   //经度
    	$lng = $this->getParam('lng');   //纬度
    	$default_distance = Tool::getConfig('site','api_distance')*1;
        $distance = $default_distance>0?$default_distance:($this->getParam('distance') ? $this->getParam('distance') : 1000);   //距离
    	$lastId = $this->getParam('lastId') ? $this->getParam('lastId') : -1;   //最新的id
    	$store_cate_id = $this->getParam('store_cate_id'); //分类
    
    	
    	$is_one = $this->getParam('is_one');						//是否一元购
    	$is_for = $this->getParam('is_for');							//是否促销
    	$is_promo = $this->getParam('is_promo');				//是否促销
    	
    	
    	$page = $this->getParam('page') ? $this->getParam('page') : 1;
    	$pageSize = $this->getParam('pageSize') ? $this->getParam('pageSize') : 20;
    
    	$cri = new CDbCriteria();
    	$cri->select = 't.id,t.name,t.province_id,t.city_id,t.district_id,t.address,t.lng,t.lat,t.type,CONCAT("' . ATTR_DOMAIN . '/",t.thumb) as thumb ';
    	if (!empty($lat) && !empty($lng)) $cri->select .= ',getDistance('.$lng.','.$lat.',t.lng,t.lat) as distance';
    	$cri->compare('t.status', FreshMachine::STATUS_ENABLE);
        $cri->join .= ' LEFT JOIN  '.  Partners::model()->tableName().' AS p ON t.partner_id=p.id ';
        $cri->compare('p.status',  Partners::STATUS_ENABLE);
    	if(!empty($store_cate_id)){
    		$cri->compare('category_id',$store_cate_id);
    	}
    
    	if (!empty($lat) && !empty($lng)) {
    		$vicinity_rs = Tool::GetRange($lat, $lng, $distance);
    		if ($vicinity_rs['maxLat'] > $vicinity_rs['minLat']) {
    			$cri->addBetweenCondition('lat', $vicinity_rs['minLat'], $vicinity_rs['maxLat']);
    		} else {
    			$cri->addBetweenCondition('lat', $vicinity_rs['maxLat'], $vicinity_rs['minLat']);
    		}
    		if ($vicinity_rs['maxLng'] > $vicinity_rs['minLng']) {
    			$cri->addBetweenCondition('lng', $vicinity_rs['minLng'], $vicinity_rs['maxLng']);
    		} else {
    			$cri->addBetweenCondition('lng', $vicinity_rs['maxLng'], $vicinity_rs['minLng']);
    		}
    	}
    
    
    	if ($lastId > 0) {
    		$cri->addCondition('id>' . $lastId);
    	}
    	
    	
    	$cri->join .= ' LEFT JOIN  '.FreshMachineGoods::model()->tableName().' as sg  ON sg.machine_id = t.id ';
    	$cri->join .= ' LEFT JOIN  '.Goods::model()->tableName().' as g  ON sg.goods_id = g.id ';
    	
    	$cri->addCondition('g.status='.Goods::STATUS_PASS.' AND sg.status='.FreshMachineGoods::STATUS_ENABLE);
    	$cri->compare('g.is_one', $is_one);
    	$cri->compare('g.is_for', $is_for);
    	$cri->compare('g.is_promo', $is_promo);
    	
    	$cri->group = 't.id';
    	
    	//分页
    	if (!empty($lat) && !empty($lng)) $cri->order = 'distance ASC';
    	$cri->limit = $pageSize;
    	$cri->offset = ($page - 1) * $pageSize;
    
    	$list = FreshMachine::model()->findAll($cri);

    	$data = array();
    	$sids = array();
    	if (!empty($list)) {
    		foreach ($list as $k => $v) {
    			$sids[] = $v['id'];
    			$data[$k] = $v->attributes;
    			$data[$k]['province_name'] = Region::getName($v->province_id);
    			$data[$k]['city_name'] = Region::getName($v->city_id);
    			$data[$k]['district_name'] = Region::getName($v->district_id);
                $data[$k]['stype'] = ($v->type==FreshMachine::FRESH_MACHINE_SMALL)?Stores::FRESH_MACHINE_SMALL:Stores::FRESH_MACHINE;
                $data[$k]['distance'] = Tool::GetDistance($lat,$lng,$v['lat'],$v['lng']);
                $data[$k]['is_one'] = Goods::IS_NOT_ONE;
                $data[$k]['is_for'] = Goods::IS_NOT_FOR;
                $data[$k]['is_promo'] = Goods::IS_NOT_PROMO;
    		}
    	
	    	//判断店铺是否有参加活动
	    	$is_list = Yii::app()->db->createCommand()
	    	->select('sg.machine_id,g.is_one,g.is_for,g.is_promo')
	    	->from(Goods::model()->tableName().' as g')
	    	->leftJoin(FreshMachine::model()->tableName().' as t', 't.partner_id=g.partner_id')
	    	->leftJoin(FreshMachineGoods::model()->tableName().' as sg', 'sg.goods_id=g.id')
	    	->where(' ( g.is_one='.Goods::IS_ONE.' OR g.is_for='.Goods::IS_FOR.' OR  g.is_promo='.Goods::IS_PROMO.' ) AND t.partner_id=g.partner_id AND sg.machine_id=t.id AND t.id IN ('.implode(',', $sids).')  AND g.status='.Goods::STATUS_PASS.' AND sg.status='.FreshMachineGoods::STATUS_ENABLE)
	    	->queryAll();
	    	
    		foreach ($data as $k=>$dv){
    			if (!empty($is_list)) {
	    			foreach ($is_list as $lv){
	    				 if ($dv['id']==$lv['machine_id'] && $lv['is_one']==Goods::IS_ONE) {
	    				 	$data[$k]['is_one'] = Goods::IS_ONE;
	    				 }
	    				
	    				 if ($dv['id']==$lv['machine_id'] && $lv['is_for']==Goods::IS_FOR) {
	    				 	$data[$k]['is_for'] = Goods::IS_FOR;
	    				 }
	    				 
	    				 if ($dv['id']==$lv['machine_id'] && $lv['is_promo']==Goods::IS_PROMO) {
	    				 	$data[$k]['is_promo'] = Goods::IS_PROMO;
	    				 }
	    				
	    			}
    			}
	    	
	    	}
    	
    	}
    	
    	$this->_success($data, $tag_name);
    }
    
    /**
     * 获取配置
     */
    public function actionGetConfig() {
    	$site_config = Tool::getConfig('site');
    	
    	$rs['domain'] = $site_config['domain'];
    	$rs['api_distance'] = empty($site_config['api_distance'])?1000:$site_config['api_distance']; //配置为空 默认1000
    	$rs['kefu_mobile'] = $site_config['kefu_mobile'];
    	
    	$this->_success($rs,'getConfig');
    }

}
