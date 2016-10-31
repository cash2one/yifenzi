<?php
/**
 * 绑定管理脚本
 * 每天执行一次
 * @author zhaoxiang.liu
 *
 */
class BindManageCommand extends CConsoleCommand {
	
	public function actionAutoBind(){
		set_time_limit(0);
		$time = date("Y-m-d H:i:s"); //脚本运行创建时间
		$end = strtotime ("-5 minute", strtotime($time));  //获取有效的挂单时间
		$status = Guadan::STATUS_DISABLE; //挂单失效状态 
		$type = Guadan::TYPE_TO_BIND; //挂单类型 待绑定
		
		//符合条件的挂单
		$collectArr = Yii::app()->db->createCommand()
		          ->select("id,bind_size,amount_bind,new_member_count,sale_amount_bind")
		          ->from(GuadanCollect::model()->tableName())
		          ->where("time_start < '{$end}' and time_end > '{$end}' and status != '{$status}'")
		          ->queryAll();
		
	   foreach ($collectArr as $val){
	   	  if($val["bind_size"]<=0) continue;
	   	  $remainder = floor($val["sale_amount_bind"] / $val["bind_size"]);
	   	  if(($remainder <= $val["new_member_count"])) continue;
	   	  $Proresult = floor($val["amount_bind"] / $val["bind_size"]); //此挂单总的绑定人数
	   	  if($Proresult == 0)continue;
	   	  $Membercount = self::GetMemberCount(); //待绑定的新用户
 	   	  if($Membercount == 0)break;
	   	//此挂单可以绑定多少新用户
	   	  $sql = "SELECT c.*,m.sku_number from 
                  (SELECT r.*,g.member_id FROM gw_sku_guadan_relation as r
                  LEFT JOIN gw_sku_guadan as g ON r.guadan_id = g.id) as c 
                  LEFT JOIN  gw_sku_member as m ON m.gai_member_id = c.member_id  WHERE c.collect_id = '{$val["id"]}' and c.type = '{$type}'";

	   	  $BindArr = Yii::app()->db->createCommand($sql)->queryAll();
	   	  foreach ($BindArr as $Bindval){
	   	  	//计算卖出多少积分用于确定绑定人数
	   	  	$sale = $Bindval["amount"] - $Bindval["amount_remain"];
	   	  	$number = ceil($sale / $val["bind_size"]);
	   	  	$count = self::GetCount($Bindval["collect_id"]);
    	  	if($number <= $count) continue;
	   	  	//当新用户足够绑定时
	   	  	if($Membercount > $Proresult || $Membercount == $Proresult ){
	   	  		$number = $number - $count;
	   	  	}else{//当新用户不够绑定时 根据每个挂单提供的积分占得比例分配人数
	   	  		$number = $number - $count;
	   	  		$tempNumber = floor($Membercount * ($sale/$val["sale_amount_bind"]));
	   	  		$number = $number > $tempNumber ? $tempNumber : $number;	
	   	  	}
	   	  	if($number == 0)continue;
	   	  	MemberBind::Bind($Bindval["sku_number"], $number,MemberBind::BIND_TYPE_AUTO,$Bindval["collect_id"]);
	   	  }
	   }
	}
	
	/**
	 * 根据挂单ID=>:collect_id 判断此次挂单的SKUGW号绑定了多少用户
 	 */
	public static function GetCount($collect_id){
		$Arr = Yii::app()->db->createCommand()
							->select("id")
							->from(MemberBind::model()->tableName())
							->where("guandan_collect_id = '{$collect_id}'")
							->querycolumn();
		if(count($Arr) != 0){
			$ArrString = implode(",", $Arr);
			$count =  Yii::app()->db->createCommand()
							->select("count(*)")
							->from(MemberBindDetail::model()->tableName())
							->where("bind_id in ({$ArrString})")
							->queryScalar();
		}else{
			$count = 0;
		}
		return $count;
	}
	
	/**
	 * 获取待绑定新用户数量
	 * @return Count
	 */
	public static function GetMemberCount(){
		$count = Yii::app()->db->createCommand()
				->select("count(id)")
				->from(Member::model()->tableName())
				->where("referrals_id = ''")
				->queryscalar();
		return $count;
	}
	
}