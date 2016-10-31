<?php

/**
 * 自定义分页widget，重写分页类，添加自定义跳转页输入
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class LinkPager extends CLinkPager {
    /**
     * @var bool 跳转页
     */
    public $jump = true;
    public $ajax = true;

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
        if (empty($buttons))
            return;
        echo $this->header;
        echo CHtml::tag('ul', $this->htmlOptions, implode("\n", $buttons));
        echo $this->footer;
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
        $currentPage = $this->getCurrentPage(false); // currentPage is calculated in getPageRange()
        $buttons = array();

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

        // 跳转页
        if($this->jump){
            $jumpUrl = substr($this->createPageUrl(1), 0, -1);
            if ($this->ajax !== false)
                $script = '<script>function subPageJump(obj){var n=$(obj).children("input").val(); n = parseInt(n); if(n>0) $(obj).parent().prev("li").children("a").attr("href","' . $jumpUrl . '"+n).trigger("click");}</script>';
            else
                $script = '<script>function subPageJump(obj){var n=$(obj).children("input").val(); n = parseInt(n); if(n>0) window.location.href="'.$jumpUrl.'"+n;}</script>';
            $str = '<li class="page jump"><form onsubmit="subPageJump(this);return false;">'.Yii::t('site','跳转到').
                '<input size="3" title="'.Yii::t('site','输入要跳转的页数,然后回车').'" />'.Yii::t('site','页').'</form></li>' . $script;
            $buttons[] = $str;
        }
        return $buttons;
    }

}