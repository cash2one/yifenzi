  
$(function() {
                //导航点击切换
                $("#guide").find("a").click(function(){
                    $("#guide a").removeClass("active");
                    $(this).addClass("active");
                })
                show_time();
            });

            function show_time() {
                var num=$(".newList ul li").length;
                for(var i=0;i<num;i++){
                    if(!$(".newList ul li").eq(i).find(".time").hasClass('end')){
                        var time=$(".newList ul li").eq(i).find(".time").attr("date");
                        time = time.replace(/-/g,'/');

                        var time_start = new Date().getTime(); //设定当前时间
                        var time_end = new Date(time).getTime(); //设定目标时间
                        var time_distance = time_end - time_start;
                        
                        if(time_distance<0){
                            $(".newList ul li").eq(i).find(".time").empty().html("已揭晓").addClass('end');
                        }
                        else{
                            // 计算时间差
                            var time_distance = time_end - time_start;
                            // 时
                            var int_hour = Math.floor(time_distance / 3600000)
                            time_distance -= int_hour * 3600000;
                            // 分
                            var int_minute = Math.floor(time_distance / 60000)
                            time_distance -= int_minute * 60000;
                            // 秒
                            var int_second = Math.floor(time_distance / 1000)
                                // 时分秒为单数时、前面加零
                            if (int_hour > 99) {
                                int_hour = "99";
                            }
                            if (int_hour < 10) {
                                int_hour = "0" + int_hour;
                            }
                            if (int_minute < 10) {
                                int_minute = "0" + int_minute;
                            }
                            if (int_second < 10) {
                                int_second = "0" + int_second;
                            }
                            // 显示时间
                            $(".newList ul li").eq(i).find("#time_h").html(int_hour);
                            $(".newList ul li").eq(i).find("#time_m").html(int_minute);
                            $(".newList ul li").eq(i).find("#time_s").html(int_second);   
                        }
                    }
                }
                // 设置定时器
                setTimeout("show_time()", 1000);
            }

            function show_time_detail() {
                var timer=$("#timer");
              
                var time=timer.attr("date");

                time = time.replace(/-/g,'/');
                var time_start = new Date().getTime(); //设定当前时间
                var time_end = new Date(time).getTime(); //设定目标时间
                var time_distance = time_end - time_start;
                if(time_distance<0){
                    timer.empty().html("已揭晓");
                }
                else{
                    // 计算时间差
                    var time_distance = time_end - time_start;
                    // 时
                    var int_hour = Math.floor(time_distance / 3600000)
                    time_distance -= int_hour * 3600000;
                    // 分
                    var int_minute = Math.floor(time_distance / 60000)
                    time_distance -= int_minute * 60000;
                    // 秒
                    var int_second = Math.floor(time_distance / 1000)
                        // 时分秒为单数时、前面加零
                    if (int_hour > 99) {
                        int_hour = "99";
                    }
                    if (int_hour < 10) {
                        int_hour = "0" + int_hour;
                    }
                    if (int_minute < 10) {
                        int_minute = "0" + int_minute;
                    }
                    if (int_second < 10) {
                        int_second = "0" + int_second;
                    }
                    // 显示时间
                    timer.find("#time_h").html(int_hour);
                    timer.find("#time_m").html(int_minute);
                    timer.find("#time_s").html(int_second);   
                }
            
  
                // 设置定时器
                setTimeout("show_time_detail()", 1000);
            }