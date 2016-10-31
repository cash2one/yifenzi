<?php 
/**
   * 一份子脚本
   * ==============================================
   * 编码时间:2016年4月25日 
   * ------------------------------------------------------------------------------------
   * 公司源码文件，未经授权不许任何使用和传播。
   * ==============================================
   * @date: 2016年4月25日
   * @author: Derek
   * @version: G-emall child One Parts 1.0.0
   **/
class YfzscriptCommand extends CConsoleCommand {
    public $status = 2; //揭晓状态
	public $winningSms = '【一份子】尊敬的{0}用户，恭喜您中奖了，中奖号{1}。';
    public $len = 3000;

    /**
      * 队列处理临时订单
      * ==============================================
      * @return: boolean
      **/
    public function actionOrderautomatic(){
        $connection = Yii::app()->gwpart;
        
        // 根据用户进入队列表中的时间先后顺序读取一条信息来进行goods表的商品库存判断
        $queueData = QueueOrder::model()->find(array(
            "order" => "add_time desc",
            "limit" => 1
        ));
        
        if (! $queueData)
            return false;
        
        $bool = true; // 验证条件
                      
        // 事务开启
        $transaction = $connection->beginTransaction();
        
        // 商品表相关数据
        $sql = "select goods_number,current_nper from gw_yifenzi_yfzgoods where goods_id = {$queueData->goods_id} for update";
        $goodsRow = $connection->createCommand($sql)->queryRow();
        
        // 判断用户购物数量是否大于库存，如果大于则不做处理
        if ($goodsRow['goods_number'] < $queueData->num)
            $bool = false;
            
            // 验证下单确定购买期
        if ($goodsRow['current_nper'] > $queueData->current_nper_ing)
            $bool = false;
            
            // 做一个删除数据处理,让用户能下订单
        if ($bool === true) {
            $delRet = $connection->createCommand()->delete("gw_yifenzi_queue_order", "id=:id", array(
                "id" => $queueData->id
            ));
            
            // 判断是否删除成功,这是一个绝对因素；如果没有删除成功前台是无法下订单的
            if (! $delRet)
                $bool = false;
        }
        
        // 程序走到这里就可以知道这个事务是否成功
        if ($bool === true) {
            $transaction->commit(); // 提交事务
        } else {
            $transaction->rollBack(); // 事务回滚
        }
    }
    
    /**
     * 处理一份子所有期满商品的揭晓工作
     * 工作原理
     * time() 比较 sumlotterytime 如果大那么此商品为揭晓状态
     */
    public function actionOrderannounced(){
        $sql = "select id,sumlotterytime,status from ".YIFENZI.'.gw_yifenzi_order_goods_nper where status < 2 order by sumlotterytime';
        $orderNperData = Yii::app()->db->createCommand($sql)->queryAll();
        
        if (!$orderNperData) return false;
        
        $i = 0;
        foreach ($orderNperData as $k=>$v){
            $i++;
            if ($i > 1) 
                break;
            
            if (time() > ($v['sumlotterytime']*1) ){
                $sql = "update ".YIFENZI.'.gw.yifenzi.order_goods_nper set status = '.$this->status.' where id='.$v['id'];
                $nperModel = YfzOrderGoodsNpers::model()->find(array("condition"=>"id={$v['id']}"));
                $nperModel->status = $this->status;
                $nperModel->save();
            }
        }
        exit('success');
    }

    /**
     * 此脚本是处理订单队列和已经生成订单的末付款订单
     * 脚本执行都是一天执行一次，清理无作用订单数据
     */
    public function actionOutDateOrder(){
        try{
            //先进行处理队列订单
            //昨天时间
            $yesterday = strtotime("-1 day");
            $yesterday = date('Y-m-d',$yesterday);
            $yesterday = strtotime($yesterday);

            //今天时间
            $today = time();
            $today = date('Y-m-d',$today);
            $today = strtotime($today);

            $sql = "select * from gw_yifenzi_queue_order where add_time < $today and add_time > $yesterday";
            $queueData = Yii::app()->gwpart->createCommand($sql)->queryAll();

            //是否有数据
            if ( $queueData )
            {
                foreach ( $queueData as $k=>$v )
                {
                    Yii::app()->gwpart->createCommand()->delete("gw_yifenzi_queue_order","id=:id",array(":id"=>$v['id']));
                }
            }

            //处理订单末支付订单
            $sql = "select * from gw_yifenzi_order where order_status = 0";

            $orderData = Yii::app()->gwpart->createCommand($sql)->queryAll();

            foreach( $orderData as $key=>$val)
            {
                Yii::app()->gwpart->createCommand()->delete("gw_yifenzi_order","order_id=:id",array(":id"=>$val['order_id']));
            }


        }catch(Exception $e){

        }
    }
	
