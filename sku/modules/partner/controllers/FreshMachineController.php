<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FreshMachine
 *
 * @author Administrator
 */
class FreshMachineController extends PController {

    public function beforeAction($action) {
        parent::beforeAction($action);

        $this->pageTitle = Yii::t('partnerModule.freshMachine', '小微企业联盟');
        $this->curr_menu_name = '/partner/freshMachine/list';

// 		$this->fresh_machine_line = empty($this->fresh_machine_line)?FreshMachine::getLineByPartnerId($this->partner_id):$this->fresh_machine_line;
// 		$this->fresh_machine_list = empty($this->fresh_machine_list)?FreshMachine::getListByPartnerId($this->partner_id):$this->fresh_machine_list;

        if (empty($this->fresh_machine_list) && empty($this->fresh_machine_line)) {
            $this->setFlash('error', Yii::t('partnerModule.freshMachine', '该用户没有生鲜机'));
            $this->redirect($this->createAbsoluteUrl('store/change'));
        }

        return true;
    }

    /**
     * 检查当前售货机是否属于当前商家
     * @param unknown $model
     */
    private function _checkAccessOld($model) {
        if (!$model->member_id || $model->member_id != $this->curr_act_member_id) {
            throw new CHttpException(403, Yii::t('partnerModule.freshMachine', '你没有权限修改别人的数据！'));
            exit();
        }
    }

    /**
     * 检查当前售货机货道是否有权限
     * @param unknown $model
     */
    private function _checkAccess($model) {
        if ($model['partner_id'] == $this->curr_act_partner_id) {
            return true;
        }
//     			$this->fresh_machine_line = empty($this->fresh_machine_line)?FreshMachine::getLineByPartnerId($this->partner_id):$this->fresh_machine_line;
//     			$this->fresh_machine_list = empty($this->fresh_machine_list)?FreshMachine::getListByPartnerId($this->partner_id):$this->fresh_machine_list;
//     			var_dump($this->fresh_machine_list);exit();
        if (!empty($this->fresh_machine_line)) {
            foreach ($this->fresh_machine_line as $l) {
                if (isset($l['machine_id']) && $model['id'] == $l['machine_id']) {
                    return true;
                }
            }
        }

        if (!empty($this->fresh_machine_list)) {
            foreach ($this->fresh_machine_list as $m) {
                if (isset($m['id']) && $model['id'] == $m['id']) {
                    return true;
                }
            }
        }

        $this->setFlash('error', Yii::t('partnerModule.freshMachine', '生鲜机、货道所有权已改变！'));
        $this->redirect($this->createAbsoluteUrl('list'));
    }

    /**
     * 检查当前售货机货道是否有权限
     * @param unknown $model
     */
    private function _checkLineAccess($model) {
        if (!$model->rent_partner_id || $model->rent_partner_id != $this->curr_act_partner_id) {
//     		throw new CHttpException(403, '你没有权限修改别人的数据！');
//     		exit();

            $this->setFlash('error', Yii::t('partnerModule.freshMachine', '你没有权限修改别人的数据！'));
            $this->redirect($this->createAbsoluteUrl('list'));
            exit();
        }
    }

    /**
     * 生鲜机列表
     */
    public function actionList() {
        $this->pageTitle = Yii::t('partnerModule.freshMachine', '盖网生鲜机管理列表 _ ') . $this->pageTitle;

        $machine_model = new FreshMachine('search');
        $machine_model->unsetAttributes();
//         $machine_model->member_id = $this->curr_act_member_id;

        if (!empty($this->fresh_machine_list)) {
            foreach ($this->fresh_machine_list as $val) {
                $machine_model->machine_ids[] = $val['id'];
            }
        }

        if (!empty($this->fresh_machine_line)) {
            foreach ($this->fresh_machine_line as $val) {
                $machine_model->machine_ids[] = $val['machine_id'];
            }
        }

        $lists = $machine_model->search();
        $machine_data = $lists->getData();
        $pager = $lists->pagination;

        $this->render('list', array(
            'machine_model' => $machine_model,
            'machine_data' => $machine_data,
            'pager' => $pager,
        ));
    }

