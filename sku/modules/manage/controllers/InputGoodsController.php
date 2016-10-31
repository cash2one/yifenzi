<?php

/**
 * 录入商品控制器
 * 操作(审核列表，发布管理)
 * @author zehui.hong
 */
class InputGoodsController extends MController {

    public function filters() {
        return array(
            'rights',
        );
    }

    /*
     * 审核列表
     */

    public function actionAdmin() {
        $model = new BarcodeGoods('searchInputGoods');

        $start_time = strtotime("today");
        $end_time = $start_time + 24 * 3600;
        $sql = "select distinct goods_id from {{apply_barcode_goods}} where create_time between " . $start_time . ' and ' . $end_time;
        $result = yii::app()->db->createCommand($sql);
        $query = $result->queryAll();
         $model->unsetAttributes();
        if (isset($_GET['BarcodeGoods'])) {
           $model->attributes = $_GET['BarcodeGoods'];
//            var_dump($model->attributes);
        }
        $this->render('admin', array(
            'model' => $model,
            'query' => $query,
        ));
    }

    /**
     * 审核产品库
     */
    public function actionApply($id) {
        $rule = EnGoodsRule::model()->findAll();
        $ids = array(intval(ApplyBarcodeGoods::STATUS_PASS),intval(ApplyBarcodeGoods::STATUS_UNPASS));
        $s = join(',', $ids);
        $applyGoods = ApplyBarcodeGoods::model()->findAll('goods_id=:gid and status not in('.$s.') ', array(':gid' => $id));
        $barcode = BarcodeGoods::model()->findByPk($id);
        $tempGoods = array();
        $tempGoods['id'] = '';
//        var_dump($applyGoods);DIE;
        foreach ($applyGoods as $v) {
            if ($v->temp_id) {
                $arr = explode(',', $v->temp_id);

                foreach ($arr as $v1) {
                    if (!empty($v1)) {
                        $tempGoods[$v1] = $v->$v1;
                        $tempGoods['id'] .=$v1 . '_and_' . $v->id . ',';
                    }
                }
            }
        }

        $this->render('apply', array('id' => $id, 'rule' => $rule, 'applyGoods' => $applyGoods, 'tempGoods' => $tempGoods, 'barcode' => $barcode));
    }

    /**
     * 重新开放
     */
    public function actionOpenGoods($id) {
        $model = new ApplyBarcodeGoods;
        $status = ApplyBarcodeGoods::STATUS_UNPASS;
        $sql = 'update {{apply_barcode_goods}} set status=' . $status . ' where goods_id=' . $id . ' and status !=' . ApplyBarcodeGoods::STATUS_PASS;
        $result = yii::app()->db->createCommand($sql);
        $query = $result->query();
        $barcodeGoods = BarcodeGoods::model()->findByPk($id);
        $barcodeGoods->status = '';
        $barcodeGoods->is_custom = BarcodeGoods::EN_CUSTOM;
        $barcodeGoods->apply_num = '';
//        $barcodeGoods->status = BarcodeGoods::STATUS_APPLY;
        if (!empty($query) && $barcodeGoods->update()) {
            $this->setFlash('success', Yii::t('InputGoods', '重新开放录入成功'));
            @SystemLog::record(Yii::app()->user->name . "重新开放录入成功：" . $model->name);
            $this->redirect(array('admin'));
        }
    }

    /*
     * 产品库商品
     */

    public function actionEnGoods() {
        $id = 12;
        $this->render('admin', array('id' => $id));
    }

    /*
     * 非产品库商品
     */

    public function actionUnGoods() {
        $id = 13;
        $this->render('admin', array('id' => $id));
    }

    /*
     * 商品录入活动发布
     */

    public function actionRelease() {
        $model = new EnGoodsRule();
        $model->unsetAttributes();
        if (isset($_GET['EnGoodsRule'])) {
            $model->attributes = $_GET['EnGoodsRule'];
        }
        $this->render('release', array('model' => $model));
    }

    /**
     * 创建产品库商品项目
     */
    public function actionEnCreate() {
        $model = new EnGoodsRule;
        $model->unsetAttributes();
        $this->performAjaxValidation($model);
        if (isset($_POST['EnGoodsRule'])) {
            $model->attributes = $_POST['EnGoodsRule'];
            if ($model->save()) {
                $this->setFlash('success', Yii::t('FreshMachine', '添加产品库商品项目成功'));
                @SystemLog::record(Yii::app()->user->name . "添加产品库商品项目成功：" . $model->name);
                $this->redirect(array('release'));
            }
        }
        $this->render('enCreate', array('model' => $model));
    }

    /**
     * 编辑产品库商品项目
     */
    public function actionUpdate($id) {
        $model = EnGoodsRule::model()->findByPk($id);
        $this->performAjaxValidation($model);
        if (isset($_POST['EnGoodsRule'])) {

            $model->attributes = $_POST['EnGoodsRule'];

            if ($model->save()) {
                $this->setFlash('success', Yii::t('FreshMachine', '编辑产品库商品项目成功'));
                @SystemLog::record(Yii::app()->user->name . "编辑产品库商品项目成功：" . $model->name);
                $this->redirect(array('release'));
            }
        }
        $this->render('update', array('model' => $model));
    }