	/**
	* 此脚本处理用户中奖信息
	**/
	public function actionWinningSmsSend(){
		//$now = time();
		$sql = "SELECT * FROM {{order_goods_nper}} WHERE mobile_is_send=0 AND status=2";
		$result = Yii::app()->gwpart->createCommand($sql)->queryAll();
		if ( !empty($result) ) {
			$content =  Tool::getConfig('yfzsm', 'winner');
			$content = $content == '' ? $this->winningSms : $content;
			foreach ($result as $v){
				$mobileSend = $v['mobile_is_send'];
				$content2 = str_replace(array('{0}','{1}'), array($v['gai_number'],$v['winning_code']), $content);
                if ($v['mobile_is_send']==0 && $v['status']==2 && time() > $v['sumlotterytime']) {
					$arr = array( $v['gai_number'], $v['winning_code'] );
                    $apiMember = new ApiMember();
			        $apiMember->sendSms($v['mobile'], $content2, ApiMember::SMS_TYPE_ONLINE_ORDER,0, ApiMember::SKU_SEND_SMS);
					$mobileSend = 1;
					$sql = "UPDATE {{order_goods_nper}} SET mobile_is_send=$mobileSend WHERE id=:id";
					Yii::app()->gwpart->createCommand($sql)->execute(array(':id'=>$v['id']));
				}else{
					$mobileSend = 1;
				}
			}
		}
		exit('success');
	}

    /*处理未支付成功的订单库存还原*/
    public function actionRegain(){
        $transaction = Yii::app()->gwpart->beginTransaction();
        try{
            $sql = "select * from {{order}} where order_status=0 and addtime<=".(time()-60);
            $result = Yii::app()->gwpart->createCommand($sql)->queryAll();

            if ( !empty($result) ) {
                foreach( $result as $k=>$v ){
                    $sql = "select * from {{order_goods}} where order_id={$v['order_id']} and addtime<=".(time()-60);
                    $orderGoodsData = Yii::app()->gwpart->createCommand($sql)->queryAll();

                    if(!$orderGoodsData){
                        $sql = "update {{order}} set order_status=3 where order_id=".$v['order_id'];
                        if (!Yii::app()->gwpart->createCommand($sql)->execute())
                            throw new Exception('订单提交失败，请联系管理人员进行处理');
                        continue;
                    }


                    foreach($orderGoodsData as $key=>$val){
                        //得到一个商品数据
                        $sql = "select * from {{yfzgoods}} where goods_id={$val['goods_id']} FOR UPDATE ";
                        $goodsData = Yii::app()->gwpart->createCommand($sql)->queryRow();
                        if ( $val['goods_id'] == $goodsData['goods_id'] && $val['current_nper'] == $goodsData['current_nper'] ){
                            $tmpCount = self::getTmpCount($val['goods_id'], $val['current_nper']);
                            $count = (($val['goods_price'] * 1) / ($val['single_price'] * 1)) * 1;

                            if ($val['goods_number'] < 1){
                                continue;
                            }

                            if ($tmpCount < $count){
                                if ( ($goodsData['goods_number'] + $val['goods_number']) <= $count){
                                    $sql = "update {{yfzgoods}} set goods_number=goods_number+".$val['goods_number'].' where goods_id='.$val['goods_id'].' and current_nper='.$val['current_nper'];
                                    if (!Yii::app()->gwpart->createCommand($sql)->execute())
                                        throw new Exception('订单提交失败，请联系管理人员进行处理');
                                }
//                                $sql = "update ".YIFENZI.'.gw_yifenzi_yfzgoods set goods_number='.$count-$tmpCount.' where goods_id='.$val['goods_id'].' and current_nper='.$val['current_nper'];
                            }
                        }
                    }

                    $sql = "update {{order}} set order_status=3 where order_id=".$v['order_id'];
                    if (!Yii::app()->gwpart->createCommand($sql)->execute())
                        throw new Exception('订单提交失败，请联系管理人员进行处理');

                }
                $transaction->commit();
            }

        }catch(Exception $e){
            $transaction->rollBack(); // 事务回滚
        }
    }

