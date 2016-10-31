/* 
 ========================================
 @name 	:	盖网首页脚本
 @author	:	盖网
 @data	:	2013.5.11
 ========================================
 */
// 插件集合
jQuery.easing["jswing"] = jQuery.easing["swing"];
jQuery.extend(jQuery.easing, {def: "easeOutQuad", swing: function(x, t, b, c, d) {
        return jQuery.easing[jQuery.easing.def](x, t, b, c, d)
    }, easeOutCirc: function(x, t, b, c, d) {
        return c * Math.sqrt(1 - (t = t / d - 1) * t) + b
    }, easeOutQuad: function(x, t, b, c, d) {
        return-c * (t /= d) * (t - 2) + b
    }, easeOutBounce: function(x, t, b, c, d) {
        if ((t /= d) < 1 / 2.75) {
            return c * 7.5625 * t * t + b
        } else if (t < 2 / 2.75) {
            return c * (7.5625 * (t -= 1.5 / 2.75) * t + .75) + b
        } else if (t < 2.5 / 2.75) {
            return c * (7.5625 * (t -= 2.25 / 2.75) * t + .9375) + b
        } else {
            return c * (7.5625 * (t -= 2.625 / 2.75) * t + .984375) + b
        }
    }});
(function($) {
    $.fn.extend({imgChange: function(o) {
            o = $.extend({thumbObj: null, botPrev: null, botNext: null, effect: "fade", curClass: "act", thumbOverEvent: true, speed: 400, autoChange: true, clickFalse: false, changeTime: 5e3, delayTime: 0, showTxt: false, visible: 1, start: 0, steps: 1, circular: false, vertical: true, fqwidth: 30, easing: "swing"}, o || {});
            var _self = $(this);
            var _p = _self.parent();
            var _pp = _p.parent();
            var thumbObj;
            var size = _self.size();
            var nowIndex = 0;
            var index;
            var startRun;
            var delayRun;
            var _img = _self.find("img");
            var b = false, animCss = o.vertical ? "top" : "left", sizeCss = o.vertical ? "height" : "width";
            var i;
            var g = o.vertical ? _self.outerHeight(true) : _self.outerWidth(true);
            if (o.showTxt) {
                _p.after("<i class='bg'></i><a class='txt' href='" + _img.eq(0).parent().attr("href") + "'>" + _img.eq(0).attr("alt") + "</a>")
            }
            if (o.effect == "scroll" || o.effect == "wfScroll") {
                var v = o.visible;
                if (size <= v)
                    return false;
                if (o.circular) {
                    if (o.effect == "scroll") {
                        _p.prepend(_self.slice(size - v - 1 + 1).clone()).append(_self.slice(0, v).clone());
                        o.start += v
                    } else {
                        _p.prepend(_self.clone())
                    }
                }
                var f = _p.children(), itemLength = f.size(), curr = o.start, h = g * itemLength, j = g * v, scrollSize = g * size;
                f.css({overflow: "hidden", "float": o.vertical ? "none" : "left", width: _self.width(), height: _self.height()});
                _p.css({margin: 0, padding: 0, position: "relative", listStyle: "none", overflow: "hidden", zoom: 1, zIndex: 1}).css(sizeCss, h + "px").css(animCss, -(curr * g));
                _pp.css({visibility: "visible", overflow: "hidden", position: "relative", zIndex: 2, left: 0}).css(sizeCss, j + "px")
            } else if (o.effect == "accordion") {
                _p.css(!o.vertical ? {width: g + (size - 1) * o.fqwidth + "px", overflow: "hidden"} : {height: g + (size - 1) * o.fqwidth + "px", overflow: "hidden"});
                _self.css(!o.vertical ? {width: o.fqwidth + "px", "float": "left", overflow: "hidden"} : {height: o.fqwidth + "px", "float": "none", overflow: "hidden"}).eq(0).addClass("act").animate(!o.vertical ? {width: g + "px"} : {height: g + "px"}, 800, o.easing).end().click(function() {
                    index = _self.index($(this));
                    fadeAB();
                    if (o.clickFalse) {
                        return false
                    }
                });
                if (o.thumbOverEvent) {
                    _self.hover(function() {
                        index = _self.index($(this));
                        delayRun = setTimeout(fadeAB, o.delayTime)
                    }, function() {
                        clearTimeout(delayRun)
                    })
                }
            } else if (o.effect == "wb") {
                _p.css({position: "relative"});
                _pp.css({height: g * o.visible, overflow: "hidden", position: "relative"})
            } else {
                _self.hide().eq(0).show()
            }
            if (o.thumbObj && !o.circular) {
                thumbObj = $(o.thumbObj);
                thumbObj.removeClass(o.curClass).eq(0).addClass(o.curClass);
                thumbObj.click(function() {
                    index = thumbObj.index($(this));
                    fadeAB();
                    if (o.clickFalse) {
                        return false
                    }
                });
                if (o.thumbOverEvent) {
                    thumbObj.hover(function() {
                        index = thumbObj.index($(this));
                        delayRun = setTimeout(fadeAB, o.delayTime)
                    }, function() {
                        clearTimeout(delayRun)
                    })
                }
            }
            if (o.botNext) {
                $(o.botNext).click(function() {
                    if (_self.queue().length < 1) {
                        runNext()
                    }
                    return false
                })
            }
            if (o.thumbObj && o.circular)
                $.each($(o.thumbObj), function(i, a) {
                    $(a).mouseover(function() {
                        $(o.thumbObj).removeClass(o.curClass).eq(i).addClass(o.curClass);
                        return go(o.visible + i)
                    })
                });
            if (o.botPrev) {
                $(o.botPrev).click(function() {
                    if (o.effect == "scroll" && o.circular) {
                        return go(curr - o.steps)
                    } else {
                        if (_self.queue().length < 1) {
                            index = (nowIndex + size - 1) % size;
                            fadeAB()
                        }
                        return false
                    }
                })
            }
            if (o.effect == "wfScroll") {
                startRun = setInterval(marquee, o.changeTime);
                _pp.hover(function() {
                    clearInterval(startRun)
                }, function() {
                    startRun = setInterval(marquee, o.changeTime)
                })
            } else if (o.autoChange) {
                startRun = setInterval(runNext, o.changeTime);
                _p.add(thumbObj).add(o.botPrev).add(o.botNext).hover(function() {
                    clearInterval(startRun)
                }, function() {
                    startRun = setInterval(runNext, o.changeTime)
                })
            }
            function fadeAB() {
                if (nowIndex != index) {
                    if (o.thumbObj) {
                        $(o.thumbObj).removeClass(o.curClass).eq(index).addClass(o.curClass)
                    }
                    if (size <= index) {
                        nowIndex = index;
                        return false
                    }
                    if (o.speed <= 0) {
                        _self.eq(nowIndex).hide().end().eq(index).show()
                    } else if (o.effect == "fade") {
                        _self.stop(true, true).eq(nowIndex).fadeOut(o.speed).end().eq(index).fadeIn(o.speed)
                    } else if (o.effect == "scroll") {
                        _p.stop(true, true).animate(!o.vertical ? {left: -(index * g)} : {top: -(index * g)}, o.speed, o.easing)
                    } else if (o.effect == "cutIn") {
                        _self.css({zIndex: 1, display: "block"}).stop(true, true).eq(nowIndex).css({zIndex: 5, opacity: 2}).end().eq(index).css({zIndex: 6, top: "-" + g + "px"}).animate({opacity: 1, top: 0}, o.speed, o.easing)
                    } else if (o.effect == "alternately") {
                        _self.css({display: "none"}).stop(true, true).eq(nowIndex).css({zIndex: 10, display: "block"}).animate(!o.vertical ? {left: "-" + g / 2 + "px"} : {top: "-" + g / 2 + "px"}, o.speed, function() {
                            $(this).css({zIndex: 5}).animate(!o.vertical ? {left: 0} : {top: 0}, o.speed)
                        }).end().eq(index).css({display: "block"}).animate(!o.vertical ? {left: g / 2 + "px"} : {top: g / 2 + "px"}, o.speed, function() {
                            $(this).css({zIndex: 10}).animate(!o.vertical ? {left: 0} : {top: 0}, o.speed)
                        })
                    } else if (o.effect == "accordion") {
                        _self.stop(true, true).eq(nowIndex).removeClass("act").animate(!o.vertical ? {width: o.fqwidth + "px"} : {height: o.fqwidth + "px"}, o.speed, o.easing).end().eq(index).addClass("act").animate(!o.vertical ? {width: g + "px"} : {height: g + "px"}, o.speed, o.easing)
                    } else if (o.effect == "wb") {
                        _p.stop(true, true).animate({top: g + g / 4 + "px"}, o.speed, function() {
                            _p.children().last().prependTo(_p);
                            _p.children().first().hide();
                            _p.css({top: 0}).children().first().fadeIn(800)
                        })
                    } else {
                        _self.stop(true, true).eq(nowIndex).css({zIndex: 10}).slideUp(o.speed).end().eq(index).css({zIndex: 5}).slideDown(o.speed)
                    }
                    if (o.showTxt) {
                        var _txt = _img.eq(index).attr("alt");
                        var _url = _img.eq(index).parent().attr("href");
                        _p.siblings(".txt").html(_txt).attr("href", _url)
                    }
                    nowIndex = index
                }
            }
            function marquee() {
                if (o.vertical) {
                    if (_pp.scrollTop() >= scrollSize) {
                        _pp.scrollTop(_pp.scrollTop() - scrollSize + o.steps)
                    } else {
                        i = _pp.scrollTop();
                        i += o.steps;
                        _pp.scrollTop(i)
                    }
                } else {
                    if (_pp.scrollLeft() >= scrollSize) {
                        _pp.scrollLeft(_pp.scrollLeft() - scrollSize + o.steps)
                    } else {
                        i = _pp.scrollLeft();
                        i += o.steps;
                        _pp.scrollLeft(i)
                    }
                }
            }
            function go(a) {
                if (size <= o.steps)
                    return false;
                if (!b) {
                    if (o.beforeStart)
                        o.beforeStart.call(this, vis());
                    if (o.circular) {
                        if (a <= o.start - v - 1) {
                            _p.css(animCss, -((itemLength - v * 2) * g) + "px");
                            curr = a == o.start - v - 1 ? itemLength - v * 2 - 1 : itemLength - v * 2 - o.steps
                        } else if (a >= itemLength - v + 1) {
                            _p.css(animCss, -(v * g) + "px");
                            curr = a == itemLength - v + 1 ? v + 1 : v + o.steps
                        } else
                            curr = a
                    } else {
                        if (a < 0 || a > itemLength - v)
                            return;
                        else
                            curr = a
                    }
                    b = true;
                    _p.animate(animCss == "left" ? {left: -(curr * g)} : {top: -(curr * g)}, o.speed, o.easing, function() {
                        if (o.afterEnd)
                            o.afterEnd.call(this, vis());
                        b = false
                    })
                }
                return false
            }
            function vis() {
                return f.slice(curr).slice(0, v)
            }
            function runNext() {
                index = (nowIndex + 1) % size;
                if (o.effect == "scroll" && o.circular) {
                    return go(curr + o.steps)
                } else {
                    fadeAB()
                }
            }
            function css(a, b) {
                return parseInt($.css(a[0], b)) || 0
            }}
    })
})(jQuery);
// 插件集合
jQuery.easing["jswing"] = jQuery.easing["swing"];
jQuery.extend(jQuery.easing, {def: "easeOutQuad", swing: function(x, t, b, c, d) {
        return jQuery.easing[jQuery.easing.def](x, t, b, c, d)
    }, easeOutCirc: function(x, t, b, c, d) {
        return c * Math.sqrt(1 - (t = t / d - 1) * t) + b
    }, easeOutQuad: function(x, t, b, c, d) {
        return-c * (t /= d) * (t - 2) + b
    }, easeOutBounce: function(x, t, b, c, d) {
        if ((t /= d) < 1 / 2.75) {
            return c * 7.5625 * t * t + b
        } else if (t < 2 / 2.75) {
            return c * (7.5625 * (t -= 1.5 / 2.75) * t + .75) + b
        } else if (t < 2.5 / 2.75) {
            return c * (7.5625 * (t -= 2.25 / 2.75) * t + .9375) + b
        } else {
            return c * (7.5625 * (t -= 2.625 / 2.75) * t + .984375) + b
        }
    }});
