/**
 * 常用工具函数
 */
var tool = {
    /**
     * 生成基于URL的图片处理 的网址
     * @param  url  图片地址
     * @param params 以逗号分隔的参数  see:http://avnpc.com/pages/evathumber
     * @returns {string}
     */
    showImg: function (url, params) {
        return url.slice(0, -4) +','+ params + url.slice(-4);
    },
    /**
     * 格式化金额
     * @param price
     * @returns {string|*}
     */
    price_format: function (price) {
        if (typeof(PRICE_FORMAT) == 'undefined') {
            PRICE_FORMAT = '&yen;%s';
        }
        price = this.number_format(price, 2);
        return PRICE_FORMAT.replace('%s', price);
    },
    /**
     * 数字格式化
     * @param num 数字
     * @param ext 小数位数
     * @returns {*}
     */
    number_format: function (num, ext) {
        if (ext < 0) {
            return num;
        }
        num = Number(num);
        if (isNaN(num)) {
            num = 0;
        }
        var _str = num.toString();
        var _arr = _str.split('.');
        var _int = _arr[0];
        var _flt = _arr[1];
        if (_str.indexOf('.') == -1) {
            /* 找不到小数点，则添加 */
            if (ext == 0) {
                return _str;
            }
            var _tmp = '';
            for (var i = 0; i < ext; i++) {
                _tmp += '0';
            }
            _str = _str + '.' + _tmp;
        } else {
            if (_flt.length == ext) {
                return _str;
            }
            /* 找得到小数点，则截取 */
            if (_flt.length > ext) {
                _str = _str.substr(0, _str.length - (_flt.length - ext));
                if (ext == 0) {
                    _str = _int;
                }
            } else {
                for (var i = 0; i < ext - _flt.length; i++) {
                    _str += '0';
                }
            }
        }
        return _str;
    }

};