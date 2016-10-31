<?php
use JPush\Model\tag;

use JPush\Model as M;
use JPush\JPushClient;
use JPush\Exception\APIConnectionException;
use JPush\Exception\APIRequestException;

class JPushTool
{
	const ApnsProduction = false; //true表示正式环境
	const Time_to_live = 86400;
	
	//售货机key
    const VMAppKey = 'dc2d67c3cc35d11288a20d46';
    const VMMasterSecret = 'd34cfea92bd0b198ce1c76b6';

    //盖掌柜key
    const GZGAppKey = 'e58a62e6458ce0f2a7795d32';
    const GZGMasterSecret = '97411adb0000643f1514480f';
    
//    const GFTAppKey = '284721c15e67bd42ca49f681';

     //盖付通
     const TokenAppKey = '284721c15e67bd42ca49f681';
     const TokenMasterSecret = '05fbd7ca4f9198f934ecbba5';
    
    /**
     * 推送接口封装
     * @param string $appKey
     * @param string $masterSecret
     * @param string $notification
     * @param array $tag
     * @param string $setPlatform
     * @throws Exception
     */
    public static function push($appKey,$masterSecret,$notification,$audience = array(),$setPlatform = '',$audienceType = 'alias')
    {
        try {
            $path = Yii::getPathOfAlias('application') . DS . 'extensions' . DS.'vendor' . DS .'autoload.php';
            require_once $path;
            $client = new JPushClient($appKey,$masterSecret);
            if(empty($setPlatform)) $setPlatform = M\all;
            if(empty($audience))$tag = M\all;
            $AudienceOption = ($audienceType == 'tag') ? M\tag($audience):M\alias($audience);
            if(is_array($notification))
            {
                $content = ' ';
                $extras = $notification;
            }elseif(is_string($notification)){
                $content = $notification;
                $extras = null;
            }
            $result = $client->push()
                    ->setPlatform(M\all)
                    ->setAudience($AudienceOption)
                    ->setNotification(M\notification(M\android($content,null,null,$extras),M\ios($content,null,'+1',null,$extras)))
                    ->setOptions(M\options(null,self::Time_to_live,null,self::ApnsProduction))
                    ->send();
            return $result;
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }
    
    public static function pushMessage($appKey,$masterSecret,$audience = array(),$content,$extras=array(),$setPlatform = '',$audienceType = 'alias')
    {
    	try {
    		$path = Yii::getPathOfAlias('application') . DS . 'extensions' . DS.'vendor' . DS .'autoload.php';
    		require_once $path;
    		$client = new JPushClient($appKey,$masterSecret);
    		if(empty($setPlatform)) $setPlatform = M\all;
    		if(empty($audience))$tag = M\all;
    		$AudienceOption = ($audienceType == 'tag') ? M\tag($audience):M\alias($audience);

    		$result = $client->push()
    		->setPlatform(M\all)
    		->setAudience($AudienceOption);
    		$result->setMessage( M\message($content, '', '', $extras));
    		$result->setOptions(M\options(null,self::Time_to_live,null,self::ApnsProduction));
    		
    		
			$jid = JpushLog::createLog(isset($extras['orderID'])?$extras['orderID']:'0',serialize($result));

    		$rs = $result->send();
    		JpushLog::updateGetData($jid, serialize($rs));
    		return $rs;
    	}catch(Exception $e){
    		throw new Exception($e->getMessage());
    	}
    }
    
    /**
     * 售货机推送接口
     * @param string $moblie
     * @param string $notification
     * @return boolean
     */
    public static function vendingMachinePush($device_id,$content,$extras=array())
    {
        try {
            return self::pushMessage(self::VMAppKey,self::VMMasterSecret,array($device_id),$content,$extras);
        }catch(Exception $e){
            return false;
        }
    }
    
    /**
     * 盖掌柜推送接口  后台推送
     * @param string $moblie
     * @param string $notification
     * @return boolean
     */
    public static function gzgPushMessage($device_id,$content,$extras=array())
    {
    	try {
    		return self::pushMessage(self::GZGAppKey,self::GZGMasterSecret,array($device_id),$content,$extras);
//            return self::pushMessage(self::TokenAppKey,self::TokenMasterSecret,array($device_id),$content,$extras); //IOS测试
    	}catch(Exception $e){
    		return false;
    	}
    }
    
    /**
     * 盖掌柜推送接口
     * @param string $moblie
     * @param string $notification
     * @return boolean
     */
    public static function gzgPush($device_id,$content)
    {
    	try {
    		return self::push(self::GZGAppKey,self::GZGMasterSecret,$content,array($device_id));
    	}catch(Exception $e){
    		return false;
    	}
    }

//     /**
//      * 盖付通推送接口
//      * @param string $moblie
//      * @param string $notification
//      * @return boolean
//      */
//     public static function tokenPush($mobile,$content)
//     {
//         try {
//             $imsiTable = "{{member_mobile_imsi}}";
//             $db = Yii::app()->db;
//             $imsis = $db->createCommand()
//                 ->select('imsi')
//                 ->from($imsiTable)
//                 ->where('mobile =:mobile and is_push = 1',array(':mobile'=>$mobile))
//                 ->queryAll();
//             if(empty($imsis))return;
//             foreach($imsis as $imsi)
//             {
//                 self::push(self::TokenAppKey,self::TokenMasterSecret,$content,array($imsi));
//             }
//             return true;
//         }catch(Exception $e){
//             return false;
//         }
//     }

}