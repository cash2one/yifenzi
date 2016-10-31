<?php
/* @var $this MemberBindDetailController */

$this->breadcrumbs=array(
	Yii::t('MemberBind','积分挂单管理'),
	Yii::t('MemberBind','绑定管理'),
);
$this->renderPartial('bind');

$this->widget('GridView',array(
		'id' => 'MemberBind-GridView',
		'dataProvider' => $model->search(),
		'cssFile' => false,
		'itemsCssClass' => 'tab-reg',
		'columns' => array(
				array(
						'headerHtmlOptions' => array('width' => '25%'),
						'header' => Yii::t('MemberBind','绑定时间'),
						'value' => 'date("Y-m-d H:i:s",$data->create_time)',
				),
				array(
						'headerHtmlOptions' => array('width' => '25%'),
						'header' => Yii::t('MemberBind','绑定类型'),
						'value' => '$data->type',
				),
				array(
						'headerHtmlOptions' => array('width' => '25%'),
						'header' => Yii::t('MemberBind','绑定用户数量'),
						'value' => 'MemberBindDetail::BindNumber($data->id,true)',
				),
				array(
						'headerHtmlOptions' => array('width' => '25%'),
						'header' => Yii::t('MemberBind','服务GW号'),
						'value' => 'MemberBindDetail::BindNumber($data->id,false)',
				),
                array(
			            'class' => 'CButtonColumn',
			            'header' => Yii::t('home', '操作'),
			            'template' => '{update}',
			            'updateButtonImageUrl' => false,
			            'buttons' => array(
			                'update' => array(
			                    'label' => Yii::t('MemberBind', '详情'),
			                    'url' => 'Yii::app()->controller->createUrl("/MemberBind/Detail",array("id"=>$data->id
			                		,"create_time"=>$data->create_time
			                		,"type"=>$data->type
			                		,"BindNumber"=>MemberBindDetail::BindNumber($data->id,true)
			                		,"BindGW"=>MemberBindDetail::BindNumber($data->id,false)))',
			                		'visible' => 'Yii::app()->user->checkAccess(\'Manage.MemberBind.Detail\') ',
			                ),
                        ),
                     ),
		),
));
?>

