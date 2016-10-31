<?php

/**
 * 挂单管理控制器
 * 操作(删除，修改，列表)
 * @author leo8705
 */
class GuadanController extends MController
{

    public function filters()
    {
        return array(
            'rights',
        );
    }

    // 挂单管理
    public function actionGuadanAdmin()
    {
        $model = new Guadan('search');
        $upload_model = new UploadForm('excel');
        $this->performAjaxValidation($model);
        $this->performAjaxValidation($upload_model);
        $model->unsetAttributes();
        if (isset($_GET['Guadan']))
            $model->attributes = $_GET['Guadan'];


        $this->render('guadanAdmin', array(
            'model' => $model,
            'upload_model' => $upload_model
        ));
    }


    /**
     * 挂单导入excel模板
     * @param string $file
     */
    public function actionGuadanImportTemplate()
    {
        //        $data = CJSON::decode($data);
        //引入phpExcel
        require Yii::getPathOfAlias('comext') . '/PHPExcel/PHPExcel/Shared/String.php';
        require Yii::getPathOfAlias('comext') . '/PHPExcel/PHPExcel.php';
        Yii::registerAutoloader(array('PHPExcel_Autoloader', 'Register'), true);
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '盖网号')
            ->setCellValue('B1', '挂单金额')
            ->setCellValue('C1', '百分比折扣');
        if($this->getParam('type')==Guadan::TYPE_NO_BIND){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A2', Guadan::GAI_NUMBER_UNBIND)
                ->setCellValue('B2', '')
                ->setCellValue('C2', '100');
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="guadanImportTemplate.xls"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        @SystemLog::record(Yii::app()->user->name . "下载挂单导入excel模板");
        unset($objPHPExcel, $objWriter);
    }