    /**
     * 商品管理
     */
    public function actionFreshGoods() {
        $mid = $this->getParam('mid') * 1;
        $status = $this->getParam('status');
        $this->pageTitle = Yii::t('partnerModule.freshMachine', '盖网生鲜机列表 _ ') . $this->pageTitle;
        $m_model = FreshMachine::model()->findByPk($mid);
        if (empty($m_model)) {
            exit('机器不存在');
        }
        $this->_checkAccess($m_model);

        $model = new FreshMachineGoods('search');
        $model->status = $status;
        $model->machine_id = $m_model->id;
        $model->partner_id = $this->curr_act_partner_id;
        //var_dump($model->attributes);die;
        if ($m_model->status == FreshMachine::STATUS_APPLY || $m_model->status == FreshMachine::STATUS_DISABLE) {
            $this->setFlash('error', Yii::t('partnerModule.freshMachine', '该生鲜机未通过审核或被禁用！'));
            $this->redirect($this->createAbsoluteUrl('list'));
        }
        if (isset($_GET[get_class($model)])) {
            $model->attributes = $this->getQuery(get_class($model));
        }

        $lists = $model->freshSearch();

        $goods_data = $lists->getData();
        $pager = $lists->pagination;

        //查询库存
        $line_ids = array();

        foreach ($goods_data as $data) {
            $line_ids[] = $data->line_id;
            //检查货道时间是否失效
            if (!empty($data->lines->expir_time) && $data->lines->expir_time < time() && $data->status != FreshMachineGoods::STATUS_DISABLE) {
//                 $this->actionGoodsDisable($data->id, FreshMachine::TYPE_LINE);
                Yii::app()->db->createCommand()->update(FreshMachineGoods::model()->tableName(), array('status' => FreshMachineGoods::STATUS_DISABLE), 'id =' . $data->id);
                $data->status = FreshMachineGoods::STATUS_DISABLE;
            }
        }
        $stocks = ApiStock::goodsStockList($m_model->id, $line_ids, API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
        $this->render('goodsList', array(
            'm_model' => $m_model,
            'model' => $model,
            'mid' => $mid,
            'goods_data' => $goods_data,
            'pager' => $pager,
            'stocks' => $stocks,
        ));
    }

    /**
     * 盖网生鲜机商品添加
     * Enter description here ...
     */
    public function actionGoodsAdd() {

        $mid = $this->getParam('mid') * 1;
        $m_model = FreshMachine::model()->findByPk($mid);
        $this->_checkAccess($m_model);
        $this->pageTitle = Yii::t('partnerModule.freshMachine', '添加生鲜机商品') . $this->pageTitle;
        $model = new FreshMachineGoods();
//        $model->scenario = 'stock';
        $this->performAjaxValidation($model);
        if ($m_model->status == FreshMachine::STATUS_APPLY || $m_model->status == FreshMachine::STATUS_DISABLE) {
            $this->setFlash('error', Yii::t('partnerModule.freshMachine', '该生鲜机未通过审核或被禁用！'));
            $this->redirect($this->createAbsoluteUrl('list'));
        }

        if (isset($_POST['FreshMachineGoods'])) {
            $post = $this->getPost('FreshMachineGoods');
            $goods = Goods::model()->findByPK($post['goods_id']);
            $model->attributes = $post;
            $model->price = $goods['price'];
            $model->machine_id = $m_model->id;
            $model->create_time = time();
            $line = FreshMachineLine::model()->findByPk($_POST['FreshMachineGoods']['line_id']);
            $model->line_code = empty($line) ? '' : $line->code;
            $model->expr_time = strtotime($model->expr_time);
            $model->stock = isset($_POST['FreshMachineGoods']['stock']) ? $_POST['FreshMachineGoods']['stock'] : '';

//             //检查重复商品
//             if (FreshMachineGoods::model()->count(' machine_id=:machine_id AND goods_id=:goods_id  ', array(':machine_id' => $m_model->id, ':goods_id' => $model->goods_id))) {
//                 $this->setFlash('error', Yii::t('partnerModule.freshMachine', '生鲜机商品已存在！'));
//                 $this->redirect($this->createAbsoluteUrl('goodsAdd', array('mid' => $mid)));
//                 return;
//             }
            //新增的货道先判断是否存在货道库存

            $goods_stock = GoodsStock::model()->find(' project=:project AND outlets=:outlets AND target = :target', array(':project' => API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID, ':outlets' => $m_model->id, ':target' => $line->id));
            if (!$goods_stock) {
                //接口创建库存   由于业务需要一台机器支持上架同个商品多次， 使用货道id做库存标记
                $stocks_rs = ApiStock::createStock($m_model->id, $line->id, $model->stock, API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
            } else {
                $stocks_rs = ApiStock::stockSet($m_model->id, $line->id, $model->stock, API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
            }

            if (!isset($stocks_rs['result']) || $stocks_rs['result'] != true) {
                $this->setFlash('error', Yii::t('partnerModule.freshMachine', '添加库存失败！'));
                $this->redirect($this->createAbsoluteUrl('goodsAdd', array('mid' => $mid)));
                return;
            }

            if ($model->save()) {

                //更改goods表的商品重量
               /// Goods::model()->updateByPk($model->goods_id,array('weight' => $model->weight));

                $this->setFlash('success', Yii::t('partnerModule.freshMachine', '添加生鲜机商品成功'));

                ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeInsert, $model->id, '添加生鲜机(' . $m_model->name . ')商品:' . Goods::model()->findByPk($model->goods_id)->name . '|初始库存:' . $model->stock);
                $this->redirect($this->createAbsoluteUrl('freshGoods', array('mid' => $mid)));
            } else {
                $this->setFlash('error', Yii::t('partnerModule.freshMachine', '添加生鲜机商品失败'));
            }
        }

        $this->render('goodsAdd', array(
            'm_model' => $m_model,
            'model' => $model,
            'mid' => $mid,
        ));
    }

    /**
     * 商品修改
     */
    public function actionGoodsEdit() {

        $goods_id = $this->getParam('id');
        $mid = $this->getParam('mid');
        $line_id = $this->getParam('line_id');
        $fresh_goods_id = $this->getParam('gid');

        $this->pageTitle = Yii::t('partnerModule.freshMachine', '修改生鲜机商品') . $this->pageTitle;
        $m_model = FreshMachine::model()->findByPk($mid);
        $model = FreshMachineGoods::model()->findByPk($fresh_goods_id);

//        var_dump($model);die;
        $this->_checkAccess($m_model);
        $o_line_id = $model->line_id;
        $o_data = $model->attributes;
        $model->expr_time = $model->expr_time > 0 ? date('Y-m-d H:i:s',$model->expr_time) : "0000-00-00 00:00:00";
        $fresh_line = FreshMachineLine::model()->findAll('status=:status and machine_id=:mid AND rent_partner_id=:pid and status=:status or id=:id ', array(':status' => FreshMachineLine::STATUS_ENABLE, ':mid' => $mid, ':pid' => $this->curr_act_partner_id, ':id' => $model->line_id));
        $lines = array();
//        var_dump($fresh_line);die;
        /**
         * 筛选去除失效货道
         */
        foreach ($fresh_line as $k => $v) {
            if ((!empty($v->expir_time) && $v->expir_time > time()) || empty($v->expir_time)) {
                $lines[$k] = $v;
            }
        }
        if (isset($_POST['FreshMachineGoods'])) {
            try {

            $model->attributes = $this->getPost('FreshMachineGoods');

            $line = FreshMachineLine::model()->findByPk($model->line_id);
            $model->line_code = $line->code;
            $model->expr_time = strtotime($model->expr_time);
            $diff = array_diff($model->attributes, $o_data);
            if (isset($diff['line_id'])) {
                unset($diff['line_id']);
            }
            $str = $model->attributeLabels();
            $content = '';
            foreach ($diff as $k => $v) {

                $content.=$str[$k] . ':' . $o_data[$k] . '->' . $v . ' | ';
            }
            $content = rtrim($content, ' | ');
            $content = empty($content) ? '无操作内容' : $content;
            //检查货道是否可用以及权限
            if ($o_line_id != $model->line_id) {
                if ($line['status'] != FreshMachineLine::STATUS_ENABLE || $line['rent_partner_id'] != $this->curr_act_partner_id) {
                    $this->setFlash('error', Yii::t('partnerModule.freshMachine', '货道不可用'));
                    $this->redirect($_SERVER['HTTP_REFERER']);
                }
            }
             //修改已支付订单货道
                $order_goods = OrdersGoods::model()->findAll('line_id=:lid',array(':lid'=>$o_line_id));              
                $order_id =array();
                foreach($order_goods as $v){
                    $order_id[]=$v['order_id'];
                }
                if(!empty($order_id)){
                    $pay_order = Yii::app()->db->createCommand(' SELECT id,code  FROM ' . Order::model()->tableName() . ' WHERE (status='.Order::STATUS_PAY.' OR status='.Order::STATUS_NEW .') AND  id IN ( ' . implode(',', $order_id) . ')')->queryAll();
                
                    $remark = '订单商品修改货道，订单自动取消';
                    foreach($pay_order as $v){
                        Order::orderCancel($v['code'],true,$remark);
                    }
              }
           
                if ($o_line_id != $model->line_id) {
                    //更新旧货道状态
                    $fresh_ol_goods = FreshMachineGoods::model()->find('line_id=:id and status=:s and id!=:gid', array(':id' => $o_line_id, ':s' => FreshMachineGoods::STATUS_ENABLE, ':gid' => $fresh_goods_id)); //是否旧货道上架的是其他商品
//                    var_dump($fresh_ol_goods);die;
                    if (!$fresh_ol_goods) {
                        Yii::app()->db->createCommand()->update(FreshMachineLine::model()->tableName(), array('status' => FreshMachineLine::STATUS_ENABLE), 'id=' . $o_line_id);
                    }
                }
                //商品已上架则修改货道状态
                if ($model->status == FreshMachineGoods::STATUS_ENABLE) {
                    Yii::app()->db->createCommand()->update(FreshMachineLine::model()->tableName(), array('status' => FreshMachineLine::STATUS_EMPLOY), 'id=' . $model->line_id);
                }

                
                
                //更新库存
                $old_stock = ApiStock::goodsStockOne($m_model['id'], $o_line_id, API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);

                ApiStock::createStock($m_model['id'], $model->line_id, 0, API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);              //
                ApiStock::stockSet($m_model['id'], $model->line_id, $old_stock['result']['stock'] * 1, API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
//                ApiStock::stockSet($m_model['id'], $o_line_id,0,API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
                $model->save();
                /*if($model->save()){
                    //更改goods表的商品重量
                    Goods::model()->updateByPk($model->goods_id,array('weight' => $model->weight));
                }*/
                $this->setFlash('success', Yii::t('partnerModule.freshMachine', '修改生鲜机商品成功'));
                ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeUpdate, $model->id, '修改生鲜机(' . $m_model->name . ')商品(' . Goods::model()->findByPk($model->goods_id)->name . ')内容 : ' . $content);
                $this->redirect($this->createAbsoluteUrl('freshGoods', array('mid' => $mid)));
              } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        $this->render('goodsEdit', array(
            'm_model' => $m_model,
            'model' => $model,
            'mid' => $mid,
            'goods_id' => $goods_id,
            'lines' => $lines
        ));
    }

    /**
     * 生鲜机商品上架
     */
    public function actionGoodsEnable($id) {
        $machine_goods = FreshMachineGoods::model()->findByPk($id);
        $goods = Goods::model()->find('id=:gid', array(':gid' => $machine_goods->goods_id));
        //判断是否存在货道
        if (!empty($machine_goods->line_id)) {
            $line = FreshMachineLine::model()->findByPk($machine_goods->line_id);
        } else {
            $this->setFlash('error', Yii::t('partnerModule.freshMachine', '请先选择货道'));
            $this->redirect($_SERVER['HTTP_REFERER']);
        }

//        var_dump($line);die;
        if ($goods->status == Goods::STATUS_NOPASS || $goods->status == Goods::STATUS_AUDIT) {
            $this->setFlash('error', Yii::t('partnerModule.freshMachine', '商品审核未通过，不能上架'));
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
        if (!empty($line->status) && $line->status == FreshMachineLine::STATUS_EMPLOY) {
            $this->setFlash('error', Yii::t('partnerModule.freshMachine', '货道已被其他商品已占用，请重新选择货道'));
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
        if (!empty($line->status) && $line->status == FreshMachineLine::STATUS_DISABLE) {
            $this->setFlash('error', Yii::t('partnerModule.freshMachine', '货道已禁用，不能上架'));
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
        //判断货道是否过期
        if (!empty($machine_goods->line_id) && !empty($line->expir_time)) {
            if ($line->expir_time < time()) {
                $this->setFlash('error', Yii::t('partnerModule.freshMachine', '货道已失效，不能上架'));
                $this->redirect($_SERVER['HTTP_REFERER']);
            }
        }
        $this->pageTitle = Yii::t('partnerModule.freshMachine', '生鲜机商品上架') . $this->pageTitle;
        $model = FreshMachineGoods::model()->findByPk($id);
        $m_model = FreshMachine::model()->findByPk($model->machine_id);
        $this->_checkLineAccess($line);

        $model->status = FreshMachineGoods::STATUS_ENABLE;

        if ($model->save()) {
            //自动下架其他商品
            Yii::app()->db->createCommand()->update(FreshMachineGoods::model()->tableName(), array('status' => FreshMachineGoods::STATUS_DISABLE), 'machine_id=:machine_id AND machine_id=:machine_id AND line_id=:line_id AND goods_id!=:goods_id  ', array(':machine_id' => $m_model->id, ':line_id' => $machine_goods->line_id, ':goods_id' => $machine_goods->goods_id));

            //设置货道为占用状态
            $line->status = FreshMachineLine::STATUS_EMPLOY;
            $line->save();
            $this->setFlash('success', Yii::t('partnerModule.freshMachine', '生鲜机商品上架成功'));
            ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeUpdate, $model->id, '生鲜机(' . $m_model->name . ')商品上架:' . Goods::model()->findByPk($model->goods_id)->name);
        } else {
            $this->setFlash('error', Yii::t('partnerModule.freshMachine', '设置售货机商品上架'));
        }
        $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * 生鲜机机商品下架
     */
    public function actionGoodsDisable($id, $type = 0, $status = null) {

        $machine_goods = FreshMachineGoods::model()->findByPk($id);

        $line = FreshMachineLine::model()->findByPk($machine_goods->line_id);

//        if ($machine_goods->status == FreshMachineGoods::STATUS_DISABLE) {
//            $this->setFlash('error', Yii::t('partnerModule.freshMachine', '商品审核未通过，已自动下架'));
//            $this->redirect($_SERVER['HTTP_REFERER']);
//        }
        $this->pageTitle = Yii::t('partnerModule.freshMachine', '售货机商品下架') . $this->pageTitle;
        $model = FreshMachineGoods::model()->findByPk($id);
        $m_model = FreshMachine::model()->findByPk($model->machine_id);
        $this->_checkLineAccess($line);

        $tran = Yii::app()->db->beginTransaction();

        $model->status = FreshMachineGoods::STATUS_DISABLE;

        if ($model->save()) {
            if (!empty($status)) {
                $line->status = $status;
            } else {
                $line->status = FreshMachineLine::STATUS_ENABLE;
            }
            $line->save();
            //取消相关未支付订单
            $orders = Yii::app()->db->createCommand()
                    ->select('t.code')
                    ->from(Order::model()->tableName() . ' t')
                    ->leftJoin(OrdersGoods::model()->tableName() . ' g', 't.id=g.order_id')
                    ->where('t.type=' . Order::TYPE_FRESH_MACHINE . '  AND (t.status=' . Order::STATUS_NEW . ' AND t.pay_status=' . Order::PAY_STATUS_NO .  ' AND g.sgid=:sgid )  OR (t.pay_status='.Order::PAY_STATUS_YES. '  AND t.status=' . Order::STATUS_PAY .  ' AND g.sgid=:sgid )',array(':sgid' => $id))
                    ->queryAll();
            foreach ($orders as $o) {
                Order::orderCancel($o['code'], true, Yii::t('partnerModule.freshMachine', '生鲜机部分商品下架，订单自动取消'), false);
            }
            
            $stock_rs = ApiStock::stockSet($model->machine_id, $model->line_id, 0, API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
            $GoodsStock = GoodsStock::model()->find('outlets=:o and target=:t',array(':o'=>$model->machine_id,':t'=>$model->line_id));
            if($GoodsStock){
                $GoodsStock->frozen_stock = 0;
                $GoodsStock->save();
            }
//             var_dump($stock_rs);die;

            if (isset($stock_rs['result']) && $stock_rs['result']) {
//            if (1==1) {
                $tran->commit();
                ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeUpdate, $model->id, '下架生鲜机(' . $m_model->name . ')商品:' . Goods::model()->findByPk($model->goods_id)->name);
                if ($type == FreshMachine::TYPE_LINE) {
                    return true;
                }
                $this->setFlash('success', Yii::t('partnerModule.freshMachine', '生鲜机商品下架成功'));
//                ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeUpdate, $model->id, '添加生鲜机商品:' .  Goods::model()->findByPk($model->goods_id)->name . '| id->' . $model->id);
            } else {
                $tran->rollback();
                $this->setFlash('error', Yii::t('partnerModule.freshMachine', '设置生鲜机商品下架失败，库存更新失败'));
            }
        } else {
            $this->setFlash('error', Yii::t('partnerModule.freshMachine', '设置生鲜机商品下架失败'));
        }
        $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * 售货机商品入库
     * @author leo8705
     * Enter description here ...
     * @param unknown_type $id
     */
    public function actionGoodsStockIn($id) {
        $this->pageTitle = Yii::t('partnerModule.freshMachine', '售货机商品进货') . $this->pageTitle;
        $model = FreshMachineGoods::model()->findByPk($id);
        $m_model = FreshMachine::model()->findByPk($model->machine_id);
        $this->_checkAccess($m_model);

        $model->scenario = 'stock';
        $this->performAjaxValidation($model);

        if (isset($_POST['FreshMachineGoods'])) {
//     		var_dump($_POST);exit();
            $model->attributes = $this->getPost('FreshMachineGoods');

            $stock = ApiStock::goodsStockOne($m_model->id, $model->goods_id, API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
            $stock = isset($stock['result']['stock']) ? $stock['result']['stock'] * 1 : 0;

            $stock_config = $this->params('stock');
            if ($stock_config['maxStock'] <= ($stock + $model->stock)) {
                $this->setFlash('error', Yii::t('partnerModule.freshMachine', '不能超过最大库存，最大库存为') . $stock_config['maxStock']);
                $this->redirect($this->createAbsoluteUrl('freshGoods', array('mid' => $m_model->id)));
            }


            //接口创建库存
            $line = FreshMachineLine::model()->findByPk($model->line_id);
            $stocks_rs = ApiStock::stockIn($m_model->id, $line->id, $model->stock, API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
//              $change_stock = empty($model->stock)?'无操作内容':$model->stock;
            if (isset($stocks_rs['result']) && $stocks_rs['result'] == true) {
                $this->setFlash('success', Yii::t('partnerModule.freshMachine', '售货机商品库存进货成功！'));
                ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeUpdate, $model->id, '生鲜机(' . $m_model->name . ')商品进货:' . Goods::model()->findByPk($model->goods_id)->name . '| 进货数:' . $model->stock);
            } else {
                $this->setFlash('error', Yii::t('partnerModule.freshMachine', '售货机商品库存进货失败'));
            }
            $this->redirect($this->createAbsoluteUrl('freshGoods', array('mid' => $m_model->id)));
        }

        $this->render('machineGoodsStockIn', array(
            'model' => $model,
        ));
    }

    /**
     * 售货机商品入库
     * @author leo8705
     * Enter description here ...
     * @param unknown_type $id
     */
    public function actionGoodsStockOut($id) {
        $this->pageTitle = Yii::t('partnerModule.freshMachine', '售货机商品出货') . $this->pageTitle;
        $model = FreshMachineGoods::model()->findByPk($id);
        $m_model = FreshMachine::model()->findByPk($model->machine_id);
        $this->_checkAccess($m_model);

        $model->scenario = 'stock';
        $this->performAjaxValidation($model);

        if (isset($_POST['FreshMachineGoods'])) {
            $model->attributes = $this->getPost('FreshMachineGoods');

            //接口创建库存
            $line = FreshMachineLine::model()->findByPk($model->line_id);
            $stocks_rs = ApiStock::stockOut($m_model->id, $line->id, $model->stock, API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
//              $change_stock = empty($model->stock)?'无操作内容':$model->stock;
            if (isset($stocks_rs['result']) && $stocks_rs['result'] == true) {
                $this->setFlash('success', Yii::t('partnerModule.freshMachine', '售货机商品库存出货成功！'));
                ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeUpdate, $model->id, '生鲜机(' . $m_model->name . ')商品出货:' . Goods::model()->findByPk($model->goods_id)->name . '|出货数:' . $model->stock);
            } else {
                $this->setFlash('error', Yii::t('partnerModule.freshMachine', '售货机商品库存出货失败'));
            }
            $this->redirect($this->createAbsoluteUrl('freshGoods', array('mid' => $m_model->id)));
        }

        $this->render('machineGoodsStockOut', array(
            'model' => $model,
        ));
    }

    /**
     * 货道列表
     */
    public function actionFreshLine() {
        $mid = $this->getParam('mid') * 1;
        $name = $this->getParam('name');
        $this->pageTitle = Yii::t('partnerModule.freshMachine', '盖网生鲜机列表 _ ') . $this->pageTitle;
        $m_model = FreshMachine::model()->findByPk($mid);
        $this->_checkAccess($m_model);

        $model = new FreshMachineLine('search');
        if ($m_model->status == FreshMachine::STATUS_APPLY || $m_model->status == FreshMachine::STATUS_DISABLE) {
            $this->setFlash('error', Yii::t('partnerModule.freshMachine', '该生鲜机未通过审核或被禁用！'));
            $this->redirect($this->createAbsoluteUrl('list'));
        }
        if (isset($_GET[get_class($model)]))
            $model->attributes = $this->getQuery(get_class($model));

        $model->machine_id = $m_model->id;
        $model->code = $name;
        $lists = $model->lineSearch();
        $goods_data = $lists->getData();
        $pager = $lists->pagination;
        $this->render('lineList', array(
            'm_model' => $m_model,
            'model' => $model,
            'mid' => $mid,
            'goods_data' => $goods_data,
            'pager' => $pager,
        ));
    }

    /*
     * 添加货道
     */

    public function actionLineAdd() {
        $mid = $this->getParam('mid');
        $machine = FreshMachine::model()->findByPk($mid);
        $model = new FreshMachineLine();
        $model->scenario = 'create';
        $model->machine_id = $mid;
        $this->performAjaxValidation($model);
        $gai_number = Partners::model()->findByPk($this->curr_act_partner_id)->gai_number;
        if (isset($_POST['FreshMachineLine'])) {
            //每台生鲜机最多添加36个货道
            $count = FreshMachineLine::model()->count('machine_id=:mid', array(':mid' => $mid));

            if ($count < 36) {
                $model->attributes = $this->getPost('FreshMachineLine');
                $model->create_time = time();
                $model->expir_time = strtotime($_POST['FreshMachineLine']['expir_time']);
                if (($_POST['FreshMachineLine']['gai_number'])) {
                    $partner = Partners::model()->find('gai_number=:gw', array(':gw' => $_POST['FreshMachineLine']['gai_number']));
                    if (!empty($partner)) {
                        $model->rent_member_id = $partner->member_id;
                        $model->rent_partner_id = $partner->id;
                    }
                }
                if ($model->save()) {
                    $this->setFlash('success', Yii::t('partnerModule.freshMachine', '货道添加成功'));
//                    SuperLog::create(SuperLog::CAT_COMPANY, SuperLog::logTypeInsert, $model->id, '添加货道：:' . $model->name . '| id->' . $model->id);
                    ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeUpdate, $model->id, '添加(' . $machine->name . ')货道:' . $model->name);
                    $this->redirect($this->createAbsoluteUrl('freshLine', array('mid' => $mid)));
                } else {
                    $this->setFlash('error', Yii::t('partnerModule.freshMachine', '货道添加失败'));
                }
            } else {
                $this->setFlash('error', Yii::t('partnerModule.freshMachine', '每台生鲜机最多添加36条货道'));
            }
        }
        $this->render('lineAdd', array(
            'model' => $model,
            'mid' => $mid,
            'gai_number' => $gai_number
        ));
    }

    /*
     * 货道修改
     */

    public function actionLineEdit($id) {
        $id = $id * 1;
        $mid = $this->getParam('mid');
        $machine = FreshMachine::model()->findByPk($mid);
        $model = FreshMachineLine::model()->findByPk($id);
        $model->scenario = 'update';
        $this->performAjaxValidation($model);
        if (!empty($model->rent_partner_id)) {
            $partner_id = $model->rent_partner_id;
            $partner = Partners::model()->findByPk($partner_id);
            $model->gai_number = $partner->gai_number;
        }

        $this->_checkAccess(FreshMachine::model()->findByPk($model['machine_id']));
        
        $old_gai = isset($partner->gai_number) ? $partner->gai_number : '';

        if (isset($_POST['FreshMachineLine'])) {
            $old_status = $model->status;
            $old_data = $model->attributes;
            $model->attributes = $this->getPost('FreshMachineLine');
            $model->machine_id = $mid;
            $model->expir_time = !empty($_POST['FreshMachineLine']['expir_time']) ? strtotime($_POST['FreshMachineLine']['expir_time']) : '0';

            $diff = array_diff($model->attributes, $old_data);
            $old_data['gai_number'] = $old_gai;
            if($old_gai!=$model->gai_number){
                $diff['gai_number'] =$model->gai_number;
            }
            $str = $model->attributeLabels();
            $content = '';
            foreach ($diff as $k => $v) {

                if ($k == 'status') {
                    $content.=$str[$k] . ':' . FreshMachineLine::getStatus($old_data[$k]) . '->' . FreshMachineLine::getStatus($v) . ' | ';
                } else {
                    $content.= ($k == 'expir_time') ? ($str[$k] . ':' . ($old_data[$k] == '0' ? '无限制' : date('Y-m-d H:i:s', ($old_data[$k]))) . '->' . ($v == '0' ? '无限制' : date('Y-m-d H:i:s', ($v))) . ' | ') : ($str[$k] . ':' . $old_data[$k] . '->' . $v . ' | ');
                }
            }

            $content = rtrim($content, ' | ');
            $content = empty($content) ? '无操作内容' : $content;
            //修改货道状态为可以用时  下架已占用货道的商品
            if (($model->status == FreshMachineLine::STATUS_ENABLE && $old_status != FreshMachineLine::STATUS_ENABLE) || ($model->status == FreshMachineLine::STATUS_DISABLE && $old_status != FreshMachineLine::STATUS_DISABLE)) {
                $goods = array();
                $goods = FreshMachineGoods::model()->findAll('line_id=:lid', array(':lid' => $id));

                if (!empty($goods)) {
                    $disable_goods_ids = array();
                    foreach ($goods as $k => $v) {
                        $disable_goods_ids[] = $v['id'];
//                          if($model->status == FreshMachineLine::STATUS_DISABLE ){
//                              $this->actionGoodsDisable($v->id,  FreshMachine::TYPE_LINE,FreshMachineLine::STATUS_DISABLE);
// //                              
//                          }else{
//                              $this->actionGoodsDisable($v->id,  FreshMachine::TYPE_LINE);
//                          }
                        //如果修改GW删除  原商家商品（已下架）
                        if ($model->gai_number != $old_gai) {

                            FreshMachineGoods::model()->deleteByPk($v->id);
                            //FreshMachineGoods::model()->updateByPk($v->id,array('status'=> FreshMachineGoods::STATUS_DISABLE));
                        }
                    }

                    if (!empty($disable_goods_ids))
                        Yii::app()->db->createCommand()->update(FreshMachineGoods::model()->tableName(), array('status' => FreshMachineGoods::STATUS_DISABLE), 'id IN(' . implode(',', $disable_goods_ids) . ')');
                }
            }
            //修改GW  删除原商家未上架商品
            if ($model->gai_number != $old_gai) {
                $goods = FreshMachineGoods::model()->findAll('line_id=:lid', array(':lid' => $id));
                if (!empty($goods)) {
                    foreach ($goods as $v) {
                        FreshMachineGoods::model()->deleteByPk($v->id);
                        //FreshMachineGoods::model()->updateByPk($v->id,array('status'=> FreshMachineGoods::STATUS_DISABLE));
                    }
                }
                $model->status = FreshMachineLine::STATUS_ENABLE;
            }
            if (isset($_POST['FreshMachineLine']['gai_number']) && $old_gai != $_POST['FreshMachineLine']['gai_number']) {
                $partner = Partners::model()->find('gai_number=:gw', array(':gw' => $_POST['FreshMachineLine']['gai_number']));
                if (!empty($partner)) {
                    $model->rent_member_id = $partner->member_id;
                    $model->rent_partner_id = $partner->id;
                }
            }
            if ($model->save()) {
                $this->setFlash('success', Yii::t('partnerModule.freshMachine', '货道修改成功'));
                ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeUpdate, $model->id, '修改生鲜机(' . $machine->name . ')货道内容:' . $content);
                $this->redirect($this->createAbsoluteUrl('freshLine', array('mid' => $mid)));
            } else {
                $this->setFlash('error', Yii::t('partnerModule.freshMachine', '货道修改失败'));
                $this->redirect($this->createAbsoluteUrl('freshLine', array('mid' => $mid)));
            }
        }
        $model->expir_time = !empty($model->expir_time) ? date('Y-m-d H:i:s', $model->expir_time) : '';

        $this->render('lineEdit', array(
            'model' => $model,
            'mid' => $mid,
        ));
    }

    /**
     * 商品批量上架
     */
    public function actionMultEnableGoods() {

        $ids = $this->getParam('idArr');
        $ids = explode(',', $ids);

        $line = array();
        if (empty($ids)) {
            echo '参数错误';
            exit();
        }

        foreach ($ids as $k => $v) {
            $ids[$k] = $v * 1;
            $machine_goods = FreshMachineGoods::model()->findByPk($v);
            $machine_id = $machine_goods->machine_id;
            $line[] = $machine_goods->line_id;
        }
        if (count($line) != count(array_unique($line))) {
            echo '批量上架失败，一个货道只能上架一个商品！';
            exit();
        }
        foreach ($line as $v) {
            $goods_line = FreshMachineLine::model()->findByPk($v);
            if ($goods_line->status != FreshMachineLine::STATUS_ENABLE) {
                echo '批量上架失败，货道：' . $goods_line->name . '已被占用或禁用，请确认';
                exit();
            }
        }
        $machine = FreshMachine::model()->findByPk($machine_id);
        $sql = 'SELECT g.name FROM ' . Goods::model()->tableName() . ' AS g LEFT JOIN ' . FreshMachineGoods::model()->tableName() . ' AS f ON g.id = f.goods_id WHERE f.id IN(' . implode(',', $ids) . ')';
        $goods_arr = Yii::app()->db->createCommand($sql)->queryAll();
        $goods_name = '';
        foreach ($goods_arr as $val) {
            $goods_name.=$val['name'] . ',';
        }
        $goods_name = rtrim($goods_name, ',');
        $trans = Yii::app()->db->beginTransaction();
        try {
            $rs = Yii::app()->db->createCommand(
                            'UPDATE ' . FreshMachineGoods::model()->tableName() . ' as t , ' . Goods::model()->tableName() . ' as g , ' . FreshMachineLine::model()->tableName() . ' as l
			SET
				t.status=' . FreshMachineGoods::STATUS_ENABLE . '
			WHERE
				t.id IN(' . implode(',', $ids) . ') AND g.member_id=' . $this->curr_act_member_id . ' AND t.goods_id=g.id  AND g.status=' . Goods::STATUS_PASS . ' AND t.status!=' . FreshMachineGoods::STATUS_ENABLE . ' 
    			AND t.line_id = l.id AND l.status=' . FreshMachineLine::STATUS_ENABLE . '
			 '
                    )->execute();

            $rs2 = Yii::app()->db->createCommand(
                            'UPDATE ' . FreshMachineLine::model()->tableName() . ' 
			SET
				status=' . FreshMachineLine::STATUS_EMPLOY . '
			WHERE
				id IN(' . implode(',', $line) . ') '
                    )->execute();

            $trans->commit();
            if ($rs && $rs2) {
                echo '批量上架成功';
                ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeInsert, 0, '批量上架生鲜机(' . $machine->name . ')商品：' . $goods_name);
            } else {
                echo '批量上架失败';
            }
            exit();
        } catch (Exception $e) {
            $trans->rollback();
            ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeInsert, 0, '批量上架生鲜机(' . $machine->name . ')商品：' . $goods_name);
            echo '批量上架失败';
            exit();
        }
    }

    /**
     * 导出Excel
     */
    public function actionExport(){

        $headList = array('导出时间','网点名称','商品名称','条形码','销售价','货道','占用库存','可用库存');
        //获取生鲜机列表信息
        $machine_model = new FreshMachine('search');
        //$machine_model->dbCriteria->limit = $machine_model->exportLimit;
        //$machine_model->dbCriteria->offset = isset($_GET['page']) ? ($_GET['page']-1)*$machine_model->exportLimit : $machine_model->exportLimit;

        $machine_model->unsetAttributes();
        $machine_model->status = FreshMachine::STATUS_ENABLE;
        $machine_model->isExport = 1;
        if (!empty($this->fresh_machine_list)) {
            foreach ($this->fresh_machine_list as $val) {
                $machine_model->machine_ids[] = $val['id'];
            }
        }

        if (!empty($this->fresh_machine_line)) {
            foreach ($this->fresh_machine_line as $val) {
                $machine_model->machine_ids[] = $val['machine_id'];
            }
        }
        $data = FreshMachine::model()->getFreshMachineExport($machine_model);
        //假设$data_array是一个多维数组
        $pagesize = 5000; //设置记录显示条数
        $totalCount = count($data['data']); //计算数组所得到记录总数
        $pagecount = ceil($totalCount / $pagesize);
        $page =  isset($_GET['page']) ? trim($_GET['page']) : 1;//初始化页码
        $offset = $page - 1; //初始化分页指针
        $start = $offset * $pagesize; //初始化下限
        if($totalCount < $pagesize){
            $pagesize = $totalCount;
        }
        $end = $start + $pagesize; //初始化上限
        $arr = array(
            'totalCount' => $totalCount,
            'pagecount'  => $pagecount,
            'success'    => true
        );

        if(isset($_REQUEST['export']) && $_REQUEST['export'] == 1){
            echo json_encode($arr);
            die;
        }else{
            $item = array();
            $tmp = array_values($data['data']);
            for($i=$start;$i<$end;$i++){
                //输出数据
                $item[$i]['exportTime']   = $tmp[$i]['exportTime'];
                $item[$i]['machineName']  = $tmp[$i]['machineName'];
                $item[$i]['goodsName']    = $tmp[$i]['goodsName'];
                $item[$i]['barcode']      = $tmp[$i]['barcode'];
                $item[$i]['goodsPrice']   = $tmp[$i]['goodsPrice'];
                $item[$i]['lineName']     = $tmp[$i]['lineName'];
                $item[$i]['frozen_stock'] = $tmp[$i]['frozen_stock'];
                $item[$i]['stock']        = $tmp[$i]['stock'];
            }

            if(isset($data['success']) && $data['success'] == false){
                $this->setFlash('error', Yii::t('partnerModule.freshMachine', $data['message']));
                $this->redirect($this->createAbsoluteUrl('list'));
                exit();
            }
            Fun::cleanExcel('生鲜机列表',$headList,$item);
            exit;
        }


        return false;

    }

}
