<?php

/**
 * HTML 帮助类
 * @author jianlin.lin <hayeslam@163.com>
 */
class HtmlHelper extends CHtml {

    const LANG_ZH_CN = 1;
    const LANG_ZH_TW = 2;
    const LANG_EN = 3;

    /**
     * 多语言选择
     * @param null $k
     * @return array|null
     */
    public static function languageInfo($k = null)
    {
        $arr = array(
            self::LANG_ZH_CN => '简体中文',
            self::LANG_ZH_TW => '繁體中文',
            self::LANG_EN => 'English',
        );
        if(is_numeric($k)){
            return isset($arr[$k]) ? $arr[$k] : null;
        }else{
            return $arr;
        }
    }

    /**
     * 生成排序链接
     * @param string $by            字段名称
     * @param string $route         路由
     * @param array  $params        用户请求参数
     * @param array  $defaultParams 规范请求参数数组
     * @param string $currClass     当前选中样式Class
     * @param array  $htmlOptions   html属性
     * @return string
     */
    public static function generateSortUrl($by, $route, $params, $defaultParams, $currClass = 'curr', $htmlOptions = array()) {
        $order = isset($defaultParams['order']) ? $defaultParams['order'] : array();
        $text = $by == 'default' ? Yii::t('category', '默认') : '';
        if ($by == 'default') {
            if ($params['order'] <= 0)
                $htmlOptions['class'] = $currClass;
            $params['order'] = 0;
        }
        if (isset($order[$by])) {
            $sort = $order[$by];
            $srv = $params['order']; // 排序请求值
            $params['order'] = $sort['defaultValue']; // 请求值替换为排序默认值
            $narr = self::_numericIndexArray($sort);
            $nkarr = array_keys($narr);
            rsort($nkarr);
            if (!empty($nkarr)) {
                $desc = true;   // $desc 等于 true 为降序 false 为升序
                if ($sort['defaultValue'] <= $nkarr[0])  // 如果默认值小于最大值则
                    $desc = false;
                if (count($nkarr) > 1 && $srv != 0)
                    $desc = $srv >= $nkarr[0] ? false : true;
            }
            $text = "<span>{$sort['text']}</span><i></i>";
            if (array_key_exists($srv, $sort)) {
                unset($sort[$srv]);
                $keys = array_keys($sort);
                $params['order'] = !empty($keys) ? is_numeric(end($keys)) ? end($keys) : $srv  : 0;

                // 定义排序样式类
                if (!isset($htmlOptions['ascClss']))
                    $htmlOptions['ascClss'] = 'currTop';
                if (!isset($htmlOptions['descClss']))
                    $htmlOptions['descClss'] = 'currBottom';
                $styleClass = $currClass .= $desc == false ? ' ' . $htmlOptions['ascClss'] : ' ' . $htmlOptions['descClss'];
                unset($htmlOptions['ascClss']);
                unset($htmlOptions['descClss']);
                if (isset($htmlOptions['class']))
                    $htmlOptions['class'] .= ' ' . $styleClass;
                else
                    $htmlOptions['class'] = $styleClass;
            }
        }
        return parent::link($text, Yii::app()->createAbsoluteUrl($route, $params), $htmlOptions);
    }

    /**
     * 格式化输出价格
     * @param float || null $price  价格
     * @param bool $tag
     * @param array $htmlOptions
     * @param bool $closeTag
     * @return string
     */
    public static function formatPrice($price, $tag = false, $htmlOptions = array(), $closeTag = true)
    {
        $symbol = Yii::app()->language == 'zh_cn' ? '￥' : 'HK$';
        $priceConvert = Yii::app()->language != 'zh_cn' ? (is_numeric($price) ? number_format(Common::rateConvert($price), 2) : $price) : $price;
        if ($tag !== false) {
            return parent::tag($tag, $htmlOptions, $symbol . $priceConvert, $closeTag);
        } else {
            return $symbol . $priceConvert;
        }
    }

    /**
     * 价格转换积分
     * @param   int     $price      价格
     * @param   string  $symbol     标志
     * @return int|string
     */
    public static function priceConvertIntegral($price, $symbol = '') {
        $str = Common::convert($price);
        if($symbol) $str .= Yii::t('common', parent::encode($symbol));
        return $str;
//        return Common::convert($price) . Yii::t('common', parent::encode($symbol));//参数默认为空,为什么要翻译?
    }

    /**
     * 输出多语言文本替换
     * @param string $text
     * @param string $value
     * @return string
     */
    public static function langsTextConvert($text, $value) {
        return str_replace('{value}', $value, Yii::t('common', $text));
    }

    /**
     * 只保留数值索引的数组元素
     * @param array $arr    数组
     * @return array
     */
    private static function _numericIndexArray($arr) {
        if (is_array($arr)) {
            foreach ($arr as $k => $v) {
                if (!is_numeric($k))
                    unset($arr[$k]);
            }
        }
        return $arr;
    }

}
