<?php
/**
 * 会员类型模型
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 15-6-1 上午10:34
 */

class MemberType extends CActiveRecord
{
    const MEMBER_OFFICAL=2;//正式会员
    const MEMBER_EXPENSE=1;//消费会员

    public function tableName()
    {
        return '{{member_type}}';
    }

    public function getDbConnection() {
        return Yii::app()->gw;
    }
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * 获取用户消费类型，并存入缓存
     * @return array
     */
    public static function getMemberType(){
        $redis = Yii::app()->redis;
        if($redis->exists('memberType')){
            $array = json_decode($redis->get('memberType'),true);
        }else{
            $types = MemberType::model()->findAll();
            $array = array();
            if(!empty($types)){
                foreach ($types as $type) {
                    $array[$type['id']] = $type['ratio'];
                    if ('消费会员' === $type['name']) {
                        $array['default'] = $type['ratio'];
                        $array['defaultType'] = $type['id'];
                    } elseif ('正式会员' === $type['name']) {
                        $array['official'] = $type['ratio'];
                        $array['officialType'] = $type['id'];
                    }
                }
                $redis->set('memberType', json_encode($array), 86400);
            }
        }
        return $array;
    }

    /**
     * 通过会员id查询会员的积分换算比率
     * @param unknown $memberid
     */
    public static function getMemberRatioByMemberId($memberid)
    {
        $memberType = self::getMemberType();
        $member = Member::model()->find(
            array(
                'condition' => 'id = :id',
                'select' => array('type_id'),
                'params' => array(':id'=>$memberid),
            )
        );
        return $memberType[$member['type_id']];
    }

    /**
     * 查询对应的积分兑换比率
     * @param $typeId
     * @return null
     */
    public static function getRatio($typeId){
        $data = self::model()->find(array(
            'select' => array('ratio'),
            'condition' => 'id = :id',
            'params' => array(':id' => $typeId)
        ));
        return $data ? $data['ratio'] : null;
    }
}