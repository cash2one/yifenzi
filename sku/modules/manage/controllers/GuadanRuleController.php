<?php

/**
 * 积分挂单规则、政策 操作
 * @author zhenjun_xu <412530435@qq.com>
 * Date: 2016/1/14 0014
 * Time: 16:06
 */
class GuadanRuleController extends MController
{

    public function filters()
    {
        return array(
            'rights',
        );
    }

    /**
     * 添加政策
     * @param int $collect_id 挂单提取id
     * @param int $type 政策类型
     * @param int $amount_bind 绑定积分
     * @param int $amount_unbind 非绑定积分
     *
     */
    public function actionAdd($collect_id, $type, $amount_bind, $amount_unbind)
    {
    	
    	$collect = GuadanCollect::model()->findByPk($collect_id);
    	if (empty($collect)) {
    		exit('政策不存在');
    	}
    	
        $model = new GuadanRule('create');
        $model->collect_id = $collect_id;
        $model->type = $type;
        $model->amount_bind = $amount_bind;
        $model->amount_unbind = $amount_unbind;
        $this->performAjaxValidation($model);
        if (isset($_POST['GuadanRule'])) {
            $model->attributes = $_POST['GuadanRule'];
            $model->status = $collect['status'];
            if ($model->save()) {
                echo '<script>var success = true; alert("添加成功");</script>';
            } else {
                $this->setFlash('error', '添加失败');
            }
        }

        $this->render('form', array('model' => $model));
    }

    /**
     * 删除政策
     * @param $id
     * @throws CDbException
     * @throws CHttpException
     */
    public function actionDel($id){
        /** @var GuadanRule $model */
        $model = $this->loadModel($id);
        if($model->delete()){
            echo json_encode(array('msg'=>'删除成功！','flag'=>true));
        }else{
            echo json_encode(array('msg'=>'删除失败！','flag'=>false));
        }
    }

    /**
     * 修改政策
     * @param $id
     * @throws CHttpException
     */
    public function actionEdit($id){
        /** @var GuadanRule $model */
        $model = $this->loadModel($id);
        $this->performAjaxValidation($model);
        if (isset($_POST['GuadanRule'])) {
            $model->attributes = $_POST['GuadanRule'];
            if ($model->save()) {
                echo '<script>var success = true; alert("修改成功");</script>';
            } else {
                $this->setFlash('error', '修改失败');
            }
        }
        $this->render('form', array('model' => $model));
    }
}