    /**
     * 挂单导入excel
     */
    public function actionExcelImport()
    {
        @ini_set('memory_limit', '2048M');
        set_time_limit(0);
        $model = new UploadForm('excel');
        $this->performAjaxValidation($model);
        // Uncomment the following line if AJAX validation is needed
        if (isset($_POST['UploadForm'])) {
            $fileSuffix = substr(strrchr($_FILES['UploadForm']['name']['file'], '.'), 1);

            if ('xls' == $fileSuffix || 'xlsx' == $fileSuffix) {
                $model->attributes = $_POST['UploadForm'];

                $path = 'guadan' . DS . 'import' . DS . date('Ym');
                $model = UploadedFile::uploadFile($model, 'file', $path, Yii::getPathOfAlias('cache'));
                UploadedFile::_saveFile('file', $model->file);

                // 上传的文件
                $filename = Yii::getPathOfAlias('cache') . DS . $model->file;
               
                //引入phpExcel
                require Yii::getPathOfAlias('comext') . '/PHPExcel/PHPExcel/Shared/String.php';
                require Yii::getPathOfAlias('comext') . '/PHPExcel/PHPExcel.php';
                Yii::registerAutoloader(array('PHPExcel_Autoloader', 'Register'), true);

                $objReader = PHPExcel_IOFactory::createReader('Excel5'); //use excel2007 for 2007 format
                $objPHPExcel = $objReader->load($filename); //$filename可以是上传的文件，或者是指定的文件
                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow(); // 取得总行数

                $message = array(); //记录失败用户
                $rs = array();
                $member_s = array();
                $gai_number_s = array();
                $type = $this->getParam('type', 1);
                for ($j = 2; $j <= $highestRow; $j++) {
                    $rs[$j]['gai_number'] = trim($objPHPExcel->getActiveSheet()->getCell("A" . $j)->getValue());
                    $rs[$j]['type'] = $type;
                    $rs[$j]['amount'] = trim($objPHPExcel->getActiveSheet()->getCell("B" . $j)->getValue());
                    $rs[$j]['amount_remain'] = $rs[$j]['amount'];
                    $rs[$j]['discount'] = trim($objPHPExcel->getActiveSheet()->getCell("C" . $j)->getValue());
                    $gai_number_s[] = $rs[$j]['gai_number'];
                }

                $member_list = Yii::app()->db->createCommand()
                    ->select('id,sku_number,gai_number,gai_member_id')
                    ->from(Member::model()->tableName())
                    ->where('gai_number IN ("' . implode('","', $gai_number_s) . '")')
                    ->queryAll();

                foreach ($member_list as $m) {
                    $member_s[$m['gai_number']] = $m;
                }

                $table = AccountFlow::monthTable(); //当月流水表
                $fail = 0; $success = 0; $count = count($rs);
                foreach ($rs as $val) {
                    if (empty($val['gai_number'])) {
                        $fail += 1;
                        continue;
                    }
                    $trans = Yii::app()->db->beginTransaction();
                    $data = array(
                        Tool::buildOrderNo(20, 'G'),
                        isset($member_s[$val['gai_number']]) ? $member_s[$val['gai_number']]['gai_member_id'] : 0,
                        $val['gai_number'],
                        $val['type'],
                        $val['amount'],
                        $val['amount_remain'],
                        $val['discount'],
                        Guadan::STATUS_ENABLE,
                        time()
                    );
                    $values = '"' . implode('","', $data) . '"';
                    $sql = "INSERT INTO {{guadan}} (code,member_id,gai_number,type,amount,amount_remain,discount,status,create_time)VALUES($values);";
                    $db = Yii::app()->db;
                    $result = $db->createCommand($sql)->execute();
                    if(!$result) { 
                        $trans->rollback();
                        $message[] = $val['gai_number'];
                        $fail += 1;
                        continue; //跳出本次循环
                    }
                    $id = $db->getLastInsertID();
                    $guadan_info = array( //写入流水挂单数据
                        'order_code' => $data[0],
                        'order_id' => $id,
                        'gai_number'=> $val['gai_number'],
                        'amount' => $val['amount'],
                    );
                    //执行流水 
                    //////////////////公共账户流水/////////
                    $types_common = array(
                        'type'=> AccountBalance::TYPE_COMMON,
                        'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_IMPORT,
                        'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_IMPORT_OUT,
                        'transaction_type' => AccountFlow::TRANSACTION_TYPE_GUADAN
                    );
                    $ccount_type = CommonAccount::TYPE_GUADAN_BINDING;
                    if($type == Guadan::TYPE_NO_BIND){
                        $ccount_type = CommonAccount::TYPE_GUADAN_UNBUNDLING;
                    }
                    $result_account = AccountBalance::ImportGuaDan($guadan_info,$types_common,$ccount_type,$table,true,true); 
                    /////////////////公共账户流水//////////

                    //////////// 用户流水 转入 ////////////
                    $types_user = array( //流水各重要节点类型
                        'type'=> AccountBalance::TYPE_GUADAN_XIAOFEI,
                        'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_IMPORT,
                        'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_IMPORT_IN,
                        'transaction_type' => AccountFlow::TRANSACTION_TYPE_GUADAN
                    );
                    $result_user = AccountBalance::ImportGuaDan($guadan_info,$types_user,$ccount_type,$table); 
                    //////////// 用户流水 转入 ////////////
                    if ($result_account && $result_user) {
                        $trans->commit();
                        $success += 1; 
                    } else {
                        $trans->rollback();
                        $message[] = $val['gai_number'];
                        $fail += 1;
                        continue;
                    }
                }
                // 删除上传的文件
                unlink($filename);
                @SystemLog::record("管理员(".$this->getUser()->name . ")批量导入{$success}笔挂单进入队列",  SystemLog::LOG_TYPE_GUADAN);
                $str = "共导入{$count}条挂单，成功{$success}条，失败{$fail}。";
                //if($fail) $str .= '失败的用户有:'.implode (',', $message);
                $this->setFlash('success', $str);
            } else {
                $this->setFlash('error', '导入挂单列表失败,请上传文件格式为:xls,xlsx');
            }
            $this->redirect(array('guadan/guadanAdmin'));
        }

    }


