<?php
/**
 * Created by PhpStorm.
 * User: Gatewang
 * Date: 2015/11/6
 * Time: 11:06
 */
class FreshQuestResultController extends MController{
    public $showBack = false;

    /**
     * 问卷列表
     */
    public function actionAdmin(){
        $model = new FreshQuestResult('search');
        $model->unsetAttributes();
        if (isset($_GET['FreshQuestResult'])) {
            $model->attributes = $_GET['FreshQuestResult'];}
        $this->render('admin',array(
            'model' => $model,

        ));
    }
    /**
     * 查看问卷详情
     */
    public function actionViewQuest($id){
        $model = $this->loadModel($id);
        $this->showBack = true;
        if(isset($model)){
            $quest = unserialize($model->data);

            $data = array();
            foreach($quest as $k=>$v){
                if(is_array($v)){
                    $str ="";
                    foreach($v as $key =>$val){
                        $str .= $val."、";
                    }
                    $str = mb_substr($str, 0, -1, 'utf-8');
                    $data[$k] = $str;


                }else{
                    $data[$k] = $v;
                }
            }
            $list = array('name'=>$model->name,'mobile'=>$model->mobile,'type'=>$model->type,'quest'=>$data);
        }
        $this->render('viewQuest',array(
            'list'=>$list
        ));
    }


    /**
     * 导出问卷的结果
     */
    public function actionExportExcel() {
        set_time_limit(0);
        ini_set('memory_limit','7000M');

        $model = new FreshQuestResult('search');
        if(isset($_GET['FreshQuestResult'])){
            $model->attributes = $this->getParam('FreshQuestResult');
        }
        if($_GET['FreshQuestResult']['type'] == ''){
            $this->setFlash('error', Yii::t('FreshMachine', '请选择要导出的问卷类型'));

            $this->redirect(array('admin'));
        }
        $data = $model->resultSearch();
//        var_dump($data);exit;
        if (isset($data)) {
            //引入phpExcel
            require Yii::getPathOfAlias('comext') . '/PHPExcel/PHPExcel/Shared/String.php';
            require Yii::getPathOfAlias('comext') . '/PHPExcel/PHPExcel.php';
            Yii::registerAutoloader(array('PHPExcel_Autoloader', 'Register'), true);
            $objPHPExcel = new PHPExcel();
            if($_GET['FreshQuestResult']['type'] == FreshQuestResult::TYPE_ZHAOSHANG){
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '序号')
                    ->setCellValue('B1', '申请人')
                    ->setCellValue('C1', '填表时间')
                    ->setCellValue('D1', '手机号码')
                    ->setCellValue('E1', '申请类型')
                    ->setCellValue('F1', '地址')
                    ->setCellValue('G1', '所提供的商品');

                $i = 2;
                foreach ($data as $k => $v) {
                    $v['create_time'] = isset($v['create_time'])?date("Y-m-d H:i:s", (int)$v['create_time']):'';
                    $v['type'] = isset($v['type'])?FreshQuestResult::getType($v['type']):'';
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $k + 1)
                        ->setCellValue('B' . $i, isset($v['name'])?$v['name']:'')
                        ->setCellValue('C' . $i, isset($v['create_time'])?$v['create_time']:'')
                        ->setCellValue('D' . $i, isset($v['mobile'])?$v['mobile']:'')
                        ->setCellValue('E' . $i,isset($v['type'])?$v['type']:'')
                        ->setCellValue('F' . $i, isset($v['您所在城市'])?$v['您所在城市']:'')
                        ->setCellValue('G' . $i, isset($v['您所提供的商品（多选）'])?$v['您所提供的商品（多选）']:'');
                    $i++;
                }
            }else{

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '序号')
                    ->setCellValue('B1', '申请人')
                    ->setCellValue('C1', '填表时间')
                    ->setCellValue('D1', '手机号码')
                    ->setCellValue('E1', '申请类型')
                    ->setCellValue('F1', '地址')
                    ->setCellValue('G1', '希望安装地点')
                    ->setCellValue('H1', '希望购买的商品')
                    ->setCellValue('I1', '增值服务');

                $i = 2;
                foreach ($data as $k => $v) {
                    $v['create_time'] = isset($v['create_time'])?date("Y-m-d H:i:s", (int)$v['create_time']):'';
                    $v['type'] = isset($v['type'])?FreshQuestResult::getType($v['type']):'';
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $k + 1)
                        ->setCellValue('B' . $i, isset($v['name'])?$v['name']:'')
                        ->setCellValue('C' . $i, isset($v['create_time'])?$v['create_time']:'')
                        ->setCellValue('D' . $i, isset($v['mobile'])?$v['mobile']:'')
                        ->setCellValue('E' . $i, isset($v['type'])?$v['type']:'')
                        ->setCellValue('F' . $i, isset($v['您所在城市'])?$v['您所在城市']:'')
                        ->setCellValue('G' . $i, isset($v['您希望盖鲜生安装的具体地址'])?$v['您希望盖鲜生安装的具体地址']:'')
                        ->setCellValue('H' . $i, isset($v['您希望在盖鲜生智能机上方便、快捷的买到哪些食品？（多选）'])?$v['您希望在盖鲜生智能机上方便、快捷的买到哪些食品？（多选）']:'')
                        ->setCellValue('I' . $i, isset($v['您还希望盖鲜生能提供哪些增值服务？（多选）'])?$v['您还希望盖鲜生能提供哪些增值服务？（多选）']:'');
                    $i++;
                }
            }

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            $name = "导出调查数据";
            $name = iconv('UTF-8', 'GB2312', $name);
            header('Pragma: public');
            header('Content-type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="'.$name.'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
            @SystemLog::record(Yii::app()->user->name . "导出调查数据数据成功");
            unset($data, $objPHPExcel, $objWriter);
        }
    }
}