    /**
     * 删除项目
     */
    public function actionDeleteRule($id) {
        $model = EnGoodsRule::model()->findByPk($id);
        if ($model->delete()) {
            $this->setFlash('success', Yii::t('InputGoods', '删除项目成功'));
            @SystemLog::record(Yii::app()->user->name . "删除项目成功：" . $model->name);
            $this->redirect(array('release'));
        };
    }

    /**
     * 店铺录入活动
     */
    public function actionStoreActive() {
        $model = new ActiveGoods;
        $criteria = new CDbCriteria();
        $criteria->order = 'id ASC';
        $count = StoreActive::model()->count($criteria);
        $pager = new CPagination($count);
        $pager->pageSize = 30;
        $pager->applyLimit($criteria);
        $data = StoreActive::model()->findAll($criteria);
        $model->unsetAttributes();
        if (isset($_GET['StoreActive'])) {
            $model->attributes = $_GET['StoreActive'];
        }
        $this->render('storeActive', array('model' => $model, 'data' => $data, 'pages' => $pager));
    }

    /**
     * 店铺添加
     */
    public function actionAddStore() {
        $model = new StoreActive;
        $model->unsetAttributes();
        $this->performAjaxValidation($model);
        if (isset($_POST['StoreActive'])) {
            $model->attributes = $_POST['StoreActive'];
            if ($model->save()) {
                $this->setFlash('success', Yii::t('InputGoods', '添加活动店铺成功'));
                @SystemLog::record(Yii::app()->user->name . "添加活动店铺成功：" . $model->name);
                $this->redirect(array('storeActive'));
            }
        }
        $this->render('addStore', array('model' => $model));
    }

    /**
     * 店铺编辑
     */
    public function actionUpdateStore($id) {
        $model = StoreActive::model()->findByPk($id);
        $this->performAjaxValidation($model);
        if (isset($_POST['StoreActive'])) {
            $model->attributes = $_POST['StoreActive'];
            if ($model->save()) {
                $this->setFlash('success', Yii::t('InputGoods', '编辑活动店铺成功'));
                @SystemLog::record(Yii::app()->user->name . "编辑活动店铺成功：" . $model->name);
                $this->redirect(array('storeActive'));
            }
        }
        $this->render('updateStore', array('model' => $model));
    }

    /**
     * 店铺删除
     */
    public function actionStoreDelete($id) {
        $model = StoreActive::model()->findByPk($id);
        if ($model->delete()) {
            $this->setFlash('success', Yii::t('InputGoods', '删除活动店铺成功'));
            @SystemLog::record(Yii::app()->user->name . "删除活动店铺成功：" . $model->name);
            $this->redirect(array('storeActive'));
        };
    }

    /**
     * 添加商品
     */
    public function actionAddGoods($id) {
        $model = new ActiveGoods;
        $data = EnGoodsRule::model()->findAll();
        $this->performAjaxValidation($model);

        if (isset($_POST['ActiveGoods'])) {
            $post_data = $this->getParam('ActiveGoods');
            $r_type_name = $post_data['type_name'];
            $r_ids = $post_data['r_ids'];
            $model->name = $post_data['name'];
            $model->type_name = $r_type_name;
            $model->rule_ids = $r_ids;
            $model->store_id = $id;
            if ($model->save()) {
                $this->setFlash('success', Yii::t('InputGoods', '添加店铺商品成功'));
                @SystemLog::record(Yii::app()->user->name . "添加店铺商品成功：" . $model->name);
                $this->redirect(array('storeActive'));
            }
        }
        $this->render('addGoods', array('model' => $model, 'data' => $data));
    }

    /*
     * 编辑商品
     */

    public function actionUpdateGoods($id) {

        $model = ActiveGoods::model()->findByPk($id);
        $data = EnGoodsRule::model()->findAll();
        $activeGoods = $model->findByPk($id);
        $rule_ids = $activeGoods->rule_ids;
        $rule_arr = explode(",", $rule_ids);
        $this->performAjaxValidation($model);
        if (isset($_POST['ActiveGoods'])) {
            $post_data = $this->getParam('ActiveGoods');
            $r_type_name = $post_data['type_name'];
            $r_ids = $post_data['r_ids'];
            $model->name = $post_data['name'];
            $model->type_name = $r_type_name;
            $model->rule_ids = $r_ids;
            if ($model->save()) {
                $this->setFlash('success', Yii::t('InputGoods', '编辑店铺商品成功'));
                @SystemLog::record(Yii::app()->user->name . "编辑铺商品成功：" . $model->name);
                $this->redirect(array('storeActive'));
            }
        }
        $this->render('updateGoods', array('model' => $model, 'data' => $data, 'rule_arr' => $rule_arr));
    }

    /**
     * 删除店铺商品
     */
    public function actionDeleteGoods($id) {
        $model = ActiveGoods::model()->findByPk($id);
        if ($model->delete()) {
            $this->setFlash('success', Yii::t('InputGoods', '删除店铺商品成功'));
            @SystemLog::record(Yii::app()->user->name . "删除店铺商品成功：" . $model->name);
            $this->redirect(array('storeActive'));
        };
    }

    public function actionSms() {
//        $model = new ApiMember();
//        $model->sendSms(13760671419, 112,ApiMember::SMS_TYPE_ONLINE_ORDER,0,ApiMember::SKU_SEND_SMS);
    }

