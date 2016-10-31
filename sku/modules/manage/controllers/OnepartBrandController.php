<?php
/**
 * 一份子品牌管理控制器
 *  deng 写于2016年4月5号
 * Date: 2016/4/5
 * Time: 13:47
 */
class OnepartBrandController extends MController
{
    public function filters() {
        return array(
            'rights',
        );
    }
    /**
     *品牌列表
     */
    public function actionAdmin(){
        $model = new Brand("search");
        $model->unsetAttributes();
        if (isset($_GET['Partners']))
            $model->attributes = $_GET['Partners'];
//        print_r($model->attributes);exit;
        $this->render('admin', array(
            'model' => $model,
        ));
    }
    /**
     * 品牌添加
     */
    public function actionAdds(){
        $model = new Brand();
        if(isset($_POST['Brand'])) {
           // var_dump($_POST['Brand']);die;
            foreach($_POST['Brand'] as $k=>$v)
            {
                $model->$k=$v;
            }
            if ($model->save()) {
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
            } else {
                Yii::app()->user->setFlash('false','添加失败！');
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('adds'));
            }
        }
        $this->render("adds",array('model'=>$model));
   }

    /**
     *编辑品牌
     **/
    public function actionUpdates($id){
        $model =Brand::model();
        $updatedata = $model->findByPk($id);
        if(isset($_POST['Brand']))
        {
            foreach($_POST['Brand'] as $k=>$v)
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
     * 删除品牌
     **/
    public function actionDelete($id){

        $model =Brand::model();
        $goodsModel  = YfzGoods::model();
        $columnData = $goodsModel->find("brand_id=:_id", array(":_id"=>$id));
//        $columnData = $goodsModel->find(array("condition"=>"brand_id={$id}"));
//        print_r($columnData);exit;
        if ( $columnData ){
           // Yii::app()->user->setFlash('del','此品牌下已添加商品不可随意删除,请先删除商品！');
           // $this->redirect('admin');
		   echo "此品牌下已添加商品不可随意删除,请先删除商品！";exit;
        }else {
            $del = $model->findByPk($id);
            if ($del->delete()) {
              //  Yii::app()->user->setFlash('del', '删除成功！');
//                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
               // $this->redirect('admin');
			    echo "删除成功！";exit;
            } else {
              //  Yii::app()->user->setFlash('del', '删除失败！');
              //  $this->redirect('admin');
			  echo "删除失败！";exit;
            }
        }
    }


}