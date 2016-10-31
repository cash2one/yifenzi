<?php
/**
 * 盖付通地区接口控制器
 * 
 * @author leo8705
 *
 */

class CRegionController extends CAPIController {
    
/**
 * 获取下级地区
 */
  public function actionRegionChild(){
      $id = $this->getParam('id');
      $list = Region::findChildregion($id);
      if(empty($list)){
          $this->_error(Yii::t('apiModule.order','不存在的地区'));
      }
      $data = array();
      foreach($list as $k=>$v){
          $data[$k]['id'] = $v['id'];
          $data[$k]['parent_id'] = $v['parent_id'];
          $data[$k]['name'] = $v['name'];
          $data[$k]['lng'] = $v['lng'];
          $data[$k]['lat'] = $v['lat'];
      }
      $this->_success($data,'selectAdd');
  }
 
     /**
    * 根据id查处一级地区
    */
   public function actionRegionTop(){
       $list = Region::getTopregion();
       if(empty($list)){
           $this->_error(Yii::t('apiModule.order','不存在的地区'));
       }
         $data = array();
      foreach($list as $k=>$v){
          $data[$k]['id'] = $v['id'];
          $data[$k]['parent_id'] = $v['parent_id'];
          $data[$k]['name'] = $v['name'];
          $data[$k]['lng'] = $v['lng'];
          $data[$k]['lat'] = $v['lat'];
      }
       $this->_success($data,'selectTopAdd');
   }
   
   
   /**
    * 根据名称查询地区
    * 
    * 绑定后台地区id
    * 
    */
   public function actionSetRegion(){
   			$name = $this->getParam('name');			//城市名

   			$id = Yii::app()->db->createCommand()
   			->select('id')
   			->from(Region::model()->tableName())
   			->where('depth=2 AND name LIKE :name',array(':name'=>'%'.$name.'%'))
   			->order('id')
   			->limit(1)
   			->queryColumn();
   			
   			if (!empty($id[0]) && $this->token) {
   				Yii::app()->db->createCommand( 'UPDATE '.ClientToken::model()->tableName().' SET city_id= '.$id[0].' WHERE token= '.$this->token );
   			}
   			
   			$this->_success($id[0],'setRegion');
   }
   
}