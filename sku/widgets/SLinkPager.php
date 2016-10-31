<?php

/**
 * 自定义分页widget, 传参控制分页
 * 可以控制 上一页和下一页  可以控制 分页 + 跳转页
 * @author wenhao.li
 */
class SLinkPager extends CLinkPager {
    /**
     * @var bool 跳转页
     */
    public $showJump  = true;//是否需要跳转页
    public $onlyPN    = false;//是否只显示上一页和下一页
	public $showTotal = true;//是否显示总页数
	public $middle    = true;//是否居中显示
	public $jump='';

    public function init() {
        if ($this->nextPageLabel === null)
            $this->nextPageLabel = Yii::t('page','下页');
        if ($this->prevPageLabel === null)
            $this->prevPageLabel = Yii::t('page','上页');
	    if ($this->firstPageLabel === null)
            $this->firstPageLabel = Yii::t('page','首页');
        if ($this->lastPageLabel === null)
            $this->lastPageLabel = Yii::t('page','尾页');
        if ($this->header === null)
//            $this->header = Yii::t('yii', 'Go to page: ');
            $this->header = '';

        if (!isset($this->htmlOptions['id']))
            $this->htmlOptions['id'] = $this->getId();
        if (!isset($this->htmlOptions['class']))
            $this->htmlOptions['class'] = 'yiiPager';
    }

    public function run() {
        $this->registerClientScript();
        $buttons = $this->createPageButtons();
        if (empty($buttons)) return;
		
        echo $this->header;
        echo CHtml::tag('ul', $this->htmlOptions, implode("\n", $buttons));
        echo $this->footer.($this->middle && $this->onlyPN == false ? '<script>$(document).ready( function (e){var yiiPageerW=parseInt($(".pageList").find(".yiiPageer").css("width")); var pageListW=parseInt($(".pageList").css("width")); var num=(pageListW-yiiPageerW)/2;$(".pageList").css("padding-left",num);});</script>' : '');
    }

    public function registerClientScript() {
        $this->cssFile = false;
    }

    /**
     * 创建分页链接
     * @return array
     */
    protected function createPageButtons() {

        if ($this->getPageCount() <= 1)
            return array();
        $pageCount = $this->getPageCount();

        list($beginPage, $endPage) = $this->getPageRange();
        $currentPage = $this->getCurrentPage(false);
        $buttons = array();
        
		if($this->onlyPN){//只需上一页和下一页, 其它内容不显示
			// 上一页
			if (($page = $currentPage - 1) < 0)
				$page = 0;
			$buttons[] = $this->createPageButton($this->prevPageLabel, $page, self::CSS_PREVIOUS_PAGE, $currentPage <= 0, false);
	
			// 下一页
			if (($page = $currentPage + 1) >= $pageCount - 1)
				$page = $pageCount - 1;
			$buttons[] = $this->createPageButton($this->nextPageLabel, $page, self::CSS_NEXT_PAGE, $currentPage >= $pageCount - 1, false);
		
		}else{//根据需求显示分页
			
			 // 首页
			$buttons[] = $this->createPageButton($this->firstPageLabel, 0, self::CSS_FIRST_PAGE, $currentPage <= 0, false);
	
			// 上一页
			if (($page = $currentPage - 1) < 0)
				$page = 0;
			$buttons[] = $this->createPageButton($this->prevPageLabel, $page, self::CSS_PREVIOUS_PAGE, $currentPage <= 0, false);
	
			// 内部的数字页
			for ($i = $beginPage; $i <= $endPage; ++$i)
				$buttons[] = $this->createPageButton($i + 1, $i, self::CSS_INTERNAL_PAGE, false, $i == $currentPage);
	
			// 下一页
			if (($page = $currentPage + 1) >= $pageCount - 1)
				$page = $pageCount - 1;
			$buttons[] = $this->createPageButton($this->nextPageLabel, $page, self::CSS_NEXT_PAGE, $currentPage >= $pageCount - 1, false);
	
			// 尾页
			$buttons[] = $this->createPageButton($this->lastPageLabel, $pageCount - 1, self::CSS_LAST_PAGE, $currentPage >= $pageCount - 1, false);
	        
			//总页数
			$htmlPN = $this->showTotal ? Yii::t('site','共').$pageCount.Yii::t('site','页').'&nbsp;&nbsp;' : '';
			
			// 跳转页
			if($this->showJump){
				$jumpUrl = substr($this->createPageUrl(1), 0, -1);
				$script = '<script>function subPageJump(obj){var n=$(obj).children("input").val(); var maxPage = '.$pageCount.'; n = parseInt(n) > maxPage ? maxPage : parseInt(n); if(n>0) window.location.href="'.$jumpUrl.'"+n;}</script>';
				
				$str = '<li class="jump"><form onsubmit="subPageJump(this);return false;">'. $htmlPN .Yii::t('site','跳转到').
					'<input size="4" class="page-num" title="'.Yii::t('site','输入要跳转的页数,然后回车').'" />'.Yii::t('site','页').'&nbsp;</form></li>' . $script;
				$buttons[] = $str;
			}
			
		}

        return $buttons;
    }

}