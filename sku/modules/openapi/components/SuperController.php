<?php
/**
 * Created by PhpStorm.
 * User: Gatewang
 * Date: 2015/10/29
 * Time: 17:39
 */
class SuperController extends POpenAPIController{
    protected $curr_super_key = 'curr_super_store_id';
    protected  $super_cache_path = 'OPENAPI_SUPER_CACHE';

    function beforeAction($action){
        parent::beforeAction($action);

        $unSelectActions = array('store/change','store/add');

        if ( in_array($this->id . '/' . $this->action->id, $unSelectActions)) {
            return true;
        }

        return true;
    }
    /**
     * 检查当前门店是否属于当前商家
     * @param unknown $model
     */
    protected function _checkAccess($store){

        if (empty($store->member_id) || $store->member_id != $this->member) {
            throw new CHttpException(403,'你没有权限修改别人的数据！');
        }
    }

    /**
     * 设置当前超市门店
     * @param unknown $id
     */
    protected function _setSuper($id){
        $this->_check($id);
        Tool::cache($this->super_cache_path)->set($this->curr_super_key.$this->member,$id);
        $this->super_id = Tool::cache($this->super_cache_path)->get($this->curr_super_key.$this->member);
    }



    /**
     * 检查操作的超市门店是否属于当前用户
     * Enter description here ...
     */
    protected function _check($super_id){
        if (empty($super_id)) return false;

        $members = Member::getAllMembers($this->member);
        $instr = ' member_id IN (0';
        foreach ($members as $m){
            $instr .= ','.$m->id;
        }

        $instr .= ') ';

        if( !Supermarkets::model()->count(" {$instr} AND id={$super_id}")){
            $this->_error(Yii::t('sellersuper', '没有权限！'));
            exit();
        }

    }

    /**
     * 超市门店页面渲染方法
     * @param unknown $dispalyName
     * @param unknown $pramas
     */
    protected function FRender($dispalyName,$pramas=array()){
        $apend_arr = array('super_id'=>$this->super_id);
        $pramas = array_merge($pramas,$apend_arr);

        $this->render($dispalyName,$pramas);
    }
    /**
     * 检查当前商品是否属于当前商家
     * @param unknown $model
     */
    protected function _checkGoodsAccess($storeGoods){
        if (empty($storeGoods) || $storeGoods->super_id !== $this->super_id) {
            throw new CHttpException(403,Yii::t('storeGoods','你没有权限修改别人的数据！'));
        }
    }




    /**
     * 保存操作记录
     */
    protected  function _saveLog($category_id=0,$type_id=0,$source_id=0,$title=''){
        $log = new ParnetLog();
        $log->category_id = $category_id;
        $log->type_id = $type_id;
        $log->create_time = time();
        $log->source = ucwords(Yii::app()->controller->id).ucwords($this->action->id);
        $log->source_id = $source_id;
        $log->member_id = !empty($this->getUser()->id)?$this->getUser()->id:'';
        $user_name  = Yii::app()->session->get('username');
        $gai_number  = Yii::app()->session->get('gai_number');
        $log->member_name =  !empty($user_name) ? $user_name: (!empty($gai_number)?$gai_number:'');
        $log->ip = Yii::app()->request->userHostAddress;
        $log->is_admin = empty($assistantId) ? ParnetLog::ADMIN_YES : ParnetLog::ADMIN_NO;

        $user_type = Yii::app()->user->getState('assistantId') ? '店小二':'商家用户';


        switch ($log->source){
            case 'StoreChange':
                $log->title =  $user_type.'('.$log->member_name.'):'. Yii::t('sellersuper', '切换超市门店');
                break;

            case 'superUpdate':
                $log->title = $user_type.$log->member_name. Yii::t('sellersuper', '更新超市门店信息');
                break;

            case 'superPwd':$user_type.$log->member_name. Yii::t('sellersuper', '修改超市门店密码');
                break;

            case 'superArtileEdit':
                $log->title = $user_type.$log->member_name. Yii::t('sellersuper', '修改超市门店文章');
                break;

            case 'superArtileAdd':
                $log->title = $user_type.$log->member_name. Yii::t('sellersuper', '添加超市门店文章');
                break;

            case 'superArtileDel':
                $log->title = $user_type.$log->member_name. Yii::t('sellersuper', '删除超市门店文章');
                break;

            case 'superCustomerCreate':
                $log->title = $user_type.$log->member_name. Yii::t('sellersuper', '添加超市门店客服');
                break;

            case 'superCustomerUpdate':
                $log->title = $user_type.$log->member_name. Yii::t('sellersuper', '更新超市门店客服信息');
                break;

            case 'superCustomerDel':
                $log->title = $user_type.$log->member_name. Yii::t('sellersuper', '删除超市门店客服');
                break;

            case 'superUploadUpload':
                $log->title = $user_type.$log->member_name. Yii::t('sellersuper', '上传超市门店图片');
                break;

            case 'superUploadDel':
                $log->title = $user_type.$log->member_name. Yii::t('sellersuper', '删除超市门店图片');
                break;

            case 'superVerifyConsumed':
                $log->title = $user_type.$log->member_name. Yii::t('sellersuper', '盖网通商城订单验证');
                break;

            default:
                $log->title = $title;

        }


        if (!empty($title)) $log->title = $title;


        $log->save();


    }
}