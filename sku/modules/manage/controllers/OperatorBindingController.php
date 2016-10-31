<?php
/**
 * @author liao jiawei <569114018@qq.com>
 * Date: 2016/4/7
 * Time: 14:15
 */

class OperatorBindingController extends MController{

    public function actionAdmin(){

        $model = new OperatorRelation('search');

        $model->unsetAttributes();
        if (isset($_GET['OperatorRelation']))
            $model->attributes = $_GET['OperatorRelation'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /*
	 * 绑定详情
	 */
    public function actionDetail($id){

        $result = OperatorRelation::getOneRestule($id);
        $this->render('detail',array('result'=>$result));

    }

    /**
     * 绑定动作
     */
    public function actionCreateBind(){
        $model = new OperatorRelation();
        $this->render('createBind',array(
            'model'=>$model,
        ));
    }

    /**
     * 检查GW号
     *
     */
    public function actionCheckGW(){
        try {
            $reuslt = Partners::getIDByGW($_POST["BindGW"]);
            exit (json_encode(array("result"=>$reuslt)));
        } catch (Exception $e) {
            exit(json_encode($e->getMessage()));
        }
    }

    public function actionBindRecord(){
        try {
            $data = array();

            $partner = Partners::getIDByGW($_POST["PGW"]);
            $operatorPartner = Partners::getIDByGW($_POST["OPGW"]);

           if(!$partner){
                $result = 'pgw';
                exit(json_encode(array("result"=>$result)));
            }
            if(!$operatorPartner){
                $result = 'opgw';
                exit(json_encode(array("result"=>$result)));
            }
            if($partner['member_id']){
                $operator = OperatorRelation::model()->find('member_id=:mid',array(':mid'=>$partner['member_id'])); //已存在
                if($operator){
                    $result = 'haspgw';
                     exit(json_encode(array("result"=>$result)));
                }
            }
            $data['member_id'] = $partner['member_id'];
            $data['partner_id'] = $partner['id'];
            $data['operator_member_id'] = $operatorPartner['member_id'];
            $data['operator_partner_id'] = $operatorPartner['id'];
            $data['status'] = $_POST["status"];
            $data['create_time'] = time();

            $result = Yii::app()->db->createCommand()->insert("{{operator_relation}}",$data);
            exit (json_encode(array("result"=>$result)));
        } catch (Exception $e) {
            exit(json_encode(array("result"=>false,"message"=>$e->getMessage())));
        }
    }

    public function actionUpBindRecord($id){
        try {
            $data = array();
            $oneData = OperatorRelation::getOneRestule($id);
            if(!$oneData) exit (json_encode(array("result"=>false)));

            $partner = Partners::getIDByGW($_POST["PGW"]);
            $operatorPartner = Partners::getIDByGW($_POST["OPGW"]);
            if(!$partner){
                $result = 'pgw';
                exit(json_encode(array("result"=>$result)));
            }
            if(!$operatorPartner){
                $result = 'opgw';
                exit(json_encode(array("result"=>$result)));
            }

            $data['member_id'] = $partner['member_id'];
            $data['partner_id'] = $partner['id'];
            $data['operator_member_id'] = $operatorPartner['member_id'];
            $data['operator_partner_id'] = $operatorPartner['id'];
            $data['status'] = $_POST["status"];
            $data['create_time'] = time();

            $result = Yii::app()->db->createCommand()->update("{{operator_relation}}",$data,'id=:id',array(':id'=>$id));
            if($result){
            exit (json_encode(array("result"=>'success')));
            }
        } catch (Exception $e) {
            exit(json_encode(array("result"=>false,"message"=>$e->getMessage())));
        }
    }

    public function actionUpdate($id){
        $result = OperatorRelation::getOneRestule($id);
        $model = new OperatorRelation();
        $this->render('update',array('result'=>$result,'model'=>$model));
    }

    //检测商家绑定
    public function actionCheckPartner(){
        try {
            $partner = Partners::getIDByGW($_POST["BindGW"]);
            $id = isset($_POST['id']) ? $_POST['id'] : 0;
            $result = OperatorRelation::checkPartnerId($partner['id'],$id);
            exit (json_encode(array("result"=>$result)));
        } catch (Exception $e) {
            exit(json_encode($e->getMessage()));
        }
    }
}