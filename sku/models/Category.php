<?php

/**
 * This is the model class for table "{{category}}".
 *
 * The followings are the available columns in table '{{category}}':
 * @property string $id
 * @property string $parent_id
 * @property string $name
 * @property string $short_name
 * @property string $alias
 * @property integer $status
 * @property integer $sort
 * @property string $keywords
 * @property string $description
 * @property string $type_id
 * @property string $thumbnail
 * @property string $picture
 * @property integer $recommend
 * @property string $tree
 * @property string $depth
 * @property string $content
 * @property integer $fee
 */
class Category extends CActiveRecord
{
    public $applyToChilden;

    // depth常量
    const DEPTH_ZERO = 0; //顶级分类
    const DEPTH_ONE = 1; //二级分类
    const DEPTH_TWO = 2; //三级分类

    const PARENT_ID = 0; //顶级分类为0
    const STATUS_DISABLE = 0;
    const STATUS_ENABLE = 1;
    const RECOMMEND_NO = 0;
    const RECOMMEND_YES = 1;

    // 定义缓存键值常量
    const CACHEDIR = 'SkuCategory'; // 缓存目录
    const CK_ALLCATEGORY = 'allCategory'; // 所有分类数据
    const CK_TREECATEGORY = 'treeCategory'; // 分类树型数据
    const CK_CATEGORYINDEX = 'categoryIndex'; // 分类索引数据
    const CK_MAINCATEGORY = 'mainCategory'; // 首页分类数据	
    const CK_TOPDATA = 'topData';		//一级分类

    public static function getRecommend()
    {
        return array(
            self::RECOMMEND_NO => Yii::t('category', '否'),
            self::RECOMMEND_YES => Yii::t('category', '是')
        );
    }

    public static function getStatus()
    {
        return array(
            self::STATUS_ENABLE => Yii::t('category', '显示'),
            self::STATUS_DISABLE => Yii::t('category', '禁用')
        );
    }

    public static function showStatus($key)
    {
        $options = self::getStatus();
        return $options[$key];
    }

