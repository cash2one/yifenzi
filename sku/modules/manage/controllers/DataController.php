<?php
/**
 * 数据处理
 * 
 * 用来作数据处理
 * 
 * 
 */
class DataController extends MController {

    public function filters() {
        return array(
            'rights',
        );
    }
   // 列表
    public function actionIndex() {
        echo 'Hello';
    }
    
    /*
     * 更新盖网id
     * 
     * 只能执行一次
     * 
     */
    public function actionSycnGaiMemberIdAll(){
        set_time_limit(3600);
        
        
        preg_match('#;dbname=(.+)#', Yii::app()->db->connectionString,$db_name_match);
        $db_name = $db_name_match[1];

        preg_match('#;dbname=(.+)#', Yii::app()->gw->connectionString,$gwdb_name_match);
        $gwdb_name = $gwdb_name_match[1];
        
        $table_names = array();
        $member_table_names[] = Address::model()->tableName();
        $member_table_names[] = Order::model()->tableName();
        $gai_number_table_names[] = ClientToken::model()->tableName();
        $gai_number_table_names[] = Partners::model()->tableName();
        $ptable_names[] = FreshMachine::model()->tableName();
        $ptable_names[] = Goods::model()->tableName();
        $ptable_names[] = Supermarkets::model()->tableName();
        $ptable_names[] = VendingMachine::model()->tableName();
//         $ptable_names[] = GoodsCategory::model()->tableName();
        
        //先同步会员表 、商家表
        foreach ($gai_number_table_names as $name){
        	$address_sql = ' UPDATE '.$db_name.'.'.$name .' as a ,'.$db_name.'.'.Member::model()->tableName() .' as m  ,'.$gwdb_name.'.gw_member as gwm
        			SET m.gai_member_id=gwm.id , a.member_id=m.id
        			WHERE a.gai_number=m.gai_number AND m.gai_number = gwm.gai_number
        		' ;
        	 
        	$rs = Yii::app()->db->createCommand($address_sql)->execute();;
        	 
        	echo '更新了'.$name.'表'.$rs.'条记录  <br/>';
        }

        //更新商家有关的表
            foreach ($ptable_names as $name){
        	$address_sql = ' UPDATE '.$db_name.'.'.$name .' as a ,'.$db_name.'.'.Partners::model()->tableName() .' as m 
        			SET  a.member_id=m.member_id
        			WHERE a.partner_id=m.id 
        		' ;
        	
        	$rs = Yii::app()->db->createCommand($address_sql)->execute();;
        	
        	echo '更新了'.$name.'表'.$rs.'条记录  <br/>';
        }
        
        
//         //再同步其他表
//         foreach ($member_table_names as $name){
//         	$address_sql = ' UPDATE '.$db_name.'.'.$name .' as a ,'.$db_name.'.'.Member::model()->tableName() .' as m  ,'.$gwdb_name.'.gw_member as gwm 
//         			SET m.gai_member_id=gwm.id , a.member_id=m.id 
//         			WHERE a.member_id=m.gai_member_id AND m.gai_number = gwm.gai_number
//         		' ;
        	
//         	$rs = Yii::app()->db->createCommand($address_sql)->execute();;
        	
//         	echo '更新了'.$name.'表'.$rs.'条记录  <br/>';
//         }
        
        
        
    }
    
//     //只能执行一次
//     public function actionSycnGaiMemberIdOnce(){
//     	set_time_limit(3600);
    
    
//     	preg_match('#;dbname=(.+)#', Yii::app()->db->connectionString,$db_name_match);
//     	$db_name = $db_name_match[1];
    
//     	preg_match('#;dbname=(.+)#', Yii::app()->gw->connectionString,$gwdb_name_match);
//     	$gwdb_name = $gwdb_name_match[1];
    
//     	$table_names = array();
//     	$member_table_names[] = Address::model()->tableName();
//     	$member_table_names[] = Order::model()->tableName();
    
//     	foreach ($member_table_names as $name){
//     		$address_sql = ' UPDATE '.$db_name.'.'.$name .' as a ,'.$db_name.'.'.Member::model()->tableName() .' as m 
//         			SET a.member_id=m.id
//         			WHERE a.member_id=m.gai_member_id
//         		' ;
    		 
//     		$rs = Yii::app()->db->createCommand($address_sql)->execute();;
    		 
//     		echo '更新了'.$name.'表'.$rs.'条记录  <br/>';
//     	}
    
    
    
//     }
    

