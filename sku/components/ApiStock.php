<?php

/**
 * 库存api使用类
 * 
 * @author leo8705
 */
class ApiStock {

	
	const CODE_SUCCESS = 1;
	
	private static function _getAPIKeys($project=null){
		$arr = array(
				API_PARTNER_SUPER_MODULES_PROJECT_ID=>API_PARTNER_MODULES_KEY,
				API_PARTNER_VENDING_MACHINE_MODULES_PROJECT_ID=>STOCK_API_VENDING_MACHINE_MODULES_KEY,
				API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID=>STOCK_API_FRESH_MACHINE_MODULES_KEY,
				API_MACHINE_CELL_STORE_PROJECT_ID=>STOCK_API_MACHINE_CELL_STORE_KEY,
		);
		
// 		$arr_config = include_once ConfigDir.DS.'apiKeys.php';
// 		$arr = $arr_config['gw_project'];

		return isset($arr[$project])?$arr[$project]:$arr;
	}
	
	/**
	 * 生成加密串
	 *
	 * 检验规则是data参数值连成json字符串，加上密文私钥，生成md5
	 *
	 */
	private static function _createEncryption($json_data,$project = API_PARTNER_SUPER_MODULES_PROJECT_ID){
		$private_key = self::_getAPIKeys($project);
		return md5($json_data.$private_key);
	}
	
	/**
	 * 处理返回
	 * @param unknown $data
	 * @return multitype:boolean unknown Ambigous <string, unknown>
	 */
	private static function _deal($data){
		$rs = array();
		if (isset($data['resultCode']) && $data['resultCode']==self::CODE_SUCCESS) {
			$rs['result'] = true;
			$rs['data'] = isset($data['result'])?$data['result']:'';
		}else{
			$rs['result'] = false;
			$rs['errorCode'] = isset($data['resultCode'])?$data['resultCode']:'';
			$rs['msg'] = isset($data['resultDesc'])?$data['resultDesc']:'';
		}
		
		return $rs;
	}
	
	/**
	 * 创建初始库存
	 */
	public static function createStock($outlets,$target,$num,$project=API_PARTNER_SUPER_MODULES_PROJECT_ID){
		$url = STOCK_API_MAIN.'in';
		$post_data = array();
		$post_data['project'] = $project;
		$post_data['data']['outlets'] = $outlets;
		$post_data['data']['target'] = $target;
		$post_data['data']['num'] = $num;
		$post_data['data'] = CJSON::encode($post_data['data']);
		$encryptCode = self::_createEncryption($post_data['data'],$project);
		$post_data['encryptCode'] = $encryptCode;
		if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
			var_dump($url, $post_data,Tool::post($url, $post_data));exit();
		}
		$data = CJSON::decode(Tool::post($url, $post_data));

