<?php

/**
 * 商品分类控制器
 * 操作(创建商品分类,修改商品分类,商品分类列表,获取表格分类数数据,分类树列表)
 * @author qingbao_deng
 */
class CategoryController extends MController {
    public $defaultAction = 'admin';

    public function filters() {
        return array(
            'rights',
        );
    }

    /**
     * 不作权限控制的action
     * @return string
     */
    public function allowedActions() {
        return 'getTreeGridData, categoryTree';
    }

    /**
     * 创建商品分类
     * @param int $parentId 父类ID
     * @throws CHttpException
     */
    public function actionCreate($parentId = 0) {
        $model = new Category;
        if ($parentId == 0)
            $model->parent_id = 0;
        else {
            $model->parent_id = intval($parentId);
            if (!$model->parentClass)
                throw new CHttpException(404, Yii::t('category', '无效的父级分类'));
            if ($model->parentClass->depth == Category::DEPTH_TWO)
                throw new CHttpException(404, Yii::t('category', '当前只允许添加到三级分类'));
        }

        $this->performAjaxValidation($model);
        if (isset($_POST['Category'])) {
            $model->attributes = $this->getPost('Category');
            $saveDir = 'category' . DS . date('Y/n/j');
            $model = UploadedFile::uploadFile($model, 'thumbnail', $saveDir);  // 上传图片
            $model = UploadedFile::uploadFile($model, 'picture', $saveDir);
            if ($model->save()) {
                UploadedFile::saveFile('thumbnail', $model->thumbnail);  // 保存图片
                UploadedFile::saveFile('picture', $model->picture);
                SystemLog::record($this->getUser()->name . "创建商品分类：" . $model->name);
                $this->setFlash('success', Yii::t('category', '添加分类') . '"' . $model->name . '"' . Yii::t('category', '成功'));
                $this->redirect(array('admin'));
            }
        }
        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * 修改商品分类
     * @param int $id 分类ID
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $this->performAjaxValidation($model);
        if (isset($_POST['Category'])) {
            $model->attributes = $this->getPost('Category');
            $oldThumbnail = $this->getPost('oldThumbnail');  // 旧图
            $oldPictrue = $this->getPost('oldPicture');
            $saveDir = 'category' . DS . date('Y/n/j');
            $model = UploadedFile::uploadFile($model, 'thumbnail', $saveDir);  // 上传图片
            $model = UploadedFile::uploadFile($model, 'picture', $saveDir);  // 上传图片
            if ($model->save()) {
                UploadedFile::saveFile('thumbnail', $model->thumbnail, $oldThumbnail, true); // 更新图片
                UploadedFile::saveFile('picture', $model->picture, $oldPictrue, true);
                SystemLog::record($this->getUser()->name . "修改商品分类：" . $model->name);
                $this->setFlash('success', Yii::t('category', '修改分类') . '"' . $model->name . '"' . Yii::t('category', '成功'));
                $this->redirect(array('admin'));
            }
        }
        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * 商品分类列表
     */
    public function actionAdmin() {
        $this->render('admin');
    }

    /**
     * 删除商城分类
     * @param $id
     * @author jianlin.lin
     */
    public function actionDelete($id)
    {
        /** @var Category $model */
        $model = $this->loadModel($id);
        if (Category::model()->count('parent_id = :id', array(':id' => $model->id)))
            $this->setFlash('error', Yii::t('category', '该类有子级分类，删除失败'));
//        else if (Goods::model()->count('cate_id = :id', array(':id' => $model->id)))
//            $this->setFlash('error', Yii::t('category', '该类有所属商品，删除失败'));
        else if ($model->delete())
            SystemLog::record($this->getUser()->name . '删除' . $model->name . '分类，ID为：' . $model->id);
        $this->render('admin');
    }

    /**
     * 获取表格分类树数据
     */
    public function actionGetTreeGridData() {
        $id = $this->getParam('id');
        $data = array();
        if (is_numeric($id)) {
            $model = new Category;
            $data = $model->getTreeData($id);
        }
        echo CJSON::encode($data);
    }

    /**
     * 分类树列表
     */
    public function actionCategoryTree() {
        $data = array();
        $model = new Category;
        $data = Tool::treeDataFormat($model->getTreeData(null, 3));
        array_unshift($data, array('id' => 0, 'text' => '顶级分类')); // 加入顶级分类选项数据
        $data = CJSON::encode($data);
        $this->render('categorytree', array(
            'data' => $data,
        ));
    }

    /**
     * 生成所有分类缓存
     */
    public function actionGenerateAllCategoryCache() {
        Tool::cache(Category::CACHEDIR)->flush();
        Category::generateCategoryCacheFiles();
        SystemLog::record($this->getUser()->name . "生成所有分类缓存文件");
        $this->setFlash('success', Yii::t('category', '成功生成所有分类缓存文件'));
        $this->redirect(array('admin'));
    }
}