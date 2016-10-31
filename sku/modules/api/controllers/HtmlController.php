<?php
/**
 * 公共页面控制器  提供商品、店铺等html页面的展示
 * 
 * @author leo8705
 *
 */

class HtmlController extends APIController {

    /*
     * 
     * 盖鲜生商家入驻问卷申请
     * 
     */
    public function actionGaiFreshJoinQuest() {


        $data = $this->getParam('data');
        if(!empty($data)){
            $callback=$_GET['callback'];
            $json = stripslashes($data);
            $data =json_decode($json,true);
            if(empty($data['name'])|| empty($data['mobile']) || empty($data['quest'])){
                $result=json_encode(array('result'=>0,'msg'=>'你的问卷未填写完整'));
                echo $callback."($result)";exit;
            }
            if(!Fun::validateMobile($data['mobile'])){
                $result=json_encode(array('result'=>0,'msg'=>'非手机号码'));
                echo $callback."($result)";exit;
            }
            $quest = array();
            foreach ($data['quest'] as $k => $v){
                if(is_array($v)){
                    foreach ($v as $key => $val){
                        $quest[$k][$key]= addslashes(Tool::filter_script($val));
                    }
                }else{
                    $quest[$k] = addslashes(Tool::filter_script($v));
                }
            };
            $model = new FreshQuestResult;
            $model->name = Tool::filter_script($data['name']);
            $model->type = FreshQuestResult::TYPE_ZHAOSHANG;
            $model->mobile = $data['mobile'];
            $model->create_time = time();
            $model->data = serialize($quest);
            if($model->save()){
                $result=json_encode(array('result'=>1,'msg'=>'提交成功'));
                echo $callback."($result)";exit;
            }else{
                $result=json_encode(array('result'=>0,'msg'=>'提交失败'));
                echo $callback."($result)";exit;
            }
        }else{
            $this->render('gaiFreshJoinQuest');
        }


    	
    }

    
    
    /*
     *
    * 盖鲜生机器装机问卷申请
    *
    */
    public function actionGaiFreshMachineApplyQuest() {
        $data = $this->getParam('data');
        if(!empty($data)){
            $callback=$_GET['callback'];
            $json = stripslashes($data);
            $data =json_decode($json,true);
            if(empty($data['name'])|| empty($data['mobile']) || empty($data['quest'])){
                $result=json_encode(array('result'=>0,'msg'=>'你的问卷未填写完整'));
                echo $callback."($result)";exit;
            }
            if(!Fun::validateMobile($data['mobile'])){
                $result=json_encode(array('result'=>0,'msg'=>'非手机号码'));
                echo $callback."($result)";exit;
            }
            $quest = array();
            foreach ($data['quest'] as $k => $v){
                if(is_array($v)){
                    foreach ($v as $key => $val){
                        $quest[$k][$key]= addslashes(Tool::filter_script($val));
                    }
                }else{
                    $quest[$k] = addslashes(Tool::filter_script($v));
                }
            };

            $model = new FreshQuestResult;
            $model->name = addslashes(Tool::filter_script($data['name']));
            $model->type = FreshQuestResult::TYPE_ZHUANGJI;
            $model->mobile = $data['mobile'];
            $model->create_time = time();

            $model->data = serialize($quest);
            if($model->save()){
                $result=json_encode(array('result'=>1,'msg'=>'提交成功'));
                echo $callback."($result)";exit;
            }else{
                $result=json_encode(array('result'=>0,'msg'=>'提交失败'));
                echo $callback."($result)";exit;
            }
        }else{
            $this->render('gaiFreshMachineApplyQuest');
        }

    }
    
