<?php

/**
 * 订单管理
 * @author zehui.hong
 */
class OrderController extends MController {

    public $showExport = false;
    public $exportAction;

    public function filters() {
        return array(
            'rights',
        );
    }

    /**
     * 订单列表
     */
    public function actionAdmin() {
        $model = new Order('search');
        $model->unsetAttributes();
        if (isset($_GET['Order'])) {
            $model->attributes = $_GET['Order'];
        }
        $c = $model->search();
        $totalCount = $c->getTotalItemCount();
        if (Yii::app()->user->checkAccess('Manage.Order.Export')) {
            $this->showExport = true;
        }

        $this->exportAction = 'export';
        $exportPage = new CPagination($totalCount);
        $exportPage->route = 'order/export';
        $exportPage->params = array_merge(array('grid_mode' => 'export'), $_GET);
        $exportPage->pageSize = 5000;
        $this->render('admin', array(
            'model' => $model,
            'exportPage' => $exportPage,
            'totalCount' => $totalCount,
        ));
    }

    /**
     * 盖鲜生订单列表
     */
    public function actionFreshAdmin() {
        $model = new Order('search');
        $model->unsetAttributes();

        if (isset($_GET['Order'])) {
            $model->attributes = $_GET['Order'];
            $machine = $model->machine_id;
            $store = $model->store_id;
            $machine_int = intval($model->machine_id);
            $store_int = intval($model->store_id);
        }

        $model->type = Order::TYPE_FRESH_MACHINE;

        $c = $model->search();
        $totalCount = $model->search()->getTotalItemCount();
        if (Yii::app()->user->checkAccess('Manage.Order.FreshExport')) {
            $this->showExport = true;
        }

        $this->exportAction = 'export';
        $exportPage = new CPagination($totalCount);
        $exportPage->route = 'order/freshExport';
        $exportPage->params = array_merge(array('grid_mode' => 'export'), $_GET);
        $exportPage->pageSize = $model->pageSize;
        if ((!empty($model->machine_id) && empty($machine_int))) {
            $model = new Order();
            $model->machine_id = $machine;
            if ((!empty($model->store_id) && empty($store_int))) {
                $model->store_id = $store;
            }
        } elseif ((!empty($model->store_id) && empty($store_int))) {
            $model = new Order();
            $model->store_id = $store;
            if ((!empty($model->machine_id) && empty($machine_int))) {
                $model->machine_id = $machine;
            }
        }
        $this->render('admin', array(
            'model' => $model,
            'exportPage' => $exportPage,
            'totalCount' => $totalCount,
        ));
    }

    /**
     * 订单详情
     */
    public function actionView($id) {
        $model = $this->loadModel($id);
        $id = $model->id;
//      $name = $model->goods->name;
        $order_goods = OrdersGoods::model()->findAll(array('condition' => 'order_id=:order_id ', 'params' => array(':order_id' => $id)));
//      var_dump($order_goods);die;
        $address = OrderAddress::model()->findByPk($model['address_id']);
        $this->render('view', array('model' => $model, 'order_goods' => $order_goods, 'address' => $address));
    }

    public function actionCloseOrder($id) {
        $model = $this->loadModel($id);
        if ($model->status == Order::STATUS_SEND) {
            $this->setFlash('error', Yii::t('order', '已发货不能关闭订单'));
            $this->redirect($this->createAbsoluteUrl('/order/admin/'));
        }
        if (isset($_POST['Order'])) {

            $post_data = $this->getPost('Order');

            if (empty($post_data['remark'])) {
                $this->setFlash('error', Yii::t('order', '备注不能为空'));
                $this->redirect($this->createAbsoluteUrl('/order/closeOrder/' . $id));
            }

            $rs = Order::orderCancel($model->code, true, $post_data['remark']);

            if ($rs) {
                @SystemLog::record(Yii::app()->user->name . "关闭订单成功：" . $model->code);
                $this->setFlash('success', Yii::t('order', '订单') . $model->code . Yii::t('order', '已关闭'));
                $this->redirect(array('admin'));
            } else {
                @SystemLog::record(Yii::app()->user->name . "关闭订单失败：" . $model->code);
                $this->setFlash('error', Yii::t('order', '订单') . $model->code . Yii::t('order', '关闭操作失败'));
            }
        }

        $this->render('closeOrder', array('model' => $model));
    }