    /**
     * 得到一个期数的商品被购买的次数和
     * @param $goods_id
     * @param $current_nper
     * @return int
     */
    public static function getTmpCount($goods_id, $current_nper){
        //在订单支付之前有库存扣的操作，为了没有支付的订单购买数量还原库存
        $sql = "select og.goods_id,og.goods_name,og.goods_number,o.order_id,o.order_status,og.current_nper from {{order_goods}} as og left join {{order}} as o on og.order_id=o.order_id  where og.goods_id=".$goods_id." and og.current_nper=".$current_nper;

        $tmpOrderGoods = Yii::app()->gwpart->createCommand($sql)->queryAll();

        $tmp_count = 0; //临时商品购买总和
        foreach($tmpOrderGoods as $mk=>$mv){
            if ($mv['order_status'] == 1){
                $tmp_count += $mv['goods_number'];
            }
        }
        return $tmp_count;
    }

    /*此脚本处理中奖用户的订单收货状态*/
    public function actionWinningOrderSign(){
        //$now = time();
        $twoMiunteBefore = time()-120;
        $sql = "SELECT order_code,state,update_time FROM {{order_express}} WHERE state=3 AND update_time = {$twoMiunteBefore}";
        $result = Yii::app()->gw->createCommand($sql)->queryAll();
        if(!empty($result)){
            foreach($result as $val){
                $state = $val['state'];
                $updateTime = $val['update_time'];
                if($state ==3 && $updateTime==$twoMiunteBefore){
                    $is_delivery =1;
                    $sql2 = "UPDATE {{order}} SET is_delivery={$is_delivery} WHERE order_sn =:orderSn";
                    Yii::app()->gwpart->createCommand($sql2)->execute(array(':orderSn'=>$val['order_code']));
                }
            }
        }
        exit('success');
    }


    /**
     * 给添加商品进行分配中奖码
     * 此任务是实时进行，把状态为下架的商品进行分配中奖码
     */
    public function actionAllocation(){
        $sql = "select * from {{yfzgoods}} where is_on_sale=0";
        $goodsData = Yii::app()->gwpart->createCommand($sql)->queryAll();
        if ( !$goodsData ) return false;

        $model = new YfzCode();

        foreach ( $goodsData as $k=>$v){
            //商品价格，单品人次
            $shop_price = $v['shop_price'];
            $single_price = $v['single_price'];
            $goods_number = $v['goods_number'];

            //重新计算，此商品的需要多少人次才开奖
            $temp_number = ( $shop_price * 1 ) / ( $single_price * 1 );
            $temp_num = $temp_number;
            if ( !is_int($temp_number) && $goods_number != $temp_number ){
                return false;
            }

            //如果一个商品的购买份额过大的话，我们对这一些中奖进行分开保存
            $save_count = ceil( $goods_number / $this->len );

            //如果这个商品本身就少于10000人次那么我们就不进行多次循环
            $nums = $this->len;
            $i = 1;
            $data = array();
            while($i <= $nums) array_push($data, $i++);

            if ( $save_count == 1 ){
                return false;
            }

            //如是份额数大于1的情况下。
            for( $s=1;$s<$save_count;$s++ ){
                $tmpData = array();
                $tmpCount = 0;
                foreach ($data as $key=>$val  ){
                    $tmpCount++;
                    array_push($tmpData, (($s * $this->len) + $val + 10000001) - $this->len);
                    shuffle($tmpData);
                }

                $tmpStr = implode(',',$tmpData);

                $sqlWhere = "insert into {{code}} (goods_id,temp_code,codes,s_cid,code_len) VALUES (".$v['goods_id'].",'$tmpStr','$tmpStr',$s,$tmpCount)";
//                Yii::app()->gwpart->createCommand($sqlWhere)->execute();
            }
            print_r('adsfasdfas');exit;
            //for最后一条只能直接生成中奖码
//            $temp_number = $temp_number - (($save_count-1) * $this->len);
//            print_r($temp_number);exit;
//
//            $maxNumber = array();
//            for($s=1;$s<=$temp_number;$s++){
//                $maxNumber[$s] = 10000001 + $temp_num;
//                $temp_num--;
//            }
//            shuffle($maxNumber);
//            $tmpStr = implode(',',$maxNumber);
//
//            $sqlWhere = "insert into {{code}} (goods_id,temp_code,codes,s_cid,code_len) VALUES (".$v['goods_id'].",'$tmpStr','$tmpStr',$save_count,$temp_number)";
            print_r($sqlWhere);
//            Yii::app()->gwpart->createCommand($sqlWhere)->execute();
            exit;
//            print_r(count($tmpData));exit;
        }

    }
    public function actionTest(){
        file_put_contents("d:/txtyii.php",time().'/',FILE_APPEND);
    }
}