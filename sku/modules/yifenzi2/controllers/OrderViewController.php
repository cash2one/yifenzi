<?php

class OrderViewController extends YfzController
{
    public function actionGetOrder()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $idList = Yii::app()->request->getParam("idList") ? Yii::app()->request->getParam("idList") : 0;
            if($idList != 0){
                $goods = explode('_',$idList);
                $number = YfzOrderGoods::getGoodsNumber($goods[0], $goods[1]);
                $luckynumber = YfzOrderGoodsNpers::getResult($goods[0], $goods[1]);
                echo (json_encode(array(
                    "err" => 1,
                    "cur_share" => !empty($number->goods_number)?$number->goods_number:0,
                    'end_share'=> $goods[2],
                    'luckynumber' => !empty($luckynumber)?$luckynumber['winning_code']:0,
                    'sumlotterytime' => !empty($luckynumber)?$luckynumber['lotterytime']:0,
                )));
                exit();
            }

        }
    }
    /**
     * 商品列表
     */
    public function actionGoodsList()
    {
        $this->footerDisplay = false;
        $model = new YfzGoods;
        $criteria = new CDbCriteria();
        $criteria->select = 't.goods_id,t.goods_name,g.goods_thumb,t.shop_price,t.single_price,t.current_nper,t.add_time,t.sort_order,t.column_id';
        $criteria->join = 'LEFT JOIN {{goods_image}} AS g ON g.goods_id = t.goods_id';
        $criteria->addCondition('t.is_closed = :closed');
        $criteria->params[':closed'] = YfzGoods::IS_CLOSED_FALSE;
        $criteria->addCondition('t.is_on_sale = :sale');
        $criteria->addInCondition('t.goods_id', array(7,5,4));
        $criteria->params[':sale'] = YfzGoods::IS_SALES_TRUE;
        $criteria->limit = 3;
        $criteria->order='t.sort_order desc,t.add_time desc';
        $lists = $model->findAll($criteria);
        $this->render('list', array('model' => $model, 'lists' => $lists));
    }
}