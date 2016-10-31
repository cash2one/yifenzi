<body>
    <header>
        <h2>计算详情</h2>
        <a href="javascript:history.go(-1);" class="goback_btn"></a>
    </header>
	
	<div class="container">
    <div class="warpbg">
        <div class="countList">
            <p class="countTop">
                <span>时间详情</span>
                <span>换算数据</span>
                <span>会员账号</span>
            </p>
            <ul>
                <li>
                    <p class="countTop">
                        <span><?php echo date('Y-m-d H:i:s',$orderNper['sumlotterytime'])?></span>
                        <span></span>
                        <span>
                            <?php 
                                $member = Member::getMemberInfo($orderNper['member_id']);
                                if(isset($member['gai_number']) && $member['gai_number']) echo substr_replace($member['gai_number'],'****',4,4);
                                else echo '用户未设置用户名';
                            ?>
                        </span>
                    </p> 
                    <div class="countDeta">
                        <p class="time">截止揭晓时间（<?php echo date('Y-m-d H:i:s',$orderNper['sumlotterytime'])?>）<br>最后100条全站购买纪录</p>
                        <?php 
                            $record = json_decode($orderNper['order_id_log']);
                            $hisdata = $record->hisdata;
                            $yffdata = $record->yffdata;
                            $sumhisdata = $record->sumhisdata;
                            if (isset($record->allusername)){
                                $allusername = $record->allusername;
                            }

                        ?>
                        <ul>
                            <?php foreach($yffdata as $k=>$v):?>
                                <li><span><?php echo $v . ' ' . $hisdata->$k?></span><span><?php echo $sumhisdata->$k ?></span><span>
                                        <?php
                                            if (isset($allusername)){
                                                echo $allusername->$k ? substr_replace($allusername->$k,'****',4,4):'未设置';
                                            }else{
                                                echo '未设置';
                                            }
                                        ?>
                                    </span></li>
                            <?php endforeach;?>
                        </ul>
                        <div class="result">
                            
                            <p>取以上数值结果得</p>
                            <p>1. 求和: <?php echo $record->formuladata->h_i_s_sum?>(上面100条数据的时间之和)</p>
                            <p>2. 取余: （<?php echo $record->formuladata->h_i_s_sum .' % '. $record->formuladata->nperall . ' ）+ '. 10000001 ?></p>
                            <p>3. 结果 <?php echo $record->formuladata->lucky_code . '+' . $record->formuladata->winning_code . ' = ' . $record->formuladata->lucky_code+$record->formuladata->winning_code?></p>
                        </div>
                    </div>
                </li>
            </ul>
        </div>   
        <div class="h60"></div>
    </div>
    </div>
    <script>
        //导航点击切换
        $("#guide").find("a").click(function () {
            $("#guide a").removeClass("active");
            $(this).addClass("active");
        })
        $(".countList ul li").click(function () {
            $(this).find(".countDeta").toggle();
        })
    </script>
</body>