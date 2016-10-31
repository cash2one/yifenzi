<?php
/**
 * 商家专属商品分类管理
 *
 * 操作(增删查改)
 * @author leo8705
 */
class GoodsCategoryController extends PController
{

    public function init()
    {
        $this->pageTitle = Yii::t('partnerModule.goodsCategory', '小微企业联盟');
        $this->curr_menu_name = '/partner/goodsCategory/index';
    }
    
    /**
     * 检查当前商品分类是否属于当前商家
     * @param unknown $model
     */
    private function _checkAccess($model){
    	if ($model->member_id !== $this->curr_act_member_id) {
    		throw new CHttpException(403,Yii::t('partnerModule.goodsCategory', '你没有权限修改别人的数据！'));
    	}
    }

    /*
     * 商家分类列表
     */
    public function actionIndex(){
       $this->pageTitle = Yii::t('partnerModule.goodsCategory','商品分类管理 _ ').$this->pageTitle;
        $model = new GoodsCategory('search');
        $model->unsetAttributes(); // clear any default values
        $model->member_id= $this->curr_act_member_id;

        if (isset($_GET['GoodsCategory']))
            $model->attributes = $this->getQuery('GoodsCategory');

        $lists = $model->search();
        $goods_data = $lists->getData();
        $pager = $lists->pagination;

        $this->render('index', array(
            'model' => $model,
            'goods_data'=>$goods_data,
            'pager'=>$pager,
        ));
    }

    /*
     * 添加商家分类
     */
    public function actionCreate(){
        $this->pageTitle = Yii::t('partnerModule.goodsCategory','添加商品分类 _ ').$this->pageTitle;
        $model = new GoodsCategory;
        $model->scenario = 'create';
        $this->performAjaxValidation($model);
        
        if(isset($_POST['GoodsCategory'])){
            $model->attributes = $this->getPost('GoodsCategory');
            $model->member_id = $this->curr_act_member_id;
            if ($model->save()) {
                $this->setFlash('success', Yii::t('partnerModule.goodsCategory','添加商家分类成功'));
                ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'添加商家分类:'.$model->name);
                $this->redirect(array('index'));
            } else {
                $this->setFlash('error', Yii::t('partnerModule.goodsCategory','添加商家分类失败'));
            }
        }

        $this->render('create',array(
            'model'=>$model
        ));
    }

    /*
     * 编辑商家分类
     */
    public function actionUpdate($id){
        $this->pageTitle = Yii::t('partnerMachine','修改商品分类 _ ').$this->pageTitle;
        $model = $this->loadModel($id);
        $model->scenario = 'update';
        $this->performAjaxValidation($model);
        
        $this->_checkAccess($model);

        if(isset($_POST['GoodsCategory'])){
            $model->attributes = $this->getPost('GoodsCategory');
            if($model->save()){
                $this->setFlash('success',Yii::t('partnerModule.goodsCategory','编辑商家分类成功'));
                ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeUpdate,$model->id,'添加商家分类:'.$model->name);
                $this->redirect(array('index'));
            }else{
                $this->setFlash('error',Yii::t('partnerModule.goodsCategory','编辑商家分类失败'));
                $this->redirect(array('index'));
            }
        }

        $this->render('update',array('model'=>$model));
    }

    /*
     * 删除商家自定义分类
     */
    public function actionDelete($id){

        $model = $this->loadModel($id);

        $this->_checkAccess($model);
        if(empty($model->goods)){
        if($model->delete()){
            $this->setFlash('success',Yii::t('partnerModule.goodsCategory','删除商家分类成功'));
            ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeDel,$model->id,'删除商家分类:'.$model->name);
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
        }else{
            $this->setFlash('error',Yii::t('partnerModule.goodsCategory','该分类下有商品，不可删除！'));
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
        
    }
    

}
