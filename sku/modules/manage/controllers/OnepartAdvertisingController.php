<?php
/**
 * 一份子广告管理控制器.
 * User: deng
 * Date: 2016/4/6
 * Time: 16:59
 */
class OnepartAdvertisingController extends MController
{
    public function actionAdmin()
    {
        $model = new Advertising("search");
        $model->unsetAttributes();
        if (isset($_GET['Partners']))
            $model->attributes = $_GET['Partners'];
        $this->render('admin', array(
            'model' => $model,
        ));

    }
    /**
     * 广告添加
     */
    public function actionAdds(){
        $model = new Advertising();
        if(isset($_POST['Advertising'])) {
            //var_dump($_POST['Advertising']);die;
            foreach($_POST['Advertising'] as $k=>$v)
            {
                $model->$k=$v;
            }
            $model->addtime = time();
            if ($model->save()) {
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
            } else {
                Yii::app()->user->setFlash('false','删除失败！');
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
            }
        }
        $this->render("adds",array('model'=>$model));
    }
    /**
     *编辑广告
     **/
    public function actionUpdates($id){
        $model =Advertising::model();
        $updatedata = $model->findByPk($id);
        //var_dump($updatedata);die;
        if(isset($_POST['Advertising']))
        {
//            $updatedata->attributes = $_POST['Advertising'];
            foreach($_POST['Advertising'] as $k=>$v)
            {
                $updatedata->$k=$v;
            }

            if($updatedata->save())
            {
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
            }else{
                Yii::app()->user->setFlash('false','修改失败！');
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('updates'));
            }
        }
        $this->render('updates',array('model'=>$updatedata));
    }


    /**
     * 删除广告
     **/
    public function actionDelete($id){
        $model =Advertising::model();
        $del = $model->findByPk($id);
        if($del->delete()){
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }else{
            Yii::app()->user->setFlash('false','删除失败！');
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }
    }
}