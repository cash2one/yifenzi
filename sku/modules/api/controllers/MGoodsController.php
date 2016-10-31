<?php
/**
 * 售货机商品接口
 * 
 * 包含输出商品列表、单个商品信息、商品进货、商品出货等操作
 * 
 * @author leo8705
 */
class MGoodsController extends VMAPIController 
{
	
	/**
	 * 获取商品列表
	 */
   	public function actionGetgoodslist(){
   		try{
   			$this->params = array('shopId');
   			$requiredFields = array('shopId');
   			$decryptFields = array('shopId');
   			$post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
   			
   			$className = $this->className;
   			$goodsClassName = $this->goodsClassName;
   			if($this->vending['status']!=$className::STATUS_ENABLE || $this->vending['is_activate']!=$className::IS_ACTIVATE_YES)
   				$this->_error(Yii::t('apiModule.goods','该售货机、生鲜机不是正常运行状态'));
   			
   			$select = 't.id ,t.goods_id, g.name , g.price ,g.score, g.thumb AS imageUrl';
   			if ($this->type==Stores::FRESH_MACHINE) {
   				$select .= ',t.weight,t.line_id';
   			}
   			$goodsList = Yii::app()->db->createCommand()
   			->select($select)
   			->from($goodsClassName::tableName().' as t')
   			->leftJoin(Goods::tableName().' as g', ' t.goods_id=g.id ')
			->where("t.machine_id = " .$this->vending['id'] ." and g.status = ".Goods::STATUS_PASS." and t.status = ".$goodsClassName::STATUS_ENABLE.' and  g.price>0')
   			->order("t.create_time desc")
   			->limit(36)->queryAll();
   			
   			//取库存
   			$g_ids = array();
   			$line_ids = array();
   			foreach ($goodsList as $key =>$value)
   			{
   				$goodsList[$key]['imageUrl'] = ATTR_DOMAIN . '/' .$value['imageUrl'];
   				$g_ids[] = $value['goods_id'];
   				if($this->type==Stores::FRESH_MACHINE) $line_ids[] = $value['line_id'];
   			}
   			
   			$stocks = ApiStock::goodsStockList($this->vending['id'], $this->type==Stores::FRESH_MACHINE?$line_ids:$g_ids,$this->stockApiProjectId);

   			foreach ($goodsList as $key=>$val){
   				$goodsList[$key] = array_merge($val,$this->type==Stores::FRESH_MACHINE?(isset($stocks[$val['line_id']])?$stocks[$val['line_id']]:0):(isset($stocks[$val['goods_id']])?$stocks[$val['goods_id']]:0));
   			}
   			 
   			$this->_success(array('goodsList'=>$goodsList));
	
   		}catch (Exception $e){
   			$this->_error($e->getMessage());
   		}
   }
   
