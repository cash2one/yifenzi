function transformBox(obj,value,has3d){
		var transl=has3d?"translate3d(0,"+value+"px,0)":"translate(0,"+value+"px)";
		obj.css({'-webkit-transform':transl});
	}

	function getTransY(obj){
		var transform=obj.css("-webkit-transform"),
			trans=transform.match(/\((.+)\)/),
			transY=0;
		if(trans){
			var transArr=trans[1].split(","),
				len=transArr.length;
			transY=transArr[len-2].replace("px","");
		}
		return Number(transY);
	}

	args={
		iniT:400,
		iniAngle:180,
		sCallback:function(tPoint){
			var _this=tPoint.self,
				_inner=_this.children();
			tPoint.setAttr("startOffset",getTransY(_inner));
		},
		mCallback:function(tPoint){
			var _this=tPoint.self,
				_inner=_this.children(),
				innerH=_inner.height();
			var transY=getTransY(_inner);
			var offset=tPoint.mY+tPoint.startOffset;
			if(Math.abs(offset)>innerH-_this.height()+40){
				offset=-(innerH-_this.height()+40);	
			}
			if(offset>0){
				offset=0;
			}
			//offset=tPoint.mY>0?offset/1.2:offset*1.2;
			transformBox(_inner,offset,tPoint.has3d);
			
		}
	}
	
	
	$("#touchMenu").Swipe(args);