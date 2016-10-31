<?php
/**
 * 盖付通地区接口控制器
 * 
 * @author leo8705
 *
 */

class PRegionController extends POpenAPIController {
    
/**
 * 获取下级地区
 */
  public function actionRegionChild(){
      try{
          $this->params = array('token','id');
          $requiredFields = array('token','id');
          $decryptFields = array('token','id');
          $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
          $id = $post['id'];        //地区id
          $list = Region::findChildregion($id);
          if(empty($list)){
              $this->_error(Yii::t('order','不存在的地区'));
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
      }catch (Exception $e){
          $this->_error($e->getMessage());
      };

  }
 
     /**
    * 根据id查处一级地区
    */
   public function actionRegionTop(){
       try{
           $this->params = array('token');
           $requiredFields = array('token');
           $decryptFields = array('token');
           $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
           $list = Region::getTopregion();
           if(empty($list)){
               $this->_error(Yii::t('order','不存在的地区'));
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
       }catch (Exception $e){
           $this->_error($e->getMessage());
       };

   }
   
   
   /**
    * 根据名称查询地区
    * 
    * 绑定后台地区id
    * 
    */
   public function actionSetRegion(){
       try{
           $this->params = array('token','name');
           $requiredFields = array('token','name');
           $decryptFields = array('token','name');
           $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
           $name = $post['name'];        //城市名
           $id = Yii::app()->db->createCommand()
               ->select('id')
               ->from(Region::model()->tableName())
               ->where('depth=2 AND name LIKE :name',array(':name'=>'%'.$name.'%'))
               ->order('id')
               ->limit(1)
               ->queryColumn();

           if (!empty($id[0]) && $this->token) {
               Yii::app()->db->createCommand( 'UPDATE '.OpenClientToken::model()->tableName().' SET city_id= '.$id[0].' WHERE token= '.$this->token );
           }

           $this->_success($id[0],'setRegion');
       }catch (Exception $e){
           $this->_error($e->getMessage());
       };

   }
   
}