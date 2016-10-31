<?php

/**
 * 网站相关配置控制器
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class HomeController extends MController {

	public function filters()
	{
		return array(
				'rights',
		);
	}
	
    /**
     * 修改配置文件
     * 文件名规则：控制器+Config 后缀，模型+ConfigForm后缀
     *
     * @param string $actionId   $this->action->id  控制器名称
     */
    private function _settingConfig($actionId) {
        $modelForm = ucfirst($actionId) . 'Form';
        $name = substr($actionId, 0, -6);
        $viewFileName = strtolower($name);
//        Tool::pr($viewFileName);
        $model = new $modelForm;
        //Ajax 验证,如果视图开启Ajax验证.这个是必须存在的
        if (isset($_POST['ajax']) && $_POST['ajax'] === $this->id . '-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        $model->setAttributes($this->getConfig($viewFileName));

        //ajax表单验证
        $this->performAjaxValidation($model);

        if (isset($_POST[$modelForm])) {
            $model->attributes = $_POST[$modelForm];
            if ($model->validate()) {
                $string = serialize($model->attributes);
                $value = WebConfig::model()->findByAttributes(array('name' => $viewFileName));
                if ($value) {
                    $webConfig = WebConfig::model();
                    $webConfig->id = $value->id;
                } else {
                    $webConfig = new WebConfig();
                }

                $webConfig->name = $viewFileName;
                $webConfig->value = $string;

//                $file = Yii::getPathOfAlias('common') . DS . 'webConfig' . DS . $viewFileName . '.config.inc';
                if ($webConfig->save()) { //向得到的文件路劲指定的文件里面插入数据
                
                    if (Tool::cache($viewFileName . 'config')->get($viewFileName)) {
                        Tool::cache($viewFileName . 'config')->set($viewFileName, $string);
                    } else {
                        Tool::cache($viewFileName . 'config')->add($viewFileName, $string);
                    }
                    //更新orderapi项目redis网站配置缓存@author xiaoyan.luo
//                    Tool::orderApiPost('config/updateCache',array('configName' => $viewFileName . 'config', 'value' => $string));
                    $this->setFlash('success', Yii::t('home', '数据保存成功'));
                    @SystemLog::record(Yii::app()->user->name . "修改配置文件：" . $this->action->id);
                } else {
                    $this->setFlash('error', Yii::t('home', '数据保存失败，请检查相关目录权限'));
                }
            }
        }
        //CActiveForm widget 参数
        $formConfig = array(
            'id' => $this->id . '-form',
            'enableAjaxValidation' => true,
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
            ),
        );
        $this->render(strtolower($actionId), array('model' => $model, 'formConfig' => $formConfig));
    }

    /**
     * 网站配置
     */
    public function actionSiteConfig() {
        $this->breadcrumbs = array(Yii::t('home', '网站配置管理'), Yii::t('home', '网站配置'));
        $this->_settingConfig($this->action->id);
    }

     /**
     * 分配配置
     */
    public function actionAssignConfig() {
        $this->breadcrumbs = array(Yii::t('home', '网站配置管理'), Yii::t('home', '分配配置'));
        $this->_settingConfig($this->action->id);
    }

    /**
     * 消费限额配置
     */
    public function actionAmountLimitConfig() {
    	$this->breadcrumbs = array(Yii::t('home', '网站配置管理'), Yii::t('home', '消费限额配置'));
    	$this->_settingConfig($this->action->id);
    }
    
     /**
     * 订单时间配置
     */
    public function actionOrderExpireTimeConfig() {
    	$this->breadcrumbs = array(Yii::t('home', '网站配置管理'), Yii::t('home', '订单时间配置'));
    	$this->_settingConfig($this->action->id);
    }

    /**
     * 后台语言包管理
     */
    public function actionLanguageBackend() {
        $this->_ajaxLanguageChange();
        $this->breadcrumbs = array(Yii::t('home', '网站数据管理'), Yii::t('home', '多语言-后台'));
        $messagesPath = Yii::app()->basePath . DS .'modules'. DS .'manage'. DS .'messages'. DS ;
        @SystemLog::record(Yii::app()->user->name . "配置后台语言包：" . $messagesPath);
        $this->_manageLanguage($messagesPath);
    }
    /**
     * sku 项目公共语言包管理
     */
    public function actionLanguageSku() {
        $this->_ajaxLanguageChange();
        $this->breadcrumbs = array(Yii::t('home', '网站数据管理'), Yii::t('home', '公共语言包管理'));
        $messagesPath = Yii::app()->basePath . DS .'messages'. DS ;

        @SystemLog::record(Yii::app()->user->name . "配置后台语言包：" . $messagesPath);
        $this->_manageLanguage($messagesPath);
    }

    /**
     * 前台语言包管理
     */
    public function actionLanguagePartner() {
        $this->_ajaxLanguageChange();
        $this->breadcrumbs = array(Yii::t('home', '网站数据管理'), Yii::t('home', '多语言-商户'));
        $messagesPath = Yii::app()->basePath .DS .'modules'. DS .'partner'. DS .'messages'. DS;
        @SystemLog::record(Yii::app()->user->name . "配置前台语言包：" . $messagesPath);
        $this->_manageLanguage($messagesPath);
    }

    /**
     * api语言包管理
     */
    public function actionLanguageApi() {
        $this->_ajaxLanguageChange();
        $this->breadcrumbs = array(Yii::t('home', '网站数据管理'), Yii::t('home', '多语言-API'));
        $messagesPath = Yii::app()->basePath .DS .'modules'. DS .'api'. DS .'messages'. DS;
        @SystemLog::record(Yii::app()->user->name . "配置API语言包：" . $messagesPath);
        $this->_manageLanguage($messagesPath);
    }


    /**
     * 语言包管理
     * @param string  $messagesPath 语言包的路径
     */
    private function _manageLanguage($messagesPath) {
        $messagesConfig = include $messagesPath . 'config.php';
        $languageFiles = array(); //语言包文件数组
        $result = array(); //搜索结果
        $languageDir = Tool::authcode($this->getQuery('languageList'), 'DECODE'); //语言包文件目录
        if ($languageDir && isset($_GET['keyword'])) {
            $keyword = $this->getQuery('keyword');
            //如果输入为空，则列出语言包目录文件
            foreach (scandir($languageDir) as $v) {
                if (substr($v, -3) !== 'php')
                    continue;
                $realPath = $languageDir . DS . $v;
                $languageFiles[$realPath] = $v;
                if (!empty($keyword)) {
                    $tmp = include $realPath;
                    $find = $this->_array_search($tmp, $keyword);
                    if (!empty($find)) {
                        $result[] = array('file' => Tool::authcode($realPath), 'result' => $find);
                    }
                }
            }

            //empty by condition
            if (!empty($keyword)) {
                if (empty($result))
                    $this->setFlash('error', Yii::t('home', '搜索结果为空'));
                $languageFiles = array();
            }else {
                $result = array();
            }
        }
        //语言包内容显示
        $languageArr = array(); //语言包数组
        $languageName = '';
        $file = Tool::authcode($this->getParam('languageFile'), 'DECODE');
        if (!empty($file) && !$this->isPost()) {
            $languageName = $messagesConfig['languageName'][basename(dirname($file))];
            if (!file_exists($file)) {
                Yii::app()->user->setFlash('error', Yii::t('home', '您访问的语言包文件不存在！:') . $file);
            } else {
                $languageArr = include $file;
            }
        }

        $dir = array(); //语言包目录数组
        //目录选择
        foreach (scandir($messagesPath) as $v) {
            $realPath = $messagesPath . $v;
            if (is_dir($realPath) && $v[0] != '.') {
                $dir[Tool::authcode($realPath)] = $messagesConfig['languageName'][$v];
            }
        }

        $this->render('languagemanage', array(
            'dir' => $dir,
            'result' => $result,
            'languageDir' => $languageDir,
            'languageFiles' => $languageFiles,
            'languageArr' => $languageArr,
            'languageName' => $languageName,
            'messagesConfig' => $messagesConfig,
        ));
    }

    /**
     * ajax 修改或者删除语言包文件
     */
    private function _ajaxLanguageChange() {
        if ($this->isAjax()) {
            $do = $this->getPost('do');
            $languageFile = Tool::authcode($this->getPost('languageFile'), 'DECODE');
            if ($do == 'delFile') {
                UploadedFile::delete($languageFile);
                @SystemLog::record(Yii::app()->user->name . "删除语言包：" . $languageFile);
            } else {
                $key = $this->getPost('key');
                $value = $this->getPost('value');
                if (!file_exists($languageFile))
                    return false;
                $language = include $languageFile;
                if ($do == 'update') {
                    $language[$key] = $value;
                    $languageStr = "<?php \r\n //语言包文件 \r\n";
                    $languageStr .= 'return ' . var_export($language, TRUE) . ';';
                    if (file_put_contents($languageFile, $languageStr)) {
                        @SystemLog::record(Yii::app()->user->name . "修改语言包：" . $languageFile);
                        echo Yii::t('home', '修改语言包成功');
                    } else {
                        echo Yii::t('home', '修改语言包失败');
                    }
                }
                if ($do == 'del') {
                    unset($language[$key]);
                    $languageStr = "<?php \r\n //语言包文件 \r\n";
                    $languageStr .= 'return ' . var_export($language, TRUE) . ';';
                    if (!file_put_contents($languageFile, $languageStr))
                        echo Yii::t('home', '删除语言包失败');

                    @SystemLog::record(Yii::app()->user->name . "删除语言包：" . $languageFile);
                }
            }
            exit;
        }
    }

    /**
     * 搜索数组中的键值
     * @param array $arr
     * @param string $needle
     * @return array
     */
    private function _array_search(Array $arr, $needle) {
        $result = array();
        foreach ($arr as $k => $v) {
            if (stripos($k, $needle) !== false || stripos($v, $needle) !== false) {
                $result[$k] = $v;
            }
        }
        return $result;
    }

    /**
     * 前台语言包管理
     */
    public function actionCreateFrontPackFromDb() {
        $this->createEnglishPackage(0);
    }

    /**
     * 后台语言包管理
     */
    public function actionCreateBackPackFromDb() {
        $this->createEnglishPackage(1);
    }

    /**
     * api语言包管理
     */
    public function actionCreateApiPackFromDb() {
        $this->createEnglishPackage(3);
//        $this->createEnglish(0);
//        $this->createEnglish(1);
//        $this->createEnglish(3);
//        $this->createEnglish(9);
    }

    /**
     * 生成语言包
     * @param bool $isBackend 后台
     */
    public function createEnglishPackage($isBackend = 0) {
        $return = '';
//        $dir = $isBackend ? 'backend' : 'frontend';
        switch ($isBackend) {
            case 0: $dir = 'modules'. DS .'manage'. DS .'messages';
                break;
            case 1: $dir = 'modules'. DS .'partner'. DS .'messages';
                break;
            case 3: $dir = 'modules'. DS .'api'. DS .'messages';
                break;
            default : $dir = 'modules'. DS .'manage'. DS .'messages';
                break;
        }
        $path = Yii::getPathOfAlias('root') . DS . $dir . DS . 'messages' . DS . 'en' . DS;
        $dirDetail = scandir($path);
        $category = Yii::app()->db->createCommand()
                ->select('DISTINCT(category)')->from('{{translate}}')->where("`en` is not null or `en` <> '' and is_backend=0")
                ->queryColumn();
        if (!empty($category)) {
            foreach ($category as $cat) {
                if ($cat == false)
                    continue;
                $reader = $packet = $packetTemp = array();
                $packString = '';
                if (in_array($cat . '.php', $dirDetail)) {
                    $packetTemp = require $path . $cat . '.php';
                    if (!empty($packetTemp))
                        $packet = $packetTemp;
                }
                unset($packetTemp, $packString);
                $sqlFind = "select * from {{translate}} where category='{$cat}' and (`en` is not null or `en` <> '') and is_backend={$isBackend} order by category ASC";
                $command = Yii::app()->db->createCommand($sqlFind);
                $command->execute();
                $reader = $command->query();
                $finished = 0;
                foreach ($reader as $key => $row) {
                    if (!empty($row)) {
                        if (!isset($packet[$row['cn']]))
                            $packet[$row['cn']] = $row['en'];
                    }
                }
                if (!empty($packet)) {
                    $languageFile = $path . $cat . '.php';
                    $languageStr = "<?php \r\n //语言包文件 \r\n";
                    $languageStr .= 'return ' . var_export($packet, TRUE) . ';';
                    if (file_put_contents($languageFile, $languageStr)) {
                        @SystemLog::record(Yii::app()->user->name . "生成语言包：" . $languageFile);
                        $return .= "生成语言包" . $cat . "成功.\r\n";
                    } else {
                        $return .= "生成语言包" . $cat . "失败.\r\n";
                    }
                }
            }
        } else {
            $return = '暂无数据可供生成!';
        }
        echo json_encode($return);
    }

    public function createEnglish($isBackend = 0) {
        $return = '';
        switch ($isBackend) {
            case 0: $dir = 'frontend';
                break;
            case 1: $dir = 'backend';
                break;
            case 3: $dir = 'api';
                break;
            default : $dir = 'backend';
                break;
        }

        /**
         * 
         */
        $reader = $packet = array();
//                $sqlFind = "select * from {{translate}} where category='{$cat}' and (`en` is not null or `en` <> '') and is_backend={$isBackend} order by category ASC";
        $sqlFind = "SELECT DISTINCT cn,en FROM gw_translate WHERE is_backend={$isBackend} and cn<>'' and cn is NOT NULL AND category<>'region' ORDER BY cn ASC";
        if ($isBackend == 9) {
            $sqlFind = "SELECT DISTINCT cn,en FROM gw_translate WHERE cn<>'' and cn is NOT NULL AND category='region' ORDER BY cn ASC";
        }
        $command = Yii::app()->db->createCommand($sqlFind);
        $command->execute();
        $reader = $command->query();
        foreach ($reader as $key => $row) {
            if (!empty($row)) {
                if ($row['en'] == null)
                    $row['en'] = '';
                $packet[$row['cn']] = $row['en'];
            }
        }
        if (!empty($packet)) {
            $languageFile = Yii::getPathOfAlias('root') . DS . 'package' . $isBackend . '.php';
            $languageStr = "<?php \r\n //语言包文件 \r\n";
            $languageStr .= 'return ' . var_export($packet, TRUE) . ';';
            if (file_put_contents($languageFile, $languageStr)) {
//                            @SystemLog::record(Yii::app()->user->name."生成语言包：".$languageFile);
                $return .= "生成语言包" . "成功.\r\n";
            } else {
                $return .= "生成语言包" . "失败.\r\n";
            }
        }
//        echo json_encode($return);
    }

}
