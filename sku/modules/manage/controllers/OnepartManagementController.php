<?php 
/**
   * 盖网一份子项目及栏目后台管理
   * ==============================================
   * Deng写于2016年4月1日
   * ------------------------------------------------------------------------------------
   * 公司源码文件，未经授权不许任何使用和传播。
   * ==============================================
   **/
class OnepartManagementController extends MController{
    public function filters() {
        return array(
            'rights',
        );
    }

    
    /**
       * 一分子后台栏目列表
       **/
    public function actionAdmin(){
        $model = new Column('search');
        $model->unsetAttributes();
        if (isset($_GET['Partners']))
            $model->attributes = $_GET['Partners'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }
    
    /**
       * 添加一个栏目
       * @date: 2016年4月1日
       * @author: deng
       * @version: G-emall child One Parts 1.0.0
       * @return:
       **/
    public function actionAdds(){
        $model = new Column();
//        $columnData = $model->findAll(array(  'select' =>array('column_name')));
        $this->performAjaxValidation($model);
        if(isset($_POST['Column'])) {
            foreach($_POST['Column'] as $k=>$v)
            {
                $model->$k=$v;
            }
            $model->addtime = time();
            $model->altertime = time();
            if ($model->save()) {
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
            } else {
                Yii::app()->user->setFlash('false','添加失败！');
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('adds'));
            }
        }
        $this->render("adds",array('model'=>$model));
    }

//    public function actionInsert(){
//        $model = new Column();
//        $this->performAjaxValidation($model);
//        $this->render("insert", array(
//            "model"=>$model
//        ));
//    }
    #修改栏目
    public function actionUpdates($id){
        $model =Column::model();
        $updatedata = $model->findByPk($id);
        if(isset($_POST['Column']))
        {
            foreach($_POST['Column'] as $k=>$v)
            {
                $updatedata->$k=$v;
            }

            //更新栏目时间
            $updatedata->altertime = time();
            if($updatedata->save())
            {
                Yii::app()->user->setFlash('false','修改失败！');
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
            }else{
                Yii::app()->user->setFlash('false','修改失败！');
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('updates'));
            }
        }
        $this->render('updates',array('model'=>$updatedata));
    }

    
    /**
       * 删除一个栏目前提条件是判断这个栏目下面是否有商品发布
     **/
    public function actionDelete($id){

        $model =Column::model();
        $goodsModel  = YfzGoods::model();
        $columnData = $goodsModel->find("column_id=:_id", array(":_id"=>$id));
        if ( $columnData ){
//            Yii::app()->user->setFlash('del','此栏目下已添加商品不可随意删除,请先删除商品！');
            echo "此栏目下已添加商品不可随意删除,请先删除商品！";exit;

            }else {
                $del = $model->findByPk($id);
                if ($del->delete()) {
//                    Yii::app()->user->setFlash('del', '');
                    echo "删除成功！";exit;
                    //$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
                } else {
//                    Yii::app()->user->setFlash('del', '删除失败！');
                    echo "删除失败！";exit;
                    //$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
                }
        }
    }
	
	
	/**
     * 栏目排序 
     */
    public function actionSort()
    {
        if($this->isAjax()){
            $sort = Yii::app()->request->getParam('sort_order');
            if(is_array($sort)){
                $fail = 0;
                foreach ($sort as $s){
                    $model = $this->loadModel($s['id']);
                    if(!$model){ $fail++; break;}
                    $model->sort_order = (int)$s['sort'];
                    if(!$model->save(false)){ $fail++; break; }
                }
                exit(CJSON::encode(array('result'=>'success','fail'=>$fail)));
            }
        }
        throw new CHttpException(404,'找不到该方法');
    }
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
    public function loadModel($id)
    {
        return $model = Column::model()->findByPk((int)$id);
    }
}