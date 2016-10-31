<?php 
/**
   * 盖网一份子项目及商品后台管理
   * ==============================================
   * Derek写于2016年3月25日 
   * ------------------------------------------------------------------------------------
   * 公司源码文件，未经授权不许任何使用和传播。
   * ==============================================
   * @date: 2016年3月25日
   * @version: Onepart 1.0
   * @return: Obj
   **/
class OnepartAnnouncedController extends MController
{
    public function filters() {
        return array(
            'rights',
        );
    }  
    
    /**
     * 最新揭晓列表
     */
    /*public function actionAdmin()
    {     
	    $time = time();
        $model = new YfzOrderGoodsNper;
        $criteria = new CDbCriteria();
		$criteria->select = 't.goods_id,t.current_nper,FROM_UNIXTIME(t.sumlotterytime,"%Y-%m-%d %H:%i:%s") as sumlotterytime,
		                    t.status,g.goods_name,g.shop_price,g.single_price,g.max_nper,g.announced_time,g.sort_order';
		$criteria->join = 'left join {{yfzgoods}} g on g.goods_id = t.goods_id';
		$criteria->addCondition('sumlotterytime  > :time');    
        $criteria->params[':time'] = $time;
		$criteria->addCondition('t.status  = :status');    
        $criteria->params[':status'] = YfzOrderGoodsNper::STATUS_ING;
		$count = $model->count($criteria);
        $pages = new CPagination($count);
		$pages->pageSize = 10;
        $pages->applyLimit($criteria);
        $data = $model->findAll($criteria);
        $this->render('admin',array('model'=>$model,'data'=>$data,'pages'=>$pages));
    }*/
	
	public function actionAdmin()
    {     
        $model = new YfzGoods('getAnnouncedNew');
        $this->render('admin',array('model'=>$model));
    }
    
    
    
    /**
     * 产品排序 
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
        return $model= YfzGoods::model()->findByPk((int)$id);
    }
	
	
}