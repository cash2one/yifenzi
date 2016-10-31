<?php

/**
 * 管理员角色控制器
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class AuthItemController extends MController {

    public function filters() {
        return array(
            'rights',
        );
    }

    public function actionAdmin() {
        $model = new AuthItem('search');
        $model->unsetAttributes();

        if (isset($_GET['AuthItem']))
            $model->attributes = $_GET['AuthItem'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    public function actionDelete() {
        $name = $this->getParam('name');
        if ($name == 'Admin') {
            $this->setSession('error', '超级管理员的角色不允许删除！');
            $this->redirect(Yii::app()->homeUrl);
        }
        @SystemLog::record(Yii::app()->user->name . "删除管理员角色：{$name}");
        Yii::app()->db->createCommand()->delete('{{auth_item_child}}', 'parent=:parent', array(':parent' => $name));
        Yii::app()->db->createCommand()->delete('{{auth_item}}', 'name=:name', array(':name' => $name));
        Yii::app()->db->createCommand()->delete('{{auth_assignment}}', 'itemname=:itemname', array(':itemname' => $name));

        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    public function actionCreateRole() {
        $model = new AuthItem;
        $this->performAjaxValidation($model);
        if (isset($_POST['AuthItem'])) {
            $model->attributes = $_POST['AuthItem'];
            $rights = $this->getPost('rights');
            if ($model->validate()) {
                Yii::app()->db->createCommand()->insert('{{auth_item}}', array(
                    'name' => $model->name,
                    'type' => AuthItem::TYPE_ADMIN,
                    'description' => $model->description
                ));
                Yii::app()->db->createCommand()->insert('{{auth_item_child}}', array(
                    'parent' => $model->name,
                    'child' => 'Main.UserInfo'
                ));
                Yii::app()->db->createCommand()->insert('{{auth_item_child}}', array(
                    'parent' => $model->name,
                    'child' => 'User.ModifyPassword'
                ));
                Yii::app()->db->createCommand()->insert('{{auth_item_child}}', array(
                    'parent' => $model->name,
                    'child' => 'Sub.User'
                ));
                if ($rights) {
                    foreach ($rights as $value) {
                        Yii::app()->db->createCommand()->insert('{{auth_item_child}}', array(
                            'parent' => $model->name,
                            'child' => $value
                        ));
                    }
                }
                
                @SystemLog::record(Yii::app()->user->name . "添加管理员角色：{$model->name}");
                
                $this->redirect(array('/authItem/admin'));
            }
        }
        $this->render('createrole', array('model' => $model));
    }

    public function actionUpdateRole($name) {
        $name = $this->getParam('name');
        if ($name == 'Admin') {
            $this->setFlash('error', '超级管理员的角色不允许修改');
            $this->redirect(Yii::app()->homeUrl);
        }

        $model = AuthItem::model()->find('name=:name', array(':name' => $name));
        $this->performAjaxValidation($model);
        if (isset($_POST['AuthItem'])) {
            $model->attributes = $_POST['AuthItem'];
            $rights = $this->getPost('rights');
            if ($model->validate()) {
            	
            	//对比插入新增的操作项
            	$auth_data_rs = AuthItem::model()->findAll();
            	$auth_data = array();
            	foreach ($auth_data_rs as $val){
            		$auth_data[$val['name']] = $val;
            	}
            	
                Yii::app()->db->createCommand()->delete('{{auth_item_child}}', 'parent=:parent', array(
                    ':parent' => $name
                ));
                Yii::app()->db->createCommand()->insert('{{auth_item_child}}', array(
                    'parent' => $model->name,
                    'child' => 'Main.UserInfo'
                ));
                Yii::app()->db->createCommand()->insert('{{auth_item_child}}', array(
                    'parent' => $model->name,
                    'child' => 'User.ModifyPassword'
                ));
                Yii::app()->db->createCommand()->insert('{{auth_item_child}}', array(
                    'parent' => $model->name,
                    'child' => 'Sub.User'
                ));
                if ($rights) {
                    foreach ($rights as $value) {
                        Yii::app()->db->createCommand()->insert('{{auth_item_child}}', array(
                            'parent' => $model->name,
                            'child' => $value
                        ));
                        
                        //对比插入新增的操作项
                        if (empty($auth_data[$value])){
                        	Yii::app()->db->createCommand()->insert('{{auth_item}}', array(
	                            'name' => $value,
	                            'type' => 0
	                        ));
                        }
                        
                    }
                }
                
            	
                
                //更新角色信息
                $sql1 = "UPDATE {{auth_item}} SET description = '{$model->description}' WHERE name='{$name}'";
                 Yii::app()->db->createCommand($sql1)->execute();
                 
//                $sql2 = "UPDATE {{auth_assignment}} SET itemname = '{$model->name}'  WHERE itemname='{$name}'";
//                 Yii::app()->db->createCommand($sql2)->execute();
//                 
//                $sql3 = "UPDATE {{auth_item_child}} SET parent = '{$model->name}'  WHERE parent='{$name}'";
//                 Yii::app()->db->createCommand($sql3)->execute();
                
                
                @SystemLog::record(Yii::app()->user->name . "修改管理员角色：{$name} 为 {$model->name}");
                $this->setFlash('success', '角色编辑成功');
                $this->redirect(array('updateRole', 'name' => $model->name));
            }
        }

        $rights = Yii::app()->db->createCommand()
                ->select('child')
                ->from('{{auth_item_child}}')
                ->where('parent=:parent', array(':parent' => $name))
                ->queryColumn();

        $this->render('updaterole', array(
            'model' => $model,
            'rights' => $rights
        ));
    }

}
