<?php

/**
 * 自定义表格widget
 * @author wanyun.liu <wanyun_liu@163.com>
 */
Yii::import('zii.widgets.grid.CGridView');

class GridView extends CGridView {

    public $template = "{items}{summary}{pager}";
    public $pager = array('class' => 'LinkPager');
    public $summaryText = '第 {start}-{end} 条 / 共 {count} 条 / {pages} 页';

    public function init() {
        $this->pager = array_merge($this->pager, array('ajax' => $this->ajaxUpdate));
        if(Yii::app()->language == 'en'){
            $this->summaryText = 'page {start} to {end} / amount {count} / toal pages {pages} '; 
        }
        parent::init();
    }
}