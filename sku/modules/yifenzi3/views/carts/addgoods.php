<?php 
    foreach ($goodsData as $k=>$v){
        $json_v = json_encode($v);
        echo "<button onclick='addGoods(".$v['goods_id'].");'>".$v['goods_name']."</button>";
    }
?>
<script src="/js/jquery-1.7.2.js" type="text/javascript" charset="utf-8"></script>

<script>
	function addGoods(goods_id){
		if ( !goods_id ) return false;
		
		var reg = new RegExp("^[0-9]*$");
		if(!reg.test(goods_id)) return false;
		
		$.getJSON("/carts/ajaxadd?goods_id="+goods_id, function(json){
			  alert(json.msg);
			
		});
	}
</script>