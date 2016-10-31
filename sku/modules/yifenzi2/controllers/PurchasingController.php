<?php
/**
 * 首页->限购专区
 * ==============================================
 * 编码时间:2016年5月11日
 * ------------------------------------------------------------------------------------
 * 公司源码文件，未经授权不许任何使用和传播。
 * ==============================================
 * @author: Derek
 * @version: G-emall child One Parts 1.0.0
 **/
class PurchasingController extends YfzController
{
    public function actionAdmin(){
        $criteria = new CDbCriteria;
        $criteria->alias  = 'g';
        $criteria->select = "g.goods_name,g.goods_id,g.limit_number,g.current_nper,g.goods_number,g.sort_order,g.shop_price,g.single_price,gi.goods_thumb";
        $criteria->addCondition('g.is_on_sale=:is_on_sale');
        $criteria->addCondition('g.is_closed=:is_closed');
        $criteria->params = array(':is_on_sale'=>YfzGoods::IS_SALES_TRUE,':is_closed'=>YfzGoods::IS_CLOSED_FALSE);
        $criteria->join = "LEFT JOIN {{goods_image}} as gi ON g.goods_id=gi.goods_id";
        $criteria->condition = 'g.limit_number > 0';
        $data = YfzGoods::model()->findAll($criteria);

        print_r($data);
    }

    public function actionList(){
        $pageLimit = CPagination::DEFAULT_PAGE_SIZE;
        $model = new YfzGoods;
        $column = Yii::app()->request->getParam('column_id', 0);
        $model->column_id = $column;
        $this->pageTitle = '限购专区';
        $lists = $model->searchAnnoucned($pageLimit = CPagination::DEFAULT_PAGE_SIZE, true);
        if (empty($lists) && $this->isAjax())
            exit(CJSON::encode(array('result' => false)));
        $list = array();
        foreach ($lists as $k => $d) { //重组数组
            $list[$k] = array_filter($d->attributes);
            $list[$k]['goods_thumb'] = $d->goods_thumb;
            $list[$k]['salesTotal'] = YfzGoods::getCurrentSales($d->goods_id, $d->current_nper);
        }

        if ($this->isAjax()) {
            exit(CJSON::encode(array('result' => true, 'data' => $list)));
            Yii::app()->end();
        }
        $this->render('list', array('model' => $model, 'limit' => $pageLimit, 'lists' => $list));
    }
}