    /**
     * 积分提取
     */
    public function actionCollect()
    {
        //部分提取
        $ids = $this->getParam('ids');
        if ($ids) {
            $ids = explode(',', $ids);
            $amount1 = Guadan::getAmount(Guadan::TYPE_TO_BIND, $ids);
            $amount2 = Guadan::getAmount(Guadan::TYPE_NO_BIND, $ids);
            //$this->setSession('currentGuadan',null);
        } else {
            //全部提取
            $amount1 = Guadan::getAmount(Guadan::TYPE_TO_BIND);
            $amount2 = Guadan::getAmount(Guadan::TYPE_NO_BIND);
            $ids = array();
        }
        if ($this->getParam('form')) { //ajax弹窗，添加政策
            $model = new GuadanCollect('create');
            $model->maxUnbind = $amount2;
            $model->maxBind = $amount1;
            $this->performAjaxValidation($model);
            $model->amount_bind = $amount1;
            $model->amount_unbind = $amount2;
            if (isset($_POST['GuadanCollect'])) {
                $model->attributes = $_POST['GuadanCollect'];
                if ($model->validate()) {
//                     	$model->status = GuadanCollect::STATUS_ENABLE;
                        if (!$model->save()) {
                            throw new Exception("保存guadanCollect失败");
                        }
                        if($model->addRelation($ids))
                        {
                            $this->setSession('currentGuadan',$model->id);
                            @SystemLog::record("管理员(" . $this->getUser()->name . ")新增售卖计划<{$model->code}>", SystemLog::LOG_TYPE_SHOUMAI);
                            echo '<script>var success = true;alert("操作成功")</script>';
                        }
                        else 
                        {
                            $this->setFlash('error', '操作失败' );
                        }
                    } 
                else {
                    $this->setFlash('error', '操作失败' . json_encode($model->getErrors()));
                }
            }

            $this->render('collectForm', array('model' => $model));
        } else {
            $data = GuadanCollect::getNotRelation();
            $this->render('collect', array('amount1' => $amount1, 'amount2' => $amount2,'data'=>$data));
        }
    }

    /**
     * 删除挂单提取
     * @param int $id 提取id
     */
    public function actionDelCollect($id)
    {
        /** @var GuadanCollect $model */
        $model = GuadanCollect::model()->findByPk($id);
        if ($model) {
            $relation = Yii::app()->db->createCommand('SELECT * FROM {{guadan_relation}} WHERE collect_id=' . $id)->queryAll();
            $trans = Yii::app()->db->beginTransaction();
            try {
                //删除政策
                Yii::app()->db->createCommand()->delete('{{guadan_rule}}', 'collect_id=' . $id);
                //提取回滚
                foreach ($relation as $v) {
                    $sql = 'UPDATE {{guadan}} SET amount_remain=amount_remain+' . $v['amount'] . ' WHERE id=' . $v['guadan_id'];
                    Yii::app()->db->createCommand($sql)->execute();
                    //删除 挂单提取关联
                    Yii::app()->db->createCommand()->delete('{{guadan_relation}}', 'collect_id=' . $v['collect_id']);
                    //流水……
                    $gai_number = Yii::app()->db->createCommand('select gai_number from gw_sku_guadan where id='.$v['guadan_id'])->queryScalar();
                    $model->addFlow($gai_number,-$v['amount'],$v['type']);
                }

                $model->delete();
                echo json_encode(array('msg'=>'删除成功！','flag'=>true));
                $trans->commit();
            } catch (Exception $e) {
                $trans->rollback();
                echo json_encode(array('msg'=>'删除失败！:'.$e->getMessage(),'flag'=>false));
            }
        }
    }


    // 积分批发管理
    public function actionPifaAdmin()
    {
        $model = new Guadan('search');
        //获取商品分类
        $Guadancategory = $model->GuadanCategory;
        $model->unsetAttributes();
        if (isset($_GET['Guadan']))
            $model->attributes = $_GET['Guadan'];


        $this->render('admin', array(
            'model' => $model,
            'Guadancategory' => $Guadancategory
        ));
    }


    // 绑定管理
    public function actionBindAdmin()
    {
        $this->redirect('/memberBind/index');
    }