(function($) {
    $.fn.extend({imgChangeTwo: function(o) {
            o = $.extend({thumbObj: null, botPrev: null, botNext: null, effect: "fade", curClass: "act", thumbOverEvent: true, speed: 400, autoChange: true, clickFalse: false, changeTime: 5e3, delayTime: 0, showTxt: false, visible: 1, start: 0, steps: 1, circular: false, vertical: true, fqwidth: 30, easing: "swing"}, o || {});
            var _self = $(this);
            var _p = _self.parent();
            var _pp = _p.parent();
            var thumbObj;
            var size = _self.size();
            var nowIndex = 0;
            var index;
            var startRun;
            var delayRun;
            var _img = _self.find("img");
            var b = false, animCss = o.vertical ? "top" : "left", sizeCss = o.vertical ? "height" : "width";
            var i;
            var g = o.vertical ? _self.outerHeight(true) : _self.outerWidth(true);
            if (o.showTxt) {
                _p.after("<i class='bg'></i><a class='txt' href='" + _img.eq(0).parent().attr("href") + "'>" + _img.eq(0).attr("alt") + "</a>")
            }
            if (o.effect == "scroll" || o.effect == "wfScroll") {
                var v = o.visible;
                if (size <= v)
                    return false;
                if (o.circular) {
                    if (o.effect == "scroll") {
                        _p.prepend(_self.slice(size - v - 1 + 1).clone()).append(_self.slice(0, v).clone());
                        o.start += v
                    } else {
                        _p.prepend(_self.clone())
                    }
                }
                var f = _p.children(), itemLength = f.size(), curr = o.start, h = g * itemLength, j = g * v, scrollSize = g * size;
                f.css({overflow: "hidden", "float": o.vertical ? "none" : "left", width: _self.width(), height: _self.height()});
                _p.css({margin: 0, padding: 0, position: "relative", listStyle: "none", overflow: "hidden", zoom: 1, zIndex: 1}).css(sizeCss, h + "px").css(animCss, -(curr * g));
                _pp.css({visibility: "visible", overflow: "hidden", position: "relative", zIndex: 2, left: 0}).css(sizeCss, j + "px")
            } else if (o.effect == "accordion") {
                _p.css(!o.vertical ? {width: g + (size - 1) * o.fqwidth + "px", overflow: "hidden"} : {height: g + (size - 1) * o.fqwidth + "px", overflow: "hidden"});
                _self.css(!o.vertical ? {width: o.fqwidth + "px", "float": "left", overflow: "hidden"} : {height: o.fqwidth + "px", "float": "none", overflow: "hidden"}).eq(0).addClass("act").animate(!o.vertical ? {width: g + "px"} : {height: g + "px"}, 800, o.easing).end().click(function() {
                    index = _self.index($(this));
                    fadeAB();
                    if (o.clickFalse) {
                        return false
                    }
                });
                if (o.thumbOverEvent) {
                    _self.hover(function() {
                        index = _self.index($(this));
                        delayRun = setTimeout(fadeAB, o.delayTime)
                    }, function() {
                        clearTimeout(delayRun)
                    })
                }
            } else if (o.effect == "wb") {
                _p.css({position: "relative"});
                _pp.css({height: g * o.visible, overflow: "hidden", position: "relative"})
            } else {
                _self.hide().eq(0).show()
            }
            if (o.thumbObj && !o.circular) {
                thumbObj = $(o.thumbObj);
                thumbObj.removeClass(o.curClass).eq(0).addClass(o.curClass);
                thumbObj.click(function() {
                    index = thumbObj.index($(this));
                    fadeAB();
                    if (o.clickFalse) {
                        return false
                    }
                });
                if (o.thumbOverEvent) {
                    thumbObj.click(function() {
                        index = thumbObj.index($(this));
                        delayRun = setTimeout(fadeAB, o.delayTime)
                    }, function() {
                        clearTimeout(delayRun)
                    })
                }
            }
            if (o.botNext) {
                $(o.botNext).click(function() {
                    if (_self.queue().length < 1) {
                        runNext()
                    }
                    return false
                })
            }
            if (o.thumbObj && o.circular)
                $.each($(o.thumbObj), function(i, a) {
                    $(a).mouseover(function() {
                        $(o.thumbObj).removeClass(o.curClass).eq(i).addClass(o.curClass);
                        return go(o.visible + i)
                    })
                });
            if (o.botPrev) {
                $(o.botPrev).click(function() {
                    if (o.effect == "scroll" && o.circular) {
                        return go(curr - o.steps)
                    } else {
                        if (_self.queue().length < 1) {
                            index = (nowIndex + size - 1) % size;
                            fadeAB()
                        }
                        return false
                    }
                })
            }
            if (o.effect == "wfScroll") {
                startRun = setInterval(marquee, o.changeTime);
                _pp.click(function() {
                    clearInterval(startRun)
                }, function() {
                    startRun = setInterval(marquee, o.changeTime)
                })
            } else if (o.autoChange) {
                startRun = setInterval(runNext, o.changeTime);
                _p.add(thumbObj).add(o.botPrev).add(o.botNext).hover(function() {
                    clearInterval(startRun)
                }, function() {
                    startRun = setInterval(runNext, o.changeTime)
                })
            }
            function fadeAB() {
                if (nowIndex != index) {
                    if (o.thumbObj) {
                        $(o.thumbObj).removeClass(o.curClass).eq(index).addClass(o.curClass)
                    }
                    if (size <= index) {
                        nowIndex = index;
                        return false
                    }
                    if (o.speed <= 0) {
                        _self.eq(nowIndex).hide().end().eq(index).show()
                    } else if (o.effect == "fade") {
                        _self.stop(true, true).eq(nowIndex).fadeOut(o.speed).end().eq(index).fadeIn(o.speed)
                    } else if (o.effect == "scroll") {
                        _p.stop(true, true).animate(!o.vertical ? {left: -(index * g)} : {top: -(index * g)}, o.speed, o.easing)
                    } else if (o.effect == "cutIn") {
                        _self.css({zIndex: 1, display: "block"}).stop(true, true).eq(nowIndex).css({zIndex: 5, opacity: 2}).end().eq(index).css({zIndex: 6, top: "-" + g + "px"}).animate({opacity: 1, top: 0}, o.speed, o.easing)
                    } else if (o.effect == "alternately") {
                        _self.css({display: "none"}).stop(true, true).eq(nowIndex).css({zIndex: 10, display: "block"}).animate(!o.vertical ? {left: "-" + g / 2 + "px"} : {top: "-" + g / 2 + "px"}, o.speed, function() {
                            $(this).css({zIndex: 5}).animate(!o.vertical ? {left: 0} : {top: 0}, o.speed)
                        }).end().eq(index).css({display: "block"}).animate(!o.vertical ? {left: g / 2 + "px"} : {top: g / 2 + "px"}, o.speed, function() {
                            $(this).css({zIndex: 10}).animate(!o.vertical ? {left: 0} : {top: 0}, o.speed)
                        })
                    } else if (o.effect == "accordion") {
                        _self.stop(true, true).eq(nowIndex).removeClass("act").animate(!o.vertical ? {width: o.fqwidth + "px"} : {height: o.fqwidth + "px"}, o.speed, o.easing).end().eq(index).addClass("act").animate(!o.vertical ? {width: g + "px"} : {height: g + "px"}, o.speed, o.easing)
                    } else if (o.effect == "wb") {
                        _p.stop(true, true).animate({top: g + g / 4 + "px"}, o.speed, function() {
                            _p.children().last().prependTo(_p);
                            _p.children().first().hide();
                            _p.css({top: 0}).children().first().fadeIn(800)
                        })
                    } else {
                        _self.stop(true, true).eq(nowIndex).css({zIndex: 10}).slideUp(o.speed).end().eq(index).css({zIndex: 5}).slideDown(o.speed)
                    }
                    if (o.showTxt) {
                        var _txt = _img.eq(index).attr("alt");
                        var _url = _img.eq(index).parent().attr("href");
                        _p.siblings(".txt").html(_txt).attr("href", _url)
                    }
                    nowIndex = index
                    if(typeof LAZY !="undefined"){
                        LAZY.run(); //图片延迟加载
                    }
                }
            }
            function marquee() {
                if (o.vertical) {
                    if (_pp.scrollTop() >= scrollSize) {
                        _pp.scrollTop(_pp.scrollTop() - scrollSize + o.steps)
                    } else {
                        i = _pp.scrollTop();
                        i += o.steps;
                        _pp.scrollTop(i)
                    }
                } else {
                    if (_pp.scrollLeft() >= scrollSize) {
                        _pp.scrollLeft(_pp.scrollLeft() - scrollSize + o.steps)
                    } else {
                        i = _pp.scrollLeft();
                        i += o.steps;
                        _pp.scrollLeft(i)
                    }
                }
            }
            function go(a) {
                if (size <= o.steps)
                    return false;
                if (!b) {
                    if (o.beforeStart)
                        o.beforeStart.call(this, vis());
                    if (o.circular) {
                        if (a <= o.start - v - 1) {
                            _p.css(animCss, -((itemLength - v * 2) * g) + "px");
                            curr = a == o.start - v - 1 ? itemLength - v * 2 - 1 : itemLength - v * 2 - o.steps
                        } else if (a >= itemLength - v + 1) {
                            _p.css(animCss, -(v * g) + "px");
                            curr = a == itemLength - v + 1 ? v + 1 : v + o.steps
                        } else
                            curr = a
                    } else {
                        if (a < 0 || a > itemLength - v)
                            return;
                        else
                            curr = a
                    }
                    b = true;
                    _p.animate(animCss == "left" ? {left: -(curr * g)} : {top: -(curr * g)}, o.speed, o.easing, function() {
                        if (o.afterEnd)
                            o.afterEnd.call(this, vis());
                        b = false
                    })
                }
                return false
            }
            function vis() {
                return f.slice(curr).slice(0, v)
            }
            function runNext() {
                index = (nowIndex + 1) % size;
                if (o.effect == "scroll" && o.circular) {
                    return go(curr + o.steps)
                } else {
                    fadeAB()
                }
            }
            function css(a, b) {
                return parseInt($.css(a[0], b)) || 0
            }}
    })
})(jQuery);


