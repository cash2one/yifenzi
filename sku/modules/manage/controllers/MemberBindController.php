<?php

class MemberBindController extends MController
{
	
	public function filters() {
		return array(
				'rights',
		);
	}
	
	
	public function allowedActions()
	{
		return 'checkGW,bindRecord';
	}
	
	public function actionIndex()
	{
		$model = new MemberBind();
		$this->render('index',array(
				'model'=>$model,
		));
	}
	
	/*
	 * 绑定详情
	 */
	public function actionDetail($id,$type,$create_time,$BindNumber,$BindGW){
		$model = new MemberBindDetail();
		$this->render('bindDetail',array(
				'model'=>$model,
				'id'=>$id,
				'type'=>$type,
				'create_time'=>$create_time,
				'BindNumber'=>$BindNumber,//绑定的用户数
				'BindGW'=>$BindGW, //被绑定的GW号（推荐人）
		));
	}
	
	/**
	 * ajax 查看绑定的GW号
	 */
	public function actionCheckBindGW(){
		try {
			$Info = array(); //返回信息
			$bind_id = $_POST["bind_id"];
			$gai_fun_member_id = $_POST["gai_fun_member_id"];
			$sku_number = $_POST["sku_number"];
		//	var_dump(1111);die();
			$sql = "SELECT m.gai_number FROM ".MemberBindDetail::model()->tableName()." as b
left JOIN ".Member::model()->tableName()." as m ON m.id = b.bind_member_id WHERE b.bind_id = '".$bind_id."' and b.gai_fun_member_id = '{$gai_fun_member_id}'";
			$reuslt = Yii::app()->db->createCommand($sql)->queryColumn();
			$Info["sku_number"] = $sku_number;
			$info["BindNumber"] = $reuslt;
			exit (json_encode(array("sku_number"=>$Info["sku_number"],"BindNumber"=>$info["BindNumber"])));
		} catch (Exception $e) {
			exit(json_encode($e->getMessage()));
		}
	}

	
	/**
	 * 绑定动作
	 */
	public function actionCreateBind(){
		$model = new MemberBind();
		$this->render('createBind',array(
				'model'=>$model,
		));
	}
	
	/**
	 * 检查GW号是否合法
	 * 
	 * 此方法不适用于未登陆过的用户
	 * 
	 */
	public function actionCheckGW(){
		try {
			$BindGW = $_POST["BindGW"];
			$sql = "SELECT count(a.id) FROM ".Member::model()->tableName()."  as a LEFT JOIN gaiwang.gw_member
			as g on a.gai_member_id = g.id WHERE (a.sku_number = '".$BindGW."' or a.gai_number = '".$BindGW."') and (a.logins != 0 or g.logins != 0)";
			$reuslt = Yii::app()->db->createCommand($sql)->queryScalar();
			exit (json_encode(array("result"=>$reuslt)));
		} catch (Exception $e) {
			exit(json_encode($e->getMessage()));
		}
	}
	
	public function actionBindRecord(){
		try {
			$BindGWnumber = $_POST["BindGW"];
			$BindNumber = $_POST["BindNumber"];
			$result = MemberBind::Bind($BindGWnumber,$BindNumber,MemberBind::BIND_TYPE_MANUA);
 			exit (json_encode(array("result"=>$result)));
		} catch (Exception $e) {
			exit(json_encode(array("result"=>false,"message"=>$e->getMessage())));
		}
	}
}