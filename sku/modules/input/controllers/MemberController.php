<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MemberController
 *
 * @author Administrator
 */
class MemberController extends IController {

    public function init() {
        $this->pageTitle = Yii::t('store', '商品录入');
    }


    /**
     * 商品录入
     */
    public function actionInputGoods() {
        if (isset($_POST['ApplyBarcodeGoods'])) {
            if (empty($_POST['ApplyBarcodeGoods']['id'])) {
                $this->setFlash('error', Yii::t('inputModule.input', '无效的数据'));
                $this->redirect(array('inputGoods'));
            }
        }
        $model = new ApplyBarcodeGoods;
        $model->unsetAttributes();
        $bgdata = BarcodeGoods::model()->findAll('is_custom=:icm', array(':icm' => BarcodeGoods::EN_CUSTOM));
        $showdata = BarcodeGoods::model()->findAllBySql('SELECT * FROM gw_sku_barcode_goods where is_custom=:icm and apply_num<:num ORDER BY RAND() LIMIT 8 ', array(':icm' => BarcodeGoods::EN_CUSTOM, ':num' => ApplyBarcodeGoods::APPLY_COUNT));
        $sid = Yii::app()->user->id;
        $member_id = Member::model()->findByPk($sid);

        $arr = array();
        foreach ($showdata as $k => $v) {
            $arr[$k]['id'] = $v->id;
            $arr[$k]['name'] = $v->name;
            $arr[$k]['barcode'] = $v->barcode;
            $arr[$k]['brand'] = $v->brand;
            $arr[$k]['model'] = $v->model;
            $arr[$k]['unit'] = $v->unit;
            $arr[$k]['thumb'] = $v->thumb;
        }
        $model->scenario = 'create';
        $this->performAjaxValidation($model);
        if (isset($_POST['ApplyBarcodeGoods'])) {
            $post = $_POST['ApplyBarcodeGoods'];
           if(!empty($post['thumb'])){
               $att = ATTR_DOMAIN;
               $post['thumb'] = str_replace($att.'/','',$post['thumb']);
           }
          
            $barcode = BarcodeGoods::model()->findByPk($post['id']);
             if ($post['barcode'] !=$barcode->barcode) {
                $this->setFlash('error', Yii::t('inputModule.input', '条码不允许修改'));
                $this->redirect(array('inputGoods'));
            }
            $old = $barcode->attributes;
            $barcode->describe = $post['describe'];
            $barcode->attributes = $post;
            $barcode->thumb = empty($post['thumb']) ? $old['thumb'] : $post['thumb'];
            if ($old == $barcode->attributes) {
                $this->setFlash('error', Yii::t('inputModule.input', '没有提交任何数据'));
                $this->redirect(array('inputGoods'));
            }
            if (empty($post['id'])) {
                $this->setFlash('error', Yii::t('inputModule.input', '条码库不存在该商品'));
                $this->redirect(array('inputGoods'));
            }
            if (count($post) == 1 && !empty($post['id'])) {
                $this->setFlash('error', Yii::t('inputModule.input', '不可录入的商品'));
                $this->redirect(array('inputGoods'));
            }

            $checkGoods = $model->find('member_id =:mid and goods_id=:gid and status =:status', array('mid' => $member_id->gai_member_id, ':gid' => $post['id'], ':status' => ApplyBarcodeGoods::STATUS_APPLY));
            if (!empty($checkGoods)) {
                $this->setFlash('error', Yii::t('inputModule.input', '您已提交该商品，请等待审核'));
                $this->redirect(array('inputGoods'));
            }
            $barcode = BarcodeGoods::model()->findByPk($post['id']);
            $apply_num = $barcode->apply_num;
            if ($apply_num >= 3) {
                $this->setFlash('error', Yii::t('inputModule.input', '申请人数已满，请选择其他商品'));
                $this->redirect(array('inputGoods'));
            }
            if ($barcode->status == BarcodeGoods::STATUS_PASS) {
                $this->setFlash('error', Yii::t('inputModule.input', '该商品其他用户已录入，请选择其他商品'));
                $this->redirect(array('inputGoods'));
            }
            $model->name = (isset($post['name']) && !empty($post['name']) && $post['name'] != $barcode->name) ? $post['name'] : '';
            $model->unit = (isset($post['unit']) && !empty($post['unit']) && $post['unit'] != $barcode->unit) ? $post['unit'] : '';
            $model->model = (isset($post['model']) && !empty($post['model']) && $post['model'] != $barcode->model) ? $post['model'] : '';
            $model->barcode =  '';
            $model->goods_id = $post['id'];
            $model->cate_name = (isset($post['cate_name']) && !empty($post['cate_name']) && $post['cate_name'] != $barcode->cate_name) ? $post['cate_name'] : '';
            $model->default_price = (isset($post['default_price']) && !empty($post['default_price']) && $post['default_price'] != $barcode->default_price) ? $post['default_price'] : '';
            $model->describe = (isset($post['describe']) && !empty($post['describe']) && $post['describe'] != $barcode->describe) ? $post['describe'] : '';
            $model->thumb = (isset($post['thumb']) && !empty($post['thumb']) && $post['thumb'] != $barcode->thumb) ? ($post['thumb']) : '';
            $model->status = ApplyBarcodeGoods::STATUS_APPLY;
            $model->member_id = $member_id->gai_member_id;
            $model->create_time = time();
            $model->reward_money = '';
            $model->apply_time = time();

            $barcode->status = BarcodeGoods::STATUS_APPLY;
            $barcode->apply_num = $apply_num + 1;
            $barcode->apply_time = time();

            if ($model->save() && $barcode->update()) {
//                UploadedFile::saveFile('thumb', $model->thumb);
                Yii::app()->user->setFlash('success', Yii::t('inputModule.input', '录入申请提交成功'), null);

                $this->redirect(array('inputGoods'));
            } else {
                $this->setFlash('error', Yii::t('inputModule.input', '录入申请提交失败'));
            }
        }
        $this->render('inputGoods', array('model' => $model, 'bgdata' => $bgdata, 'arr' => $arr));
    }

