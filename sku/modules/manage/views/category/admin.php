<?php
/* @var $this CategoryController */
/* @var $model Category */
$this->breadcrumbs = array(Yii::t('category', '商品分类') => array('admin'), Yii::t('category', '列表'));
?>

<?php if ($this->getUser()->checkAccess('Manage.Category.Create')): ?>
    <a  href="<?php echo $this->createAbsoluteUrl('/category/create') ?>"><input class="regm-sub" type="submit" name="yt0" value="<?php echo Yii::t('category', '添加分类') ?>"></a>
    
<?php endif; ?>
<?php //if ($this->getUser()->checkAccess('Manage.Category.GenerateAllCategoryCache')): ?>
<!--    <a class="regm-sub" href="--><?php //echo $this->createAbsoluteUrl('/category/generateAllCategoryCache') ?><!--">--><?php //echo Yii::t('category', '更新分类缓存') ?><!--</a>-->
<?php //endif; ?>

<?php
$operateLinks = '';
if ($this->getUser()->checkAccess('Manage.Category.Update')):
    $operateLinks .= "<a href='" . urldecode($this->createAbsoluteUrl('category/update', array('id' => '"+value+"'))) . "'>【编辑】</a>";
endif;
if ($this->getUser()->checkAccess('Manage.Category.Create')):
    $operateLinks .= "<a href='" . urldecode($this->createAbsoluteUrl('category/create', array('parentId' => '"+value+"'))) . "'>【添加子类别】</a>";
endif;
if ($this->getUser()->checkAccess('Manage.Category.Delete')):
    $comfirmText = Yii::t('category', '确定要删除此分类？');
    $operateLinks .= "<a href='" . urldecode($this->createAbsoluteUrl('category/delete', array('id' => '"+value+"'))) . "' class='delete' onclick='return confirm(&apos;$comfirmText&apos;);'>【删除】</a>";
endif;
?>
<div class="c10"></div>
<table id="treeGrid"></table>
<script type="text/javascript">
    jQuery(function($) {
        $('#treeGrid').treegrid({
            url: '<?php echo Yii::app()->createAbsoluteUrl('/category/getTreeGridData'); ?>',
            idField: 'id',
            treeField: 'text',
            queryParams: {'id': 0, 'YII_CSRF_TOKEN': '<?php echo Yii::app()->request->csrfToken; ?>'},
            columns: [[
                    {field: 'text', title: '<?php echo Yii::t('category', '名称'); ?>', width: '200'},
                    {field: 'id', title: '<?php echo Yii::t('category', 'ID'); ?>', width: '50', align: 'center'},
                    
                    {field: 'typename', title: '<?php echo Yii::t('category', '所属类型'); ?>', width: '100', align: 'center'},
                    {field: 'fee', title: '<?php echo Yii::t('category', '服务费'); ?>', width: '100', align: 'center', formatter: function(value) {
                            return value + '%'
                        }
                    },
                    {field: 'recommend', title: '<?php echo Yii::t('category', '推荐'); ?>', width: '100', align: 'center', formatter: function(value) {
                            return value == 1 ? '<?php echo Category::showRecommend(1); ?>' : '<?php echo Category::showRecommend(0); ?>'
                        }
                    },
                    {field: 'status', title: '<?php echo Yii::t('category', '状态'); ?>', width: '100', align: 'center', formatter: function(value) {
                            return value == 1 ? '<?php echo Category::showStatus(1); ?>' : '<?php echo Category::showStatus(0); ?>'
                        }
                    },
                    {field: 'sort', title: '<?php echo Yii::t('category', '排序'); ?>', width: '100', align: 'center'},

                    {field: 'tid', title: '<?php echo Yii::t('category', '操作'); ?>', width: '300', formatter: function(value) {
                            return  <?php echo "\"$operateLinks\""; ?>
                        }
                    },
                    
                ]],
            onBeforeExpand: function(row) {
                //动态设置展开查询的url
                var url = "<?php echo $this->createAbsoluteUrl('/category/getTreeGridData'); ?>?id=" + row.id + '&YII_CSRF_TOKEN=<?php echo Yii::app()->request->csrfToken; ?>';
                $("#treeGrid").treegrid("options").url = url;
                return true;
            }
        });
    });
</script>
<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/js/easyui/themes/default/easyui.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/js/easyui/themes/icon.css");
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/easyui/jquery.easyui.min.js");
?>