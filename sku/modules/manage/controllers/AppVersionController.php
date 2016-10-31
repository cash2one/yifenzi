<?php

/**
 * APP版本控制器
 * Class AppVersionController
 */
class AppVersionController extends MController {

    public function filters() {
        return array(
            'rights',
        );
    }

    /**
     * 列表
     */
    public function actionAdmin() {
        $model = new AppVersion('search');
        $model->unsetAttributes();
        $model->type = AppVersion::FLAG_TYPE_GAME; //只显示游戏的
        if (isset($_GET['AppVersion']))
            $model->attributes = $_GET['AppVersion'];
        $this->render('admin', array(
            'model' => $model,
        ));
    }

    public function getIP() {
        $cip = "0";
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (!empty($_SERVER["REMOTE_ADDR"])) {
            $cip = $_SERVER["REMOTE_ADDR"];
        }
        return $cip;
    }

    /**
     * 添加
     */
    public function actionCreate() {
        ini_set('post_max_size', '50M');
        ini_set('upload_max_filesize','50M');
        set_time_limit(0);
        $model = new AppVersion();
        $this->performAjaxValidation($model);
        if (isset($_POST['AppVersion'])) {
            $_POST['AppVersion']['create_time'] = time();
            $_POST['AppVersion']['update_time'] = time();
            $_POST['AppVersion']['ip'] = Tool::ip2int($this->getIP());
            $_POST['AppVersion']['user_id'] = $this->getUser()->id;
            $model->attributes = $_POST['AppVersion'];

            //保存上传的APK
            if($_POST['AppVersion']['system_type'] == AppVersion::SYSTEM_TYPE_ANDROID){
                $file = CUploadedFile::getInstance($model, 'apk_name');
                if (!$file){
                    $this->setFlash('error', Yii::t('appVersion', '请上传安装文件'));
                    $this->redirect(array('admin'));
                    Yii::app()->end();
                }
                $fileName = $file->getName();
                $fileName = substr($fileName, 0, strrpos($fileName, '.'));
                $model = UploadedFile::uploadFile($model, 'apk_name', 'app', null, $fileName);
                $model->url = ATTR_DOMAIN . '/' . $model->apk_name;
                $model->size = round(($file->getSize() / 1024)*10000)/10000;
            }
            if($_POST['AppVersion']['system_type'] == AppVersion::SYSTEM_TYPE_IOS){
                if( $_POST['AppVersion']['ios_select'] == AppVersion::IOS_SELECT_URL){
                    $model->url = $_POST['AppVersion']['url'];
                }
                if( $_POST['AppVersion']['ios_select'] == AppVersion::IOS_SELECT_IPD){
                    //苹果IPA
                    $file = CUploadedFile::getInstance($model, 'ios_ipd');
                    if (!$file){
                        $this->setFlash('error', Yii::t('appVersion', '请上传安装文件'));
                        $this->redirect(array('admin'));
                        Yii::app()->end();
                    }
                    $fileName = $file->getName();
                    $fileName = substr($fileName, 0, strrpos($fileName, '.'));
                    $model = UploadedFile::uploadFile($model, 'ios_ipd', 'app', null, $fileName);
                    $model->url = ATTR_DOMAIN . '/' . $model->ios_ipd;
                    $model->size = round(($file->getSize() / 1024)*10000)/10000;
                    $model->apk_name = $model->ios_ipd;
                    //图片
                    $model->ios_img_url = CUploadedFile::getInstance($model, 'ios_img_url');
                    if(!$model->ios_img_url){
                        $this->setFlash('error', Yii::t('appVersion', '请上传图片'));
                        $this->redirect(array('admin'));
                        Yii::app()->end();
                    }else{
                        $imgName = $model->ios_img_url->getName();
                        $imgName = substr($imgName, 0, strrpos($imgName, '.'));
                        $model = UploadedFile::uploadFile($model, 'ios_img_url', 'app', null, $imgName);
                    }
                }
            }
            //保存上传的图片
            $model->img_url = CUploadedFile::getInstance($model, 'img_url');
            if(!$model->img_url){
                $this->setFlash('error', Yii::t('appVersion', '请上传图片'));
                $this->redirect(array('admin'));
                Yii::app()->end();
            }else{
                $imgName = $model->img_url->getName();
                $imgName = substr($imgName, 0, strrpos($imgName, '.'));
                $model = UploadedFile::uploadFile($model, 'img_url', 'app', null, $imgName);
            }
            if ($model->save()) {
                UploadedFile::saveFile('apk_name', $model->apk_name);
                UploadedFile::saveFile('ios_ipd', $model->ios_ipd);
                UploadedFile::saveFile('img_url', $model->img_url);
                UploadedFile::saveFile('ios_img_url', $model->ios_img_url);
                @SystemLog::record(Yii::app()->user->name."添加客户端：{$model->name}");
                $this->setFlash('success', Yii::t('appVersion', '添加客户端成功：') . $model->name);
                $this->redirect(array('admin'));
            }else{
                $errors = $model->getErrors();
                $errStr = '';
                if(!empty($errors)){
                    foreach ($errors as $key => $value){
                        $errStr .= '\r\n'.$key.':';
                        if(is_array($value))foreach ($value as $val){
                            $errStr .= ' '.$val;
                        }
                    }
                }
                @SystemLog::record(Yii::app()->user->name."添加客户端：{$model->name} 失败");
                $this->setFlash('success', Yii::t('appVersion', '添加客户端失败：') . $model->name.$errStr);
                $this->redirect(array('admin'));
            }
        }
        $this->render('_form', array(
            'model' => $model,
        ));
    }