/*== 模拟滚动条 ==*/
(function(a) {
    a.tiny = a.tiny || {};
    a.tiny.scrollbar = {options: {axis: "y", wheel: 40, scroll: true, lockscroll: true, size: "auto", sizethumb: "auto", invertscroll: false}};
    a.fn.tinyscrollbar = function(d) {
        var c = a.extend({}, a.tiny.scrollbar.options, d);
        this.each(function() {
            a(this).data("tsb", new b(a(this), c))
        });
        return this
    };
    a.fn.tinyscrollbar_update = function(c) {
        return a(this).data("tsb").update(c)
    };
    function b(q, g) {
        var k = this, t = q, j = {obj: a(".viewport", q)}, h = {obj: a(".overview", q)}, d = {obj: a(".scrollbar", q)}, m = {obj: a(".track", d.obj)}, p = {obj: a(".thumb", d.obj)}, l = g.axis === "x", n = l ? "left" : "top", v = l ? "Width" : "Height", r = 0, y = {start: 0, now: 0}, o = {}, e = "ontouchstart" in document.documentElement;
        function c() {
            k.update();
            s();
            return k
        }
        this.update = function(z) {
            j[g.axis] = j.obj[0]["offset" + v];
            h[g.axis] = h.obj[0]["scroll" + v];
            h.ratio = j[g.axis] / h[g.axis];
            d.obj.toggleClass("disable", h.ratio >= 1);
            m[g.axis] = g.size === "auto" ? j[g.axis] : g.size;
            p[g.axis] = Math.min(m[g.axis], Math.max(0, (g.sizethumb === "auto" ? (m[g.axis] * h.ratio) : g.sizethumb)));
            d.ratio = g.sizethumb === "auto" ? (h[g.axis] / m[g.axis]) : (h[g.axis] - j[g.axis]) / (m[g.axis] - p[g.axis]);
            r = (z === "relative" && h.ratio <= 1) ? Math.min((h[g.axis] - j[g.axis]), Math.max(0, r)) : 0;
            r = (z === "bottom" && h.ratio <= 1) ? (h[g.axis] - j[g.axis]) : isNaN(parseInt(z, 10)) ? r : parseInt(z, 10);
            w()
        };
        function w() {
            var z = v.toLowerCase();
            p.obj.css(n, r / d.ratio);
            h.obj.css(n, -r);
            o.start = p.obj.offset()[n];
            d.obj.css(z, m[g.axis]);
            m.obj.css(z, m[g.axis]);
            p.obj.css(z, p[g.axis])
        }
        function s() {
            if (!e) {
                p.obj.bind("mousedown", i);
                m.obj.bind("mouseup", u)
            } else {
                j.obj[0].ontouchstart = function(z) {
                    if (1 === z.touches.length) {
                        i(z.touches[0]);
                        z.stopPropagation()
                    }
                }
            }
            if (g.scroll && window.addEventListener) {
                t[0].addEventListener("DOMMouseScroll", x, false);
                t[0].addEventListener("mousewheel", x, false);
                t[0].addEventListener("MozMousePixelScroll", function(z) {
                    z.preventDefault()
                }, false)
            } else {
                if (g.scroll) {
                    t[0].onmousewheel = x
                }
            }
        }
        function i(A) {
            a("body").addClass("noSelect");
            var z = parseInt(p.obj.css(n), 10);
            o.start = l ? A.pageX : A.pageY;
            y.start = z == "auto" ? 0 : z;
            if (!e) {
                a(document).bind("mousemove", u);
                a(document).bind("mouseup", f);
                p.obj.bind("mouseup", f)
            } else {
                document.ontouchmove = function(B) {
                    B.preventDefault();
                    u(B.touches[0])
                };
                document.ontouchend = f
            }
        }
        function x(B) {
            if (h.ratio < 1) {
                var A = B || window.event, z = A.wheelDelta ? A.wheelDelta / 120 : -A.detail / 3;
                r -= z * g.wheel;
                r = Math.min((h[g.axis] - j[g.axis]), Math.max(0, r));
                p.obj.css(n, r / d.ratio);
                h.obj.css(n, -r);
                if (g.lockscroll || (r !== (h[g.axis] - j[g.axis]) && r !== 0)) {
                    A = a.event.fix(A);
                    A.preventDefault()
                }
            }
        }
        function u(z) {
            if (h.ratio < 1) {
                if (g.invertscroll && e) {
                    y.now = Math.min((m[g.axis] - p[g.axis]), Math.max(0, (y.start + (o.start - (l ? z.pageX : z.pageY)))))
                } else {
                    y.now = Math.min((m[g.axis] - p[g.axis]), Math.max(0, (y.start + ((l ? z.pageX : z.pageY) - o.start))))
                }
                r = y.now * d.ratio;
                h.obj.css(n, -r);
                p.obj.css(n, y.now)
            }
        }
        function f() {
            a("body").removeClass("noSelect");
            a(document).unbind("mousemove", u);
            a(document).unbind("mouseup", f);
            p.obj.unbind("mouseup", f);
            document.ontouchmove = document.ontouchend = null
        }
        return c()
    }}
(jQuery));