   /**
    * 设置商品总数
    */
   public function actionSetgoodscount(){
	   try {
		   	$this->params = array('shopId','goodsId','goodsCount');
		   	$requiredFields = array('shopId','goodsId','goodsCount');
		   	$decryptFields = array('shopId','goodsId','goodsCount');
		   	$post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
//                        $post['shopId'] = '201504153221';
//                        $post['goodsId'] = '23';
//                        $post['goodsCount'] = '-30';
			
		   	$className = $this->className;
		   	$goodsClassName = $this->goodsClassName;
		   	
		   	if($this->vending['status']!=$className::STATUS_ENABLE || $this->vending['is_activate']!=$className::IS_ACTIVATE_YES)
		   		$this->_error(Yii::t('apiModule.goods','该售货机不是正常运行状态'));

		   	if(!is_numeric($post['goodsCount']))
		   		$this->_error(Yii::t('apiModule.goods','商品总数参数异常'));
		   	
		   	if(!is_numeric($post['goodsId']) || $post['goodsId'] <= 0)
		   		$this->_error(Yii::t('apiModule.goods','商品ID参数异常'));
		   	$db = Yii::app()->db;
		   	$select = "id,goods_id,machine_id";
		   	if ($this->type==Stores::FRESH_MACHINE) $select.=',line_id';
		   	$goods = $db->createCommand()
		   					   ->select($select)
		   					   ->from($goodsClassName::model()->tableName())
		   					   ->where("id = " .$post['goodsId'])
		   					   ->queryRow();
		   	if(!$goods) $this->_error(Yii::t('apiModule.goods','无此商品信息'));

		   	//查询库存
		   	$stock = ApiStock::goodsStockOne($this->vending['id'], $this->type==Stores::FRESH_MACHINE?$goods['line_id']:$goods['goods_id'],$this->stockApiProjectId);
		   	$num =$post['goodsCount'];
		   	
		   	//调用接口设置库存
		   	if ($num>0) {
		   		ApiStock::stockIn($this->vending['id'], $this->type==Stores::FRESH_MACHINE?$goods['line_id']:$goods['goods_id'],$num,$this->stockApiProjectId);
		   	}else	if ($num<0){
		   		ApiStock::stockOut($this->vending['id'], $this->type==Stores::FRESH_MACHINE?$goods['line_id']:$goods['goods_id'],$num,$this->stockApiProjectId);
		   	}
		   	
		   	$this->_success();
	   }catch (Exception $e){
	   		
	   		$this->_error($e->getMessage());
	   }
   }
   
   
   /**
    * 补货短信发送
    */
   public function actionSendmsg(){
   	try{
   		$this->params = array('shopId','goodsName','code','phoneNos');
   		$requiredFields = array('shopId','goodsName','code');
   		$decryptFields = array('shopId','goodsName','code','phoneNos');
   		$post = $this->decrypt($_POST,$requiredFields,$decryptFields);
   		
   		$className = $this->className;
   		$goodsClassName = $this->goodsClassName;
   		
   		if($this->vending['status']!=$className::STATUS_ENABLE || $this->vending['is_activate']!=$className::IS_ACTIVATE_YES)
   			$this->_error(Yii::t('apiModule.goods','该售货机不是正常运行状态'));
   		if(Fun::cache('vending')->get($post['code']))
   			$this->_error(Yii::t('apiModule.goods','该短信已发送'));
   		if(!isset($post['phoneNos']))$this->_success();
   		$nosArr = explode(",",$post['phoneNos']);
   		foreach ($nosArr as $value){
   			if(Validator::isMobile($value)){
                $model = new ApiMember();
   				$content = "您好，".$this->vending['name']." 自动售货机、生鲜机里面的 “".$post['goodsName']."” 已售罄，请注意补货";
   				$smsRes = $model->sendSms($value, $content, $model::SMS_TYPE_VENDING_COMPLEMENT);
   			}
   		}
   		Fun::cache('vending')->set($post['code'],1,43200);
   		$this->_success();
   
   	}catch (Exception $e){
   		$this->_error($e->getMessage());
   	}
   }
   
   
   /**
    * 获取商品列表
    */
   public function actionGoodsList(){
   	try{
        if ($this->getParam('onlyTest')==1) {
            $post = $_REQUEST;
        }else{
            $this->params = array('shopId');
            $requiredFields = array('shopId');
            $decryptFields = array('shopId');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
        }
   		$className = $this->className;
   		$goodsClassName = $this->goodsClassName;
   		if($this->vending['status']!=$className::STATUS_ENABLE || $this->vending['is_activate']!=$className::IS_ACTIVATE_YES)
   			$this->_error(Yii::t('apiModule.goods','该售货机、生鲜机不是正常运行状态'));
//                if($this->type==Stores::FRESH_MACHINE &&$this->vending['type'] == FreshMachine::FRESH_MACHINE_SMALL){
//                    $select = 'g.name , g.price ,g.thumb AS imageUrl,t.goods_id';
//                }else{
   		$select = 't.id ,g.name , g.price ,g.thumb AS imageUrl,t.goods_id';
//                }
   		if ($this->type==Stores::FRESH_MACHINE)  $select .= ',CONCAT(t.weight,"g") AS unit,l.code as line,t.line_id';
   		elseif ($this->type==Stores::MACHINE)  $select .= ',t.line';
             
   		$goodsList = Yii::app()->db->createCommand()
   		->select($select)
   		->from($goodsClassName::model()->tableName().' as t')
   		->leftJoin(Goods::model()->tableName().' as g', ' t.goods_id=g.id ')
   		->leftJoin(FreshMachineLine::model()->tableName().' as l', ' t.line_id=l.id ')
   		->where("t.machine_id = " .$this->vending['id'] ." and g.status = ".Goods::STATUS_PASS." and t.status = ".$goodsClassName::STATUS_ENABLE.' ')
   		->order("t.id desc")
   		->group('l.code')
   		->limit(36)->queryAll();
               
   		$count = count($goodsList);
   
   		$stock_ids = array();
   		foreach ($goodsList as $key =>$value)
   		{
   			$goodsList[$key]['count'] = $count;
   			$goodsList[$key]['imageUrl'] = ATTR_DOMAIN . '/' .$value['imageUrl'];
   			$stock_ids[] = $this->type==Stores::FRESH_MACHINE?$value['line_id']:$value['goods_id'];
   		}

   		$stocks = ApiStock::goodsStockList($this->vending['id'], $stock_ids,$this->stockApiProjectId);
   		
   		foreach ($goodsList as $key=>$val){
   			$goodsList[$key] = array_merge($val,$this->type==Stores::FRESH_MACHINE?(isset($stocks[$val['line_id']])?$stocks[$val['line_id']]:array('stock'=>0,'frozenStock'=>0)):$stocks[$val['goods_id']]);
   		}
                $hds = array();
                $goods_id = array();
                $goods_count = 0;
                if($this->type==Stores::FRESH_MACHINE &&$this->vending['type'] == FreshMachine::FRESH_MACHINE_SMALL){
                    foreach($goodsList as $k=>$g){
                        $goods_id[$k] = $g['goods_id'];
                    }
                    foreach($goodsList as $k=>$v){
                        foreach ($goodsList as $k1=>$v1){
                            if($v['goods_id'] ==$v1['goods_id']){
                                $hds[$v['goods_id']][$k]['hd_id'] = $v['line_id'];
                                $hds[$v['goods_id']][$k]['hd_code'] = $v['line'];
                                $hds[$v['goods_id']][$k]['count'] = $v['stock'];
//                                 $hds[$v['goods_id']][$k]['store_goods_id'] = $v['id'];
                            }
                        }
                    }
                    $goods_id = array_unique($goods_id);
                    foreach($goods_id as $k=>$v){
                        $goods_id[$k] = $goodsList[$k];
                        foreach($hds as $k1=>$v1){
                            if($k1 ==$v){
                                $goods_id[$k]['hdList'] = json_encode(array_values($v1));
                               if(count($v1)>1){
                                   foreach($v1 as $v2){
                                       $goods_count +=$v2['count']*1;
                                   }
                                   $goods_id[$k]['count'] = $goods_count;
                               }else{
                                   $goods_id[$k]['count'] = $v1[$k]['count'];
                               }                             
                            }
                        }
                        unset($goods_id[$k]['id']);
                        unset($goods_id[$k]['stock']);
                        unset($goods_id[$k]['frozenStock']);
                        unset($goods_id[$k]['unit']);
                        unset($goods_id[$k]['line']);
                        unset($goods_id[$k]['line_id']);
                    }
                    
                }
                if($goods_id){
                    $this->_success(array('goodsList'=>array_values($goods_id)));
                }else{
   		$this->_success(array('goodsList'=>$goodsList));
                }
   
   	}catch (Exception $e){
   		$this->_error($e->getMessage());
   	}
   }
   
   
   /**
    * 获取商品数量
    * 
    * 参数list为数组形式
    * 
    * 
    */
   public function actionGoodsDetail(){
   	try{
   		
   		if ($this->getParam('onlyTest')==1) {
   			$post = array();
   			$post['shopId'] = $this->getParam('shopId');
   			$post['goodsId'] =  $this->getParam('goodsId');
   			$post['goodsListO'] = $post['goodsId'];
   			$post['goodsId'] = json_decode(str_replace("\\\"", "\"", $post['goodsId']), true);
   		
   		}else{
   			$this->params = array('goodsId');
	   		$requiredFields = array('goodsId');
	   		$decryptFields = array('goodsId');
	   		$post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
	   		$post['goodsId'] = json_decode(str_replace("\\\"", "\"", $post['goodsId']), true);
   		}
   			$className = $this->className;
   			$goodsClassName = $this->goodsClassName;
   			if($this->vending['status']!=$className::STATUS_ENABLE || $this->vending['is_activate']!=$className::IS_ACTIVATE_YES)
   				$this->_error(Yii::t('apiModule.goods','该售货机、生鲜机不是正常运行状态'));
                           $select = 't.id ,t.goods_id';
                        if($this->vending['type'] == FreshMachine::FRESH_MACHINE_SMALL && $this->type ==Stores::FRESH_MACHINE){    
//                            var_dump(1111);die;
                            if ($this->type==Stores::FRESH_MACHINE) $select .= ',t.line_id,t.line_code';
                            if(!empty($post['goodsId'])) {
                            $goodsList = Yii::app()->db->createCommand()
   				->select($select)
   				->from($goodsClassName::model()->tableName().' as t')
   				->leftJoin(Goods::model()->tableName().' as g', ' t.goods_id=g.id ')
   				->where("t.machine_id = " .$this->vending['id'] ." and g.status = ".Goods::STATUS_PASS." and t.status = ".$goodsClassName::STATUS_ENABLE.' and  g.price>0  AND t.goods_id IN('.implode(',', $post['goodsId']).')')
   				->order("t.create_time desc")
   				->limit(36)->queryAll();
                
                            //取库存
   				$g_ids = array();
   				$line_ids = array();
   				foreach ($goodsList as $key =>$value)
   				{
   					$g_ids[] = $value['goods_id'];
   					$line_ids[] = $value['line_id'];
   				}
   				
   				$stocks = ApiStock::goodsStockList($this->vending['id'], $this->type==Stores::FRESH_MACHINE?$line_ids:$g_ids,$this->stockApiProjectId);
   				
   				$rs_list = array();
   				foreach ($goodsList as  $k=>$v){
   						
   					$rs_list[] = $this->type==Stores::FRESH_MACHINE?(array('id'=>$v['id'],'stock'=>$stocks[$v['line_id']]['stock'],'goods_id'=>$v['goods_id'],'line_id'=>$v['line_id'],'line_code'=>$v['line_code'])):(array('id'=>$v['id'],'stock'=>$stocks[$v['goods_id']]['stock'],'goods_id'=>$v['goods_id'],'line_id'=>$v['line_id'],'line_code'=>$v['line_code']));
   				}
                            
                                         $hds = array();
                                foreach($rs_list as $k=>$v){
                                   foreach ($rs_list as $k1=>$v1){
                                       if($v['goods_id'] ==$v1['goods_id']){
                                           $hds[$v['goods_id']][$k]['hd_id'] = $v['line_id'];
                                           $hds[$v['goods_id']][$k]['hd_code'] = $v['line_code'];
                                           $hds[$v['goods_id']][$k]['count'] = $v['stock'];
                                           $hds[$v['goods_id']][$k]['sgid'] = $v['id'];
                                       }
                                   }
                               }
                               $goods_ids = array();
                               $goods_count = 0;
                               foreach ($goodsList as $v){
                                   $goods_ids[] =$v['goods_id'];
                               }
                               $goods_ids = array_unique($goods_ids);
                               foreach ($goods_ids as $k=>$v){
                                   $goods_ids[$k] = $rs_list[$k];
                                    foreach($hds as $k1=>$v1){
                                    if($k1 ==$v){
                                        $goods_ids[$k]['hdList'] = json_encode(array_values($v1));
                                       if(count($v1)>1){
                                           foreach($v1 as $v2){
                                               $goods_count +=$v2['count']*1;
                                           }
                                           $goods_ids[$k]['count'] = $goods_count;
                                       }else{
                                           $goods_ids[$k]['count'] = $v1[$k]['count']*1;
                                       }                             
                                    }
                                }                                 
                                    unset($goods_ids[$k]['stock']);
                                   unset($goods_ids[$k]['line_id']);
                                   unset($goods_ids[$k]['line_code']);
                               }
                            }else{
                                 $this->_error('商品已下架');
                            }
                        }
   			if ($this->type==Stores::FRESH_MACHINE && $this->vending['type'] != FreshMachine::FRESH_MACHINE_SMALL){ $select .= ',t.line_id';
   			if (!empty($post['goodsId'])) {
   				$goodsList = Yii::app()->db->createCommand()
   				->select($select)
   				->from($goodsClassName::model()->tableName().' as t')
   				->leftJoin(Goods::model()->tableName().' as g', ' t.goods_id=g.id ')
   				->where("t.machine_id = " .$this->vending['id'] ." and g.status = ".Goods::STATUS_PASS." and t.status = ".$goodsClassName::STATUS_ENABLE.' and  g.price>0  AND t.id IN('.implode(',', $post['goodsId']).')')
   				->order("t.create_time desc")
   				->limit(36)->queryAll();
   				if(!empty($goodsList)){
   				//取库存
   				$g_ids = array();
   				$line_ids = array();
   				foreach ($goodsList as $key =>$value)
   				{
   					$g_ids[] = $value['goods_id'];
   					$line_ids[] = $value['line_id'];
   				}
   				
   				$stocks = ApiStock::goodsStockList($this->vending['id'], $this->type==Stores::FRESH_MACHINE?$line_ids:$g_ids,$this->stockApiProjectId);
   				
   				$rs_list = array();
   				foreach ($goodsList as  $k=>$v){
   						
   					$rs_list[] = $this->type==Stores::FRESH_MACHINE?(array('id'=>$v['id'],'stock'=>$stocks[$v['line_id']]['stock'])):(array('id'=>$v['id'],'stock'=>$stocks[$v['goods_id']]['stock']));
   				}
                                }else{
                                    $this->_error('商品已下架');
                                }
   					
   			}else{
   				$rs_list = array();
   			}
                        }
                        if(isset($goods_ids)&&!empty($goods_ids)){
                            $this->_success(array('goodsList'=>  array_values($goods_ids)));
                        }else{
   			$this->_success(array('goodsList'=>$rs_list));
                        }
	
   		}catch (Exception $e){
   			$this->_error($e->getMessage());
   		}
   }
   
   
   /**
    * 获取格仔铺商品列表
    */
   public function actionCellStoreGoodsList(){
   	try{
        if ($this->getParam('onlyTest')==1) {
            $post = $this->getParams();
        }else{
            $this->params = array('shopId');
            $requiredFields = array('shopId');
            $decryptFields = array('shopId');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
        }

   		 
   		$className = $this->className;
   		$goodsClassName = $this->goodsClassName;
   		if($this->vending['status']!=$className::STATUS_ENABLE || $this->vending['is_activate']!=$className::IS_ACTIVATE_YES)
   			$this->_error('该售货机、生鲜机不是正常运行状态');
   		 
   		$select = 't.id,t.code ,g.name , g.price , CONCAT("'.ATTR_DOMAIN.'",g.thumb) AS imageUrl';
   		$goodsList = Yii::app()->db->createCommand()
   		->select($select)
   		->from(VendingMachineCellStore::tableName().' as t')
   		->leftJoin(Goods::tableName().' as g', ' t.goods_id=g.id ')
   		->where("t.machine_id = " .$this->vending['id'] ." and g.status = ".Goods::STATUS_PASS." and t.status = ".VendingMachineCellStore::STATUS_ENABLE.' and  g.price>0')
   		->order("t.create_time desc")
   		->limit(72)->queryAll();
   		 
   		$this->_success(array('goodsList'=>$goodsList));
   		 
   	}catch (Exception $e){
   		$this->_error($e->getMessage());
   	}
   }
   