    /**
     * sku商户加盟资料提交页面
     */
    public function actionPartnerJoinAuditing(){
    	$model = new PartnerJoinAuditing();
                   $this->performAjaxValidation($model,"PartnerJoinAuditing-form");
    	 if (isset($_POST['PartnerJoinAuditing'])){
    	 	$bankARR = BankAccount::getBankList();
    	 	$model->bank = $bankARR[$_POST['PartnerJoinAuditing']['bank']];
    	 	$model->referrals_gai_number = $_POST['PartnerJoinAuditing']['referrals_gai_number'];
    	 	$model->store_province_id = $_POST['PartnerJoinAuditing']['store_province_id'];
    	 	$model->store_city_id = $_POST['PartnerJoinAuditing']['store_city_id'];
    	 	$model->store_district_id = $_POST['PartnerJoinAuditing']['store_district_id'];
    	 	$model->store_address = $_POST['PartnerJoinAuditing']['store_address'];
    	 	$model->store_mobile = $_POST['PartnerJoinAuditing']['store_mobile'];
    	 	$model->license_to_time = $_POST['PartnerJoinAuditing']['license_to_time'];
    	 	
    	 	$model->store_name = $_POST['PartnerJoinAuditing']['store_name'];
    	 	$model->bank_account_name = $_POST['PartnerJoinAuditing']['bank_account_name'];

    	 	$model->bank_account = $_POST['PartnerJoinAuditing']['bank_account'];
    	 	$model->bank_province_id = $_POST['PartnerJoinAuditing']['bank_province_id'];
    	 	$model->bank_city_id = $_POST['PartnerJoinAuditing']['bank_city_id'];
    	 	$model->bank_district_id = $_POST['PartnerJoinAuditing']['bank_district_id'];
    	 	$model->bank_branch = $_POST['PartnerJoinAuditing']['bank_branch'];
    	 	$model->id_name = $_POST['PartnerJoinAuditing']['id_name'];
    	 	$model->id_card = $_POST['PartnerJoinAuditing']['id_card'];
    	 	$model->id_card_to_time = $_POST['PartnerJoinAuditing']['id_card_to_time'];
    	 	$model->name = $_POST['PartnerJoinAuditing']['name'];
    	 	$model->mobile = $_POST['PartnerJoinAuditing']['mobile'];
    	 	$model->gai_number = $_POST['PartnerJoinAuditing']['gai_number'];
    	 	$model->create_time = time();
    	 	
    	 	

    	 	
    	 	
    	 	$model->license_to_time = strtotime($model->license_to_time);
    	 	$model->id_card_to_time = strtotime($model->id_card_to_time);
    	 	

    	 	$license_img = CUploadedFile::getInstance($model, 'license_img');
    	 	$bank_img = CUploadedFile::getInstance($model, 'bank_img');
    	 	$id_card_font_img = CUploadedFile::getInstance($model, 'id_card_font_img');
    	 	$id_card_back_img = CUploadedFile::getInstance($model, 'id_card_back_img');
    	 	$head = CUploadedFile::getInstance($model, 'head');
    	 	$model->license_img = CUploadedFile::getInstance($model, 'license_img');
    	 	$model->bank_img = CUploadedFile::getInstance($model, 'bank_img');
    	 	$model->id_card_font_img = CUploadedFile::getInstance($model, 'id_card_font_img');
    	 	$model->id_card_back_img = CUploadedFile::getInstance($model, 'id_card_back_img');
    	 	$model->head = CUploadedFile::getInstance($model, 'head');

      	 	$arr = array("license_img"=>$license_img,"bank_img"=>$bank_img,"id_card_font_img"=>$id_card_font_img,"id_card_back_img"=>$id_card_back_img,"head"=>$head);

    	 	//上传图片
    	 	
            

    	 	if($model->save()){

    	 		$saveDir = 'partnerjoinauditing/' . $model->id;
    	 		$upDir = 'partnerjoinauditing/' . $model->id.'/';
    	 		foreach ($arr as $key=>$val){
    	 			if(!empty($val)){
    	 				$model->$key = $val;
	    	 			$fileNameArr = explode(".", $val->name);
	    	 			$model = UploadedFile::uploadFile($model, $key,$saveDir,Yii::getPathOfAlias('att'),$fileNameArr[0]);
	    	 			UploadedFile::saveFile($key, $model->$key);
	    	 			$Sql = "UPDATE ".PartnerJoinAuditing::model()->tablename()." SET {$key} = '{$model->$key}' WHERE id = '{$model->id}'";
	    	 			Yii::app()->db->createCommand($Sql)->execute();
    	 			}
    	 		}
    	 		echo "<script language= javascript >alert('提交成功！');</script> ";
    	 	}

    	 	 $model->bank = $_POST['PartnerJoinAuditing']['bank'];
    	 	 
    	 }
    	$this->render('partnerjoinauditing',array(
    			'model'=>$model,
    	));
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
    
    /**
     * SKU平台商户协议
     */
    public function actionPartnerJoinAuditingAbout(){
    	$this->render('partnerjoinauditingabout');
    }
    
    /**
     * SKU商户主体资质要求
     * 
     */
    public function actionPartnerJoinAuditingZhuTi(){
    	$this->render('partnerjoinauditingzhuti');
    }
    
    /**
     * SKU商品类目资质要求
     */
    public function actionPartnerJoinAuditingLeiMu(){
    	$this->render('partnerjoinauditingleimu');
    }
    
    /**
     * 禁售商品管理规范
     */
    public function actionPartnerJoinAuditingJinShou(){
    	$this->render('partnerjoinauditingjinshou');
    }
    
    /*
     * 测试post
     */
    public function actionPost(){
        $data = array(
            'GWnumber'=>'86ebb2ef4bfa8f08b516434cf986592f0e11a2ef0e9f9a4448ec4aa2890619de9a2c54c97d171c5908ed828ca9e91e72a3de25eea88df4129a73115b77daf219b2d7dc2de86d76ec1f5b99f34f7c5e5c947c280c4c6c46e98b42d46d4ce0db2f20b9ea8fe0e0916e4f8d433ee3ecfc66757657ab7f0c8a911d042d29cb4eec91',
            'pwd'=>'58311d8b177d54010ccb163a3cb65f96c8625748c75f97fa33d594d9a81cd9fe128239cab5650646dcd6d31ac2b36a0370fd0918c3cfe440ec308ecef650120a4a1edaa8b3003849c63eccfe75dc6333f51ff313fe7597cfb7ca639f3853b9cf2f07890d4ac27f690f7bc53b9b2d6718e73fe4647c79cb749becf1052169b6cd',
            'MacCode'=>'hweD5IoS1y8YGhm86IUnD7m5Yxp7RvFV2nbbFkm9',
            'ver'=>1
            );
        $url = "http://token.gatewangapi.net/token/login";
        $RS= Tool::post($url, $data);
        print_r($RS);
    }
}