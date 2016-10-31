<?php

/**
 * 没有找不到语言包翻译的时候，将使用自动转换
 *
 * @author zhenjun_xu<412530435@qq.com>
 */
class ZhTranslateEventHandler {

    static function ZhMissingTranslation($event) {
        // 这个事件的事件类是 CMissingTranslationEvent
        // 因此我们能获得这个message的一些信息
        //翻译
        if ($event->language == 'zh_tw') {
            $event->message = ZhTranslate::convert($event->message,'zh-hk');
            return $event->message;
        }else if($event->language == 'en'){
            if(YII_DEBUG){
                 if(in_array($event->category,array('yii-debug-toolbar','YiiDebug.yii-debug-toolbar'))){
                     return $event->message;
                 }
            }
            /**
            $configCache = explode('-', Yii::app()->user->getState('tran','0-'.(time()+300)));
            if($configCache[1] >= time()){
                $langConfig = Common::getConfig('languageTranslate');
                if(!empty($langConfig) && $langConfig['tran']){
                    Yii::app()->user->setState('tran','on-'.time());
                }else{
                    Yii::app()->user->setState('tran','off-'.time());
                }
            }
            // 更新语言包
            if(strpos(Yii::app()->user->getState('tran'),'on') !== false){
                $event->message = self::addLog($event->message,$event);
            }
            **/
            return $event->message;
        } else {
            return $event->message;
        }
    }
    
    public static function addLog($messageCn,$event){
        if((!is_numeric($messageCn) && preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $messageCn) && 
                strpos($messageCn, '.jpg') === false && strpos($messageCn, '.png') === false && 
                !ctype_alnum($messageCn)) == false){
            return $messageCn;
        }
        // find in database
        if(strpos($event->sender->basePath,'frontend') !== false){
            $isBackend = '0';
        }elseif(strpos($event->sender->basePath,'backend') !== false){
            $isBackend = '1';
        }elseif(strpos($event->sender->basePath,'agent') !== false){
            $isBackend = 2;
        }elseif(strpos($event->sender->basePath,'api') !== false){
            $isBackend = 3;
        }else{
            $isBackend = 4;
        }
        $english = Yii::app()->db->createCommand()
                ->select('en')->from('{{translate}}')
                ->where('category=:cat and cn=:cn and is_backend=:ib', array(':cat'=>$event->category,':cn'=>$messageCn,':ib'=>$isBackend))->queryScalar();
        if($english){
//            $event->message = $english;
//            $sql = "UPDATE {{translate}} SET `num`=`num`+1 WHERE category=:cat and cn=:cn";
//            Yii::app()->db->createCommand($sql)->execute(array(':cat'=>$event->category, ':cn'=>$messageCn));
        }else{
            $controllerId = Yii::app()->controller->id;
            $actionId = '';
            $action = Yii::app()->controller->getAction();
            if($action) $actionId = $action->id;
            $str = '';
            $english = $messageCn;
            $oldStr = str_replace(array('盖网通','盖网'), 'GATEWANG', $messageCn);
            $str = GoogleTranslate::staticTranslate($oldStr,'zh-cn','en');
            if(!empty($str) && $str != false){
                $english = $str;
                $sql = "INSERT INTO {{translate}} ".
                        "SET category=:cat,`cn`=:cn, `en`=:en, `num`=1, `controller`=:c, `action`=:a, `create_time`=now(), is_backend=:ib ".
                        "ON DUPLICATE KEY UPDATE `en`=:en, `num`=`num`+1, `controller`=:c, `action`=:a";
                Yii::app()->db->createCommand($sql)->execute(array(':cat'=>$event->category, ':cn'=>$messageCn, ':en'=>$str, ':c'=>$controllerId, ':a'=>$actionId, ':ib'=>$isBackend));
            }else{
                $sql = "INSERT INTO {{translate}} ".
                       "SET category=:cat,`cn`=:cn, `num`=1, `controller`=:c, `action`=:a, `create_time`=now(), is_backend=:ib ".
                       "ON DUPLICATE KEY UPDATE `num`=`num`+1, `controller`=:c, `action`=:a";
                Yii::app()->db->createCommand($sql)->execute(array(':cat'=>$event->category, ':cn'=>$messageCn, ':c'=>$controllerId, ':a'=>$actionId, ':ib'=>$isBackend));
            }
        }
        return $english;
    }
}