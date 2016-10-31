<?php

/**
 * 重写CUrlManager类
 * 实现 URL 路由 自动断字
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class UrlManager extends CUrlManager {

    public $showScriptName = false;
    public $appendParams = false;
    public $useStrictParsing = true;
    public $urlSuffix = '/';

    public function createUrl($route, $params = array(), $ampersand = '&') {
        $route = preg_replace_callback('/(?<![A-Z])[A-Z]/', function($matches) {
                    return '-' . lcfirst($matches[0]);
                }, $route);
        return parent::createUrl($route, $params, $ampersand);
    }

    public function parseUrl($request) {
        $route = parent::parseUrl($request);
        return lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $route))));
    }

}