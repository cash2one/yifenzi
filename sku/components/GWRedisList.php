<?php

/**
 * redis 队列执行类
 * @author LC
 *
 */
class GWRedisList {

    const DEFAULT_SEND_COUNT = 3;   //默认发送3次，如果3次都返回失败则不再发
    const GT_SMS_SEND_LIST = 'orderapi_sms_send_list';  //盖网通短信队列(消费的信息)
    const GT_SMS_SEND_LIST_CODE = 'orderapi_sms_send_list_code';  //盖网通短信队列(验证码)
    const GT_EMAIL_SEND_LIST = 'orderapi_email_send_list';    //盖网通邮件队列

    /**
     * 盖网通发短信(消费的信息)
     * @param array $arr
     * $arr = array(
     * 		'id' => 1,
     * 		'mobile'=>'',
     * 		'content'=>''
     *      )
     * @return bool
     */

    public static function sendSmsGT(Array $arr) {
        $redisStatus = Tool::getConfig('smsmodel', 'isRedis');
        $arr['count'] = 0;
        if ($redisStatus == 1) {
            $sms = CJSON::encode($arr);
            self::set(self::GT_SMS_SEND_LIST, $sms);
        } else {
            self::send($arr);
        }
        return true;
    }

    /**
     * 盖网通发短信（验证码）
     * @param array $arr
     * $arr = array(
     *      'id' => 1,
     *      'mobile'=>'',
     *      'content'=>''
     *      )
     * @return bool
     */
    public static function sendSmsGTCode($arr) {
        $redisStatus = Tool::getConfig('smsmodel', 'isRedis');
        $arr['count'] = 0;
        if ($redisStatus == 1) {
            $sms = CJSON::encode($arr);
            self::set(self::GT_SMS_SEND_LIST_CODE, $sms);
        } else {
           self::send($arr);
        }
        return true;
    }

    /**
     * 盖网通发送邮件
     * @param array $arr
     * @return boolean
     */
    public static function sendEmailGT(Array $arr) {
        $redisStatus = Tool::getConfig('emailmodel', 'isRedis');

        $arr['count'] = 0;
        if ($redisStatus == 1) {
            $email = CJSON::encode($arr);
            self::set(self::GT_EMAIL_SEND_LIST, $email);
        } else {
            self::sendEmail($arr);
        }
        return true;
    }

    /**
     * 
     * 盖网通短信发送进程
     */
    public static function daemonSmsGT() {
        self::daemonSms(self::GT_SMS_SEND_LIST);
    }

    /**
     * 盖网通验证码的发送进程
     */
    public static function daemonSmsGTCode() {
        self::daemonSms(self::GT_SMS_SEND_LIST_CODE);
    }

    /**
     * 盖网通发送邮件进程
     */
    public static function daemonEmailGT() {
        self::daemonEmail(self::GT_EMAIL_SEND_LIST);
    }

    /**
     * 守护进程(邮件)
     */
    public static function daemonEmail($key) {
        self::delete($key);
        while (true) {
            try {
                self::daemonSH($key);
            } catch (Exception $e) {
                echo $e->getMessage() . "\n";
            }
            sleep(1);
        }
    }

    /**
     * 守护进程(邮件)
     */
    public static function daemonSH($key) {
        $list = new ARedisList($key);
        foreach ($list as $result)
            if ($result !== false) {
                if ($list->remove($result)) {
                    $info = CJSON::decode($result);
                    self::sendEmail($info);
                }
            }
    }

    /**
     * 守护进程(短信)
     */
    public static function daemonSms($key) {
//         self::delete($key);
        while (true) {
            try {
            	//增加如果是夜间就不发送短信
            	if($key == self::GT_SMS_SEND_LIST)
            	{
            		//如果是非验证码类型，则延迟到白天发送
            		$hour = date('G');
            		$minute = intVal(date('i'));
            		
            		if($hour<23 && $hour>7)
            		{
            			self::daemon($key);
            		}
            		elseif ($hour == 7 && $minute >=30)
            		{
            			self::daemon($key);
            		}
            	}
            	else 
            	{
            		self::daemon($key);
            	}
                
            } catch (Exception $e) {
                echo $e->getMessage() . "\n";
            }
            sleep(1);
        }
    }

    /**
     * 守护进程
     */
    public static function daemon($key) {
        $list = new ARedisList($key);
        foreach ($list as $result)           
            if ($result !== false) {
                if ($list->remove($result)) {
                    $info = CJSON::decode($result);
                    self::send($info);
                }
            }
    }

    /**
     * 执行发送短信
     */
    public static function send(&$info) {
        $info['count'] += 1; //发送的次数
        $rs = Sms::send($info['mobile'], $info['content'], $info['datas'], $info['tmpId'], $info['api']);
        if ($rs['send_status'] == SmsLog::STATUS_FAILD && $info['count'] < self::DEFAULT_SEND_COUNT) {
            //如果发送失败，重新发送
            self::send($info);
        } else {
            $updateArr = array(
                'status' => $rs['send_status'],
                'count' => $info['count'],
                'send_time' => $rs['create_time'],
                'interface' => $rs['send_api']
            );
            $tn = SmsLog::model()->tableName();
            Yii::app()->gw->createCommand()->update($tn, $updateArr, 'id=' . $info['id']);
        }
    }

    /**
     * 执行发送邮件
     */
    public static function sendEmail(&$info) {
        $info['count'] += 1; //发送的次数
        $rs =Tool::sendEmail($info['email'], $info['subject'], $info['content'],$info['id']);
        if ($rs['send_status'] == EmailLog::STATUS_FAILD && $info['count'] < self::DEFAULT_SEND_COUNT) {
            //如果发送失败，重新发送
            self::sendEmail($info);
        }else{
            $updateArr = array(
                'status' => $rs['send_status'],
                'count' => $info['count'],
                'send_time' => $rs['create_time'],
            );
            Yii::app()->db->createCommand()->update(EmailLog::model()->tableName(), $updateArr, 'id=' . $info['id']);
        }
    }

    /**
     * 入队
     * @param $key  string 队列的名称
     * @param $string string 入队的内容
     */
    public static function set($key, $string) {
        $list = new ARedisList($key);
        $list->push($string);
    }

    /**
     * 后进先出，插入到第一列
     */
    public static function lset($key, $string) {
        $list = new ARedisList($key);
        $list->unshift($string);
    }

    /**
     * 出队
     */
    public static function shift($key) {
        $list = new ARedisList($key);
        $result = $list->shift();
        return $result;
    }

    /**
     * 清除redis的数据-防止重复短信
     */
    public static function delete($key) {
        $list = new ARedisList($key);
        $list->clear();
    }

    /**
     * 出对列，先进先出
     * @param $key
     * @return mixed
     * @throws CException
     */
    public static function pop($key) {
        $list = new ARedisList($key);
        return $list->pop();
    }

    /**
     * 删除指定值
     * @param $key
     * @param $str
     * @throws CException
     */
    public static function remove($key, $str) {
        $list = new ARedisList($key);
        $list->remove($str);
    }

}