    /**
     * 记录保存至草案
     */
    public function actionTempGoods() {
        $tempid = $_POST['tempid'];
        $arrId = $_POST['arrId'];
        $data = $_POST;
//        var_dump($data);die;
        //------------------------------------------------全选保存至草案----------------------------------------------------------//
        if (!empty($tempid) && empty($arrId)) {
            if (!is_numeric($tempid)) {
                $tempid = rtrim($tempid, ',');
                $temparr = explode(',', $tempid);
                $b = explode('_and_', $temparr[0]);
                $tempid = $b[1];
                $model = ApplyBarcodeGoods::model()->findByPk($tempid);
                $this->setFlash('error', Yii::t('inputGoods', '该记录已保存至草案，请勿重复操作!'));
                $this->redirect(array('inputGoods/apply', 'id' => $model->goods_id));
            }
            $model = ApplyBarcodeGoods::model()->findByPk($tempid);

            $model->status = ApplyBarcodeGoods::STATUS_TEMP;
//            $temp = 'name,barcode,cate_name,thumb,describe,model';
            $temp = '';

            $rule = EnGoodsRule::model()->findAll('is_input=:in', array(':in' => EnGoodsRule::EN_INPUT));
            foreach ($rule as $v) {
                $name = $v->name;
                if (!empty($model->$name)) {
                    $temp .=$v->name . ',';
                }
            }
            $temp = rtrim($temp, ',');
            $model->temp_id = $temp;
            $outher_temp = ApplyBarcodeGoods::model()->updateAll(array('status' => ApplyBarcodeGoods::STATUS_APPLY, 'temp_id' => ''), 'goods_id=:gid and status=:status', array(':gid' => $model->goods_id, ':status' => ApplyBarcodeGoods::STATUS_TEMP));

            if ($model->update()) {
                $this->setFlash('success', Yii::t('inputGoods', '保存至草案成功!'));
            }

            $this->redirect(array('inputGoods/apply', 'id' => $model->goods_id));
        }
        //------------------------------------------------全选保存至草案结束----------------------------------------------------------//
        //------------------------------------------------单选保存至草案--------------------------------------------------------------//
        if (!empty($arrId) && empty($tempid)) {
            $arrId = ltrim($arrId, ',');
            $arr = explode(',', $arrId);
//            var_dump($arr);die;
            foreach ($arr as $v) {
                $a = explode('_and_', $v);
                $model = ApplyBarcodeGoods::model()->findByPk($a[1]);

                $model->temp_id .= $a[0] . ',';
                $model->status = ApplyBarcodeGoods::STATUS_TEMP;
                $model->update();
            }

            $this->setFlash('success', Yii::t('inputGoods', '保存至草案成功!'));
            $this->redirect(array('inputGoods/apply', 'id' => $model->goods_id));
        }
        //------------------------------------------------单选保存至草案结束--------------------------------------------------------------//
        //------------------------------------------------全选再单选保存至草案--------------------------------------------------------------//
        if (!empty($tempid) && !empty($arrId)) {
            //--------------------------------------------已保存至草案后再次单选保存----------------------------------------------//
            if (!is_numeric($tempid)) {
                $arrId = ltrim($arrId, ',');
                $arr = explode(',', $arrId);
                foreach ($arr as $v) {
                    $a = explode('_and_', $v);
                    $model = ApplyBarcodeGoods::model()->findByPk($a[1]);

                    if ($model->temp_id && $model->temp_id != $a[0]) {
                        $temp = $model->temp_id;
                        $temp = rtrim($temp,',');
                        $temp = ltrim($temp, ',');                    
                        $model->temp_id = ltrim($temp, ',') . ',' . $a[0];
//                        var_dump($model->temp_id);die;
                        $outher_temp = ApplyBarcodeGoods::model()->findAll('goods_id=:gid and status=:status', array(':gid' => $model->goods_id, ':status' => ApplyBarcodeGoods::STATUS_TEMP));
//                        var_dump($outher_temp);die;
                        if (!empty($outher_temp)) {
                            foreach ($outher_temp as $v1) {
                                $o_temp = $v1->temp_id;
                                if (strpos($o_temp, $a[0]) !== false) {
                                    $count = strpos($o_temp, $a[0]);
                                    $total = strlen($o_temp);
                                    $lg = strlen($a[0]);
                                    $total2 = $lg + $count;
                                    if ($total == $total2 && $count != 0) {
                                        $str = substr_replace($o_temp, "", $count - 1, $lg + 1);
                                    } else {
                                        $str = substr_replace($o_temp, "", $count, $lg + 1);
                                    }
                                    if (empty($str)) {
                                        $v1->status = ApplyBarcodeGoods::STATUS_APPLY;
                                    }
                                    $v1->temp_id = $str;
                                    if ($v1->update()) {
                                        $model->update();
                                    }
                                }
                            }
                        }
                    } else {

                        $model->temp_id = $a[0];
                        $model->status = ApplyBarcodeGoods::STATUS_TEMP;

                        $outher_temp = ApplyBarcodeGoods::model()->findAll('goods_id=:gid and status=:status', array(':gid' => $model->goods_id, ':status' => ApplyBarcodeGoods::STATUS_TEMP));

                        if (!empty($outher_temp)) {

                            foreach ($outher_temp as $v1) {
                                $o_temp = $v1->temp_id;
                                if (strpos($o_temp, $a[0]) !== false) {
                                    $count = strpos($o_temp, $a[0]);
                                    $total = strlen($o_temp);
                                    $lg = strlen($a[0]);
                                    $total2 = $lg + $count;
                                    if ($total == $total2 && $count != 0) {
                                        $str = substr_replace($o_temp, "", $count - 1, $lg + 1);
                                    } else {
                                        $str = substr_replace($o_temp, "", $count, $lg + 1);
                                    }
                                    if (empty($str)) {
                                        $v1->status = ApplyBarcodeGoods::STATUS_APPLY;
                                    }
//                                    var_dump($str);die;
                                    $v1->temp_id = $str;

                                    if ($v1->update()) {
                                        $model->update();
                                    }
                                }
                            }
                        }
                    }
                }
                $this->setFlash('success', Yii::t('inputGoods', '保存至草案成功!'));
                $this->redirect(array('inputGoods/apply', 'id' => $model->goods_id));
            }
            //--------------------------------------------已保存至草案后再次单选保存结束----------------------------------------------//
            //--------------------------------------------第一次保存全选后再次单选保存----------------------------------------------//
            if (is_numeric($tempid)) {
                $arrId = ltrim($arrId, ',');
                $arr = explode(',', $arrId);
                $model = ApplyBarcodeGoods::model()->findByPk($tempid);
                $temp = '';
                $rule = EnGoodsRule::model()->findAll('is_input=:in', array(':in' => EnGoodsRule::EN_INPUT));
                foreach ($rule as $v) {
                    $temp.=$v->name . ',';
                }
                $temp = rtrim($temp, ',');
                foreach ($arr as $v) {
                    $a = explode('_and_', $v);
                    if (strpos($temp, $a[0]) !== false) {
                        $count = strpos($temp, $a[0]);
                        $total = strlen($temp);
                        $lg = strlen($a[0]);
                        $total2 = $lg + $count;
                        if ($total == $total2 && $count != 0) {
                            $temp = substr_replace($temp, "", $count - 1, $lg + 1);
                        } else {
                            $temp = substr_replace($temp, "", $count, $lg + 1);
                        }
                    }
                }
                $model->temp_id = $temp;
                $model->status = ApplyBarcodeGoods::STATUS_TEMP;
                if (empty($temp)) {
                    $model->status = ApplyBarcodeGoods::STATUS_APPLY;
                }
                $outher_temp = ApplyBarcodeGoods::model()->updateAll(array('status' => ApplyBarcodeGoods::STATUS_APPLY, 'temp_id' => ''), 'goods_id=:gid and status=:status', array(':gid' => $model->goods_id, ':status' => ApplyBarcodeGoods::STATUS_TEMP));
//                var_dump($temp);
                $model->update();
                foreach ($arr as $v) {
                    $a = explode('_and_', $v);
//                    var_dump($a[0]);
                    $arrmodel = ApplyBarcodeGoods::model()->findByPk($a[1]);
                    if (empty($arrmodel->temp_id)) {
                        $arrmodel->temp_id = $a[0];
                    } else {
                        $arrmodel->temp_id = $arrmodel->temp_id . ',' . $a[0];
                    }
                    $arrmodel->status = ApplyBarcodeGoods::STATUS_TEMP;
                    $arrmodel->update();
                }
                $this->setFlash('success', Yii::t('inputGoods', '保存至草案成功!'));
                $this->redirect(array('inputGoods/apply', 'id' => $model->goods_id));
            }
            //--------------------------------------------第一次保存全选后再次单选保存结束----------------------------------------------//
        }
        //------------------------------------------------全选再单选至草案结束--------------------------------------------------------------//
    }