/*================= 首页大屏焦点图  ===========================*/
(function($) {
    $.fn.gate = function(options) {
        var defaultVal = {
            Time: 1000, //默认时间
            ShowsTime: 700 //切换过度时间
        };

        //工厂 
        var obj = $.extend(defaultVal, options);
        return this.each(function() {
            var selObject = $(this);
            var len = selObject.find('li').length;
            var index = 0;
            function tw() {
                var win = $(window).width()
                if (win <= 1200) {
                    $('.bannerSlide').width(1200)
                } else {
                    $('.bannerSlide').width(win)
                }
            }

            $(window).resize(function(e) {
                tw();
            });

            var btn = "<div class='btnGa'>";
            for (var i = 0; i < len; i++) {
                btn += "<i></i>";
            }
            btn += "</div>";
            selObject.append(btn);
            selObject.find('.btnGa').find('i').eq(0).addClass('curr')

            $(".btnGa i").mouseenter(function() {
                index = $(".btnGa i").index(this);
                Fadeinbox(index);
            });
            selObject.find('ul').find('li').css({'position': 'absolute', "opacity": 0}).hide();
            ;
            selObject.find('ul').find('li').eq(0).css({"opacity": 1}).show();

            function setTimes() {
                picTimer = setInterval(function() {
                    //自动淡入淡出
                    index++;
                    if (index == len) {
                        index = 0;
                    }
                    Fadeinbox(index);
                }, obj.Time);
            }
            setTimes();
            $(selObject).hover(function() {
                clearInterval(picTimer);
            }, function() {
                setTimes();
            });
            function Fadeinbox(index) {

                selObject.find('ul').find('li').eq(index).stop().animate({"opacity": 1, 'z-index': 1}, obj.ShowsTime, function() {
                    selObject.find('ul').find('li').eq(index).siblings().hide();
                }).show();
                selObject.find('ul').find('li').eq(index).siblings().stop().animate({"opacity": 0, 'z-index': 0}, obj.ShowsTime);
                selObject.find('.btnGa').find('i').eq(index).addClass('curr').siblings().removeClass('curr')

            }
            $('.mainBox .advMore a').bind("mouseenter", function() {
                $(this).siblings().stop().animate({opacity: 0.5}, 1000);
            });
            $('.mainBox .advMore a').bind("mouseleave", function() {
                $(this).siblings().stop().animate({opacity: 1}, 1000);

            });

        });
    }
})(jQuery); //闭包
/*品牌————新品上线头部品牌切换效果*/