		return  self::_deal($data);
	}
	

	/**
	 * 获取列表商品的库存
	 */
	public static function goodsStockList($outlets,$target_arr,$project=API_PARTNER_SUPER_MODULES_PROJECT_ID){
		$url = STOCK_API_MAIN.'getByList';
		$post_data = array();
		$post_data['project'] = $project;
		$post_data['data']['outlets'] = $outlets;
		$post_data['data']['list'] = $target_arr;
		$post_data['data'] = CJSON::encode($post_data['data']);
		$encryptCode = self::_createEncryption($post_data['data'],$project);
		
		$post_data['encryptCode'] = $encryptCode;
		$rs = Tool::post($url, $post_data);
// 		var_dump($rs);
		$data = CJSON::decode($rs);
		$data = self::_deal($data);
		return  isset($data['data']['list'])?$data['data']['list']:array();
	}
	
	
	/**
	 * 获取列表商品的库存
	 */
	public static function goodsStockOne($outlets,$target,$project=API_PARTNER_SUPER_MODULES_PROJECT_ID){
		$url = STOCK_API_MAIN.'getOne';
		$post_data = array();
		$post_data['project'] = $project;
		$post_data['data']['outlets'] = $outlets;
		$post_data['data']['target'] = $target;
		$post_data['data'] = CJSON::encode($post_data['data']);
		$post_data['encryptCode'] = self::_createEncryption($post_data['data'],$project);
	
		return  CJSON::decode(Tool::post($url, $post_data));
	}
	
	
	
	/**
	 * 
	 * @param unknown $outlets   网点id
	 * @param unknown $target		目标id
	 * @param unknown $num		数量
	 * @return Ambigous <mixed, boolean, NULL, number, multitype:, stdClass, string, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> , multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> > >
	 */
	public static function stockIn($outlets,$target,$num,$project=API_PARTNER_SUPER_MODULES_PROJECT_ID){
		$url = STOCK_API_MAIN.'in';
		$post_data = array();
		$post_data['project'] = $project;
		$post_data['data']['outlets'] = $outlets;
		$post_data['data']['target'] = $target;
		$post_data['data']['num'] = $num;
		$post_data['data'] = CJSON::encode($post_data['data']);
		$post_data['encryptCode'] = self::_createEncryption($post_data['data'],$project);
		$data = self::_deal(CJSON::decode(Tool::post($url, $post_data)));
		return $data ;
	}
	
	/**
	 * 更新单个库存
	 * @param unknown $outlets   网点id
	 * @param unknown $target		目标id
	 * @param unknown $num		数量
	 * @return Ambigous <mixed, boolean, NULL, number, multitype:, stdClass, string, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> , multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> > >
	 */
	public static function stockSet($outlets,$target,$num,$project=API_PARTNER_SUPER_MODULES_PROJECT_ID){
		$url = STOCK_API_MAIN.'set';
		$post_data = array();
		$post_data['project'] = $project;
		$post_data['data']['outlets'] = $outlets;
		$post_data['data']['target'] = $target;
		$post_data['data']['num'] = $num;
		$post_data['data'] = CJSON::encode($post_data['data']);
		$post_data['encryptCode'] = self::_createEncryption($post_data['data'],$project);
// 		var_dump(Tool::post($url, $post_data));exit();
		$data = self::_deal(CJSON::decode(Tool::post($url, $post_data)));
		return $data ;
	}
	
	/**
	 * 批量更新库存
	 * @param unknown $outlets   网点id
	 * @param unknown $target		目标id
	 * @param unknown $num		数量
	 * @return Ambigous <mixed, boolean, NULL, number, multitype:, stdClass, string, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> , multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> > >
	 */
	public static function stockSetList($outlets,$targets,$nums,$project=API_PARTNER_SUPER_MODULES_PROJECT_ID){
		$url = STOCK_API_MAIN.'setList';
		$post_data = array();
		$post_data['project'] = $project;
		$post_data['data']['outlets'] = $outlets;
		$post_data['data']['targets'] = $targets;
		$post_data['data']['nums'] = $nums;
		$post_data['data'] = CJSON::encode($post_data['data']);
		$post_data['encryptCode'] = self::_createEncryption($post_data['data'],$project);
		$data = self::_deal(CJSON::decode(Tool::post($url, $post_data)));
		return $data ;
	}
	
	public static function stockOut($outlets,$target,$num,$project=API_PARTNER_SUPER_MODULES_PROJECT_ID){
		$url = STOCK_API_MAIN.'out';
		$post_data = array();
		$post_data['project'] = $project;
		$post_data['data']['outlets'] = $outlets;
		$post_data['data']['target'] = $target;
		$post_data['data']['num'] = $num;
		$post_data['data'] = CJSON::encode($post_data['data']);
		$post_data['encryptCode'] = self::_createEncryption($post_data['data'],$project);
		$data = self::_deal(CJSON::decode(Tool::post($url, $post_data)));
		return $data ;
	}
	
	/**
	 *
	 * @param unknown $outlets   网点id
	 * @param unknown $target		目标id
	 * @param unknown $num		数量
	 * @return Ambigous <mixed, boolean, NULL, number, multitype:, stdClass, string, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> , multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> > >
	 */
	public static function stockFrozen($outlets,$target,$num,$project=API_PARTNER_SUPER_MODULES_PROJECT_ID){
		$url = STOCK_API_MAIN.'frozen';
		$post_data = array();
		$post_data['project'] = $project;
		$post_data['data']['outlets'] = $outlets;
		$post_data['data']['target'] = $target;
		$post_data['data']['num'] = $num;
		$post_data['data'] = CJSON::encode($post_data['data']);
		$post_data['encryptCode'] = self::_createEncryption($post_data['data'],$project);
		$data = self::_deal(CJSON::decode(Tool::post($url, $post_data)));
		return $data ;
	}
	
	/**
	 *
	 * @param unknown $outlets   网点id
	 * @param unknown $target		目标id
	 * @param unknown $num		数量
	 * @return Ambigous <mixed, boolean, NULL, number, multitype:, stdClass, string, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> , multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> > >
	 */
	public static function stockFrozenList($outlets,$targets,$nums,$project=API_PARTNER_SUPER_MODULES_PROJECT_ID){
		$url = STOCK_API_MAIN.'frozenList';
		$post_data = array();
		$post_data['project'] = $project;
		$post_data['data']['outlets'] = $outlets;
		$post_data['data']['targets'] = $targets;
		$post_data['data']['nums'] = $nums;
           
		$post_data['data'] = CJSON::encode($post_data['data']);
		$post_data['encryptCode'] = self::_createEncryption($post_data['data'],$project);
              
                                       $arr = CJSON::decode(Tool::post($url, $post_data));
		$data = self::_deal($arr);             
		return $data ;
	}
	
	/**
	 *
	 * @param unknown $outlets   网点id
	 * @param unknown $target		目标id
	 * @param unknown $num		数量
	 * @return Ambigous <mixed, boolean, NULL, number, multitype:, stdClass, string, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> , multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> > >
	 */
	public static function stockFrozenRestore($outlets,$target,$num,$project=API_PARTNER_SUPER_MODULES_PROJECT_ID){
		$url = STOCK_API_MAIN.'frozenRestore';
		$post_data = array();
		$post_data['project'] = $project;
		$post_data['data']['outlets'] = $outlets;
		$post_data['data']['target'] = $target;
		$post_data['data']['num'] = $num;
		$post_data['data'] = CJSON::encode($post_data['data']);
		$post_data['encryptCode'] = self::_createEncryption($post_data['data'],$project);
		$data = self::_deal(CJSON::decode(Tool::post($url, $post_data)));
		return $data ;
	}
	
	/**
	 *
	 * @param unknown $outlets   网点id
	 * @param unknown $target		目标id
	 * @param unknown $num		数量
	 * @return Ambigous <mixed, boolean, NULL, number, multitype:, stdClass, string, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> , multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> > >
	 */
	public static function stockFrozenOut($outlets,$target,$num,$project=API_PARTNER_SUPER_MODULES_PROJECT_ID){
		$url = STOCK_API_MAIN.'frozenOut';
		$post_data = array();
		$post_data['project'] = $project;
		$post_data['data']['outlets'] = $outlets;
		$post_data['data']['target'] = $target;
		$post_data['data']['num'] = $num;
		$post_data['data'] = CJSON::encode($post_data['data']);
		$post_data['encryptCode'] = self::_createEncryption($post_data['data'],$project);
		$data = self::_deal(CJSON::decode(Tool::post($url, $post_data)));
		return $data ;
	}
	
	
	/**
	 *
	 * @param unknown $outlets   网点id
	 * @param unknown $target		目标id
	 * @param unknown $num		数量
	 * @return Ambigous <mixed, boolean, NULL, number, multitype:, stdClass, string, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> , multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> > >
	 */
	public static function stockFrozenOutList($outlets,$targets,$nums,$project=API_PARTNER_SUPER_MODULES_PROJECT_ID){
		$url = STOCK_API_MAIN.'frozenOutList';
		$post_data = array();
		$post_data['project'] = $project;
		$post_data['data']['outlets'] = $outlets;
		$post_data['data']['targets'] = $targets;
		$post_data['data']['nums'] = $nums;
		$post_data['data'] = CJSON::encode($post_data['data']);
		$post_data['encryptCode'] = self::_createEncryption($post_data['data'],$project);
		$data = self::_deal(CJSON::decode(Tool::post($url, $post_data)));
		return $data ;
	}
	

	/**
	 *
	 * @param unknown $outlets   网点id
	 * @param unknown $target		目标id
	 * @param unknown $num		数量
	 * @return Ambigous <mixed, boolean, NULL, number, multitype:, stdClass, string, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> , multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> > >
	 */
	public static function stockFrozenRestoreList($outlets,$targets,$nums,$project=API_PARTNER_SUPER_MODULES_PROJECT_ID){
		$url = STOCK_API_MAIN.'frozenRestoreList';
		$post_data = array();
		$post_data['project'] = $project;
		$post_data['data']['outlets'] = $outlets;
		$post_data['data']['targets'] = $targets;
		$post_data['data']['nums'] = $nums;
		$post_data['data'] = CJSON::encode($post_data['data']);
		$post_data['encryptCode'] = self::_createEncryption($post_data['data'],$project);
		$data = self::_deal(CJSON::decode(Tool::post($url, $post_data)));
		return $data ;
	}
	
	/**
	 * 批量归还库存
	 * @param unknown $outlets   网点id
	 * @param unknown $target		目标id
	 * @param unknown $num		数量
	 * @return Ambigous <mixed, boolean, NULL, number, multitype:, stdClass, string, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> , multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> > >
	 */
	public static function stockRestoreList($outlets,$targets,$nums,$project=API_PARTNER_SUPER_MODULES_PROJECT_ID){
		$url = STOCK_API_MAIN.'frozenRestoreList';
		$post_data = array();
		$post_data['project'] = $project;
		$post_data['data']['outlets'] = $outlets;
		$post_data['data']['targets'] = $targets;
		$post_data['data']['nums'] = $nums;
		$post_data['data'] = CJSON::encode($post_data['data']);
		$post_data['encryptCode'] = self::_createEncryption($post_data['data'],$project);
		$data = self::_deal(CJSON::decode(Tool::post($url, $post_data)));
		return $data ;
	}
	
	/**
	 * 批量更新变动库存
	 * @param unknown $outlets   网点id
	 * @param unknown $target		目标id
	 * @param unknown $num		数量
	 * @return Ambigous <mixed, boolean, NULL, number, multitype:, stdClass, string, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> , multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass, multitype:Ambigous <mixed, boolean, NULL, number, string, multitype:, stdClass> > >
	 */
	public static function stockChangeList($outlets,$targets,$nums,$project=API_PARTNER_SUPER_MODULES_PROJECT_ID){
		$url = STOCK_API_MAIN.'changeList';
		$post_data = array();
		$post_data['project'] = $project;
		$post_data['data']['outlets'] = $outlets;
		$post_data['data']['targets'] = $targets;
		$post_data['data']['nums'] = $nums;
		$post_data['data'] = CJSON::encode($post_data['data']);
		$post_data['encryptCode'] = self::_createEncryption($post_data['data'],$project);
		$data = self::_deal(CJSON::decode(Tool::post($url, $post_data)));
		return $data ;
	}
	
}
