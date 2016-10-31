<?php
/**
 * 获取各个api接口参数的工具类
 * 
 * @author leo8705
 */
class APost extends CComponent{

	/**
	 * 获取post提交参数
	 * @param string $name
	 * @param string $defaultValue
	 * @param boolean $filter
	 * @return string|array
	 */
	static  function getPost($name, $defaultValue = null, $filter = true) {
		$data = Yii::app()->request->getPost($name, $defaultValue);
		if (!$filter)
			return $data;
		return self::magicQuotes($data);
	}
	
	/**
	 * 转义数据
	 * @param string|array $var
	 * @return string|array
	 */
	static  function magicQuotes(&$var) {
		if (!get_magic_quotes_gpc()) {
			if (is_array($var)) {
				foreach ($var as $k => $v)
					$var[$k] = self::magicQuotes($v);
			}
			else
				$var = addslashes($var);
		}
		return $var;
	}
	
	/**
	 * 获取创建库存参数
	 * @return multitype:Ambigous <string, multitype:> Ambigous <string, multitype:, multitype:s t r i n g a y , unknown, mixed>
	 */
	static function getStockCreateData(){
		$data = array();
		$data['project'] = self::getPost('project');
		$data['type'] = self::getPost('type');
		$data['member_id'] = self::getPost('member_id');
		$data['target'] = self::getPost('target');
		$data['stock'] = self::getPost('stock');
		$data['outlets'] = self::getPost('outlets');
		$data['stock'] = self::getPost('stock');
		return $data;
	}
	
	/**
	 * 获取列表创建库存参数
	 * @return multitype:Ambigous <string, multitype:> Ambigous <string, multitype:, multitype:s t r i n g a y , unknown, mixed>
	 */
	static function getStockCreateListData(){
		$data = array();
		$data['project'] = self::getPost('project');
		$data['list'] = stripcslashes(self::getPost('list'));
		return $data;
	}
	
	
	/**
	 * 获取添加库存参数
	 * @return multitype:Ambigous <string, multitype:> Ambigous <string, multitype:, multitype:s t r i n g a y , unknown, mixed>
	 */
	static function getStockChangeData(){
		$data = array();
		$data['project'] = self::getPost('project');
		$data['outlets'] = self::getPost('outlets');
		$data['target'] = self::getPost('target');
		$data['num'] = abs(self::getPost('num'));
		return $data;
	}
	
	
	/**
	 * 获取冻结库存参数
	 * @return multitype:Ambigous <string, multitype:> Ambigous <string, multitype:, multitype:s t r i n g a y , unknown, mixed>
	 */
	static function getStockFronzenData(){
		$data = array();
		$data['project'] = self::getPost('project');
		$data['outlets'] = self::getPost('outlets');
		$data['target'] = self::getPost('target');
		$data['num'] = abs(self::getPost('num'));
		return $data;
	}
	
	/**
	 * 列表库存数据
	 * @return multitype:Ambigous <string, multitype:> Ambigous <string, multitype:, multitype:s t r i n g a y , unknown, mixed>
	 */
	static function getStockListData(){
		$data = array();
		$data['project'] = self::getPost('project');
		$data['outlets'] = self::getPost('outlets');
		$data['list'] = stripcslashes(self::getPost('list'));		//反转义
		return $data;
	}
	
	/**
	 * 列表库存数据
	 * @return multitype:Ambigous <string, multitype:> Ambigous <string, multitype:, multitype:s t r i n g a y , unknown, mixed>
	 */
	static function getOneStockData(){
		$data = array();
		$data['project'] = self::getPost('project');
		$data['outlets'] = self::getPost('outlets');
		$data['target'] = self::getPost('target');
		return $data;
	}
	
	/**
	 * 获取数据
	 * @return multitype:Ambigous <string, multitype:> Ambigous <string, multitype:, multitype:s t r i n g a y , unknown, mixed>
	 */
	static function getData(){
		return CJSON::decode(self::getPost('data'));
	}
	
	
	
	
	
	
	
	
}

?>