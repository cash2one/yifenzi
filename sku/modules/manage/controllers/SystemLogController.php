<?php

class SystemLogController extends MController
{
	public function actionIndex()
	{
		$criteria=new CDbCriteria;
		$criteria->addNotInCondition('type',array(SystemLog::LOG_TYPE_OTHERS));
                $criteria->addCondition('create_time >1473436800');
		$criteria->select = "username,info,create_time,type, case type when 1 then '挂单处理' when 2 then '账号绑定' when 3 then '售卖计划' else '其他' end as type";
		$criteria->order = 'create_time DESC';

		$dataProvider = new CActiveDataProvider('SystemLog', array(
				'criteria' => $criteria,
				'pagination' => array(
						'pageSize' => 20,
				),
		));
		$this->render('index', array(
				'dataProvider' => $dataProvider,
		));
	}

}