    public static function showRecommend($key)
    {
        $options = self::getRecommend();
        return $options[$key];
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{category}}';
	}

    public function rules()
    {
        return array(
            array('name, fee', 'required'),
            array('name, alias, short_name', 'unique'),
            array('thumbnail, picture', 'required', 'on' => 'insert'),
//            array('thumbnail, picture','on'=>'safe'),
            array('status, sort', 'numerical', 'integerOnly' => true),
            array('fee', 'match', 'pattern' => '/^[1-9]\\d*$/'),
            array('parent_id, type_id', 'length', 'max' => 11),
            array('name, alias, keywords, short_name', 'length', 'max' => 128),
            array('description', 'length', 'max' => 256),
            array('fee', 'validateRate'), // 不得超出 "100"
            array('status, recommend', 'in', 'range' => array(0, 1)),
            array('parent_id', 'checkCreateCategory', 'on' => 'update'),
            array('thumbnail, picture', 'file', 'types' => 'jpg,gif,png', 'maxSize' => 1024 * 1024 * 1, 'allowEmpty' => true,
                'tooLarge' => Yii::t('category', '{attribute}最大不超过1MB，请重新上传!'),'safe'=>true),
            array('applyToChilden', 'boolean')
        );
    }

    /**
     * 验证分类费率
     * @param type $attribute
     * @param type $params
     */
    public function validateRate($attribute, $params)
    {
        if ($this->$attribute > 100)
            $this->addError($attribute, $this->getAttributeLabel($attribute) . '值不能超出"100"');
    }

    /**
     * 检查添加分类是否合法
     */
    public function checkCreateCategory()
    {
        $raw = $this->find('id = :parent_id And parent_id = :id', array('parent_id' => $this->parent_id, 'id' => $this->id)); // 查询是否有自身子类记录
        if ($this->id == $this->parent_id or !is_null($raw)) // 判断选择父类是否是自身或自身子类的分类
            $this->addError('parent_id', Yii::t('category', '选择父类不合法，不可以自身类和自身子类作为父类！'));
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        return array(
            'type' => array(self::BELONGS_TO, 'Type', 'type_id'),
            'parentClass' => array(self::BELONGS_TO, 'Category', 'parent_id'),
            'childClass' => array(self::HAS_MANY, 'Category', 'parent_id', 'order' => 'sort desc'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
        return array(
            'id' => Yii::t('category', 'ID'),
            'parent_id' => Yii::t('category', '所属父类'),
            'name' => Yii::t('category', '名称'),
            'short_name' => Yii::t('category', '简写'),
            'alias' => Yii::t('category', '别名'),
            'status' => Yii::t('category', '状态'),
            'sort' => Yii::t('category', '排序'),
            'keywords' => Yii::t('category', '关键词'),
            'description' => Yii::t('category', '描述'),
            'type_id' => Yii::t('category', '类型'),
            'thumbnail' => Yii::t('category', '小图'),
            'picture' => Yii::t('category', '大图'),
            'recommend' => Yii::t('category', '推荐'),
            'rate' => Yii::t('category', '费率'),
            'fee' => Yii::t('category', '服务费'),
            'tree' => Yii::t('category', '树'),
            'depth' => Yii::t('category', '级'),
            'applyToChilden' => Yii::t('category', '应用于所有子类')
        );
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('parent_id',$this->parent_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('short_name',$this->short_name,true);
		$criteria->compare('alias',$this->alias,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('sort',$this->sort);
		$criteria->compare('keywords',$this->keywords,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('type_id',$this->type_id,true);
		$criteria->compare('thumbnail',$this->thumbnail,true);
		$criteria->compare('picture',$this->picture,true);
		$criteria->compare('recommend',$this->recommend);
		$criteria->compare('tree',$this->tree,true);
		$criteria->compare('depth',$this->depth,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('fee',$this->fee);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * 获取指定父类ID分类树数据
     * @param int $id 可指定父类ID，$id为Null则查询所有分类， "0" :则获取顶级分类
     * @return array
     */
    public function getTreeData($id = null, $max_depth = null)
    {
        $data = array();
        $command = Yii::app()->db->createCommand();

        // 如指定父类ID，则加条件
        if ($id !== null && $max_depth == null) {
            $command->where('t.parent_id = :parent_id', array('parent_id' => intval($id)));
        }
        if ($id !== null && $max_depth !== null) {
            $command->where('t.parent_id = :parent_id AND t.depth < :depth', array('parent_id' => intval($id), 'depth' => intval($max_depth)));
        }
        if ($id == null && $max_depth !== null) {
            $command->where('t.depth < :depth', array('depth' => intval($max_depth)));
        }

        $record = $command->from(self::tableName() . ' as t') // type.name as typename,
            ->select('t.id, t.id as tid,t.name as text, t.parent_id, t.status, t.sort, t.recommend, t.fee, (select b.id from ' . self::tableName() . ' as b where b.parent_id = t.id limit 1) as state') // name 字段别名了 text
            ->order('sort desc, id asc')
            ->leftJoin('{{type}} as type', 't.type_id = type.id')
            ->queryAll();
        foreach ($record as $k => $v) {
            $data[$k] = $v;
            $data[$k]['state'] = is_null($v['state']) ? 'open' : 'closed';
        }
        return $data;
    }

    /**
     * 生成所有分类相应缓存
     */
    public static function generateCategoryCacheFiles()
    {
        

        Tool::cache(self::CACHEDIR)->flush();
        
        self::allCategoryData(); // 生成分类数据缓存文件
        self::treeCategory(true,false); // 生成树形分类数据文件
        self::categoryIndexing(true,false); // 生成分类索引
        self::generateMainCategoryData(false); // 生成前台主要分类数据
        self::getTop(null,false);
        
        return true;
    }

    /**
     * 所有分类数据
     * @param boolean $generate 是否生成缓存，默认为 true
     * @return array
     */
    public static function allCategoryData($generate = true)
    {
        $data = array();
        $categorys = Yii::app()->db->createCommand()->from('{{category}}')
            ->where('status = :status', array(':status' => Category::STATUS_ENABLE))
            ->order('sort DESC, id ASC')->queryAll();
        foreach ($categorys as $val) // 这里键原有的键值替换为分类自身ID
            $data[$val['id']] = $val;
        if ($generate === true) // 生成缓存
            Tool::cache(self::CACHEDIR)->set(self::CK_ALLCATEGORY, $data);
        return $data;
    }

    /**
     * 树型分类数据
     * @param boolean $generate 是否生成缓存，默认为 true
     * @return array
     */
    public static function treeCategory($generate = true,$falg=true)
    {
        $categorys = $falg?Tool::cache(self::CACHEDIR)->get(self::CK_ALLCATEGORY):false;
        $categorys = !$categorys?self::allCategoryData():$categorys;
        
        $tree = array();
        $tempData = $categorys;
        foreach ($categorys as $val) {
            if (isset($tempData[$val['parent_id']])) {
                $tempData[$val['parent_id']]['childClass'][$val['id']] = & $tempData[$val['id']];
            } else {
                if ($val['parent_id'] == '0') {
                    $tree[$val['id']] = & $tempData[$val['id']];
                }
            }
        }
        if ($generate === true)
            Tool::cache(self::CACHEDIR)->set(self::CK_TREECATEGORY, $tree);
        return $tree;
    }

    /**
     * 分类索引（包含自身、父级、爷级的分类数据）
     * @param boolean $generate 是否生成缓存，默认为 true
     * @return array 数据中的type代表分类层次 1：顶级分类、2：父级分类、3：三级分类
     */
    public static function categoryIndexing($generate = true,$flag = true)
    {
//         if (!$category = Tool::cache(self::CACHEDIR)->get(self::CK_ALLCATEGORY))
//             $category = self::allCategoryData();
        
        $category = $flag?Tool::cache(self::CACHEDIR)->get(self::CK_ALLCATEGORY):false;
        $category = !$category?self::allCategoryData():$category;
        
        $breadcrumbs = array();
        foreach ($category as $k => $v) {
            // 自身分类
            $breadcrumbs[$k]['id'] = $v['id'];
            $breadcrumbs[$k]['name'] = $v['name'];
            $breadcrumbs[$k]['type'] = 1;
            if ($v['parent_id'] == 0)
                continue;
            // 获取父级
            if (isset($category[$v['parent_id']])) {
                $parent = $category[$v['parent_id']];
                $breadcrumbs[$k]['parentId'] = $parent['id'];
                $breadcrumbs[$k]['parentName'] = $parent['name'];
                $breadcrumbs[$k]['type'] = 2;
                if ($parent['parent_id'] == 0)
                    continue;
                // 获取爷级
                if (isset($category[$parent['parent_id']])) {
                    $grandpa = $category[$parent['parent_id']];
                    $breadcrumbs[$k]['grandpaId'] = $grandpa['id'];
                    $breadcrumbs[$k]['grandpaName'] = $grandpa['name'];
                    $breadcrumbs[$k]['type'] = 3;
                }
            }
        }
        if ($generate === true)
            Tool::cache(self::CACHEDIR)->set(self::CK_CATEGORYINDEX, $breadcrumbs);
        return $breadcrumbs;
    }

    /**
     * 生成前台主要分类数据
     * @return array
     */
    public static function generateMainCategoryData($flag= true)
    {
//         if (!$tree = Tool::cache(self::CACHEDIR)->get(self::CK_TREECATEGORY))
//             $tree = self::treeCategory();
        
        $tree  = $flag?Tool::cache(self::CACHEDIR)->get(self::CK_TREECATEGORY):false;
        $tree = !$tree?self::treeCategory():$tree;
        
//         foreach ($tree as $k => $v) {
            // 推荐分类
//             $tree[$k]['recommends'] = self::findRecommendCategory($v['id']);
//             $brands = Yii::app()->db->createCommand()->from('{{brand}}')
//                 ->where('category_id = :catid And status = :status', array(':catid' => $v['id'], ':status' => Brand::STATUS_THROUGH))
//                 ->order('sort DESC')
//                 ->limit(6)
//                 ->queryAll(); // 关联品牌
//             $tree[$k]['brands'] = $brands;
//             $adverts = array();
//             $adp = Yii::app()->db->createCommand()->from('{{advert}}') // 关联广告位
//                 ->where('category_id = :catid And status = :status And type = :type And direction = :direction', array(
//                         ':catid' => $v['id'], ':status' => Advert::STATUS_ENABLE, ':type' => Advert::TYPE_IMAGE, ':direction' => Advert::DIRECTION_CATEGORY)
//                 )->queryRow();
//             if ($adp != false) { // 关联广告
//                 $adverts = Yii::app()->db->createCommand()->from('{{advert_picture}}')
//                     ->where('advert_id = :aid And status = :status ', array(':aid' => $adp['id'], ':status' => AdvertPicture::STATUS_ENABLE))
//                     ->order('sort DESC, start_time DESC, id DESC')->queryRow();
//             }
//             $tree[$k]['adverts'] = $adverts;
//         }
        Tool::cache(self::CACHEDIR)->set(self::CK_MAINCATEGORY, $tree); // 生成缓存
        return $tree;
    }

    /**
     * 查找推荐分类 只查找三级的推荐分类
     * @param int $tCid 顶级分类ID
     * @return array
     */
    public static function findRecommendCategory($tCid)
    {
        if (!$categorys = Tool::cache(self::CACHEDIR)->get(self::CK_ALLCATEGORY))
            $categorys = self::allCategoryData();
        if (!$tree = Tool::cache(self::CACHEDIR)->get(self::CK_TREECATEGORY))
            $tree = self::treeCategory();
        $rCategory = $childClass = array();
        if (is_numeric($tCid)) {
            if (isset($categorys[$tCid]) && $categorys[$tCid]['parent_id'] == '0') {
                if (isset($tree[$tCid]['childClass'])) {
                    $childs = array_keys($tree[$tCid]['childClass']);
                    foreach ($categorys as $id => $val) {
                        if ($val['parent_id'] != 0 && in_array($val['parent_id'], $childs) && $val['recommend'] == 1) {
                            $rCategory[$id] = $val;
                            if (count($rCategory) == 4)
                                break;
                        }
                    }
                }
            }
        }
        return $rCategory;
    }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Category the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * 取出所有顶级分类
	 * @param int $topId category_id
	 * @return array
	 */
	public static function getTop($topId = null,$flag=true)
	{
		if (empty($topId)) {
			$topData = $flag?Tool::cache(self::CACHEDIR)->get(self::CK_TOPDATA):array();
			if (empty($topData)) {
				$topData = Category::model()->findAll('parent_id=0 and status=' . Category::STATUS_ENABLE);
				Tool::cache(self::CACHEDIR)->set(self::CK_TOPDATA,$topData);
			}
			
		} else {
			$topData = Category::model()->findAll('parent_id=0 and id=' . $topId . ' and status=' . Category::STATUS_ENABLE);
		}
		return $topData;
	}
	
	/**
	 * 取出所有顶级分类或子分类
	 * @param int $topId category_id
	 * @return array
	 */
	public static function getCates($topId = null)
	{
		if (empty($topId)) {
			$topData = Tool::cache(self::CACHEDIR)->get(self::CK_TOPDATA);
			if (empty($topData)) {
				$topData = Category::model()->findAll('parent_id=0 and status=' . Category::STATUS_ENABLE);
				Tool::cache(self::CACHEDIR)->set(self::CK_TOPDATA,$topData);
			}
				
		} else {
			if (!$categorys = Tool::cache(self::CACHEDIR)->get(self::CK_ALLCATEGORY))
            $categorys = self::allCategoryData();
			$topData = array();
			foreach ($categorys as $c){
				if ($c['parent_id']==$topId && $c['status']==self::STATUS_ENABLE) {
					$topData[] =$c;
				}
			}
		}
		return $topData;
	}
	
	
	/**
	 * 根据分类id,查找对应分类下的子分类.拼装好数据,生成json格式.给商品添加第一步选择分类的js用
	 * @param int $cid 分类id
	 * @param bool $json 是否返回json格式的数据
	 * @return string
	 */
	public static function getCategory($cid, $json = true)
	{
		$cateData = Category::model()->findAllByAttributes(array('parent_id' => $cid, 'status' => Category::STATUS_ENABLE));
		if (!empty($cateData)) {
			$cateJson = array();
			foreach ($cateData as $k => $v) {
				$cateJson[$k]['id'] = $v->id;
				$cateJson[$k]['name'] = Yii::t('category', $v->name);
				$cateJson[$k]['type_id'] = $v->type_id;
			}
			return $json ? CJSON::encode($cateJson) : $cateJson;
		} else {
			return '';
		}
	}
	
	/**
	 * 获取分类名称
	 * @param int $name 分类ID
	 * @return string 返回分类名称
	 */
	public static function getCategoryName($id,$flag=true)
	{
		if($flag){
			if (!$categorys = Tool::cache(self::CACHEDIR)->get(self::CK_ALLCATEGORY))
				$categorys = self::allCategoryData();
			return isset($categorys[$id]['name']) ? $categorys[$id]['name'] : '';
		}else{
			$cate  =self::model()->findByPk($id);
			return isset($cate['name']) ? $cate['name'] : '';
		}
		
	}
	
	
	/**
	 * 获取分类名称
	 * @param int $name 分类ID
	 * @return string 返回分类名称
	 */
	public static function getCateName($id)
	{
		$cate  = self::model()->findByPk($id*1);
		return !empty($cate) ? $cate['name'] : '';
	}
	
	
	public function afterSave(){
		parent::afterSave();
		self::generateCategoryCacheFiles();
		return true;
	}
	
	public function afterDelete(){
		parent::afterDelete();
		self::generateCategoryCacheFiles();
		return true;
	}
	
	
}