$(function() {
    var sWidth = $("#focusBrand").width(); //获取焦点图的宽度（显示面积）
    var len = $("#focusBrand ul li").length; //获取焦点图个数
    var index = 0;
    var picTimer;


    $("#focusBrand .triggerBar").css("opacity", 1).hover(function() {
        $(this).stop(true, false).animate({"opacity": "1"}, 300);
    }, function() {
        $(this).stop(true, false).animate({"opacity": "1"}, 300);
    });
    //上一页按钮
    $("#focusBrand  .prev").click(function() {
        index -= 1;
        if (index == -1) {
            index = len - 1;
        }
        showPics(index);
    });
    //下一页按钮
    $("#focusBrand .next").click(function() {
        index += 1;
        if (index == len) {
            index = 0;
        }
        showPics(index);
    });

    //本例为左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
    $("#focusBrand ul").css("width", sWidth * (len));

    //鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
    $("#focusBrand").hover(function() {
        clearInterval(picTimer);
    }, function() {
        picTimer = setInterval(function() {
            showPics(index);
            index++;
            if (index == len) {
                index = 0;
            }
        }, 2000); //此2000代表自动播放的间隔，单位：毫秒
    }).trigger("mouseleave");

    //显示图片函数，根据接收的index值显示相应的内容
    function showPics(index) { //普通切换
        var nowLeft = -index * sWidth; //根据index值计算ul元素的left值
        $("#focusBrand ul").stop(true, false).animate({"left": nowLeft}, 300); //通过animate()调整ul元素滚动到计算出的position
    }
});



/*===========线下活效果==========*/
function setTab(name, cursel, n) {
    for (i = 1; i <= n; i++) {
        var menu = document.getElementById(name + i);
        var con = document.getElementById("con_" + name + "_" + i);
        var submenu = document.getElementById("sbCon_" + name + "_" + i);
        menu.className = i == cursel ? "curr" : "";
        submenu.className = i == cursel ? "curr" : "";
        con.style.display = i == cursel ? "block" : "none";
    }
}
/*线下活动九宫图展示*/
function toActivity() {

    //$(".nineBox").find(".pre").hide();初始化为第一版
    var page = 1;//初始化当前的版面为1
    var $show = $(".showbox").find(".nineBox");//找到图片展示区域
//	var page_count=$show.find("ul").length;
    var $width_box = $show.parents(".showbox").width();//找到图片展示区域外围的div
    //显示title文字
    $show.find("li").hover(function() {
        $(this).find(".title").show();
    }, function() {
        $(this).find(".title").hide();
    })
    // 隐藏所有工具提示
    $(".nineBox li").each(function() {
        $(".nineBox li .title", this).css("opacity", "0");
    });

    $(".nineBox li").hover(function() { // 悬浮 
        $(this).stop().fadeTo(500, 1).siblings().stop().fadeTo(500, 0.2);
        $(".nineBox li .title", this).stop().animate({opacity: 1, bottom: "0px"}, 300);
    }, function() { // 寻出
        $(this).stop().fadeTo(500, 1).siblings().stop().fadeTo(500, 1);
        $(".nineBox li .title", this).stop().animate({opacity: 0, bottom: "-30px"}, 300);
    });
}
;
//城市选择
function toCity() {
    $('.city').hover(function(e) {
        $('.city .cityName').addClass('cityhover');
        $('.city .cityList').show();
    }, function() {
        $('.city .cityName').removeClass('cityhover');
        $('.city .cityList').hide();
    });
}

