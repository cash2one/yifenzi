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
class SkusendCommand extends CConsoleCommand
{
    public function actionJfsend(){
        $acount = array(
            'GW83391557',
            'GW70775311',
            'GW87377531',
            'GW87640308',
            'GW20692594',
            'GW30414965',
            'GW50150041',
            'GW20285141',
            'GW44421231',
            'GW15792473',
            'GW88938628',
            'GW67942200',
            'GW61174651',
            'GW50508360',
            'GW91021250',
            'GW76693473',
            'GW40451119',
            'GW81427897',
            'GW21562409',
            'GW19461765',
            'GW65901276',
            'GW16585362',
            'GW44512430',
            'GW42303869',
            'GW23227486',
            'GW52115775',
            'GW37754721',
            'GW31043090',
            'GW21465192',
            'GW80199712',
            'GW82542969',
            'GW84307792',
            'GW72234286',
            'GW12158596',
            'GW42863856',
            'GW10866536',
            'GW32945137',
            'GW73407213',
            'GW17504960',
            'GW66078999',
            'GW67528892',
            'GW81667980',
            'GW76053988',
            'GW41563193',
            'GW29422876',
            'GW87272356',
            'GW53634143',
            'GW13571640',
            'GW44592017',
            'GW95822180',
            'GW48572200',
            'GW34894983',
            'GW44625995',
            'GW87716142',
            'GW35895709',
            'GW59359034',
            'GW16301432',
            'GW28884339',
            'GW65140451',
            'GW17399978',
            'GW11835217',
            'GW86374174',
            'GW79180886',
            'GW83244245',
            'GW26168431',
            'GW12445201',
            'GW29947826',
            'GW15762937',
            'GW34469716',
            'GW95939375',
            'GW58609327',
            'GW73535036',
            'GW82305400',
            'GW72048132',
            'GW66307855',
            'GW33498129',
            'GW41675312',
            'GW48097493',
            'GW20864839',
            'GW89860574',
            'GW15349532',
            'GW38358150',
            'GW54456065',
            'GW39273487',
            'GW48543406',
            'GW59633337',
            'GW70327778',
            'GW28103273',
            'GW83024022',
            'GW69084778',
            'GW95980942',
            'GW71429472',
            'GW64540597',
            'GW30237276',
            'GW24822153',
            'GW82403419',
            'GW42254814',
            'GW11740593',
            'GW66589121',
            'GW17385505',
            'GW56532648',
            'GW65051147',
            'GW17754867',
            'GW51230344',
            'GW13113093',
            'GW31440604',
            'GW74896931',
            'GW22422708',
            'GW68630081',
            'GW94512173',
            'GW55036877',
            'GW63463082',
            'GW44371050',
            'GW68438957',
            'GW73888694',
            'GW68712184',
            'GW44448750',
            'GW14468381',
            'GW39762604',
            'GW12543727',
            'GW54162918',
            'GW93386763',
            'GW23348080',
            'GW98887941',
            'GW94584732',
            'GW75392038',
            'GW46550773',
            'GW82104101',
            'GW93833112',
            'GW28230954',
            'GW70561586',
            'GW94392350',
            'GW39054834',
            'GW20361289',
            'GW37499336',
            'GW20080574',
            'GW75098052',
            'GW43940428',
            'GW74767952',
            'GW79837763',
            'GW12522122',
            'GW44117345',
            'GW14574154',
            'GW82353931',
            'GW74116065',
            'GW27534127',
            'GW54349076',
            'GW42160272',
            'GW81993682',
            'GW31682867',
            'GW77898837',
            'GW66300675',
            'GW36283526',
            'GW79565736',
            'GW73699316',
            'GW34466967',
            'GW46624739',
            'GW53841851',
            'GW30156216',
            'GW33772498',
            'GW10080816',
            'GW27769753',
            'GW68294520',
            'GW26450880',
            'GW27429597',
            'GW18528515',
            'GW91791545',
            'GW50781720',
            'GW64649722',
            'GW36403773',
            'GW95810785',
            'GW86521886',
            'GW81668149',
            'GW15631732',
            'GW43994833',
            'GW86228458',
            'GW27100157',
            'GW96638935',
            'GW92337263',
            'GW40791025',
            'GW20935056',
            'GW85081343',
            'GW96065831',
            'GW20956656',
            'GW76534653',
        );

        foreach( $acount as $k=>$v ){
//             $transaction = Yii::app()->db->beginTransaction();
//             try {
                $sql = "select id,gai_number,gai_member_id,sku_number from {{member}} where gai_number='{$v}'";
                $tMemberData = Yii::app()->db->createCommand($sql)->queryRow();
                if (!$tMemberData){
                    continue;
                }
                
                $orderData['code'] = Fun::buildOrderNo();
                $orderData['member_id'] = $tMemberData['id'];
                $orderData['total_price'] = 1;
                $orderData['create_time'] = time();
                $orderData['remark']    =   '新用户'.$tMemberData['gai_number'].'，活动赠送积分(1)';
                if (!Yii::app()->db->createCommand()->insert("gw_sku_orders_act", $orderData)) {
                    continue;
                }
                //
                $order_id = Yii::app()->db->getLastInsertID();
                if (!$order_id){
                    continue;
                }
                ////
                $apiLogData['order_id'] = $order_id;
                $apiLogData['order_code'] = $orderData['code'];
                $apiLogData['remark'] = isset($post['remark']) ?: $orderData['code'] . '新用户'.$tMemberData['gai_number'].'，活动赠送积分(1)';
                $apiLogData['money'] = 1;
                $apiLogData['account_id'] = $tMemberData['id'];
                $apiLogData['sku_number'] = $tMemberData['sku_number'];
                $apiLogData['gai_number'] = $tMemberData['gai_member_id'];
                $apiLogData['data'] = json_encode(array("orderID" => $order_id, "pay_type" => 2, "orderSN" => $orderData['code'],"f"=>array(),"t"=>$tMemberData));
                $apiLogData['create_time'] = time();
                AccountBalance::AccountOutIn($apiLogData);
                //
                echo 'success';
//                 $transaction->commit();
//             }catch (Exception $e) {
//                 $transaction->rollBack();
//             }
        }
    }
}