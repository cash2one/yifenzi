<?php

/**
 * This is the model class for table "{{goods_stock}}".
 *
 * The followings are the available columns in table '{{goods_stock}}':
 * @property integer $id
 * @property integer $project
 * @property integer $type
 * @property integer $outlets
 * @property integer $target
 * @property integer $stock
 * @property integer $frozen_stock
 * @property integer $shipment_sum
 * @property integer $purchase_sum
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property GwMember $member
 * @property GoodsStockBalance[] $goodsStockBalances
 */
class GoodsStock extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{goods_stock}}';
    }

    const PROJECT_GAIXIANG = 101;                   //盖象商城项目
    const PROJECT_VENDING_MACHINE = 102;              //售货机项目
    const PROJECT_FRANCHISEE_OFFLINE = 103;              //盖网线下项目
    const PROJECT_SKU_SUPER = 104;                  //sku超市项目
    const GOODS_TYPE_SUPER = 1;                  //超市商品
    const GOODS_TYPE_VENDING_MACHINE = 2;             //售货机商品
    const GOODS_TYPE_ONLINE = 3;                  //盖象线上商品
    const GOODS_TYPE_OFFLINE = 4;                  //加盟商线下商品

    static function getProject($p = null) {
        $arr = array(
            self::PROJECT_GAIXIANG =>Yii::t('goodsStock','盖象商城项目'),
            self::PROJECT_VENDING_MACHINE => Yii::t('goodsStock','售货机项目'),
            self::PROJECT_FRANCHISEE_OFFLINE => Yii::t('goodsStock','盖网线下项目'),
            self::PROJECT_SKU_SUPER => Yii::t('goodsStock','sku超市项目'),
        );

        return $p === null ? $arr : (isset($arr[$p]) ? $arr[$p] :Yii::t('goodsStock','无效'));
    }

    static function getGoodsType($t = null) {
        $arr = array(
            self::GOODS_TYPE_SUPER =>Yii::t('goodsStock','超市商品'),
            self::GOODS_TYPE_VENDING_MACHINE =>Yii::t('goodsStock','售货机商品'),
            self::GOODS_TYPE_ONLINE =>Yii::t('goodsStock','盖象线上商品'),
            self::GOODS_TYPE_OFFLINE => Yii::t('goodsStock','加盟商线下商品'),
        );

        return $t === null ? $arr : (isset($arr[$t]) ? $arr[$t] : Yii::t('goodsStock','无效'));
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('project,type,outlets,target,stock', 'required'),
            array('project, type, outlets, target, stock, frozen_stock, shipment_sum, purchase_sum, create_time', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, project, type, outlets, target, stock, frozen_stock, shipment_sum, purchase_sum, create_time', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'goodsStockBalances' => array(self::HAS_MANY, 'GoodsStockBalance', 's_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => Yii::t('goodsStock','id'),
            'project' => Yii::t('goodsStock','所属项目  售货机、商城、线下商品、超市项目'),
            'type' =>Yii::t('goodsStock','商品类型( 售货机商品、超市商品、线上商品等 )'),
            'outlets' =>Yii::t('goodsStock','销售网点id （售货机 超市等的id,0时为线上商品）'),
            'target' => Yii::t('goodsStock','目标商品id或条形码'),
            'stock' => Yii::t('goodsStock','现有库存'),
            'frozen_stock' => Yii::t('goodsStock','冻结库存'),
            'shipment_sum' =>  Yii::t('goodsStock','出货总数'),
            'purchase_sum' => Yii::t('goodsStock','进货总数'),
            'create_time' =>Yii::t('goodsStock','创建时间'),
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
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('project', $this->project);
        $criteria->compare('type', $this->type);
        $criteria->compare('outlets', $this->outlets);
        $criteria->compare('target', $this->target);
        $criteria->compare('stock', $this->stock);
        $criteria->compare('frozen_stock', $this->frozen_stock);
        $criteria->compare('shipment_sum', $this->shipment_sum);
        $criteria->compare('purchase_sum', $this->purchase_sum);
        $criteria->compare('create_time', $this->create_time);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return GoodsStock the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * 生成库存记录  公共逻辑
     * @param unknown $project
     */
    static function createCommon($project, $data) {
        $goods_stock = new GoodsStock();
        $goods_stock->attributes = $data;
        $goods_stock->project = $project;
        $goods_stock->create_time = time();
        $rs1 = $goods_stock->save();
        $s_id = Yii::app()->db->getLastInsertID();

        $goods_stock_balance = new GoodsStockBalance();
        $goods_stock_balance->s_id = $s_id;
        $goods_stock_balance->node = GoodsStockBalance::NODE_STOCK_CREATE;
        $goods_stock_balance->node_type = GoodsStockBalance::NODE_TYPE_IN;
        $goods_stock_balance->num = $goods_stock->stock;
        $goods_stock_balance->balance = $goods_stock->stock;
        $goods_stock_balance->cur_balance = $goods_stock->stock;
        $goods_stock_balance->cur_frozen = 0;
        $goods_stock_balance->remark = '';
        $goods_stock_balance->data = serialize($data);
        $goods_stock_balance->create_time = time();
        $rs2 = $goods_stock_balance->save();

        if ($rs1 && $rs2)
            return true;
        return false;
    }

    /**
     * 查询单个库存记录
     * @param unknown $project
     */
    static function apiGetOne($project, $outlets, $target) {
        $rs = array(
            'result' => false,
            'error_code' => ErrorCode::COMMOM_UNKNOW,
        );

        try {
            $targetCount = self::model()->count(' project=:project AND outlets=:outlets AND target=:target', array(':project' => $project, ':outlets' => $outlets, ':target' => $target,));

            //如果记录不存在则自动创建记录
            if (!$targetCount) {
                $create_data['outlets'] = $outlets;
                $create_data['target'] = $target;
                $create_rs = self::createByProject($project, $create_data, false);
                if ($create_rs['result'] != true) {
                    return $create_rs;
                }
            }

            $targetObj = self::model()->find(' project=:project AND outlets=:outlets AND target=:target', array(':project' => $project, ':outlets' => $outlets, ':target' => $target,));

            $rs['result'] = true;
            unset($rs['error_code']);
            $rs['stock'] = $targetObj->stock;
            $rs['frozenStock'] = $targetObj->frozen_stock;
        } catch (Exception $e) {
            $rs['error_code'] = ErrorCode::COMMOM_SYS_ERROR;
            $rs['error_msg'] = $e->getMessage();
        }

        return $rs;
    }

    /**
     * 查询库存列表记录
     * @param unknown $project
     */
    static function apiGetList($project, $outlets, $target_arr) {
        $rs = array(
            'result' => false,
            'error_code' => ErrorCode::COMMOM_UNKNOW,
        );

        try {
            $target_cond = 'target IN (0';
            foreach ($target_arr as $target) {
                $target_cond .= ",{$target}";
            }
            $target_cond .= ') ';

            $targetObjs = self::model()->findAll(' project=:project AND outlets=:outlets AND ' . $target_cond, array(':project' => $project, ':outlets' => $outlets));


            //插入不存在的记录
            $unexist_target = $target_arr;
            foreach ($targetObjs as $k => $o) {
            	
            	foreach ($unexist_target as $kk=>$val){
            		if (in_array($o->target, $target_arr)) {
            			unset($unexist_target[$kk]);
            		}
            	}
            	
            }
            if (count($unexist_target) > 0) {
                $target_cond_unexist = 'target IN (0';
                foreach ($unexist_target as $t) {
                    $target_cond_unexist .= ",{$t}";

                    //插入记录
                    $create_data = array();
                    $create_data['outlets'] = $outlets;
                    $create_data['target'] = $t;
                    $create_rs = self::createByProject($project, $create_data, false);
                    if ($create_rs['result'] != true) {
                        return $create_rs;
                    }
                }
                $target_cond_unexist .= ') ';
                $newTargetObjs = self::model()->findAll(' project=:project AND outlets=:outlets AND ' . $target_cond_unexist, array(':project' => $project, ':outlets' => $outlets));
                $targetObjs = array_merge($targetObjs, $newTargetObjs);
            }


            $rs['result'] = true;
            unset($rs['error_code']);

            $rs_arr = array();
            foreach ($targetObjs as $obj) {
                $rs_arr[$obj->target]['stock'] = $obj->stock;
                $rs_arr[$obj->target]['frozenStock'] = $obj->frozen_stock;
            }
            $rs['list'] = $rs_arr;
        } catch (Exception $e) {
            $rs['error_code'] = ErrorCode::COMMOM_SYS_ERROR;
            $rs['error_msg'] = $e->getMessage();
        }

        return $rs;
    }

    /**
     * 生成库存记录
     * @param unknown $project
     */
    static function createByProject($project, $data, $doTrans = true) {
        $rs = array(
            'result' => false,
            'error_code' => ErrorCode::COMMOM_UNKNOW,
        );

        if ($doTrans == true)
            $trans = Yii::app()->db->beginTransaction();

        try {
            $checkrs = self::model()->count(' project=:project AND outlets=:outlets AND target=:target', array(':project' => $project, ':outlets' => $data['outlets'], ':target' => $data['target'],));
            if ($checkrs > 0) {
                $rs['error_code'] = ErrorCode::GOOD_STOCK_EXIST;
                return $rs;

            }
            if (self::createCommon($project, $data)) {
                if ($doTrans == true)
                    $trans->commit();
                $rs['result'] = true;
                unset($rs['error_code']);
            } else {
                $trans->rollback();
                $rs['error_code'] = ErrorCode::COMMOM_SYS_ERROR;
            }
        } catch (Exception $e) {
            if ($doTrans == true)
                $trans->rollback();
            $rs['error_code'] = ErrorCode::COMMOM_SYS_ERROR;
            $rs['error_msg'] = $e->getMessage();
        }

        return $rs;
    }

    /**
     * 列表生成库存记录
     * @param unknown $project
     */
    static function createListByProject($project, $arr_data) {
        $rs = array(
            'result' => false,
            'error_code' => ErrorCode::COMMOM_UNKNOW,
        );

        $trans = Yii::app()->db->beginTransaction();
        
        try {
            
            foreach ($arr_data as $k => $data) {
                $checkrs = self::model()->count(' project=:project AND outlets=:outlets AND target=:target', array(':project' => $project, ':outlets' => $data['outlets'], ':target' => $data['target'],));
                if ($checkrs > 0) {
                    $rs['error_code'] = ErrorCode::GOOD_STOCK_EXIST;
                    return $rs;
                }

                if (!self::createCommon($project, $data)) {
                    $trans->rollback();
                    $rs['error_code'] = ErrorCode::COMMOM_SYS_ERROR;
                    return $rs;
                }
            }

            $trans->commit();

            $rs['result'] = true;
            unset($rs['error_code']);
        } catch (Exception $e) {
            $trans->rollback();
            $rs['error_msg'] = $e->getMessage();
        }

        return $rs;
    }

    /**
     * 商品库存变动
     * @param unknown $project
     * @param unknown $outlets
     * @param unknown $target
     * @param unknown $num
     * @param unknown $node
     * @param unknown $node_type
     * @param string $remark
     * @return multitype:boolean number
     */
    static function stockChange($project, $outlets, $target, $num, $node, $node_type, $remark = '') {
        $rs = array(
            'result' => false,
            'error_code' => ErrorCode::COMMOM_UNKNOW,
        );

        $trans = Yii::app()->db->beginTransaction();
        try {
            $targetCount = self::model()->count(' project=:project AND outlets=:outlets AND target=:target', array(':project' => $project, ':outlets' => $outlets, ':target' => $target,));
            //如果记录不存在则自动创建记录
            if (!$targetCount) {
                $create_data['outlets'] = $outlets;
                $create_data['target'] = $target;
                $create_rs = self::createByProject($project, $create_data, false);
                if ($create_rs['result'] != true) {
                    return $create_rs;
                }
            }

            $targetObj = self::model()->find(' project=:project AND outlets=:outlets AND target=:target', array(':project' => $project, ':outlets' => $outlets, ':target' => $target,));

            if (($targetObj->stock + $num) < 0) {
                $rs['error_code'] = ErrorCode::GOOD_STOCK_NOT_ENOUGH;
                return $rs;
            }

            $rs1 = Yii::app()->db->createCommand(" UPDATE " . self::model()->tableName() . " SET `stock`=`stock`+{$num} WHERE project={$project} AND outlets={$outlets} AND target={$target}")->execute();

            $goods_stock_balance = new GoodsStockBalance();
            $goods_stock_balance->s_id = $targetObj->id;
            $goods_stock_balance->node = $node;
            $goods_stock_balance->node_type = $node_type;
            $goods_stock_balance->num = abs($num);
            $goods_stock_balance->balance = $num;
            $goods_stock_balance->cur_balance = $targetObj->stock + $num;
            $goods_stock_balance->cur_frozen = $targetObj->frozen_stock;
            $goods_stock_balance->remark = $remark;
            $goods_stock_balance->create_time = time();
            $rs2 = $goods_stock_balance->save();

            if (($num==0 || $rs1) && $rs2) {
                $trans->commit();
                $rs['result'] = true;
                unset($rs['error_code']);
            } else {
                $rs['error_code'] = ErrorCode::GOOD_STOCK_DATA_ERROR;
                $trans->rollback();
            }
        } catch (Exception $e) {
            $trans->rollback();
            $rs['error_code'] = ErrorCode::COMMOM_SYS_ERROR;
            $rs['error_msg'] = $e->getMessage();
        }

        return $rs;
    }

    /**
     * 商品库存 入库、进货
     * @param unknown $project
     */
    static function stockIn($project, $outlets, $target, $num, $remark = '') {
        return self::stockChange($project, $outlets, $target, abs($num), GoodsStockBalance::NODE_STOCK_IN, GoodsStockBalance::NODE_TYPE_IN, $remark);
    }

    /**
     * 商品库存 出库、出货
     * @param unknown $project
     */
    static function stockOut($project, $outlets, $target, $num, $remark = '') {
        return self::stockChange($project, $outlets, $target, -abs($num), GoodsStockBalance::NODE_STOCK_OUT, GoodsStockBalance::NODE_TYPE_OUT, $remark);
    }

    /**
     * 商品库存 冻结变动  扣除与还原
     * @param unknown $project
     */
    static function stockFrozenChange($project, $outlets, $target, $num, $node, $node_type, $remark = '') {
        $rs = array(
            'result' => false,
            'error_code' => ErrorCode::COMMOM_UNKNOW,
        );

        try {
            $targetObj = self::model()->find(' project=:project AND outlets=:outlets AND target=:target', array(':project' => $project, ':outlets' => $outlets, ':target' => $target,));

            if (!$targetObj) {
                $rs['error_code'] = ErrorCode::GOOD_STOCK_NOT_EXIST;
                return $rs;
            }

            if (($targetObj->stock - $num) < 0) {
                $rs['error_code'] = ErrorCode::GOOD_STOCK_NOT_ENOUGH;
                return $rs;
            }

            if (($targetObj->frozen_stock + $num) < 0) {
                $rs['error_code'] = ErrorCode::GOOD_FROZEN_STOCK_NOT_ENOUGH;
                return $rs;
            }

            $trans = Yii::app()->db->beginTransaction();

            $rs1 = Yii::app()->db->createCommand(" UPDATE " . self::model()->tableName() . " SET `stock`=`stock`-{$num} , `frozen_stock` =`frozen_stock`+{$num}  WHERE project={$project} AND outlets={$outlets} AND target={$target}")->execute();

            $goods_stock_balance = new GoodsStockBalance();
            $goods_stock_balance->s_id = $targetObj->id;
            $goods_stock_balance->node = $node;
            $goods_stock_balance->node_type = $node_type;
            $goods_stock_balance->num = abs($num);
            $goods_stock_balance->balance = -$num;
            $goods_stock_balance->cur_balance = $targetObj->stock - $num;
            $goods_stock_balance->cur_frozen = $targetObj->frozen_stock + $num;
            $goods_stock_balance->remark = $remark;
            $goods_stock_balance->create_time = time();
            $rs2 = $goods_stock_balance->save();

            if ($rs1 && $rs2) {
                $trans->commit();
                $rs['result'] = true;
                unset($rs['error_code']);
            } else {
                $rs['error_code'] = ErrorCode::GOOD_STOCK_DATA_ERROR;
                $trans->rollback();
            }
        } catch (Exception $e) {
            $trans->rollback();
            $rs['error_code'] = ErrorCode::COMMOM_SYS_ERROR;
            $rs['error_msg'] = $e->getMessage();
        }

        return $rs;
    }

    /**
     * 商品库存 冻结变动  扣除与还原
     * @param unknown $project
     */
    static function stockFrozenChangeList($project, $outlets, $target_list, $nums, $node, $node_type, $remark = '') {
        $rs = array(
            'result' => false,
            'error_code' => ErrorCode::COMMOM_UNKNOW,
        );
        $i=0;
        
        $trans = Yii::app()->db->beginTransaction();
        try {
            if (count($target_list) != count($nums)) {
                $rs['error_code'] = ErrorCode::COMMON_PARAMS_LESS;
                $trans->rollback();
                return $rs;
            }

            $targetObjList = self::model()->findAll(' project=:project AND outlets=:outlets AND target IN ('.implode(',', $target_list).')', array(':project' => $project, ':outlets' => $outlets));

            foreach ($targetObjList as $targetObj) {
            	foreach ($target_list as $k=>$target){
            	if ($targetObj['target']==$target) {
            		$num = $nums[$k];

            		if (($targetObj->stock - $num) < 0) {
            			$rs['error_code'] = ErrorCode::GOOD_STOCK_NOT_ENOUGH;
            			$trans->rollback();
            			return $rs;
            		}
            		
            		if (($targetObj->frozen_stock + $num) < 0) {
            			$rs['error_code'] = ErrorCode::GOOD_FROZEN_STOCK_NOT_ENOUGH;
            			$trans->rollback();
            			return $rs;
            		}
            		
            		$rs1 = Yii::app()->db->createCommand(" UPDATE " . self::model()->tableName() . " SET `stock`=`stock`-{$num} , `frozen_stock` =`frozen_stock`+{$num}  WHERE project={$project} AND outlets={$outlets} AND target={$target}")->execute();
            		
            		$goods_stock_balance = new GoodsStockBalance();
            		$goods_stock_balance->s_id = $targetObj->id;
            		$goods_stock_balance->node = $node;
            		$goods_stock_balance->node_type = $node_type;
            		$goods_stock_balance->num = abs($num);
            		$goods_stock_balance->balance = -$num;
            		$goods_stock_balance->cur_balance = $targetObj->stock - $num;
            		$goods_stock_balance->cur_frozen = $targetObj->frozen_stock + $num;
            		$goods_stock_balance->remark = $remark;
            		$goods_stock_balance->create_time = time();
            		$rs2 = $goods_stock_balance->save();
            		if ($rs1 && $rs2) {
//             			$trans->commit();
//             			$rs['result'] = true;
//             			unset($rs['error_code']);
            		} else {
            			$rs['error_code'] = ErrorCode::GOOD_STOCK_DATA_ERROR;
            			$trans->rollback();
            		}
            		
            		
            	}
            	}
            }
            
            $trans->commit();
            $rs['result'] = true;
            unset($rs['error_code']);
            
            
        } catch (Exception $e) {
            $trans->rollback();
            $rs['error_code'] = ErrorCode::COMMOM_SYS_ERROR;
            $rs['error_msg'] = $e->getMessage();
        }

        return $rs;
    }

    /**
     * 冻结库存
     * @param unknown $project
     */
    static function stockFrozen($project, $outlets, $target, $num, $remark = '') {
        return self::stockFrozenChange($project, $outlets, $target, abs($num), GoodsStockBalance::NODE_STOCK_FROZEN, GoodsStockBalance::NODE_TYPE_OUT, $remark);
    }

    /**
     * 冻结库存
     * @param unknown $project
     */
    static function stockFrozenList($project, $outlets, $target_ids, $nums, $remark = '') {

        return self::stockFrozenChangeList($project, $outlets, $target_ids, $nums, GoodsStockBalance::NODE_STOCK_FROZEN, GoodsStockBalance::NODE_TYPE_OUT, $remark);
    }

    /**
     * 冻结库存还原
     * @param unknown $project
     */
    static function stockFrozenRestore($project, $outlets, $target, $num, $remark = '') {
        return self::stockFrozenChange($project, $outlets, $target, -abs($num), GoodsStockBalance::NODE_FROZEN_STOCK_RESTORE, GoodsStockBalance::NODE_TYPE_IN, $remark);
    }

    /**
     * 商品库存  扣除冻结库存
     * @param unknown $project
     */
    static function stockFrozenOut($project, $outlets, $target, $num, $remark = '') {
        $rs = array(
            'result' => false,
            'error_code' => ErrorCode::COMMOM_UNKNOW,
        );

        $trans = Yii::app()->db->beginTransaction();
        
        try {
            $targetObj = self::model()->find(' project=:project AND outlets=:outlets AND target=:target', array(':project' => $project, ':outlets' => $outlets, ':target' => $target,));

            if (!$targetObj) {
                $rs['error_code'] = ErrorCode::GOOD_STOCK_NOT_EXIST;
                return $rs;
            }

            if (($targetObj->frozen_stock - $num) < 0) {
                $rs['error_code'] = ErrorCode::GOOD_FROZEN_STOCK_NOT_ENOUGH;
                return $rs;
            }

            $rs1 = Yii::app()->db->createCommand(" UPDATE " . self::model()->tableName() . " SET  `frozen_stock` =`frozen_stock`-{$num}  WHERE project={$project} AND outlets={$outlets} AND target={$target}")->execute();

            $goods_stock_balance = new GoodsStockBalance();
            $goods_stock_balance->s_id = $targetObj->id;
            $goods_stock_balance->node = GoodsStockBalance::NODE_FROZEN_STOCK_OUT;
            $goods_stock_balance->node_type = GoodsStockBalance::NODE_TYPE_OUT;
            $goods_stock_balance->num = 0;
            $goods_stock_balance->balance = 0;
            $goods_stock_balance->cur_balance = $targetObj->stock;
            $goods_stock_balance->cur_frozen = $targetObj->frozen_stock - $num;
            $goods_stock_balance->remark = $remark;
            $goods_stock_balance->create_time = time();
            $rs2 = $goods_stock_balance->save();

            if ($rs1 && $rs2) {
                $trans->commit();
                $rs['result'] = true;
                unset($rs['error_code']);
            } else {
                $rs['error_code'] = ErrorCode::GOOD_STOCK_DATA_ERROR;
                $trans->rollback();
            }
        } catch (Exception $e) {
            $trans->rollback();
            $rs['error_code'] = ErrorCode::COMMOM_SYS_ERROR;
            $rs['error_msg'] = $e->getMessage();
        }

        return $rs;
    }

    /**
     * 商品库存  扣除冻结库存
     * @param unknown $project
     */
    static function stockFrozenOutList($project, $outlets, $targets, $nums, $remark = '') {
        $rs = array(
            'result' => false,
            'error_code' => ErrorCode::COMMOM_UNKNOW,
        );
        
        $trans = Yii::app()->db->beginTransaction();

        try {

            if (count($targets) != count($nums)) {
                $rs['error_code'] = ErrorCode::COMMON_PARAMS_LESS;
                $trans->rollback();
                return $rs;
            }
            

            $targetObjList = self::model()->findAll(' project=:project AND outlets=:outlets AND target IN ('.implode(',', $targets).')', array(':project' => $project, ':outlets' => $outlets));
            
            foreach ($targetObjList as $targetObj) {
            	foreach ($targets as $k=>$target){
            		if ($targetObj['target']==$target) {
            			$num = $nums[$k];
            			
            			if (($targetObj->frozen_stock - $num) < 0) {
            				$rs['error_code'] = ErrorCode::GOOD_FROZEN_STOCK_NOT_ENOUGH;
            				$trans->rollback();
            				return $rs;
            			}
            			$num = abs($num);
            			$rs1 = Yii::app()->db->createCommand(" UPDATE " . self::model()->tableName() . " SET  `frozen_stock` =`frozen_stock`-{$num}  WHERE project={$project} AND outlets={$outlets} AND target={$target}")->execute();
            			
            			$goods_stock_balance = new GoodsStockBalance();
            			$goods_stock_balance->s_id = $targetObj->id;
            			$goods_stock_balance->node = GoodsStockBalance::NODE_FROZEN_STOCK_OUT;
            			$goods_stock_balance->node_type = GoodsStockBalance::NODE_TYPE_OUT;
            			$goods_stock_balance->num = abs($num);
            			$goods_stock_balance->balance = -$num;
            			$goods_stock_balance->cur_balance = $targetObj->stock - $num;
            			$goods_stock_balance->cur_frozen = $targetObj->frozen_stock + $num;
            			$goods_stock_balance->remark = $remark;
            			$goods_stock_balance->create_time = time();
            			$rs2 = $goods_stock_balance->save();
            			
            			if (!$rs1 || !$rs2) {
            				$rs['error_code'] = ErrorCode::GOOD_STOCK_DATA_ERROR;
            				$trans->rollback();
            			}
            			
            		}
            	}
            }
            
            $trans->commit();
            $rs['result'] = true;
            unset($rs['error_code']);
            
        } catch (Exception $e) {
            $trans->rollback();
            $rs['error_code'] = ErrorCode::COMMOM_SYS_ERROR;
            $rs['error_msg'] = $e->getMessage();
        }

        return $rs;
    }

    /**
     * 批量商品库存变动
     * @param unknown $project
     */
    static function stockSet($project, $outlets, $target, $num, $remark = '') {
        $rs = array(
            'result' => false,
            'error_code' => ErrorCode::COMMOM_UNKNOW,
        );
        
        $trans = Yii::app()->db->beginTransaction();

        try {
           
            $targetObj = self::model()->find(' project=:project AND outlets=:outlets AND target = :target', array(':project' => $project, ':outlets' => $outlets, ':target' => $target,));
            $change_num = $num - $targetObj->stock;
            if ($targetObj->stock <= $num) {
                $node = GoodsStockBalance::NODE_STOCK_IN;
                $node_type = GoodsStockBalance::NODE_TYPE_IN;
            } else {
                $node = GoodsStockBalance::NODE_STOCK_OUT;
                $node_type = GoodsStockBalance::NODE_TYPE_OUT;
            }

            $rs1 = Yii::app()->db->createCommand(" UPDATE " . self::model()->tableName() . " SET `stock`={$num}  WHERE project={$project} AND outlets={$outlets} AND target={$target}")->execute();

            $goods_stock_balance = new GoodsStockBalance();
            $goods_stock_balance->s_id = $targetObj->id;
            $goods_stock_balance->node = $node;
            $goods_stock_balance->node_type = $node_type;
            $goods_stock_balance->num = abs($change_num);
            $goods_stock_balance->balance = $change_num;
            $goods_stock_balance->cur_balance = $num;
            $goods_stock_balance->cur_frozen = $targetObj->frozen_stock;
            $goods_stock_balance->remark = $remark;
            $goods_stock_balance->create_time = time();
            $rs2 = $goods_stock_balance->save();

            if (!$rs2) {
                $rs['error_code'] = ErrorCode::GOOD_STOCK_DATA_ERROR;
                $trans->rollback();
                return $rs;
            }
        } catch (Exception $e) {
            $trans->rollback();
            $rs['error_code'] = ErrorCode::COMMOM_SYS_ERROR;
            $rs['error_msg'] = $e->getMessage();
            return $rs;
        }

        $trans->commit();
        $rs['result'] = true;
        unset($rs['error_code']);
        return $rs;
    }

    /**
     * 批量商品库存变动 
     * @param unknown $project
     */
    static function stockSetList($project, $outlets, $target_list, $nums, $remark = '') {
        $rs = array(
            'result' => false,
            'error_code' => ErrorCode::COMMOM_UNKNOW,
        );


        $trans = Yii::app()->db->beginTransaction();
        try {

            if (count($target_list) != count($nums)) {
                $rs['error_code'] = ErrorCode::COMMON_PARAMS_LESS;
                return $rs;
            }

            $targetObj_arr = array();
            $targetObjs = self::model()->findAll(' project=:project AND outlets=:outlets AND target  IN (' . implode(',', $target_list) . ')', array(':project' => $project, ':outlets' => $outlets,));

            foreach ($targetObjs as $val) {
                $targetObj_arr[$val->target] = $val;
            }

            foreach ($target_list as $k => $target) {
                $num = $nums[$k] * 1;

                if (!isset($targetObj_arr[$target])) {
// 					$rs['error_code'] = ErrorCode::GOOD_STOCK_NOT_EXIST;
// 					return $rs;
                    continue;
                }

                $targetObj = $targetObj_arr[$target];

                $change_num = $num - $targetObj->stock;
                if ($targetObj->stock <= $num) {
                    $node = GoodsStockBalance::NODE_STOCK_IN;
                    $node_type = GoodsStockBalance::NODE_TYPE_IN;
                } else {
                    $node = GoodsStockBalance::NODE_STOCK_OUT;
                    $node_type = GoodsStockBalance::NODE_TYPE_OUT;
                }

                $rs1 = Yii::app()->db->createCommand(" UPDATE " . self::model()->tableName() . " SET `stock`={$num}  WHERE project={$project} AND outlets={$outlets} AND target={$target}")->execute();

                $goods_stock_balance = new GoodsStockBalance();
                $goods_stock_balance->s_id = $targetObj->id;
                $goods_stock_balance->node = $node;
                $goods_stock_balance->node_type = $node_type;
                $goods_stock_balance->num = abs($change_num);
                $goods_stock_balance->balance = $change_num;
                $goods_stock_balance->cur_balance = $num;
                $goods_stock_balance->cur_frozen = $targetObj->frozen_stock;
                $goods_stock_balance->remark = $remark;
                $goods_stock_balance->create_time = time();
                $rs2 = $goods_stock_balance->save();

                if (!$rs2) {
                    $rs['error_code'] = ErrorCode::GOOD_STOCK_DATA_ERROR;
                    $trans->rollback();
                    return $rs;
                }
            }
        } catch (Exception $e) {
            $trans->rollback();
            $rs['error_code'] = ErrorCode::COMMOM_SYS_ERROR;
            $rs['error_msg'] = $e->getMessage();
            return $rs;
        }

        $trans->commit();
        $rs['result'] = true;
        unset($rs['error_code']);
        return $rs;
    }
    
    /**
     * 商品库存 冻结变动  扣除与还原
     * @param unknown $project
     */
    static  function stockFrozenRestoreList($project,$outlets,$target_list,$nums,$node,$node_type,$remark=''){
    	$rs = array(
    			'result'=>false,
    			'error_code'=>ErrorCode::COMMOM_UNKNOW,
    	);
    
    	$trans = Yii::app()->db->beginTransaction();
    	try {
    
    		if (count($target_list) != count($nums)) {
    			$rs['error_code'] = ErrorCode::COMMON_PARAMS_LESS;
    			$trans->rollback();
    			return $rs;
    		}
    		

    		$targetObjList = self::model()->findAll(' project=:project AND outlets=:outlets AND target IN ('.implode(',', $target_list).')', array(':project' => $project, ':outlets' => $outlets));
    		
    		foreach ($targetObjList as $targetObj) {
    			foreach ($target_list as $k=>$target){
    				if ($targetObj['target']==$target) {
    					$num = $nums[$k];
    					
    					if (($targetObj->frozen_stock-$num)<0) {
    						$rs['error_code'] = ErrorCode::GOOD_STOCK_NOT_ENOUGH;
    						$trans->rollback();
    						return $rs;
    					}
    					
    					$rs1 = Yii::app()->db->createCommand(" UPDATE ".self::tableName()." SET `stock`=`stock`+{$num} , `frozen_stock` =`frozen_stock`-{$num}  WHERE project={$project} AND outlets={$outlets} AND target={$target}")->execute();
    					
    					$goods_stock_balance = new GoodsStockBalance();
    					$goods_stock_balance->s_id = $targetObj->id;
    					$goods_stock_balance->node = $node;
    					$goods_stock_balance->node_type = $node_type;
    					$goods_stock_balance->num = abs($num);
    					$goods_stock_balance->balance = $num;
    					$goods_stock_balance->cur_balance = $targetObj->stock+$num;
    					$goods_stock_balance->cur_frozen = $targetObj->frozen_stock-$num;
    					$goods_stock_balance->remark = $remark;
    					$goods_stock_balance->create_time = time();
    					$rs2 = $goods_stock_balance->save();

    					if (!$rs1 || !$rs2) {
    						$rs['error_code'] = ErrorCode::GOOD_STOCK_DATA_ERROR;
    						$trans->rollback();
    					}
    					
    				}
    			}
    		}
    		
    		$trans->commit();
    		$rs['result'] = true;
    		unset($rs['error_code']);
    		
    	}catch (Exception $e){
    		$trans->rollback();
    		$rs['error_code'] = ErrorCode::COMMOM_SYS_ERROR;
    		$rs['error_msg'] = $e->getMessage();
    	}
    
    	return $rs;
    }
    
    /**
     * 批量商品库存变动
     * @param unknown $project
     */
    static function stockChangeList($project, $outlets, $target_list, $nums, $remark = '') {
    	$rs = array(
    			'result' => false,
    			'error_code' => ErrorCode::COMMOM_UNKNOW,
    	);
    
    	$trans = Yii::app()->db->beginTransaction();
    
    	try {
    		if (count($target_list) != count($nums)) {
    			$rs['error_code'] = ErrorCode::COMMON_PARAMS_LESS;
    			return $rs;
    		}
    
    		$targetObj_arr = array();
    		$targetObjs = self::model()->findAll(' project=:project AND outlets=:outlets AND target  IN (' . implode(',', $target_list) . ')', array(':project' => $project, ':outlets' => $outlets,));
    
    		foreach ($targetObjs as $val) {
    			$targetObj_arr[$val->target] = $val;
    		}
    
    		foreach ($target_list as $k => $target) {
    			$num = $nums[$k] * 1;
    
    			if (!isset($targetObj_arr[$target])) {
    				// 					$rs['error_code'] = ErrorCode::GOOD_STOCK_NOT_EXIST;
    				// 					return $rs;
    				continue;
    			}
    
    			$targetObj = $targetObj_arr[$target];
    
    			$change_num = $num - $targetObj->stock;
    			if ($num>=0) {
    				$node = GoodsStockBalance::NODE_STOCK_IN;
    				$node_type = GoodsStockBalance::NODE_TYPE_IN;
    			} else {
    				$node = GoodsStockBalance::NODE_STOCK_OUT;
    				$node_type = GoodsStockBalance::NODE_TYPE_OUT;
    			}
    
    			$rs1 = Yii::app()->db->createCommand(" UPDATE " . self::model()->tableName() . " SET `stock`=`stock`+{$num}  WHERE project={$project} AND outlets={$outlets} AND target={$target}")->execute();
    
    			$goods_stock_balance = new GoodsStockBalance();
    			$goods_stock_balance->s_id = $targetObj->id;
    			$goods_stock_balance->node = $node;
    			$goods_stock_balance->node_type = $node_type;
    			$goods_stock_balance->num = abs($change_num);
    			$goods_stock_balance->balance = $change_num;
    			$goods_stock_balance->cur_balance = $num;
    			$goods_stock_balance->cur_frozen = $targetObj->frozen_stock;
    			$goods_stock_balance->remark = $remark;
    			$goods_stock_balance->create_time = time();
    			$rs2 = $goods_stock_balance->save();
    
    			if (!$rs2) {
    				$rs['error_code'] = ErrorCode::GOOD_STOCK_DATA_ERROR;
    				$trans->rollback();
    				return $rs;
    			}
    		}
    	} catch (Exception $e) {
    		$trans->rollback();
    		$rs['error_code'] = ErrorCode::COMMOM_SYS_ERROR;
    		$rs['error_msg'] = $e->getMessage();
    		return $rs;
    	}
    
    	$trans->commit();
    	$rs['result'] = true;
    	unset($rs['error_code']);
    	return $rs;
    }

}