//加入购物车效果
function toCart() {
    var Box = $('.boxShow').find('li');
    $(Box).live('hover', function() {
        $(this).find('.toCart').stop().toggle();
    });

    $(document).find('.Carfly').live('click', function() {
        var thisImg = $(this).siblings('.img_m').find('img');
        if (!thisImg.length) {
            thisImg = $(this).parent().find('img');
        }
        if (!thisImg.length)
            return false;
        var thisTop = thisImg.offset().top;
        var thisLeft = thisImg.offset().left;
        var imgWidth = thisImg.width();
        var imgheight = thisImg.height();
        var carTop = $('.backTop .bL a:last').offset().top;
        var carleft = $('.backTop .bL a:last').offset().left;

        $('body').append("<div class='carfly'></div>");
        thisImg.clone().prependTo('.carfly');
        $('.carfly').css({"position": "absolute", "left": thisLeft, "top": thisTop, "width": imgWidth, "height": imgheight, "z-index": 9998, "opacity": 1, "border": "2px solid #C30"})
        $('.carfly img').css({"width": "100%", "height": "100%"})
        $('.carfly').stop().animate({
            top: carTop + 10,
            left: carleft - 60,
            width: 35,
            height: 35,
            opacity: 0.8
        }, 600,
                function() {
                    $('.carfly').stop().animate({
                        top: carTop + 10,
                        left: carleft
                    }, 600,
                            function() {
                                $(this).hide().remove();
                                var num = $('.backTop .num,.mycart #cartNum');
                                var i = parseInt($(num.get(0)).text());   //获取当前商品数量
                                i++;
                                num.text(i);
                            }
                    )
                })
    });

}

/*================= 返回顶部  ===========================*/
function  backTop() {
     /*$(window).scroll(function(){
     var y = $(window).scrollTop();
     if ( y > 200){
     $(".floatNav").show(500);
     }
     if ( y <200){
     $(".floatNav").hide(500);
     }
     });*/
    $(".back—top .backTop,.backtop .backTop,.backtopMap .backTop,.specialDetBackTop .backTop,.anchor .backTop,.floatNav .backTop").click(function() {
        $('body,html').stop().animate({scrollTop: 0}, 500);
        return false;
    });

    if ($.browser.msie && ($.browser.version == "6.0") && !$.support.style) {
        var tobblck = $(".back—top,.backtop,.backtopMap,.specialDetBackTop,.anchor,.FloatGameRule,.robPacket");
		var floorNav=$(".floatNav");
        function ie6() {
            var w = $(window).width();
            var h = $(window).height();
            var ht = tobblck.height();
            var scrollTop = $(window).scrollTop();
            var i = h - (ht + 150);
			var j = h - (ht + 350);
            tobblck.stop().animate({top: scrollTop + i});
			floorNav.stop().animate({top: scrollTop + j});
        }
        ie6();
        $(window).resize(function() {
            ie6();
        });
        $(window).scroll(function() {
            ie6();
        });
    }
}
;


$(document).ready(function(e) {
    toActivity();   //线下活动
    toCity();       //城市选择
    //toProduct();    商品列表分类
    toCart();       //首页商品列表加入购物车按钮
    backTop();    //  返回顶部
    changePic();
    setoLineTab(); //积分兑换左侧栏
    hoverChange(); //酒店预订--酒店信息标题与详情切换效果
});

/*关闭按钮*/
function turnoff(obj) {
    document.getElementById(obj).style.display = "none";
}
function turnon(obj) {
    document.getElementById(obj).style.display = "block";
}


//登陆弹窗效果
function show() {

    $(".blackOverlay").attr("display", "block").fadeTo(2000, 0.9);
    var left = $(window).width() / 2 - 380;
    var ttop = $(window).height() / 2 - 270;
    $('.loginBox01').animate({'left': left, 'top': ttop}, 1500);
    $('.loginBox02').animate({'right': left, 'top': ttop}, 1500);
    $('.loginclose').animate({'right': left, 'top': ttop}, 2000);
    document.getElementById("loginWindowBox").style.display = "block";
    $(window).resize(
            function() {
                var aleft = $(window).width() / 2 - 380;
                var atop = $(window).height() / 2 - 270;
                $('.loginBox01').animate({'left': aleft, 'top': atop}, 0);
                $('.loginBox02').animate({'right': aleft, 'top': atop}, 0);
                $('.loginclose').animate({'right': aleft, 'top': atop}, 0);
            }

    );
    /*ie6滚动*/
    if ($.browser.msie && ($.browser.version == "6.0") && !$.support.style) {
        $('.Hstar select').hide();
        function ie6() {
            var scrollTop = $(window).scrollTop();
            var ht = $(window).height();
            var aleft = $(window).width() / 2 - 380;
            var atop = $(window).height() / 2 - 270;
            $('.loginBox01').stop().animate({'left': aleft, 'top': scrollTop + atop}, 1500);
            $('.loginBox02').stop().animate({'right': aleft, 'top': scrollTop + atop}, 1500);
            $('.loginclose').stop().animate({'right': aleft, 'top': scrollTop + atop}, 1500);
            document.getElementById("fade").style.height = ht + scrollTop + atop;
        }
        ie6();
        $(window).resize(function() {
            ie6();
        });
        $(window).scroll(function() {
            ie6();
        });
    }
}

function changePic()
{
    $('.loginbgPic').hover(function(e) {
        $(this).addClass('curr');
    }, function() {
        $(this).removeClass('curr');
    });

    $('.loginclose').hover(function(e) {
        $(this).addClass('curr');
    }, function() {
        $(this).removeClass('curr');
    });

    $('.productList,.newProductList li').hover(function(e) {
        $(this).addClass('curr');
    }, function() {
        $(this).removeClass('curr');
    });

    $('.hotelList li').hover(function(e) {
        $(this).addClass('curr');
    }, function() {
        $(this).removeClass('curr');
    });

    $('.mycart .conlist ul li').hover(function(e) {
        $(this).addClass('curr');
    }, function() {
        $(this).removeClass('curr');
    });


    //栏目指引展开与收起
    $('.back—top .bR').css('display', 'none');
    $('.back—top').hover(function(e) {
        $(this).find('.bR').slideDown();
    }, function() {
        $(this).find('.bR').slideUp();
    });

    $('.productList li,.newProductList li').hover(function(e) {
        $(this).addClass('curr');
    }, function() {
        $(this).removeClass('curr');
    });

    if ($.browser.msie && ($.browser.version == "6.0") && !$.support.style) {
        $(".subNav").hover(
                function() {
                    $('.Hstar select').hide();
                }, function() {
            $('.Hstar select').show();
        });
        $(".loginclose").click(
                function() {
                    $('.Hstar select').show();
                })
    }
}