    // 日志
    public function actionLog()
    {
    	$this->redirect('/SystemLog/Index');
    }
    /**
     * 撤销挂单 返还积分
     * @param type $id 挂单ID
     */
    public function actionDisable($id){
        if(!is_numeric($id)) throw new Exception('未知参数');
        $model = new Guadan('select');
        $model = $model->findByPk($id);
        //检测是否已经售完
        if(!$model) throw new CException('该挂单不存在！');
        if(!(float)$model->amount_remain || $model->status == Guadan::STATUS_DISABLE){
            $this->setFlash ('error','已经售完或已经撤销挂单不允许撤销！');
            $this->redirect(array('guadan/guadanAdmin'));
            Yii::app()->end();
        }
        //计算剩余积分
        //流水需要挂单的几个数据
        $guadan_info = array(
            'order_id' => $model->id,
            'order_code' => $model->code,
            'gai_number' => $model->gai_number,
            'amount' => - $model->amount_remain,
        );
        $table = AccountFlow::monthTable();
        $trans = Yii::app()->db->beginTransaction();
        //撤销挂单
        $model->status = Guadan::STATUS_DISABLE;
        if($model->save()){
            $account_type = CommonAccount::TYPE_GUADAN_BINDING;
            if($model->type == Guadan::TYPE_NO_BIND){
                $account_type = CommonAccount::TYPE_GUADAN_UNBUNDLING;
            }
            /*************撤销挂单总账户转出---退回**************/
            $types_common = array(
                'type'=> AccountBalance::TYPE_COMMON,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_IMPORT_DEL,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_IMPORT_DEL_OUT,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_GUADAN
            );
            $result_common = AccountBalance::ImportGuaDan($guadan_info, $types_common,$account_type, $table, true, true);
            /*************撤销挂单总账户转出---退回**************/
            /*************撤销挂单用户账号转入---退回**************/
            $types_user = array(
                'type'=> AccountBalance::TYPE_GUADAN_XIAOFEI,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_IMPORT_DEL,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_IMPORT_DEL_IN,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_GUADAN
            );
            $result_user = AccountBalance::ImportGuaDan($guadan_info, $types_user,$account_type, $table);
            /*************撤销挂单用户账号转入---退回**************/
//            exit;
            if($result_common && $result_user){
                $trans->commit();
                $this->setFlash('success','撤销成功');
                @SystemLog::record("管理员(".$this->getUser()->name.")撤销了1笔挂单",  SystemLog::LOG_TYPE_GUADAN);
            } else {
                $trans->rollback();
                $this->setFlash('error','撤销失败');
            }
        } else {
            $trans->rollback();
            $str = serialize($model->getErrors());
            $this->setFlash('error','撤销挂单失败'.$str);
        }
        $this->redirect(array('guadan/guadanAdmin'));
    }
    
    /**
     * 冻结挂单
     * @param type $id
     */
    public function actionFrozen($id){
        if(is_numeric($id)){
            $rs = Guadan::model()->updateByPk($id, array('status'=>  Guadan::STATUS_FROZEN));
            if($rs) {
               $this->setFlash('success','挂单已冻结'); 
               @SystemLog::record("管理员(".$this->getUser()->name.")冻结了1笔挂单",  SystemLog::LOG_TYPE_GUADAN);
               $this->redirect(array('guadan/guadanAdmin'));
            }
        }
        $this->setFlash('error','挂单冻结失败');
        $this->redirect(array('guadan/guadanAdmin'));
    }
    /**
     * 挂单解冻
     * @param type $id
     */
    public function actionUnfreeze($id){
        if(is_numeric($id)){
            $rs = Guadan::model()->updateByPk($id, array('status'=>  Guadan::STATUS_ENABLE));
            if($rs) {
               $this->setFlash('success','挂单已解冻'); 
               @SystemLog::record("管理员(".$this->getUser()->name.")解冻了1笔挂单",  SystemLog::LOG_TYPE_GUADAN);
               $this->redirect(array('guadan/guadanAdmin'));
            }
        }
        $this->setFlash('error','挂单解冻失败');
        $this->redirect(array('guadan/guadanAdmin'));
    }
}
