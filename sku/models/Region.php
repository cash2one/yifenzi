<?php

/**
 *  省份，城市，区县 模型类
 *  @author wanyun.liu <wanyun_liu@163.com>
 * This is the model class for table "{{region}}".
 *
 * The followings are the available columns in table '{{region}}':
 * @property integer $id
 * @property integer $parent_id
 * @property string $name
 * @property integer $depth
 * @property string $member_id
 * @property string $tree
 * @property string $lng
 * @property string $lat
 * @property string $area_code
 * @property string $zip_code
 * @property string $phone_code
 * @property string $mobile_code
 * @property string $description
 * @property string $short_name
 * @property integer $sort
 * @property integer $area
 */
class Region extends CActiveRecord {

	/**
	 * @return CDbConnection the database connection used for this class
	 */
	public function getDbConnection()
	{
		return Yii::app()->db;
	}
	
    /**
     * 总后台-代理管理-代理列表
     * @author LC
     */
    public $agent_gai_number, $agent_username, $agent_mobile, $agent_old_gai_number;
    public $isExport;   //是否导出excel
    public $exportPageName = 'page'; //导出excel起始
    public $exportLimit = 5000; //导出excel长度

    /**
     * 省份的 parent_id
     */

    const CACHEDIR = 'region';  // 缓存目录
    const PROVINCE_PARENT_ID = 1;
    const CK_ALLRegion = 'allRegion';       // 所有分类数据
    const CK_TREERegion = 'treeRegion';     // 分类树型数据
    const CK_RegionINDEX = 'RegionIndex';   // 分类索引数据
    const CK_MAINRegion = 'mainRegion';     // 首页分类数据
    const AREA_NORTH = 1;  //北方盖网通
    const AREA_SOUTH = 2;  //南方盖网通

    public $parentName;