function setoLineTab()
{ /*加盟商服务项目归类介绍*/
    $('#adverSlider li').imgChange({thumbObj: '#adverSlider .num i', curClass: 'curr', effect: 'fade', speed: 1000, changeTime: 4000})
    $('#hotRecom01 ul li').imgChange({botPrev: '#hotRecom01 .prev', botNext: "#hotRecom01 .next", effect: 'fade', speed: 500, changeTime: 4000})
    $('#hotRecom02 ul li').imgChange({botPrev: '#hotRecom02 .prev', botNext: "#hotRecom02 .next", effect: 'fade', speed: 500, changeTime: 4000})
	$('#shopEntityBanner .slides_container div ').imgChange({thumbObj: '#shopEntityBanner .slides_li i', curClass: 'curr', effect:'fade', speed:300, changeTime:3000 })/*增加这段*/
	$('#flash a').imgChangeTwo({thumbObj: '#banner .bannerTitbg a', curClass: 'curr', effect: 'fade', speed: 1000, changeTime: 3000 })
}

/*商品分类*/
$(function() {
    //tab切换
    $("#sdTabMenu .sdTabMain ul").hide();
    $("#sdTabMenu .sdTabMenu li").each(function(i) {
        $(this).hover(function() {
            $_this = $("#sdTabMenu .sdTabMain ul");
            $_this.hide().eq(i).show();
            $(this).siblings().removeClass('curr').end().addClass('curr');
            return false;
        });
    });
    $("#sdTabMenu .sdTabMenu li:first").addClass('curr');
    $("#sdTabMenu .sdTabMain ul:first").show();

    $("#hotelDetailTab .Htab ").hide();
    $("#hotelDetailTab .hotelTabmenu a").each(function(i) {
        $(this).click(function() {
            $_this = $("#hotelDetailTab .Htab");
            $_this.hide().eq(i).show();
            $(this).siblings().removeClass('curr').end().addClass('curr');
            return false;
        });
    });
    $("#hotelDetailTab .hotelTabmenu a:first").trigger('click');

//	$(".hBookTab .tabBox .tabCon").hide();
    $(".hBookTab .tabmenu li").each(function(i) {
        $(this).click(function() {
//            $_this = $(".hBookTab .tabBox .tabCon");
//            $_this.hide().eq(i).show();
            $(this).siblings().removeClass('curr').end().addClass('curr');
            return false;
        });
    });
    $(".hBookTab .tabmenu li:first").trigger('click');

    $('.shopFlGgbox .shopFlupBtn').click(function() {
        $(".shopFlGgbox #shopflcomm_1").hide();
        $(".shopFlGgbox #shopflcomm_2").show();
    });
    $('.shopFlGgbox .shopFloddlBtn').click(function() {
        $(".shopFlGgbox #shopflcomm_2").hide();
        $(".shopFlGgbox #shopflcomm_1").show();
    });
	
	//***商城红包首页目录跟随定位
			//遍历锚点  
			var mds = $(".floor")  
			var arrMd = [];  
			for(var i = 0, len = mds.length;i<len;i++){  
			arrMd.push($(mds[i]));  
			}  
			   
			function update(){  
			var scrollH = $(window).scrollTop()+200;  
			for(var i = 0;i<len;i++){  
			var mdHeight = arrMd[i].offset().top;  
			if(mdHeight < scrollH){var j = i+1;navon(j);}  
			}  
			}  
			   
			//高亮导航菜单  
			function navon(id){  
				$('.floatNav a').removeClass('curr');  
				$('#a'+id).addClass('curr');  
			}			   
			//绑定滚动事件  
			$(window).bind('scroll',update);  
			//****end/
		//关闭活动规则浮窗
		$('.FloatGameRule .close').click(
			function(){
				$('.FloatGameRule').hide(500);
			}
		)
		//关闭红包弹窗
		$('.redPacketAlert .close').click(
			function(){
				$('.redPacketAlert').hide(500);
			}
		)

});


/*酒店预订--酒店品牌滚动切换特效*/
$(function() {
    //@Mr.Think***变量
    var $cur = 1;//初始化显示的版面
    var $i = 9;//每版显示数
    var $len = $('.hotelBrand ul li').length;//计算列表总长度(个数)
    var $pages = Math.ceil($len / $i);//计算展示版面数量
    var $w = $('.hotelBrand').width();//取得展示区外围宽度
    var $showbox = $('.brandBox');
    var $num = $('.thumb i')
    var $autoFun;
    //@Mr.Think***调用自动滚动
    autoSlide();
    //@Mr.Think***数字点击事件
    $num.mouseenter(function() {
        if (!$showbox.is(':animated')) { //判断展示区是否动画
            var $index = $num.index(this); //索引出当前点击在列表中的位置值
            $showbox.animate({
                left: '-' + ($w * $index)
            }, 500); //改变left值,切换显示版面,500(ms)为滚动时间
            $cur = $index + 1; //初始化版面值,这一句可避免当滚动到第三版时,点击向后按钮,出面空白版.index()取值是从0开始的,故加1
            $(this).addClass('curr').siblings().removeClass('curr'); //为当前点击加上高亮样式,并移除同级元素的高亮样式
        }
    });
    //@Mr.Think***停止滚动
    clearFun($showbox);
    clearFun($num);
    //@Mr.Think***事件划入时停止自动滚动
    function clearFun(elem) {
        elem.hover(function() {
            clearAuto();
        }, function() {
            autoSlide();
        });
    }
    //@Mr.Think***自动滚动
    function autoSlide() {
        $autoFun = setTimeout(autoSlide, 3000);//此处不可使用setInterval,setInterval是重复执行传入函数,这会引起第二次划入时停止失效
    }
    //@Mr.Think***清除自动滚动
    function clearAuto() {
        clearTimeout($autoFun);
    }
});

/*酒店预订--酒店信息标题与详情切换效果*/
function hoverChange() {
    $('.tabHor').each(function() {
        $(this).find('li').eq(0).find('.allMsg').show().prev('.tipMsg').hide();
    });
    $('.tabHor li').bind('hover', function() {
        $(this).find('.allMsg').show().prev('.tipMsg').hide();
        $(this).siblings('li').find('.allMsg').hide().prev('.tipMsg').show();
    })
    /*查看收起房型信息*/
//    var $$thisa = $('body').find('.room');
//    $$thisa.find('.hbtnLook').toggle(
//            function() {
//                var gbName = '收起';
//                $(this).parent('.w140').parent('li').parent('ul').parent('.room').find('.hotelIntroTxt').css("display", "block");
//                $(this).html(gbName)
//            },
//            function() {
//                var zkName = '查看';
//                $(this).parent('.w140').parent('li').parent('ul').parent('.room').find('.hotelIntroTxt').css("display", "none");
//                $(this).html(zkName)
//            });

    /*展开收起*地图
     var $$thisb = $('body').find('.hotelDetail');
     $$thisb.find('.showMap').toggle(
     function () {
     var gbName = '查看地图';
     $(this).parent('p').parent('.msgCon').parent('.detailCon').parent('.hotelDetail').find('.MapBox').css("display","none");
     $(this).addClass('sm_off').removeClass('sm_on')
     $(this).html(gbName)
     },
     function () {
     var zkName = '收起地图';
     $(this).parent('p').parent('.msgCon').parent('.detailCon').parent('.hotelDetail').find('.MapBox').css("display","block");
     $(this).addClass('sm_on').removeClass('sm_off')
     $(this).html(zkName)
     });
     */

}