    /**
     * 录入记录
     */
    public function actionInputRecords() {

        $sid = Yii::app()->user->id;
        $member_id = Member::model()->findByPk($sid);
        $criteria = new CDbCriteria();
        $criteria->select = 't.*, b.name,b.barcode';
        $criteria->order = 'id ASC';   
        $criteria->join = 'LEFT JOIN {{barcode_goods}} as b ON b.id=t.goods_id'; 
        $criteria->addCondition('t.member_id=' . $member_id->gai_member_id);      //根据条件查询
        $count = ApplyBarcodeGoods::model()->count($criteria);
    
        $pager = new CPagination($count);
        $pager->pageSize = 10;
        $pager->applyLimit($criteria);
        $data = ApplyBarcodeGoods::model()->findAll($criteria);

        $start_time = strtotime("today");
        $today_end_time = $start_time + 24 * 3600;
        $sql = "select sum(reward_money) from {{apply_barcode_goods}} where status =" . ApplyBarcodeGoods::STATUS_PASS . " and member_id=" . $member_id->gai_member_id . " and apply_time between " . $start_time . ' and ' . $today_end_time;
        $result = yii::app()->db->createCommand($sql);
        $today = $result->queryAll();
        //周
        $weekNum = date('N') - 1;
        $weekDate = date('Y-m-d 00:00:00', strtotime("-$weekNum  day"));
        $week_start = strtotime($weekDate);
        $week_end = $week_start + 24 * 3600 * 7;
        $week_sql = "select sum(reward_money) from {{apply_barcode_goods}} where status =" . ApplyBarcodeGoods::STATUS_PASS . " and member_id=" . $member_id->gai_member_id . " and apply_time between " . $week_start . ' and ' . $week_end;
        $week_result = yii::app()->db->createCommand($week_sql);
        $week = $week_result->queryAll();
        //总获利
        $total_sql = "select sum(reward_money) from {{apply_barcode_goods}} where status =" . ApplyBarcodeGoods::STATUS_PASS . " and member_id=" . $member_id->gai_member_id;
        $total_result = yii::app()->db->createCommand($total_sql);
        $total = $total_result->queryAll();
        $this->render('inputRecords', array('data' => $data, 'pages' => $pager, 'today' => $today, 'week' => $week, 'total' => $total));
    }

    /**
     * 
     * ajax 获取数据
     */
    public function actionGetData() {
        if (Yii::app()->request->isAjaxRequest) {
             $sid = Yii::app()->user->id;
            $member_id = Member::model()->findByPk($sid);
            $apply = ApplyBarcodeGoods::model()->findAll('status=:s and member_id = :mid',array(':s'=>  ApplyBarcodeGoods::STATUS_APPLY, ':mid'=>$member_id->gai_member_id));
            $ids = array();
            
            foreach($apply as $v){
                $ids[] = intval($v['goods_id']); 
            }
           $ids = !empty($ids) ? join(',', $ids) : '0';
            $data = BarcodeGoods::model()->findAllBySql('SELECT * FROM gw_sku_barcode_goods where is_custom=:icm and apply_num<:num and id not in('.$ids.') ORDER BY RAND() LIMIT 8 ', array(':icm' => BarcodeGoods::EN_CUSTOM, ':num' => ApplyBarcodeGoods::APPLY_COUNT));
            if (!empty($data)) {
                $arr = array();
                foreach ($data as $k => $v) {
                    $arr[$k]['id'] = $v->id;
                    $arr[$k]['name'] = $v->name;
                    $arr[$k]['barcode'] = $v->barcode;
                    $arr[$k]['brand'] = $v->brand;
                    $arr[$k]['model'] = $v->model;
                    $arr[$k]['unit'] = $v->unit;
                    $arr[$k]['thumb'] = $v->thumb;
                }
                $arr = json_encode($arr);
                echo $arr;
            }
        }
    }