    const DEPTH_ZERO = 0; // 代表国家
    const DEPTH_ONE = 1; // 代表省分
    const DEPTH_TWO = 2; // 代表城市
    const DEPTH_THREE = 3; // 代表区县

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{region}}';
    }

    public function relations() {
        return array(
            'parent' => array(self::BELONGS_TO, 'Region', 'parent_id'),
            'member' => array(self::BELONGS_TO, 'Member', 'member_id'),
        );
    }

    /**
     * 根据id 取城市或者省份名称
     *
     * @param 可以同时传入多个 id 参数
     * @return string 返回城市名或者省名
     */
    public static function getName(/* int [, $arg1...$argN] */) {
        $args = func_get_args();
        if (array_sum($args) == 0)
            return null;
        $nameArr = array();
        $c = new CDbCriteria();
        $c->addInCondition('id', $args);
        $find = self::model()->findAll($c);
        foreach ($find as $v) {
            $nameArr[] = Yii::t('region', $v->name);
        }
        return implode(' ', $nameArr);
    }

    /**
     * 根据地址，找到相应的地址id
     * //天津市 天津市所有县 宁河县 =>
     * @param string $address
     * @return array|bool
     */
    public static function getIds($address) {
        $addressArr = explode(' ', $address);
        if (count($addressArr) != 3)
            return false;
        $select = 'id,name,parent_id';
        $province = self::$db->createCommand()->select($select)
                        ->from('{{region}}')->where('name=:n', array(':n' => $addressArr[0]))->queryRow();
        $city = self::$db->createCommand()->select($select)->from('{{region}}')
                        ->where('name=:n and parent_id=:id', array(':n' => $addressArr[1], ':id' => $province['id']))->queryRow();
        $distinct = self::$db->createCommand()->select($select)->from('{{region}}')
                        ->where('name=:n and parent_id=:id', array(':n' => $addressArr[2], ':id' => $city['id']))->queryRow();
        if ($province && $city && $distinct) {
            return array($province['id'], $city['id'], $distinct['id']);
        }
        return false;
    }

    /**
     * 根据id组,获取地区名称
     * @param string $ids 地区id组，例如： 1,2,3,4
     * @param string $separator  分隔符
     * @return array
     */
    public static function getNameArray($ids, $separator = ',') {
        $c = new CDbCriteria();
        $c->addInCondition('id', explode($separator, $ids));
        $find = self::model()->findAll($c);
        $nameArr = array();
        foreach ($find as $v) {
            $nameArr[$v->id] = Yii::t('region', $v->name);
        }
        return $nameArr;
    }

    /**
     * 生成城市数组,为下拉列表用
     */
    public static function cityNameArr() {
        $models = self::model()->findAll('parent_id>1');
        $cityArr = array();
        foreach ($models as $v) {
            $cityArr[$v->id] = Yii::t('region', $v->name);
        }
        return $cityArr;
    }

    /**
     * 根据省份\城市 parent_id 获取下面的区域数据
     * @param int $id
     * @return array 
     */
    public static function getRegionByParentId($id) {
        if (empty($id))
            return array();
        $models = self::model()->findAll('parent_id = ' . $id);
        $cityArr = array();
        foreach ($models as $v) {
            $cityArr[$v->id] = Yii::t('region', $v->name);
        }
        return $cityArr;
    }

    /**
     * 搜索城市名称
     * @param string $city 城市名称
     * @param mixed $select @use CDbCommand->select() 
     * @return array 返回匹配名称的数据
     */
    public function searchCityName($city, $select = '*') {
        $command = Yii::app()->db->createCommand();
        return $command->select($select)->from('{{region}}')->where(array('like', 'name', "%$city%"))->queryAll();
    }

    public function rules() {
        return array(
            array('name, pinyin, lng, lat', 'required'),
//            array('name', 'unique'),  //不同地区下面，可能会有相同的名称
            array('parent_id, sort, depth', 'numerical', 'integerOnly' => true),
            array('name, parentName, alias', 'length', 'max' => 128),
            array('description', 'length', 'max' => 256),
            array('pinyin', 'match', 'pattern' => '/^[A-Z]{1}/', 'message' => '请输入名称中第一个字的首字母（大写）'),
            array('name', 'safe', 'on' => 'search'),
        );
    }

    public function attributeLabels() {
        return array(
            'id' => Yii::t('region', '主键'),
            'parent_id' => Yii::t('region', '父级'),
            'parentName' => Yii::t('region', '父级名称'),
            'name' => Yii::t('region', '名称'),
            'depth' => Yii::t('region', '深度'),
            'area_code' => Yii::t('region', '地区编码'),
            'sort' => Yii::t('region', '排序'),
            'lng' => Yii::t('region', '经度'),
            'lat' => Yii::t('region', '纬度'),
            'zip_code' => Yii::t('region', '邮政编码'),
            'phone_code' => Yii::t('region', '电话代码'),
            'mobile_code' => Yii::t('region', '移动电话代码'),
            'description' => Yii::t('region', '描述'),
            'agent_gai_number' => Yii::t('region', '会员编号'),
            'agent_username' => Yii::t('region', '代理用户名'),
            'agent_mobile' => Yii::t('region', '代理手机号'),
            'agent_old_gai_number' => Yii::t('region', '旧会员编号'),
            'alias' => Yii::t('region', '别名'),
            'pinyin' => Yii::t('region', '首字母（大写）'),
        );
    }

    /**
     * 总后台列表
     * @return \CActiveDataProvider
     * @author wanyun.liu <wanyun_liu@163.com>
     */
    public function search() {
        $criteria = new CDbCriteria;
        $criteria->compare('name', $this->name, true);
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * 保存前的操作
     * 获取当前数据的层级
     * @return boolean
     * @author wanyun.liu <wanyun_liu@163.com>
     */
    public function beforeSave() {
        if (parent::beforeSave()) {
            if ($this->isNewRecord)
                $this->_setDepthAndTree();
            return true;
        } else
            return false;
    }

    /**
     * 创建地区数据时
     * 工获取tree数据
     * 获取当前数据所在层级
     * @return int
     * @author wanyun.liu <wanyun_liu@163.com>
     */
    private function _setDepthAndTree() {
        if (!$this->parent_id) {
            $this->depth = self::DEPTH_ZERO;
            $this->tree = $this->primaryKey;
        } else {
            $parent = $this->_getParent($this->parent_id);
            $this->depth = $parent->depth + 1;
            if ($this->depth == self::DEPTH_ONE)
                $this->tree = $this->primaryKey . '|' . $this->parent_id;
            elseif ($this->depth == self::DEPTH_TWO)
                $this->tree = $this->primaryKey . '|' . $this->parent_id . '|' . $parent->parent_id;
            else {
                $grandpa = $this->_getParent($parent->parent_id);
                $this->tree = $this->primaryKey . '|' . $this->parent_id . '|' . $parent->parent_id . '|' . $grandpa->parent_id;
            }
        }
    }

    /**
     * 创建地区时获取父级数据
     * @param int $id
     * @return CModel
     * @author wanyun.liu <wanyun_liu@163.com>
     */
    private function _getParent($id) {
        return $this->find(array(
                    'select' => 'id, depth, parent_id',
                    'condition' => 'id=:id',
                    'params' => array(':id' => $id),
        ));
    }

    /**
     * 获取指定父类ID分类树数据
     * @param int $id   可指定父类ID，$id为Null则查询所有分类， "0" :则获取顶级分类
     * @return array
     */
    public function getTreeData($id = null) {
        $data = array();
        $command = Yii::app()->db->createCommand();
        if ($id !== null) // 如指定父类ID，则加条件
            $command->where('t.parent_id = :parent_id', array('parent_id' => intval($id)));
        $record = $command->from($this->tableName() . ' as t') // type.name as typename,
                ->select('t.id, t.name as text, t.parent_id, t.sort, (select b.id from ' . $this->tableName() . ' as b where b.parent_id = t.id limit 1) as state') // name 字段别名了 text
                ->order('sort desc, id asc')
                ->queryAll();
        foreach ($record as $k => $v) {
            $data[$k] = $v;
            $data[$k]['state'] = is_null($v['state']) ? 'open' : 'closed';
        }
        return $data;
    }

    /**
     * 所有分类数据
     * @param boolean $generate 是否生成缓存，默认为 true
     * @return array
     */
    public static function allregionData($generate = true) {
        $data = array();
        $regions = Yii::app()->db->createCommand()->from('{{region}}')
                        ->order('sort DESC, id ASC')->queryAll();
        foreach ($regions as $val) // 这里键原有的键值替换为分类自身ID
            $data[$val['id']] = $val;
        if ($generate === true) // 生成缓存
            Tool::cache(self::CACHEDIR)->set(self::CK_ALLRegion, $data);
        return $data;
    }

    /**
     * 树型分类数据
     * @param boolean $generate 是否生成缓存，默认为 true
     * @return array
     */
    public static function treeregion($generate = true) {
        if (!$regions = Tool::cache(self::CACHEDIR)->get(self::CK_ALLRegion))
            $regions = self::allregionData();
        $tree = array();
        $tempData = $regions;
        foreach ($regions as $val) {
            if (isset($tempData[$val['parent_id']])) {
                $tempData[$val['parent_id']]['childClass'][$val['id']] = &$tempData[$val['id']];
            } else {
                if ($val['parent_id'] == '0') {
                    $tree[$val['id']] = &$tempData[$val['id']];
                }
            }
        }
        if ($generate === true)
            Tool::cache(self::CACHEDIR)->set(self::CK_TREERegion, $tree);
        return $tree;
    }

    /**
     * 分类索引（包含自身、父级、爷级的分类数据）
     * @param boolean $generate 是否生成缓存，默认为 true
     * @return array 数据中的type代表分类层次 1：顶级分类、2：父级分类、3：三级分类
     */
    public static function regionIndexing($generate = true) {
        if (!$region = Tool::cache(self::CACHEDIR)->get(self::CK_ALLRegion))
            $region = self::allregionData();
        $breadcrumbs = array();
        foreach ($region as $k => $v) {
            // 自身分类
            $breadcrumbs[$k]['id'] = $v['id'];
            $breadcrumbs[$k]['name'] = $v['name'];
            $breadcrumbs[$k]['type'] = 1;
            if ($v['parent_id'] == 0)
                continue;
            // 获取父级
            if (isset($region[$v['parent_id']])) {
                $parent = $region[$v['parent_id']];
                $breadcrumbs[$k]['parentId'] = $parent['id'];
                $breadcrumbs[$k]['parentName'] = $parent['name'];
                $breadcrumbs[$k]['type'] = 2;
                if ($parent['parent_id'] == 0)
                    continue;
                // 获取爷级
                if (isset($region[$parent['parent_id']])) {
                    $grandpa = $region[$parent['parent_id']];
                    $breadcrumbs[$k]['grandpaId'] = $grandpa['id'];
                    $breadcrumbs[$k]['grandpaName'] = $grandpa['name'];
                    $breadcrumbs[$k]['type'] = 3;
                }
            }
        }
        if ($generate === true)
            Tool::cache(self::CACHEDIR)->set(self::CK_RegionINDEX, $breadcrumbs);
        return $breadcrumbs;
    }

    /**
     * 查找所属所有子类元素节点
     * @param mixed $regionId 分类ID
     * @return array
     */
    public static function findChildregionElement($regionId) {
        if (!$regions = Tool::cache(self::CACHEDIR)->get(self::CK_ALLRegion))
            $regions = self::allregionData();
        $data = array();
        if (isset($regions[$regionId])) {
            if ($regions[$regionId]['parent_id'] == 0) {
                $data = self::treeregion($regionId, false);
            } else {
                foreach ($regions as $k => $item) {
                    if ($k == $regionId)
                        $data[$regionId] = $item;
                    if ($item['parent_id'] == $regionId)
                        $data[$regionId]['childClass'][] = $item;
                }
            }
        }
        return $data;
    }

    /**
     * 生成所有分类相应缓存
     */
    public static function generateregionCacheFiles() {
        self::allregionData(); // 生成分类数据缓存文件
        self::treeregion(); // 生成树形分类数据文件
//     	self::regionIndexing(); // 生成分类索引
    }

    /**
     * 保存后的操作
     */
    public function afterSave() {
        parent::afterSave();
        // 生成分类缓存
        self::generateregionCacheFiles();
        return true;
    }

    /**
     * 获取顶级分类
     * @return array
     */
    public static function getTopregion() {
        if (!$regions = Tool::cache(self::CACHEDIR)->get(self::CK_ALLRegion))
            $regions = self::allregionData();
        $topregion = array();
        foreach ($regions as $val) {
            if ($val['parent_id'] == 0)
                $topregion[$val['id']] = $val;
        }
        return $topregion;
    }

    /**
     * 获取分类名称
     * @param int $name 分类ID
     * @return string 返回分类名称
     */
    public static function getregionName($id) {
        if (!$regions = Tool::cache(self::CACHEDIR)->get(self::CK_ALLRegion))
            $regions = self::allregionData();
        return isset($regions[$id]) ? $regions[$id]['name'] : '';
    }

    /**
     * 根据分类id,查找对应分类下的子分类.拼装好数据,生成json格式.给商品添加第一步选择分类的js用
     * @param int $cid 分类id
     */
    public static function getregion($cid) {
        $cateData = region::model()->findAll('parent_id=:cid', array(':cid' => $cid));
        if (!empty($cateData)) {
            $cateJson = array();
            $i = 0;
            foreach ($cateData as $v) {
                $cateJson[$i]['id'] = $v->id;
                $cateJson[$i]['name'] = $v->name;
                $i++;
            }
            return CJSON::encode($cateJson);
        } else {
            return '';
        }
    }

    /**
     * 取出所有顶级分类
     */
    public static function getTop() {
        $topData = Region::model()->findAll('parent_id = 0');
        return $topData;
    }

    /**
     * 总后台-代理管理-代理列表
     * @author LC
     */
    public function searchAgent() {
        $criteria = new CDbCriteria;
        $member_table = Member::model()->tableName();
        $criteria->select = 't.id,t.parent_id,t.name,t.depth,m.gai_number as agent_gai_number,m.username as agent_username,m.mobile as agent_mobile';
        $criteria->join = 'left join ' . $member_table . ' m on m.id = t.member_id';
//        $criteria->with = array(
//            'member' => array('select' => 'gai_number,username,mobile')
//        );
        
        $criteria->addCondition('t.depth>0');
        $criteria->compare('t.name', $this->name, true);
        $criteria->compare('m.gai_number', $this->agent_gai_number);


        $pagination = array();
        if (!empty($this->isExport)) {
            $pagination['pageVar'] = $this->exportPageName;
            $pagination['pageSize'] = $this->exportLimit;
        }

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => $pagination,
        ));
    }

    /**
     * 组织地区名称
     * @param type $id
     * @param type $separator
     * @return string
     */
    public static function organizeRegionName($id, $separator = '') {
        $text = '';
        if (($model = self::model()->findByPk($id))) {
            $text = $model->name;
            if ($model->parent) {
                $text = $model->parent->name . $separator . $text;
                if ($model->parent->parent) {
                    if ($model->parent->parent->parent_id != 0)
                        $text = $model->parent->parent->name . $separator . $text;
                }
            }
        }
        return $text;
    }

    /**
     * 总后台-代理管理-代理列表-代理的级别
     */
    public static function getAgentLevel($depth) {
        $data = array(
            1 => '省级/直辖市',
            2 => '市级',
            3 => '地区/县级',
        );
        return $data[$depth];
    }

    /**
     * 获取省级地区的简称
      Array
      (
      [2] => 河北
      [3] => 山西
      [4] => 内蒙古……
     * @return array|mixed
     */
    public static function getProvinceShort() {
        $region = Tool::cache(self::CACHEDIR)->get('provinceShort');
        if (!$region) {
            $data = Region::model()->findAllByAttributes(array('parent_id' => 1));
            $region = array();
            foreach ($data as $v) {
                $province = str_replace('市', '', $v->short_name);
                $region[$v->id] = str_replace('省', '', $province);
            }
            Tool::cache(self::CACHEDIR)->set('provinceShort', $region);
        }
        return $region;
    }

    /**
     * 获取省市级城市简称
     * @return array|mixed
     */
    public static function getCityShort() {
        $region = Tool::cache(self::CACHEDIR)->get('cityShort');
        if (!$region) {
            $sql = "  SELECT id,short_name FROM {{region}}
                WHERE
                    (
                        (
                            parent_id IN (
                                SELECT
                                    id
                                FROM
                                    {{region}}
                                WHERE
                                    parent_id = 1
                            )
                            AND parent_id NOT IN (4, 5, 12, 25)
                        )
                        OR id IN (4, 5, 12, 25)
                    )
                AND short_name NOT LIKE '%行政单位'";
            $data = Yii::app()->db->createCommand($sql)->queryAll();
            $region = array();
            foreach ($data as $v) {
                $region[$v['id']] = $v['short_name'];
            }
            Tool::cache(self::CACHEDIR)->set('cityShort', $region);
        }
        return $region;
    }

    /**
     * 获取省市两级结构数据，用于商品详情页，配送地区选择
     * @return array|mixed
     */
    public static function getRegion2() {
        $region = Tool::cache(self::CACHEDIR)->get('getRegion2');
        if (!$region) {
            $region = array();
            foreach (self::getProvinceShort() as $k => $v) {
                $children = self::getRegionByParentId($k);
                $region[] = array('province_id' => $k, 'province_name' => $v, 'cities' => $children);
            }
            Tool::cache(self::CACHEDIR)->set('getRegion2', $region);
        }
        return $region;
    }

    /**
     * 获取地区上一级别名称，返回父级加本地区全称
     */
    public static function actionGetAreaName($parent_id, $depth, $name) {
        if ($depth > 1) {
            $rs = Yii::app()->db->createCommand('select name from {{region}} where id =' . $parent_id)->queryRow();
            return $rs['name'] . $name;
        } else {
            return $name;
        }
    }

    
     /**
      * 盖付通
     * 所有分类数据
     * @param boolean $generate 是否生成缓存，默认为 true
     * @return array
     */
    public static function allregionList($generate = true) {
        $data = array();
        $regions = Yii::app()->db->createCommand()->from('{{region}}')
                        ->queryAll();
        foreach ($regions as $val) // 这里键原有的键值替换为分类自身ID
            $data[$val['id']] = $val;
        if ($generate === true) // 生成缓存
            Tool::cache(self::CACHEDIR)->set(self::CK_ALLRegion, $data);
        return $data;
    }
    
    /**
     * 盖付通
     * 查找所属所有子类元素节点
     * @param mixed $regionId 分类ID
     * @return array
     */
    public static function findChildregion($regionId) {
        if (!$regions = Tool::cache(self::CACHEDIR)->get(self::CK_ALLRegion))
            $regions = self::allregionList();
        $data = array();
        if (isset($regions[$regionId])) {
                foreach ($regions as $item) {                 
                    if ($item['parent_id'] == $regionId)
                        $data[] = $item;  
            }
        }
        return $data;
    }
}