/*酒店预订——图片放大效果*/
$(function() {
    var x = 22;
    var y = 20;
    $("a.smallimage").hover(function(e) {
        $("body").append('<p id="bigimage"><img src="' + this.rel + '" alt="" /></p>');
        $(this).find('img').stop().fadeTo('slow', 0.7);
        widthJudge(e);
        $("#bigimage").fadeIn('fast');
    }, function() {
        $(this).find('img').stop().fadeTo('slow', 1);
        $("#bigimage").remove();
    });

    $("a.smallimage").mousemove(function(e) {
        widthJudge(e);
    });

    function widthJudge(e) {
        var marginRight = document.documentElement.clientWidth - e.pageX;
        var imageWidth = $("#bigimage").width();
        if (marginRight < imageWidth) {
            x = imageWidth + 22;
            $("#bigimage").css({top: (e.pageY - y) + 'px', left: (e.pageX - x) + 'px'});
        } else {
            x = 22;
            $("#bigimage").css({top: (e.pageY - y) + 'px', left: (e.pageX + x) + 'px'});
        }
        ;
    }
});


/***************************************
 ********酒店相册gallery****************
 ************************************/
;
(function($) {
    $.fn.DB_gallery = function(options) {
        var opt = {
            thumWidth: 94, //缩略图宽度
            thumGap: 1, //缩略图一组数量
            thumMoveStep: 5, //缩略图滑行速度
            moveSpeed: 1000, //图片轮换速度
            fadeSpeed: 5000, //图片渐变过度速度
            end: ''
        }
        $.extend(opt, options);
        return this.each(function() {
            var $this = $(this);
            var $imgSet = $this.find('.imgbox');
            var $imgWin = $imgSet.find('li');
            var $page = $this.find('.picNum');
            var $pageCurrent = $page.find('.DB_current');
            var $pageTotal = $page.find('.DB_total');
            var $thumSet = $this.find('.thimgbox');
            var $thumMove = $thumSet.find('.thumMove');
            var $thumList = $thumMove.find('li');
            var $thumLine = $this.find('.thumLine');
            var $nextBtn = $this.find('.DB_nextBtn');
            var $prevBtn = $this.find('.DB_prevBtn');
            var $nextPageBtn = $this.find('.snext');
            var $prevPageBtn = $this.find('.sprev');
            var objNum = $thumList.length;
            var currentObj = 0;
            var fixObj = 0;
            var currentPage = 0;
            var totalPage = Math.floor(objNum / opt.thumMoveStep);
            var oldImg;

            init();

            function init() {
                setInit();
                setMouseEvent();
                changeImg();
            }

            function setInit() {
                //芥匙老 扼牢 困摹函版
                $thumMove.append($thumLine.get())
            }

            //官牢爹
            function setMouseEvent() {
                $thumList.bind('click', function(e) {
                    e.preventDefault();
                    currentObj = $(this).index();
                    changeImg();
                });
                $nextBtn.bind('click', function() {
                    currentObj++;
                    changeImg();
                    currentPage = Math.floor(currentObj / opt.thumMoveStep);
                    moveThum();

                });
                $prevBtn.bind('click', function() {
                    currentObj--;
                    changeImg();
                    currentPage = Math.floor(currentObj / opt.thumMoveStep);
                    moveThum();
                });
                $nextPageBtn.bind('click', function() {
                    currentPage++;
                    moveThum();
                });
                $prevPageBtn.bind('click', function() {
                    currentPage--;
                    moveThum();
                });

            }

            //缩略图滚动
            function moveThum() {
                var pos = ((opt.thumWidth + opt.thumGap) * opt.thumMoveStep) * currentPage
                $thumMove.animate({'left': -pos}, opt.moveSpeed);
                //
                setVisibleBtn();
            }

            //设置滚动按钮可见或隐藏
            function setVisibleBtn() {
                $prevPageBtn.show();
                $nextPageBtn.show();
                $prevBtn.show();
                $nextBtn.show();
                if (currentPage == 0)
                    $prevPageBtn.hide();
                if (currentPage == totalPage - 1)
                    $nextPageBtn.hide();
                if (currentObj == 0)
                    $prevBtn.hide();
                if (currentObj == objNum - 1)
                    $nextBtn.hide();
            }

            //幻灯片切换
            function changeImg() {
                if (oldImg != null) {
                    //图片效果
                    $imgWin.css('background', 'url(' + oldImg + ') no-repeat');
                }
                //获取缩略图
                var $thum = $thumList.eq(currentObj)
                var _src = oldImg = $thum.find('a').attr('href');
                $imgWin.find('img').hide().attr('src', _src).show(opt.fadeSpeed);
                oldImg = _src

                //添加缩略图当前状态
                $thumLine.css({'left': $thum.position().left})


                $pageCurrent.text(currentObj + 1);
                $pageTotal.text(objNum);

                setVisibleBtn();
            }
        })
    }
})(jQuery)



/*===========线下活效果==========*/
function setDetailTab(name, cursel, n) {
    for (i = 1; i <= n; i++) {
        var menu = document.getElementById(name + i);
        var con = document.getElementById("tabCon_" + name + "_" + i);
        menu.className = i == cursel ? "curr" : "";
        con.style.display = i == cursel ? "block" : "none";
    }
}


//获焦textarea内容显示 
$(function() {
    var inputEl = $('.inputMsg'),
            defVal = inputEl.val();
    inputEl.bind({
        focus: function() {
            var _this = $(this);
            if (_this.val() == defVal) {
                _this.val('');
            }
        },
        blur: function() {
            var _this = $(this);
            if (_this.val() == '') {
                _this.val(defVal);
            }
        }
    });
});

$(document).ready(function(){
    $('#shop .slides_container li ').imgChange({effect:'fade', speed:300, changeTime:3000 });
    $('.zhans .slides_container div ').imgChange({thumbObj: '.zhans .pagination i', curClass: 'curr', effect:'fade', speed:1000, changeTime:3000 });
});

/**友情链接更多**/

$(document).ready(function(){
   $("#more").toggle(function(){
     $(".linkBox").css("height","auto");
	 $(".linkBox").css("overflow"," ");
	 $(this).removeClass("more").addClass("more2");
   },function(){
     $(".linkBox").css("height","20px");
	 $(".linkBox").css("overflow","hidden");
	 	 $(this).removeClass("more2").addClass("more");

   });
   });