
/// <reference path="/Res/global/libs/jquery/jquery-1.5.1-vsdoc.js" />
(function($) {
    $.getUrlParam = function(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null)
            return unescape(r[2]);
        return null;
    }

    $.cookie = function(name, value, options) {
        if (typeof value != 'undefined') { // name and value given, set cookie
            options = options || {};
            if (value === null) {
                value = '';
                options.expires = -1;
            }
            var expires = '';
            if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
                var date;
                if (typeof options.expires == 'number') {
                    date = new Date();
                    date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
                } else {
                    date = options.expires;
                }
                expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
            }
            var path = options.path ? '; path=' + options.path : '';
            var domain = options.domain ? '; domain=' + options.domain : '';
            var secure = options.secure ? '; secure' : '';
            document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
        } else { // only name given, get cookie
            var cookieValue = null;
            if (document.cookie && document.cookie != '') {
                var cookies = document.cookie.split(';');
                for (var i = 0; i < cookies.length; i++) {
                    var cookie = jQuery.trim(cookies[i]);
                    // Does this cookie string begin with the name we want?
                    if (cookie.substring(0, name.length + 1) == (name + '=')) {
                        cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                        break;
                    }
                }
            }
            return cookieValue;
        }
    };
    $.rawUrl = function() {
        var p = window.location.href.indexOf('?');
        if (p > 0) {
            return window.location.href.substring(0, p);
        }
        return window.location.href;
    };
})(jQuery);

function QueryStringHelper() {
    this.qhash = {};
    var url = window.location.href;
    var qstr = [];
    if (url.indexOf('?') > 0) {
        qstr = url.substring(url.indexOf('?') + 1).split('&');
        for (var i = 0; i < qstr.length; i++) {
            var s = qstr[i].split('=');
            var k = s[0], v = '';
            if (s.length == 2)
                v = s[1];
            this.qhash[k] = unescape(v);
        }
    }
}
;
QueryStringHelper.prototype.get = function(k) {
    var s = this.qhash[k];
    if (s)
        return s;
    else
        return '';
};
QueryStringHelper.prototype.set = function(k, v) {
    this.qhash[k] = v;
    return this;
};
QueryStringHelper.prototype.remove = function(k) {
    if (this.qhash.hasOwnProperty(k))
        delete this.qhash[k];
    return this;
};
QueryStringHelper.prototype.toQueryString = function() {
    var s = [];
    for (var o in this.qhash) {
        s.push(o + '=' + encodeURIComponent(this.qhash[o]));
    }
    return s.join('&');
};

var _viewPage = 'viewPage';
var _viewMod = 'viewMode';
var default_mod = '';
var default_url = '';

function AdminApp() {
    var Obj = function(id) {
        return document.getElementById(id);
    }
    this.resize = function() {
        var isLeftHidden = $('#dLeft').hasClass('hidden');
        var sideWidth = 0;
        if (!isLeftHidden)
            sideWidth = $('#dLeft').width();

        var topHeight = $('#dTop').height();
        var navHeight = $('#dNavTab').height();
        var splitWidth = $('#dSplitbar').width();
        var bodyHeight = $(window).height();
        var bodyWidth = $(window).width();
        var ctxHeight = bodyHeight - navHeight - topHeight;

        $('#dLeft').height(ctxHeight);
        $('#dSplitbar').height(ctxHeight);
        $('#dSplitbar .btn').css('margin-top', Math.round((ctxHeight - $('#dSplitbar .btn').height()) / 2));
        $('#dBody').height(ctxHeight);
        $('#dBody').width(bodyWidth - splitWidth - sideWidth);
    };
    this.togSidebar = function() {
        var self = this;
        if ($('#dLeft').hasClass('hidden')) {
            $('#dLeft').removeClass('hidden');
            $('#dLeft').show(500, function() {
                self.resize();
            });
        } else {
            $('#dLeft').addClass('hidden')
            $('#dLeft').hide(10, function() {
                self.resize();
            });

        }

    };
    this.loadMod = function(_modName, _openUrl) {
        $.cookie(_viewMod, _modName);
        $.cookie(_viewPage, _openUrl);
        var qs = new QueryStringHelper();
        qs.set('mod', _modName);
        var qstr = qs.toQueryString();
        window.location = $.rawUrl() + '?' + qstr;
    };
    this.openUrl = function(aUrl) {
        if (!aUrl || $.trim(aUrl) == '')
            return;
        $('#dCtxFrame').attr('src', aUrl);
        $.cookie(_viewPage, aUrl);
        this.activeAction(aUrl);
    };
    this.activeAction = function(aUrl) {
        if (!aUrl || $.trim(aUrl) == '')
            return;
        var action = "javascript:app.openUrl('" + aUrl + "')";
        $('.actionGroup .item').each(function(i, ele) {
            if ($(ele).find('a').attr('href') == action) {
                if (!$(ele).hasClass('selected'))
                    $(ele).addClass('selected');

            } else {
                $(ele).removeClass('selected');
            }
        });
    };
    this.getAccordionIndex = function() {
        var selEle = null;
        $('#dLeft .ctx').each(function(i, ele) {
            $(ele).data('accidx', i);
        });
        $('.actionGroup .item').each(function(i, ele) {
            if (selEle == null) {
                if ($(ele).hasClass('selected'))
                    selEle = ele;
            }
        });

        if (!selEle)
            return;
        var accEle = $(selEle).parent().parent();
        if (!accEle)
            return;

        return $(accEle).data('accidx');
    };
    this.loadDefault = function() {
//        var modFromUrl = $.getUrlParam('mod');
//        if (modFromUrl && modFromUrl != '') {
//            default_mod = modFromUrl;
//        }
//        if (default_mod != '') {
//            if (!$('#dmod_' + default_mod).hasClass('selected'))
//                $('#dmod_' + default_mod).addClass('selected');
//        }
//        var urlFromCookie = $.cookie(_viewPage);
//        if (urlFromCookie && $.trim(urlFromCookie) != '') {
//            default_url = urlFromCookie;
//        }
//        if (default_url != '') {
            this.openUrl(default_url);
//        }
    };

}
var app = new AdminApp();

$(window).resize(function() {
    app.resize();
});
$(window).ready(function() {
    app.resize();
});
