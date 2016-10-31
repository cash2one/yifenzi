// JavaScript Document
function getObject(objectId) { 
 if(document.getElementById  &&  document.getElementById(objectId)) { 
 return document.getElementById(objectId); 
 } 
 else if (document.all  &&  document.all(objectId)) { 
 return document.all(objectId); 
 } 
 else if (document.layers  &&  document.layers[objectId]) { 
 return document.layers[objectId]; 
 } 
 else { 
 return false; 
 } 
} 

function showHide(e,objname){     
    var obj = getObject(objname);
    if(!obj) return true;
    if(obj.style.display == "none"){ 
        obj.style.display = "block";
		e.className="on";  
    }else{ 
        obj.style.display = "none"; 
        e.className="bk";
    } 
} 


/*产品筛选*/
/*关闭按钮
function turnoff(obj1,obj2){
document.getElementById(obj1).style.display="block";
document.getElementById(obj2).style.display="none";
}*/
var abc = [];
$(function(){
	//选中filter下的所有a标签，为其添加hover方法，该方法有两个参数，分别是鼠标移上和移开所执行的函数。
	$("#filter .filterCon a").hover(function(){
		$(this).addClass("seling");
	},function(){
		$(this).removeClass("seling");
	});

	//选中filter下所有的dt标签，并且为dt标签后面的第一个dd标签下的a标签添加样式seled。(感叹jquery的强大)
	$("#filter .filterCon dt+dd a").attr("class", "seled"); /*注意：这儿应该是设置(attr)样式，而不是添加样式(addClass)，
	不然后面通过$("#filter a[class='seled']")访问不到class样式为seled的a标签。*/       

	//为filter下的所有a标签添加单击事件
	$("#filter .filterCon a").click(function(){
		$(this).parents("dl").children("dd").each(function(){
			$(this).children("a").removeClass("seled");
		});
	
		$(this).attr("class", "seled");
		var needhide = $(this);
		needhide.parentsUntil(".filterItem ").parent().hide(".filterItem");
		abc.push(needhide);
		var val = $(this).html().replace(/ /g, "kongge");
		var condition = '<a class="inbtn pzbtn" rel="'+$(this).html()+'"><span onclick=deleteC("'+val+'")>'+$(this).html()+'</span></a>';
		$("#condition").append(condition);
		// alert(RetSelecteds()); //返回选中结果
	});
// alert(RetSelecteds()); //返回选中结果
});

function deleteC(v){
	var val = v.replace(/kongge/g, " ");
	$("#condition").find("a[rel='"+val+"']").remove();
	for(var i = 0; i<abc.length; i++){
		if(abc[i].html() == val){
			abc[i].parentsUntil("dl").parent().show();
			abc.splice(i, 1);
			i--;
		}else{
			abc[i].parentsUntil("dl").parent().hide();
		}
	}
}

function RetSelecteds(){
	var result = "";
	$("#filter .filterCon a[class='seled']").each(function(){
		result += $(this).html()+"\n";
	});
	return result;
}


//商家首页广告轮播效果
/*
var currentindex=1;
$(".bannerSlide02").css("background-color",$("#flash1").attr("name"));
function changeflash(i){	
	currentindex=i;
	for(j=1;j<=4;j++){
		if(j==i){
			$("#flash"+j).fadeIn("normal");
			$("#flash"+j).css("display","block");
			$("#ban"+j).removeClass();
			$("#ban"+j).addClass("curr");
			$(".bannerSlide02").css("background-color",$("#flash"+j).attr("name"));
		}else{
			$("#flash"+j).css("display","none");
			$("#ban"+j).removeClass();
			$("#ban"+j).addClass("no");
		}
	}
}
function startAm(){
	timerID = setInterval("timer_tick()",3000);
}
function stopAm(){
	clearInterval(timerID);
}
function timer_tick(){
	currentindex=currentindex>=4?1:currentindex+1;
	changeflash(currentindex);
}
$(document).ready(function(){
	$(".bannerTit a").mouseover(function(){
		stopAm();
	}).mouseout(function(){
		startAm();
	});
	startAm();
});
*/