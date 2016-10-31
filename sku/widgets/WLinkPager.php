<?php

/**
 * 自定义分页widget，重写分页类，添加自定义下拉页
 * @author wyee <yanjie.wang@gatewang.com>
 */
class WLinkPager extends CLinkPager {
    /**
     * @var bool 跳转页
     */
    public $jump = true;
    public $ajax = true;

    public function init() {
        if ($this->nextPageLabel === null)
            $this->nextPageLabel = Yii::t('page','下一页');
        if ($this->prevPageLabel === null)
            $this->prevPageLabel = Yii::t('page','上一页');      
        if ($this->header === null)
            $this->header = '';
        if (!isset($this->htmlOptions['id']))
            $this->htmlOptions['id'] = $this->getId();
        if (!isset($this->htmlOptions['class']))
            $this->htmlOptions['class'] = 'yiiPager';
    }

    public function run() {
        $this->registerClientScript();
        $buttons = $this->createPageButtons();
        if (empty($buttons))
            return;
        echo '<div class="gxui-pages"  pagesize="9" count="4"  container="#goodlist">';
        echo $buttons;
        echo '</div>';
        $buttons = '';
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
        list($beginPage, $endPage)=$this->getPageRange();
        $currentPage = $this->getCurrentPage(false); // currentPage is calculated in getPageRange()
        $currentPage=$currentPage+1;
        $buttons = '';
        //分页链接
        $jumpUrl = substr($this->createPageUrl(1), 0, -1);
        // 上一页
        if (($page = $currentPage - 1) < 0)
            $page = 1;
            if($currentPage<=1){
                $buttons.='<span>上一页</span>';
            }else{
               $PrePage=$currentPage-1;
               $buttons.='<a href="'.$jumpUrl.$PrePage.'">上一页</a>';
            }
		       $buttons.='<div class="gxui-select"><span><b class="red">'.$currentPage.'</b>/'.$pageCount.'</span>
				<del class="arrow-bottom"></del>       
				<select id="pageJump" onchange="subPageJump(this.options[this.options.selectedIndex].value)">
                ';
        for ($i = $beginPage; $i <= $endPage; ++$i){
              if($i+1==$currentPage){
                  $buttons.='<option value="'.($i+1).'" selected>'.($i+1).'</option>';
              }else{
                  $buttons.='<option value="'.($i+1).'">'.($i+1).'</option>';
              }
           } 
        // 下一页
        if (($page = $currentPage-1) >= $pageCount - 1)
            $page = $pageCount-1;
            $NextPage=$currentPage+1;
           $buttons.='</select></div>';
           if($currentPage>$endPage){
              $buttons.='<span>下一页</span>';
           }else{   
                 $buttons.='<a href="'.$jumpUrl.$NextPage.'">下一页</a>';
           }
           
          $script = '<script>function subPageJump(n){var n = parseInt(n);if(n>0) window.location.href="'.$jumpUrl.'"+n;}</script>';
          $buttons.=$script;
             
        return $buttons;
}
   }