    /**
     * 保存至产品库
     */
    public function actionInputGoods() {

        $id = $_POST['inputGoodsid'];
        $inputarrId = $_POST['inputarrId'];
//        var_dump($_POST);die;
        if (!is_numeric($id) && !empty($id)) {
            $id = rtrim($id, ',');
            $id = ltrim($id, ',');
            $id = explode(',', $id);
        }
//        var_dump(!is_numeric($id));die;
        //-----------------------------------------全选后直接保存至产品库-----------------------------------------------//
        if (is_numeric($id) && empty($inputarrId)) {

            $result = '';
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $ApplyGoods = ApplyBarcodeGoods::model()->findByPk($id);
                $model = BarcodeGoods::model()->findByPk($ApplyGoods->goods_id);
                if (!empty($ApplyGoods->temp_id)) {
                    $name = $ApplyGoods->temp_id;
                    $name = ltrim($name, ',');
                    $name = rtrim($name, ',');
                    $name = explode(',', $name);
//            var_dump($name);die;
                    foreach ($name as $v) {
                        $model->$v = $ApplyGoods->$v;
                    }
                } else {
                    $model->name = empty($ApplyGoods->name) ? $model->name : $ApplyGoods->name;
                    $model->barcode = empty($ApplyGoods->barcode) ? $model->barcode : $ApplyGoods->barcode;
                    $model->cate_name = empty($ApplyGoods->cate_name) ? $model->cate_name : $ApplyGoods->cate_name;
                    $model->default_price = empty($ApplyGoods->default_price) ? $model->default_price : $ApplyGoods->default_price;
                    $model->model = empty($ApplyGoods->model) ? $model->model : $ApplyGoods->model;
                    $model->unit = empty($ApplyGoods->unit) ? $model->unit : $ApplyGoods->unit;
                    $model->describe = empty($ApplyGoods->describe) ? $model->describe : $ApplyGoods->describe;
                    $model->thumb = empty($ApplyGoods->thumb) ? $model->thumb : $ApplyGoods->thumb;
                    $arr = EnGoodsRule::getName();
                    $inopt = '';
                    foreach ($arr as $k => $v) {
                        if ($ApplyGoods->$k) {
                            if ($k == 'default_price' && $ApplyGoods->$k == '0.0') {
                                continue;
                            }
                            $model->$k = $ApplyGoods->$k;
                            $inopt[] = $k;
                        }
                    }
                    //判断会员类型
                    $sku_member = Member::model()->find('gai_member_id=:mid', array(':mid' => $ApplyGoods->member_id));
                    $radio_type = $sku_member->ratio;
                    $t = 0;
                    foreach ($inopt as $v) {
                        $model->$v = $ApplyGoods->$v;
                        $rule = EnGoodsRule::model()->find('name=:n', array(':n' => $v));
                        $total_pore = $rule->upload_bonus + $rule->adopt_bonus;
                        $total_money = $total_pore ;
                        $total_money = substr(sprintf("%.3f", $total_money), 0, -1);
                        $t +=$total_money;
                    }
                }
                $model->create_time = time();
                $model->is_custom = BarcodeGoods::NO_CUSTOM;
                $model->status = BarcodeGoods::STATUS_PASS;
                $ApplyGoods->status = ApplyBarcodeGoods::STATUS_PASS;
                $ApplyGoods->apply_time = time();
                $ApplyGoods->reward_money = $t;
                $model->apply_num = 0;
                $model->update();
                $ApplyGoods->update();
                $cout = ApplyBarcodeGoods::model()->count('goods_id=:id', array(':id' => $model->id));
                if ($cout > 1) {
                    ApplyBarcodeGoods::model()->updateAll(array('status' => ApplyBarcodeGoods::STATUS_UNPASS, 'temp_id' => ''), 'goods_id=:gid and status!=:status', array(':gid' => $model->id, ':status' => ApplyBarcodeGoods::STATUS_PASS));
                }
                //生成订单号 作为标识
                $order = Tool::buildOrderNo();
                //奖励积分操作
                $rs = AccountBalance::JiFenBalance($ApplyGoods->member_id, $total_money, $order);
                $transaction->commit();
                $result = true;
            } catch (Exception $ex) {
                $transaction->rollBack();
                throw new Exception($ex . '(积分奖励失败)');
                $result = false;
            }
            if ($result) {
                $this->setFlash('success', Yii::t('InputGoods', '保存至产品库成功！'));
                @SystemLog::record(Yii::app()->user->name . "保存至产品库成功：" . $model->name);
                $this->redirect(array('admin'));
            } else {
                $this->setFlash('error', Yii::t('InputGoods', '保存至产品库失败！'));
                $this->redirect(array('admin'));
            }
        }
        //-------------------------------------------------全选后直接保存至产品库结束---------------------------------------------------------//
        //--------------------------------------------------保存草案后再保存/单选后直接保存----------------------------------------------------------------------//
        if (!is_numeric($id) && empty($inputarrId) || empty($id) && !empty($inputarrId)) {
            $aname = array();
            $aid = array();
            //--------------------------------------保存草案后在保存至产品库------------------------------------------------//
            if (!is_numeric($id) && empty($inputarrId)) {
                $result = '';
                $transaction = Yii::app()->db->beginTransaction();
                try {
                    foreach ($id as $v) {
                        $a = explode('_and_', $v);
                        $aname[] = $a[0];
                        $aid[] = $a[1];
                    }
                    $aid = array_unique($aid);
//                    var_dump($aid);die;
                    //生成订单号 作为标识
                    $order = Tool::buildOrderNo();
                    $ApplyGoods = ApplyBarcodeGoods::model()->findByPk($aid[0]);
                    $model = BarcodeGoods::model()->findByPk($ApplyGoods->goods_id);
                    foreach ($aid as $v) {

                        $apb = ApplyBarcodeGoods::model()->findByPk($v);
//判断会员类型
                        $sku_member = Member::model()->find('gai_member_id=:mid', array(':mid' => $apb->member_id));
                        $radio_type = $sku_member->ratio;
                        if (!empty($apb->temp_id)) {
                            $temp_id = ltrim($apb->temp_id, ',');
                            $temp_id = rtrim($temp_id, ',');
                            $temp_id = explode(',', $temp_id);
                            $total_money = 0;
                            $t = 0;
                            foreach ($temp_id as $v1) {
                                $model->$v1 = $apb->$v1;
                                $rule = EnGoodsRule::model()->find('name=:n', array(':n' => $v1));
                                $total_pore = $rule->upload_bonus + $rule->adopt_bonus;
                                $total_money = $total_pore ;
                                $total_money = substr(sprintf("%.3f", $total_money), 0, -1);
                                $t +=$total_money;
                            }
                        }
                      
                        $apb->reward_money = $t;
                         $apb->apply_time = time();
                        $apb->status = ApplyBarcodeGoods::STATUS_PASS;

                        $apb->update();
                        //奖励积分操作
                        AccountBalance::JiFenBalance($apb->member_id, $t, $order);
                    }
                    $model->create_time = time();
                    $model->is_custom = BarcodeGoods::NO_CUSTOM;
                    $model->status = BarcodeGoods::STATUS_PASS;
                    $model->apply_num = 0;
                    $model->update();
                    $cout = ApplyBarcodeGoods::model()->count('goods_id=:id', array(':id' => $model->id));
                    if ($cout > 1) {
                        ApplyBarcodeGoods::model()->updateAll(array('status' => ApplyBarcodeGoods::STATUS_UNPASS, 'temp_id' => ''), 'goods_id=:gid and status!=:status', array(':gid' => $model->id, ':status' => ApplyBarcodeGoods::STATUS_PASS));
                    }
                    $transaction->commit();
                    $result = true;
                } catch (Exception $ex) {
                    $transaction->rollBack();
                    throw new Exception($ex . '(积分奖励失败)');
                    $result = false;
                }
                if ($result) {
                    $this->setFlash('success', Yii::t('InputGoods', '保存至产品库成功！'));
                    @SystemLog::record(Yii::app()->user->name . "保存至产品库成功：" . $model->name);
                    $this->redirect(array('admin'));
                } else {
                    $this->setFlash('error', Yii::t('InputGoods', '保存至产品库失败！'));
                    $this->redirect(array('admin'));
                }
            }
            //--------------------------------------保存草案后在保存至产品库结束------------------------------------------------//         
            //--------------------------------------单选后直接保存至产品库------------------------------------------------//
            if (empty($id) && !empty($inputarrId)) {

                $result = '';
                $transaction = Yii::app()->db->beginTransaction();
                try {
                    $inputarrId = rtrim($inputarrId, ',');
                    $inputarrId = ltrim($inputarrId, ',');
                    $inputarrId = explode(',', $inputarrId);
                    $aname = array();
                    $aid = array();
                    foreach ($inputarrId as $v) {
                        $a = explode('_and_', $v);
                        $aname[] = $a[0];
                        $aid[] = $a[1];
                    }
                    $aid = array_unique($aid);
                    $aid2 = array();
                    foreach ($inputarrId as $v) {
                        $a = explode('_and_', $v);
                        if (in_array($a[1], $aid)) {
                            $aid2[$a[1]][] = $a[0];
                        }
                    }
                 
                    //生成订单号 作为标识
                    $order = Tool::buildOrderNo();

                    $ApplyGoods = ApplyBarcodeGoods::model()->findByPk($aid[0]);
                    $model = BarcodeGoods::model()->findByPk($ApplyGoods->goods_id);

                    foreach ($aid2 as $k => $v) {                     
                        $apb = ApplyBarcodeGoods::model()->findByPk($k);

//判断会员类型
                        $sku_member = Member::model()->find('gai_member_id=:mid', array(':mid' => $apb->member_id));
                        $radio_type = $sku_member->ratio;
                        $t=0;
                        foreach ($v as $v1) {
                            $model->$v1 = $apb->$v1;
                            $rule = EnGoodsRule::model()->find('name=:n', array(':n' => $v1));
                            $total_pore = $rule->upload_bonus + $rule->adopt_bonus;
                            $total_money = $total_pore ;
                            $total_money = substr(sprintf("%.3f", $total_money), 0, -1);
                            $t += $total_money;
                        }
                      
                        $apb->reward_money = $t;
                        $apb->apply_time = time();
                        $apb->status = ApplyBarcodeGoods::STATUS_PASS;
                        $apb->update();
//                        var_dump($apb->member_id);
//                        var_dump($t);
//                        var_dump($apb->attributes);
                        //奖励积分操作
                        AccountBalance::JiFenBalance($apb->member_id, $t, $order);
//                               var_dump($v);
                    }
//                          die;
                    $model->create_time = time();
                    $model->is_custom = BarcodeGoods::NO_CUSTOM;
                    $model->status = BarcodeGoods::STATUS_PASS;
                    $model->apply_num = 0;
                    $model->update();
                    $cout = ApplyBarcodeGoods::model()->count('goods_id=:id', array(':id' => $model->id));
                    if ($cout > 1) {
                        ApplyBarcodeGoods::model()->updateAll(array('status' => ApplyBarcodeGoods::STATUS_UNPASS, 'temp_id' => ''), 'goods_id=:gid and status!=:status ', array(':gid' => $model->id, ':status' =>  ApplyBarcodeGoods::STATUS_PASS));
                    }
                    $transaction->commit();
                    $result = true;
                } catch (Exception $ex) {
                    $transaction->rollBack();
                    throw new Exception($ex . '(积分奖励失败)');
                    $result = false;
                }
                if ($result) {
                    $this->setFlash('success', Yii::t('InputGoods', '保存至产品库成功！'));
                    @SystemLog::record(Yii::app()->user->name . "保存至产品库成功：" . $model->name);
                    $this->redirect(array('admin'));
                } else {
                    $this->setFlash('error', Yii::t('InputGoods', '保存至产品库失败！'));
                    $this->redirect(array('admin'));
                }
            }
            //--------------------------------------单选后直接保存至产品库结束------------------------------------------------//
        }
        //--------------------------------------------------保存草案后再保存/单选后直接保存结束----------------------------------------------------------------------//
        //---------------------------------------------------------------保存至草案后再单选保存至产品库----------------------------------------------------------------------------------//
        if (!empty($id) && !empty($inputarrId)) {
            //-------------------------------------------------全选后再单选然后保存至产品库---------------------------------------------------------------//
            if (is_numeric($id) && !empty($inputarrId)) {
                $result = '';
                $transaction = Yii::app()->db->beginTransaction();
                try {
                    $inputarrId = ltrim($inputarrId, ',');
                    $inputarrId = rtrim($inputarrId, ',');
                    $inputarrId = explode(',', $inputarrId);
                    $aname = array();
                    $aid = array();
                    foreach ($inputarrId as $v) {
                        $a = explode('_and_', $v);
                        $aname[] = $a[0];
                        $aid[] = $a[1];
                    }
                    $aid = array_unique($aid);
                    $aid2 = array();
                    foreach ($inputarrId as $v) {
                        $a = explode('_and_', $v);
                        if (in_array($a[1], $aid)) {
                            $aid2[$a[1]][] = $a[0];
                        }
                    }
                    //生成订单号 作为标识
                    $order = Tool::buildOrderNo();
//                    $aname = implode(',', $aname);
//                    var_dump($aname);die;
                    $ApplyGoods = ApplyBarcodeGoods::model()->findByPk($id);
                    $model = BarcodeGoods::model()->findByPk($ApplyGoods->goods_id);
                    $rule1 = EnGoodsRule::model()->findAll('is_input=:s', array(':s' => EnGoodsRule::EN_INPUT));
                    $inarr = array();
                    foreach ($rule1 as $v) {
                        $inarr[] = $v->name;
                    }
                    $inarr = array_diff($inarr, $aname);
                    foreach ($inarr as $k => $v) {
                        if (empty($ApplyGoods->$v)) {
                            unset($inarr[$k]);
                        }
                    }
                    $t1 = 0;
                    foreach ($inarr as $v1) {
                        $model->$v1 = $ApplyGoods->$v1;
                        $rule = EnGoodsRule::model()->find('name=:n', array(':n' => $v1));
                        $sku_member = Member::model()->find('gai_member_id=:mid', array(':mid' => $ApplyGoods->member_id));
                        $radio_type = $sku_member->ratio;
                        $total_pore1 = $rule->upload_bonus + $rule->adopt_bonus;
                        $total_money1 = $total_pore1 ;
                        $total_money1 = substr(sprintf("%.3f", $total_money1), 0, -1);
                        $t1 += $total_money1;
                    }
                    $model->create_time = time();
                    $model->is_custom = BarcodeGoods::NO_CUSTOM;
                    $model->status = BarcodeGoods::STATUS_PASS;
                    $ApplyGoods->reward_money = $t1;
                     $ApplyGoods->apply_time = time();
                    $ApplyGoods->status = ApplyBarcodeGoods::STATUS_PASS;
//                    $model->update();
                    $ApplyGoods->update();
                    //奖励积分操作
                    AccountBalance::JiFenBalance($ApplyGoods->member_id, $t1, $order);
                    $cout = ApplyBarcodeGoods::model()->count('goods_id=:id', array(':id' => $model->id));
                    if ($cout > 1) {
                        ApplyBarcodeGoods::model()->updateAll(array('status' => ApplyBarcodeGoods::STATUS_UNPASS, 'temp_id' => ''), 'goods_id=:gid and member_id!=:id', array(':gid' => $model->id, ':id' => $ApplyGoods->member_id));
                    }


                    foreach ($aid2 as $k => $v) {
                        $apb = ApplyBarcodeGoods::model()->findByPk($k);
//判断会员类型
                        $sku_member = Member::model()->find('gai_member_id=:mid', array(':mid' => $apb->member_id));
                        $radio_type = $sku_member->ratio;
                        $t = 0;
                        foreach ($v as $v1) {
                            $model->$v1 = $apb->$v1;
                            $rule = EnGoodsRule::model()->find('name=:n', array(':n' => $v1));
                            $total_pore = $rule->upload_bonus + $rule->adopt_bonus;
                            $total_money = $total_pore ;
                            $total_money = substr(sprintf("%.3f", $total_money), 0, -1);
                            $t = $total_money;
                        }
                        $model->create_time = time();
                        $model->is_custom = BarcodeGoods::NO_CUSTOM;
                        $model->status = BarcodeGoods::STATUS_PASS;
                        $apb->reward_money = $t;
                        $apb->apply_time = time();
                        $apb->status = ApplyBarcodeGoods::STATUS_PASS;
                        $apb->update();
//                        $model->update();
                        //生成订单号 作为标识
                        $order = Tool::buildOrderNo();
                        //奖励积分操作
                        AccountBalance::JiFenBalance($apb->member_id, $t, $order);

                        
                    }
                    $model->apply_num = 0;
                    $model->update();
                    $cout = ApplyBarcodeGoods::model()->count('goods_id=:id', array(':id' => $model->id));
                        if ($cout > 1) {
                            ApplyBarcodeGoods::model()->updateAll(array('status' => ApplyBarcodeGoods::STATUS_UNPASS, 'temp_id' => ''), 'goods_id=:gid and member_id!=:id', array(':gid' => $model->id, ':id' => $apb->member_id, ));
                        }
                    $transaction->commit();
                    $result = true;
                } catch (Exception $ex) {
                    $transaction->rollBack();
                    throw new Exception($ex . '(积分奖励失败)');
                    $result = false;
                }
                if ($result) {
                    $this->setFlash('success', Yii::t('InputGoods', '保存至产品库成功！'));
                    @SystemLog::record(Yii::app()->user->name . "保存至产品库成功：" . $model->name);
                    $this->redirect(array('admin'));
                } else {
                    $this->setFlash('error', Yii::t('InputGoods', '保存至产品库失败！'));
                    $this->redirect(array('admin'));
                }
            }
            //-------------------------------------------------全选后再单选然后保存至产品库结束---------------------------------------------------------------//
            //-----------------------------------------------------------保存至草案后再单选保存至产品库-----------------------------------------------------------------------------//
            if (!is_numeric($id) && !empty($inputarrId)) {

                $result = '';
                $transaction = Yii::app()->db->beginTransaction();
                try {

                    $inputarrId = ltrim($inputarrId, ',');
                    $inputarrId = rtrim($inputarrId, ',');
                    $inputarrId = explode(',', $inputarrId);
                    $inputname = array();
                    $temid = array();
                    foreach ($inputarrId as $v) {
                        $a = explode('_and_', $v);
                        $inputname[] = $a[0];
                    }foreach ($id as $v) {
                        $a = explode('_and_', $v);
                        $temid[] = $a[1];
                    }
                    $temid = array_unique($temid);
                    $only = array();
                    foreach ($inputname as $v) {
                        $only[] = $v . '_and_' . $temid[0];
                    }

                    $bin = array_merge($id, $inputarrId);

                    $bin = array_diff($bin, $only);
//var_dump($bin);die;

                    $aname = array();  //需要录入的项目
                    $aid = array();
                    foreach ($bin as $v) {
                        $a = explode('_and_', $v);
                        $aname[] = $a[0];
                        $aid[] = $a[1];
                    }

                    $aid = array_unique($aid);
                    $aid2 = array();
                    foreach ($bin as $v) {
                        $a = explode('_and_', $v);
                        if (in_array($a[1], $aid)) {
                            $aid2[$a[1]][] = $a[0];
                        }
                    }
                    //生成订单号 作为标识
                    $order = Tool::buildOrderNo();
//                    var_dump($aid2);die;
                    $ApplyGoods = ApplyBarcodeGoods::model()->findByPk($aid[0]);
                    $model = BarcodeGoods::model()->findByPk($ApplyGoods->goods_id);
                    foreach ($aid2 as $k => $v) {
                        $t = 0;
                        $apb = ApplyBarcodeGoods::model()->findByPk($k);
                        $apb->temp_id = '';

//判断会员类型
                        $sku_member = Member::model()->find('gai_member_id=:mid', array(':mid' => $apb->member_id));
                        $radio_type = $sku_member->ratio;
                        foreach ($v as $v1) {
                            $model->$v1 = $apb->$v1;
                            $rule = EnGoodsRule::model()->find('name=:n', array(':n' => $v1));
                            $total_pore = $rule->upload_bonus + $rule->adopt_bonus;
                            $total_money = $total_pore ;
                            $total_money = substr(sprintf("%.3f", $total_money), 0, -1);
                            $apb->temp_id .= $v1 . ',';
                            $t += $total_money;
                        }
//                        var_dump($apb->temp_id);
//                         var_dump($t);
                        $apb->temp_id = rtrim($apb->temp_id, ',');
                        $model->create_time = time();
                        $model->is_custom = BarcodeGoods::NO_CUSTOM;
                        $model->status = BarcodeGoods::STATUS_PASS;
                        $apb->reward_money = $t;
                        $apb->apply_time = time();
                        $apb->status = ApplyBarcodeGoods::STATUS_PASS;
                        $apb->update();
                        

                        //奖励积分操作
                        $rs = AccountBalance::JiFenBalance($apb->member_id, $t, $order);

                       
                    }
                    $model->apply_num = 0;
                    $model->update();
                      $cout = ApplyBarcodeGoods::model()->count('goods_id=:id', array(':id' => $model->id));
                        if ($cout > 1) {
                            ApplyBarcodeGoods::model()->updateAll(array('status' => ApplyBarcodeGoods::STATUS_UNPASS, 'temp_id' => ''), 'goods_id=:gid and member_id!=:id', array(':gid' => $model->id, ':id' => $apb->member_id,));
                        }
                    $transaction->commit();
                    $result = true;
                } catch (Exception $ex) {
                    $transaction->rollBack();
                    throw new Exception($ex . '(积分奖励失败)');
                    $result = false;
                }
                $result = true;
                if ($result) {
                    $this->setFlash('success', Yii::t('InputGoods', '保存至产品库成功！'));
                    @SystemLog::record(Yii::app()->user->name . "保存至产品库成功：" . $model->name);
                    $this->redirect(array('admin'));
                } else {
                    $this->setFlash('error', Yii::t('InputGoods', '保存至产品库失败！'));
                    $this->redirect(array('admin'));
                }
            }
            //-----------------------------------------------------------保存至草案后再单选保存至产品库-----------------------------------------------------------------------------//
        }

        //---------------------------------------------------------------保存至草案后再单选保存至产品库结束----------------------------------------------------------------------------------//
    }

}
