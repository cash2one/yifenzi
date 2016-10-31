<!DOCTYPE html>
<html>
    <head>
        <title>选择坐标</title>
        <script type="text/javascript">
            //document.domain = 'gatewang.com';
        </script>
        <link href="/css/reg.css" rel="stylesheet" type="text/css" />
        <script src="/js/jquery-1.5.1.min.js" type="text/javascript"></script>
        <script src="/js/jquery.artDialog.js?skin=default" type="text/javascript"></script>
        <script src="/js/iframeTools.source.js" type="text/javascript"></script>
        <script type="text/javascript" src="http://api.map.baidu.com/api?v=1.3"></script>
        <script type="text/javascript" src="http://api.map.baidu.com/library/CityList/1.2/src/CityList_min.js"></script>
        <script type="text/javascript" src="http://api.map.baidu.com/library/MarkerTool/1.2/src/MarkerTool_min.js"
        charset="gb2312"></script>
        <script type="text/javascript">
            var btnCancelClick = function() {
                art.dialog.close();
            }
        </script>
    </head>
    <body>
        <div class="main">
            <div class="head-title">
                <div class="t-left"></div>
                <div class="t-com ws">
                    <div class="t-sub"><input type="button" value="<?php echo Yii::t('partnerModule.map', '取消'); ?>" class="reg-sub" id="btnCancel" onclick="btnCancelClick()" /></div>
                    <div class="breadcrumbs"><?php echo Yii::t('partnerModule.map', '选取坐标'); ?></div>
                </div>
                <div class="t-right"></div>
            </div>
            <div class="com-box">
                <div class="border-info clearfix">
                    <?php echo Yii::t('partnerModule.map', '地址搜索'); ?>：<input type="text" id="txtAddr" class="text-input-bj  least" />
                    <input type="button" value="<?php echo Yii::t('partnerModule.map','搜索');?>" onclick="searchAddr()" class="reg-sub" />
                    <?php echo Yii::t('partnerModule.map', '当前城市'); ?>：<input type="text" id="txtCurrentCity" class="text-input-bj  least" disabled="disabled" />
                    <input type="button" value="<?php echo Yii::t('partnerModule.map', '修改'); ?>" onclick="changeCity()" class="reg-sub" />
                    <div style="width: 400px; height: 300px; margin-bottom: 20px; border: 1px solid gray;overflow-y: auto; display: none; position: absolute; z-index: 999; background-color: White" id="city_container"></div>
                    <input type="button" value="<?php echo Yii::t('partnerModule.map', '选取坐标'); ?>" onclick="markClick()" class="regm-sub" />
                </div>
                <div id="allmap" style="width: 684px; height: 400px; float: left"></div>
            </div>
        </div>
        <script type="text/javascript">
            var lng = '<?php echo $this->lng; ?>';
            var lat = '<?php echo $this->lat; ?>';
            
            function initMap() {
                createMap();
                setMapEvent();
                addMapControl();
                createCL();
                createMark();
            }

            function createMap() {
                var map = new BMap.Map("allmap"); // 在百度地图容器中创建一个地图
                var point = new BMap.Point(lng, lat); // 定义一个中心点坐标
                map.centerAndZoom(point, 12); // 设定地图的中心点和坐标并将地图显示在地图容器中
                window.map = map; // 将map变量存储在全局
            }
            function createCL() {
                // 创建CityList对象，并放在city_container节点内
                var myCl = new BMapLib.CityList({"container": "city_container", "map": map});
                // 给城市点击时，添加切换地图视野的操作
                myCl.addEventListener("cityclick", function(e) {
                    $('#txtCurrentCity').val(e.name);
                    $('#city_container').css('display', 'none');
                    // 由于此时传入了map对象，所以点击的时候会自动帮助地图定位，不需要下面的地图视野切换语句
                    // map.centerAndZoom(e.center, e.level);
                });
                window.myCL = myCl;
            }

            function createMark() {
                var mkrTool = new BMapLib.MarkerTool(map, {autoClose: true});
                mkrTool.addEventListener("markend", function(evt) {
                    var mkr = evt.marker;
                    var p = artDialog.open.origin;
                    if (p && p.onSelected) {
                        p.onSelected(mkr.point.lat, mkr.point.lng);
                    }
                    p.doClose();
                    //$('#POS_Y').val(mkr.point.lat);
                    //$('#POS_X').val(mkr.point.lng);
                });

                var marker1 = new BMap.Marker(new BMap.Point(lng, lat));  // 创建标注
                map.addOverlay(marker1); // 将标注添加到地图中
                window.mkrTool = mkrTool;
            }

            //地图事件设置函数：
            function setMapEvent() {
                map.enableScrollWheelZoom(); // 启用地图滚轮放大缩小
                map.enableKeyboard(); // 启用键盘上下左右键移动地图
            }

            //地图控件添加函数：
            function addMapControl() {
                //向地图中添加缩放控件
                var ctrl_nav = new BMap.NavigationControl({anchor: BMAP_ANCHOR_TOP_LEFT, type: BMAP_NAVIGATION_CONTROL_LARGE});
                map.addControl(ctrl_nav);
                //向地图中添加缩略图控件
                var ctrl_ove = new BMap.OverviewMapControl({anchor: BMAP_ANCHOR_BOTTOM_RIGHT, isOpen: 1});
                map.addControl(ctrl_ove);
                //向地图中添加比例尺控件
                var ctrl_sca = new BMap.ScaleControl({anchor: BMAP_ANCHOR_BOTTOM_LEFT});
                map.addControl(ctrl_sca);
            }

            initMap();
            var searchAddr = function() {
                var addr = $('#txtAddr').val();
                var local = new BMap.LocalSearch(map, {
                    renderOptions: {map: map}
                });
                local.search(addr);
            }

            var changeCity = function() {
                $('#city_container').css('display', 'block');
            }

            var markClick = function() {
                mkrTool.open();
            }
        </script>
    </body>
</html>

