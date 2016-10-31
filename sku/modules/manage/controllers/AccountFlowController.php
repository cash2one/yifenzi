<?php

/**
 * 流水控制器 
 * 操作 (列表，详情)
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class AccountFlowController extends MController {

    public function filters() {
        return array(
            'rights',
        );
    }

    /**
     * 不作权限控制的action
     * @return string
     */
    public function allowedActions() {
        return 'admin,  changeMonth,getCountBeforeExportCsv,getCountBeforeExportBatchCsv';
    }

    public function actionAdmin() {
        $model = new AccountFlow('search');
        $model->unsetAttributes();
        if (isset($_GET['AccountFlow']))
            $model->attributes = $this->getParam('AccountFlow');
        if (!$this->getSession('accountFlowMonth'))
            $model->month = date('Y-m', time());

        $this->showExport = true;
        $this->exportAction = 'export';
        $totalCount = $model->backendSearch()->getTotalItemCount();
        $exportPage = new CPagination($totalCount);
        $exportPage->route = 'accountFlow/export';
        $exportPage->params = array_merge(array('grid_mode' => 'export'), $_GET);
        $exportPage->pageSize = 5000;

        $this->render('admin', array(
            'model' => $model,
            'exportPage' => $exportPage,
            'totalCount' => $totalCount,
        ));
    }

    public function actionView($id) {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * 设置当前搜索月份
     */
    public function actionChangeMonth() {
        if ($this->isAjax())
            $this->setSession('accountFlowMonth', $this->getPost('month'));
    }

    /**
     * 导出流水excel
     *
     */
    public function actionExport() {
        $model = new AccountFlow('search');
        $model->unsetAttributes();
        if (isset($_GET['AccountFlow']))
            $model->attributes = $this->getParam('AccountFlow');
        if (!$this->getSession('accountFlowMonth'))
            $model->month = date('Y-m', time());
        $model->isExport = 1;
        $this->render('export', array(
            'model' => $model,
        ));
    }

    public function actionExportMonth() {
        $this->render('exportMonth');
    }

    /**
     * 导出流水csv前检查数量
     */
    public function actionGetCountBeforeExportCsv(){
        if ($this->isAjax()){
            // 查询
            $month = date('Ym', time());
            $today = strtotime(date('Y-m-d'));
            $count = Yii::app()->ac->createCommand("SELECT count(id) FROM {{account_flow_".$month."}} WHERE export_batch=0 and create_time<'".$today."'")->queryScalar();
            if(!$count){
                $lastMonth = date("Ym", strtotime("-1 month"));
                $count = Yii::app()->ac->createCommand("SELECT count(id) FROM {{account_flow_".$lastMonth."}} WHERE export_batch=0")->queryScalar();
            }
            echo CJSON::encode($count>0 ? 'success' : 'fail');
        }
    }
    /**
     * 按批号导出流水csv前检查数量
     */
    public function actionGetCountBeforeExportBatchCsv(){
        if ($this->isAjax()){
            // 查询
            $batch = $this->getParam('batch');
            $month = substr($batch,0,6);
            if(strtotime($month.'01') > time()){
                echo CJSON::encode('fail');exit;
            }
            $file = Yii::getPathOfAlias('root') . DS . '..' . DS . 'source' . DS . 'attachments' . DS . 'export_data' . DS . 'flow-'.$batch.'.csv';
            if(file_exists($file)){
                echo CJSON::encode('exist');exit;
            }
            $sql = "SELECT count(id) FROM {{account_flow_".$month."}} WHERE export_batch='".$batch."'";
            $count = Yii::app()->ac->createCommand($sql)->queryScalar();
            echo CJSON::encode($count>0 ? 'success' : 'fail');
        }
    }
    /**
     * 导出流水csv
     */
    public function actionExportCsv() {
        $yiic = dirname(dirname(dirname(__FILE__))).DS.'console'.DS.'yiic.php';
        $batch = $this->getParam('batch',0);
        if($batch){
            exec("php $yiic exportFlow exportBatchCsv $batch &");
        }else{
            exec("php $yiic exportFlow exportCsv &");
        }
        @SystemLog::record(Yii::app()->user->name . "导出流水");
        $this->setFlash('success', Yii::t('AccountFlow', '导出申请提交成功,请稍后下载文件'));
        $this->redirect($this->createAbsoluteUrl('/accountFlow/exportMonth'));
    }

    /**
     * 下载列表
     */
    public function actionDownloadList(){
        $date = strtotime(date("Y-m"));
        $sql = "SELECT * FROM {{flow_export_batch}} where last_time>='{$date}' ORDER BY create_time DESC";
        $log = Yii::app()->ac->createCommand($sql)->queryAll();
        $this->render('exportList', array(
            'log' => $log,
        ));

    }
    public function actionDownloadListFile(){
        $file_dir = dirname(dirname(dirname(dirname(__FILE__)))).DS.'source'.DS.'attachments'.DS.'export_data'.DS;
//        $str = $this->tree($file_dir);

        $str = '';
        if(is_dir($file_dir)){
            $str .= "<ul><li><font color='#ff00cc'><b>代扣</b></font></li>";
            $mydir = dir($file_dir);
            while($file=$mydir->read()){
                if(($file!=".") && ($file!="..") && $file != 'flow'){
                    $str .= "<li><a href='".$this->createAbsoluteUrl('/accountFlow/download',array('file'=>$file))."'>$file</a></li>";
                }
            }
            $str .= "</ul>";
            $mydir->close();
        }

        if(is_dir($file_dir.DS.'flow')){
            $str .= "<br/><br/><ul><li><font color='#ff00cc'><b>流水</b></font></li>";
            $mydir = dir($file_dir.DS.'flow');
            while($file=$mydir->read()){
                if(($file!=".") && ($file!="..")){
                    $str .= "<li><a href='".$this->createAbsoluteUrl('/accountFlow/download',array('file'=>$file,'path'=>'flow'))."'>$file</a></li>";
                }
            }
            $str .= "</ul>";
            $mydir->close();
        }

        $this->render('downloadList', array(
            'str' => $str,
        ));
    }

    /**
     * 打印文件
     * @param $directory
     * @return string
     */
    public function tree($directory){
        $mydir=dir($directory);
        $str = '';
        $str .= "<ul>";
        while($file=$mydir->read()){
            if(($file!=".") && ($file!="..")){
                if((is_dir("$directory/$file"))){
                    $str .= "<li><font color='#ff00cc'><b>$file</b></font></li>";
                    $str .= $this->tree("$directory/$file");
                }else{
                    $str .= "<li><a href='".$this->createAbsoluteUrl('/accountFlow/download',array('file'=>$file))."'>$file</a></li>";
                }
            }
        }
        $str .= "</ul>";
        $mydir->close();
        return $str;
    }

    /**
     * 下载文件
     */
    public function actionDownload(){
        $file_name = $this->getParam('file',0);
        if(!$file_name)exit;
        $file_dir = dirname(dirname(dirname(dirname(__FILE__)))).DS.'source'.DS.'attachments'.DS.'export_data'.DS;
        if($this->getParam('path') == 'flow'){
            $file_dir .= "flow".DS;
        }
        $file = fopen($file_dir . $file_name,"r"); // 打开文件
        // 输入文件标签
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length: ".filesize($file_dir . $file_name));
        Header("Content-Disposition: attachment; filename=" . $file_name);
        // 输出文件内容
        echo fread($file,filesize($file_dir . $file_name));
        fclose($file);
        exit();
    }

    public function actionExportBalance(){
        set_time_limit(0);
        ini_set('memory_limit','7000M');
        if($this->isPost() && $_POST['date']){
            $date = $this->getPost('date');
            $month = date('Ym',strtotime($date));

            $sql = 'SHOW TABLES LIKE \'%gw_account_check_'.$month.'%\'';
            $is_table = Yii::app()->kac->createCommand($sql)->queryRow();
            if(empty($is_table)){
                die('没有数据');
            }

            $sql = "SELECT sku_number,today_amount,`type`,`date` FROM `gw_account_check_{$month}` WHERE `date`='{$date}' ORDER BY id ASC";
            $result = Yii::app()->kac->createCommand($sql)->queryAll();
            if(empty($result)){
                die('没有数据');
            }

            $time =date('Ymd');
            // 输出Excel文件头
            header('Content-Type: application/vnd.ms-excel;charset=GBK');
            header("Content-Disposition: attachment;filename=".$time.".csv");
            header('Cache-Control: max-age=0');

            $title = array('所属账号', '余额', '类型', '日期');

            // PHP文件句柄，php://output 表示直接输出到浏览器
            $fp = fopen('php://output', 'a');
            foreach ($title as $key => $value) {
                $title[$key]=iconv("utf-8", "GBK//IGNORE",  $value);
            }
            // 写入列头
            fputcsv($fp, array_values($title));
            // 计数器
            $cnt = 0;
            // 每隔$limit行，刷新一下输出buffer，节约资源
            $limit = 10000;
            foreach ($result as $key => $value) {
                $cnt ++;
                if ($limit == $cnt) { //刷新一下输出buffer，防止由于数据过多造成问题
                    ob_flush();
                    flush();
                    $cnt = 0;
                }
                foreach ($value as $k => $val) {
                    if($k == 'type'){
                        $val = AccountBalance::showType($val);
                    }
                    $value[$k]=iconv("utf-8", "GBK//IGNORE", $val);
                }
                fputcsv($fp,$value);
                unset($value);
            }
        }

        $this->render('balance');
    }

}