    /*
     * 更新会员表盖网id
    */
    public function actionSycnGaiMemberId(){
    	set_time_limit(3600);
    
    	preg_match('#;dbname=(.+)#', Yii::app()->db->connectionString,$db_name_match);
    	$db_name = $db_name_match[1];
    
    	preg_match('#;dbname=(.+)#', Yii::app()->gw->connectionString,$gwdb_name_match);
    	$gwdb_name = $gwdb_name_match[1];
    
    	$address_sql = ' UPDATE '.$db_name.'.'.Member::model()->tableName() .' as m  ,'.$gwdb_name.'.gw_member as gwm SET m.gai_member_id=gwm.id  WHERE
        		m.gai_number = gwm.gai_number
        		' ;
    
    	$rs = Yii::app()->db->createCommand($address_sql)->execute();;
    
    	echo '更新了'.Member::model()->tableName().'表'.$rs.'条记录  <br/>'.$address_sql;
    
    }
    
    /*
     * 同步盖网会员
    */
    public function actionSycnGaiInfo(){
    
    	$gai_member_id = $this->getParam('gid');
    	$member_info = Member::getMemberInfoByGaiId($gai_member_id);
    	
    	if (!empty($member_info)) {
    		var_dump($member_info);
    		echo '更新了 '.$gai_member_id.' 的信息 ';
    	}else {
    		echo  '失败';
    	}
    
    }
    

    
    /*
     * 清空token表
    */
    public function actionClearToken(){
    	set_time_limit(3600);
    
    	Yii::app()->db->createCommand("delete  from {{client_token}}" )->execute();
    	Yii::app()->db->createCommand("delete  from {{partner_token}}" )->execute();
    	
    	Yii::app()->db->createCommand("delete  from {{open_client_token}}" )->execute();
    	Yii::app()->db->createCommand("delete  from {{open_partner_token}}" )->execute();
    
    	Tool::cache('skuClientTokenCache')->flush();
    	Tool::cache('skuApiPartnerTokenCache')->flush();
    	
    	echo 'ok';
    	
    }
    

    public function actionGetGaiMember(){
    	$mid = $this->getParam('mid',0);
    	 
    	$rs = Yii::app()->gw->createCommand( 'SELECT * FROM {{member}} WHERE id='.$mid  )->queryRow();
    	 
    	var_dump($rs);
    	
    	echo 'ok ';
    	 
    }
    
    public function actionGetSkuMember(){
    	$mid = $this->getParam('mid',0);
    
    	$rs = Yii::app()->db->createCommand( 'SELECT * FROM {{member}} WHERE id='.$mid  )->queryRow();
    
    	var_dump($rs);
    	 
    	echo 'ok ';
    
    }
    
    public function actionGetSkuMemberByGaiNumber(){
    	$gai_number = $this->getParam('gai_number',0);
    
    	$rs = Yii::app()->db->createCommand()
    	->from(Member::model()->tableName())
    	->where('gai_number=:gai_number',array('gai_number'=>$gai_number))
    	->queryRow();
    
    	var_dump($rs);
    
    	echo 'ok ';
    
    }
    
    
    public function actionSyncOrderGaiMember(){
		
    	 
    	echo 'ok ';
    
    }
    
    public function actionTest(){
    
//     	$rs = JPushTool::gzgPushMessage('eec5a3338afd5a68ee7c70c99d3a34e5232fa3a8', '自定义');
//     	var_dump($rs);
        $s=bcdiv(1, bcdiv(78, 100, 5), 2);
        var_dump($s);
     	echo 'ads';
    
    }
    
    
}