    /**
     * 冻结订单
     * @param unknown $id
     */
    public function actionFrozen($id) {
        $model = $this->loadModel($id);
        if ($model->status == Order::STATUS_COMPLETE) {
            $this->setFlash('error', Yii::t('order', '已完成订单不能冻结'));
            $this->redirect($this->createAbsoluteUrl('/order/admin/'));
        }
        if (isset($_POST['Order'])) {

            $post_data = $this->getPost('Order');

            if (empty($post_data['remark'])) {
                $this->setFlash('error', Yii::t('order', '备注不能为空'));
                $this->redirect($this->createAbsoluteUrl('/order/frozen/' . $id));
            }

            if (!empty($post_data['remark']))
                $model->seller_remark .= ' [订单冻结]' . $post_data['remark'];
            $model->status = Order::STATUS_FROZEN;

            if ($model->save()) {
                @SystemLog::record(Yii::app()->user->name . "冻结订单成功：" . $model->code);
                $this->setFlash('success', Yii::t('order', '订单') . $model->code . Yii::t('order', '已冻结'));
                $this->redirect(array('admin'));
            } else {
                @SystemLog::record(Yii::app()->user->name . "冻结订单失败：" . $model->code);
                $this->setFlash('error', Yii::t('order', '订单') . $model->code . Yii::t('order', '冻结操作失败'));
            }
        }

        $this->render('frozen', array('model' => $model));
    }

    /**
     * 完成订单
     * @param unknown $id
     */
    public function actionComplete($id) {
        $model = $this->loadModel($id);
        $order_goods = $model->ordersGoods;
//          var_dump($model);die;
        $gids = array();
        $nums = array();

        foreach ($order_goods as $g) {
            $gids[] = $g->sgid;
            $nums[$g->sgid] = $g->num;
        }
        if ($model->status == Order::STATUS_COMPLETE) {
            $this->setFlash('error', Yii::t('order', '已完成订单不能再次完成'));
            $this->redirect($this->createAbsoluteUrl('/order/admin/'));
        }

        if ($model->pay_status != Order::PAY_STATUS_YES) {
            $this->setFlash('error', Yii::t('order', '未支付订单不能完成'));
            $this->redirect($this->createAbsoluteUrl('/order/admin/'));
        }

        if (isset($_POST['Order'])) {

            $post_data = $this->getPost('Order');

            if (empty($post_data['remark'])) {
                $this->setFlash('success', Yii::t('order', '备注不能为空'));
                $this->redirect($this->createAbsoluteUrl('/order/complete/' . $id));
            }

            $rs = Order::orderSign($model->code, true, $post_data['remark']);

            if ($rs['success'] == true) {
                if ($model->machine_take_type == Order::MACHINE_TAKE_TYPE_WITH_CODE) {
                    //扣除库存
                    $lines = Yii::app()->db->createCommand()
                            ->select('line_id,id,goods_id')
                            ->from(FreshMachineGoods::model()->tableName())
                            ->where('id IN (' . implode(',', $gids) . ')')
                            ->queryAll();


                    $line_ids = array();
                    foreach ($lines as $l) {
                        $line_ids[$l['id']] = $l['line_id'];
                    }

                    ksort($nums);
                    ksort($line_ids);
                    $line_ids = array_values($line_ids);

                    $nums = array_values($nums);
                    ApiStock::stockFrozenOutList($model->machine_id, $line_ids, $nums, API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
                }
                //首次充值 返回10%金额
                $modelOrder = new Order();
                $modelOrder->giveBackAmountFirstConsume($model->code, $model->member_id);

                @SystemLog::record(Yii::app()->user->name . "订单完成操作成功：" . $model->code);
                $this->setFlash('success', Yii::t('order', '订单') . $model->code . Yii::t('order', '已完成'));
                $this->redirect(array('admin'));
            } else {
                $msg = ErrorCode::getErrorStr($rs['code']) . ' ' . $rs['error_msg'];
                @SystemLog::record(Yii::app()->user->name . "订单完成操作失败：" . $model->code);
                $this->setFlash('error', Yii::t('order', '订单') . $model->code . Yii::t('order', '完成操作失败') . ' ' . $msg);
            }
        }

        $this->render('complete', array('model' => $model));
    }

    /**
     * 导出订单商品excel
     *
     */
    public function actionExport() {
        $model = new Order('search');
        $model->unsetAttributes();
        if (isset($_GET['Order']))
            $model->attributes = $this->getParam('Order');
        if (!$this->getSession('Order'))
            $model->month = date('Y-m', time());
        $model->isExport = 1;
        $this->render('export', array(
            'model' => $model,
        ));
    }

    /**
     * 导出订单商品excel
     *
     */
    public function actionFreshExport() {
        $model = new Order('search');
        $model->unsetAttributes();
        if (isset($_GET['Order']))
            $model->attributes = $this->getParam('Order');

        $model->type = Order::TYPE_FRESH_MACHINE;
        if (!$this->getSession('Order'))
            $model->month = date('Y-m', time());
        $model->isExport = 1;
        $this->render('export', array(
            'model' => $model,
        ));
    }

}