    /**
     * ajax搜索
     */
    public function actionAjaxSearch() {
        if (Yii::app()->request->isAjaxRequest) {
            $name = ($_POST['name']);
             $sid = Yii::app()->user->id;
            $member_id = Member::model()->findByPk($sid);
            $apply = ApplyBarcodeGoods::model()->findAll('status=:s and member_id = :mid',array(':s'=>  ApplyBarcodeGoods::STATUS_APPLY, ':mid'=>$member_id->gai_member_id));
             $ids = array();
            
            foreach($apply as $v){
                $ids[] = intval($v['goods_id']); 
            }
           $ids = !empty($ids) ? join(',', $ids) : '0';
//          echo $ids;die;
            if (!empty($name)) {
                $data = BarcodeGoods::model()->findAllBySql('select name,id from {{barcode_goods}} where is_custom=:icm and id not in('.$ids.') and (name like :name or barcode like :name)', array(':name' => '%' . $name . '%', ':icm' => BarcodeGoods::EN_CUSTOM));

                $names = array();
                if(!empty($data)){
                foreach ($data as $k => $v) {
                    $names[$k]['name'] = $v->name;
                    $names[$k]['id'] = $v->id;
                }
                $names = json_encode($names);
                echo $names;
            }else{
                echo false;
            }
            }
        }
    }

    /*
     * ajax获取数据
     */

    public function actionGetOne() {
        if (Yii::app()->request->isAjaxRequest) {
            $id = $_POST['id'];
           
            if (!empty($id)) {

                $data = BarcodeGoods::model()->findByPk($id);

                $count = $data->apply_num;
                if ($count < ApplyBarcodeGoods::APPLY_COUNT) {
                    $arr = array();
                    foreach ($data as $k => $v) {
                        $arr[$k] = $v;
                    }
                    $data = '[' . json_encode($arr) . ']';
                    echo $data;
                } else {
                    return FALSE;
                }
            }
        }
    }

    /*
     * 获取条形码数据
     */

    public function actionGetCode() {
        if (Yii::app()->request->isAjaxRequest) {
            $barcode = $_POST['barcode'];
            if (!empty($barcode)) {
                $data = BarcodeGoods::model()->find('barcode=:barcode and is_custom=:icm', array(':barcode' => $barcode, ':icm' => BarcodeGoods::EN_CUSTOM));
                if (!empty($data)) {
                    $arr = array();
                    foreach ($data as $k => $v) {
                        $arr[$k] = $v;
                    }
                    $data = '[' . json_encode($arr) . ']';
                    echo $data;
                } else {
                    return FALSE;
                }
            }
        }
    }

    /*
     * 预览图片
     */
//
//    public function actionAjaxImg() {
//    
//        $model = new ApplyBarcodeGoods();
//        $saveDir = 'yulan/' . date('Y/n/j');
//        $model = UploadedFile::uploadFile($model, 'thumb', $saveDir, Yii::getPathOfAlias('att'));
//        UploadedFile::saveFile('thumb', $model->thumb);
//        $lod = $model->thumb;
//        echo $lod;
//    }

    public function actionImg() {
//        $action = $_GET['act'];
//        if ($action == 'delimg') { //删除图片 
//            $filename = $_POST['imagename'];
//            if (!empty($filename)) {
//                unlink('files/' . $filename);
//                echo '1';
//            } else {
//                echo '删除失败.';
//            }
//        } else { //上传图片 
            $picname = $_FILES['ApplyBarcodeGoods']['name']['thumb'];
            $picsize = $_FILES['ApplyBarcodeGoods']['size']['thumb'];
            if ($picname != "") {
                if ($picsize > 1024000) { //限制上传大小                 
                    echo '图片大小不能超过1M';
                    exit;
                }
                $type = strstr($picname, '.'); //限制上传格式 
                if ($type != ".jpeg" && $type != ".jpg" && $type != ".png") {
                    echo '图片格式不对！';
                    exit;
                }
                $rand = rand(100, 999);
                $pics = date("YmdHis") . $rand . $type; //命名图片名称 
                //上传路径 
//        $pic_path = "files/". $pics; 
                
                $saveDir = Yii::getPathOfAlias('att') . '/input/' . date('Y/n/j');
                

                if(!is_dir($saveDir)){
                    mkdir($saveDir,0755, true);
                }
                $pic_path = $saveDir .DS. $pics;
                move_uploaded_file($_FILES['ApplyBarcodeGoods']['tmp_name']['thumb'], $pic_path);
            }
            $size = round($picsize / 1024, 2); //转换成kb 
//            $arr = array(
//                'name' => $picname,
//                'pic' => $pics,
//                'size' => $size
//            );
            $img = new ImageTool();
            $dir= '/input/' . date('Y/n/j').'/'.$pics;
//            echo $pics;
            $S = $img->resizeImage($dir, 800, 500);   
       
            echo json_encode($S); //输出json数据 
//        }
    }

}