    /**
     * 更新
     * @param type $id
     */
    public function actionUpdate($id) {
        ini_set('post_max_size', '50M');
        ini_set('upload_max_filesize','50M');
        set_time_limit(0);
        $model = $this->loadModel($id);
        $this->performAjaxValidation($model);
        $oldImg = $model->img_url;
        $oldIOSImg = $model->ios_img_url;
        $model->ios_select = empty($model->ios_img_url) ? 0 : 1;
        $model->ios_ipd = $model->apk_name;
        if (isset($_POST['AppVersion'])) {
            $_POST['AppVersion']['update_time'] = time();
            $_POST['AppVersion']['ip'] = Tool::ip2int($this->getIP());
            $_POST['AppVersion']['user_id'] = $this->getUser()->id;
            $model->attributes = $_POST['AppVersion'];
            //保存上传的APK
            if($_POST['AppVersion']['system_type'] == AppVersion::SYSTEM_TYPE_ANDROID){
                $file = CUploadedFile::getInstance($model, 'apk_name');
                if(!empty($file)){
                    $fileName = $file->getName();
                    $fileName = substr($fileName, 0, strrpos($fileName, '.'));
                    $model = UploadedFile::uploadFile($model, 'apk_name', 'app', null, $fileName);
                    $model->url = ATTR_DOMAIN . '/' . $model->apk_name;
                    $model->size = round(($file->getSize() / 1024)*10000)/10000;
                }
            }

            if($_POST['AppVersion']['system_type'] == AppVersion::SYSTEM_TYPE_IOS){
                if( $_POST['AppVersion']['ios_select'] == AppVersion::IOS_SELECT_URL){
                    $model->url = $_POST['AppVersion']['url'];
                }
                if( $_POST['AppVersion']['ios_select'] == AppVersion::IOS_SELECT_IPD){
                    //苹果IPA
                    $iosfile = CUploadedFile::getInstance($model, 'ios_ipd');
                    if(!empty($iosfile)){
                        $fileName = $iosfile->getName();
                        $fileName = substr($fileName, 0, strrpos($fileName, '.'));
                        $model = UploadedFile::uploadFile($model, 'ios_ipd', 'app', null, $fileName);
                        $model->url = ATTR_DOMAIN . '/' . $model->ios_ipd;
                        $model->size = round(($iosfile->getSize() / 1024)*10000)/10000;
                        $model->apk_name = $model->ios_ipd;
                    }
                    //图片
                    $Iosimg = CUploadedFile::getInstance($model, 'ios_img_url');
                    $model->ios_img_url = $Iosimg ? $Iosimg : $oldIOSImg;
                    if(!empty($Iosimg)){
                        $imgName = $model->ios_img_url->getName();
                        $imgName = substr($imgName, 0, strrpos($imgName, '.'));
                        $model = UploadedFile::uploadFile($model, 'ios_img_url', 'app', null, $imgName);
                    }
                }
            }
            //保存上传的图片
            $img = CUploadedFile::getInstance($model, 'img_url');
            $model->img_url = $img ? $img : $oldImg;
            if($img){
                $imgName = $model->img_url->getName();
                $imgName = substr($imgName, 0, strrpos($imgName, '.'));
                $model = UploadedFile::uploadFile($model, 'img_url', 'app', null, $imgName);
            }
            if ($model->save()) {
                @SystemLog::record(Yii::app()->user->name."更新客户端：{$model->name}");
                if(!empty($file)){UploadedFile::saveFile('apk_name', $model->apk_name, $this->getParam('oldFile'), true);}
                if(!empty($iosfile)){UploadedFile::saveFile('ios_ipd', $model->ios_ipd, $this->getParam('oldIOSFile'), true);}
                if(!empty($model->img_url)){UploadedFile::saveFile('img_url', $model->img_url, $this->getParam('oldImg'), true);}
                if(!empty($model->ios_img_url)){UploadedFile::saveFile('ios_img_url', $model->ios_img_url, $this->getParam('oldIOSImg'), true);}
                $this->redirect(array('admin', 'id' => $model->id));
            }else{
                @SystemLog::record(Yii::app()->user->name."更新客户端：{$model->name} 失败");
            }
        }

        $this->render('_form', array(
            'model' => $model,
        ));
    }

    /**
     * 删除
     * @param type $id
     */
    public function actionDelete($id) {
        $this->loadModel($id)->delete();
        @SystemLog::record(Yii::app()->user->name."删除客户端：{$id}");
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * 批量删除
     */
    public function actionDelAll() {
        if ($this->isPost()) {
            if (!empty($_POST['selectdel']))
                foreach ($_POST['selectdel'] as $val) {
                    $version = AppVersion::model()->findByPk($val);
                    if ($version->apk_name)
                        UploadedFile::delete(Yii::getPathOfAlias('att') . DS . $version->apk_name);
                    if ($version->img_url)
                        UploadedFile::delete(Yii::getPathOfAlias('att') . DS . $version->img_url);
                    $version->delete();
                }


            @SystemLog::record(Yii::app()->user->name."批量删除客户端：".implode(',', $_POST['selectdel']));
            if ($this->isAjax()) {
                echo CJSON::encode(array('success' => true));
            } else {
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
            }
        } else {
            throw new CHttpException(400, Yii::t('appVersionPicture', '无效的请求'));
        }
    }

    /**
     * ajax 获取app_type列表
     */
    public function actionList(){
        if($this->isAjax()){
            $type = $this->getPost('type');
            $list = AppVersion::getAppList($type);
            exit(CJSON::encode($list));
        }
    }
}