   /**
    * 获取单个商品库商品信息
    */
   public function actionGoodsInfo(){
   	try{
        if ($this->getParam('onlyTest')==1) {
            $post = $this->getParams();
        }else{
            $this->params = array('shopId','goodsId');
            $requiredFields = array('shopId','goodsId');
            $decryptFields = array('shopId','goodsId');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
        }
   		$goodsId =$post['goodsId'];
   		$data = Yii::app()->db->createCommand()
   		->select("g.id as goods_id, g.name, g.sec_title, g.content, g.supply_price, g.barcode, g.is_one, g.is_promo, g.is_for,  g.price,g.thumb,g.status,gc.name cate_name,gc.id cate_id,c.name as sys_cate_name,c.id as sys_cate_id,p.name as partner_name,p.id as partner_id")
   		->from("{{goods}} as g")
   		->leftjoin("{{goods_category}} as gc","g.cate_id = gc.id")
   		->leftjoin("{{category}} as c","g.source_cate_id = c.id")
   		->leftjoin(Partners::model()->tableName()." as p","g.partner_id = p.id")
   		->where('g.id = :id',array(':id'=>$goodsId))
   		->queryRow();
   
   		if (empty($data)) {
   			$this->_error(Yii::t('apiModule.goods', '商品不存在'));
   		}
   
   		if(!empty($data['thumb'])){
   			$data['thumb'] = ATTR_DOMAIN . '/' .$data['thumb'];
   		}
   
   		$imgs = Yii::app()->db->createCommand()
   		->select("id,CONCAT('". IMG_DOMAIN ."','/',path) as path,sort")
   		->from(GoodsPicture::model()->tableName()." as t")
   		->where('t.goods_id=:goods_id',array(':goods_id'=>$goodsId))
   		->queryAll();
   
   		$data['imgs'] = $imgs;
   
   		$this->_success(array('goods'=>$data));
   
   	}catch (Exception $e){
   
   		$this->_error($e->getMessage());
   	}
   
   }
   
   
}