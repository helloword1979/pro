/*!
 * jQuery UI Core 1.11.0
 * http://jqueryui.com
 *
 * Copyright 2014 jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/category/ui-core/
 */

(function(e) {
	typeof define == "function" && define.amd ? define(["jquery"], e) : e(jQuery)
})(function(e) {
	function t(t, r) {
		var i, s, o, u = t.nodeName.toLowerCase();
		return "area" === u ? (i = t.parentNode, s = i.name, !t.href || !s || i.nodeName.toLowerCase() !== "map" ? !1 : (o = e("img[usemap=#" + s + "]")[0], !!o && n(o))) : (/input|select|textarea|button|object/.test(u) ? !t.disabled : "a" === u ? t.href || r : r) && n(t)
	}

	function n(t) {
		return e.expr.filters.visible(t) && !e(t).parents().addBack().filter(function() {
			return e.css(this, "visibility") === "hidden"
		}).length
	}
	e.ui = e.ui || {}, e.extend(e.ui, {
		version: "1.11.0",
		keyCode: {
			BACKSPACE: 8,
			COMMA: 188,
			DELETE: 46,
			DOWN: 40,
			END: 35,
			ENTER: 13,
			ESCAPE: 27,
			HOME: 36,
			LEFT: 37,
			PAGE_DOWN: 34,
			PAGE_UP: 33,
			PERIOD: 190,
			RIGHT: 39,
			SPACE: 32,
			TAB: 9,
			UP: 38
		}
	}), e.fn.extend({
		scrollParent: function() {
			var t = this.css("position"),
				n = t === "absolute",
				r = this.parents().filter(function() {
					var t = e(this);
					return n && t.css("position") === "static" ? !1 : /(auto|scroll)/.test(t.css("overflow") + t.css("overflow-y") + t.css("overflow-x"))
				}).eq(0);
			return t === "fixed" || !r.length ? e(this[0].ownerDocument || document) : r
		},
		uniqueId: function() {
			var e = 0;
			return function() {
				return this.each(function() {
					this.id || (this.id = "ui-id-" + ++e)
				})
			}
		}(),
		removeUniqueId: function() {
			return this.each(function() {
				/^ui-id-\d+$/.test(this.id) && e(this).removeAttr("id")
			})
		}
	}), e.extend(e.expr[":"], {
		data: e.expr.createPseudo ? e.expr.createPseudo(function(t) {
			return function(n) {
				return !!e.data(n, t)
			}
		}) : function(t, n, r) {
			return !!e.data(t, r[3])
		},
		focusable: function(n) {
			return t(n, !isNaN(e.attr(n, "tabindex")))
		},
		tabbable: function(n) {
			var r = e.attr(n, "tabindex"),
				i = isNaN(r);
			return (i || r >= 0) && t(n, !i)
		}
	}), e("<a>").outerWidth(1).jquery || e.each(["Width", "Height"], function(t, n) {
		function o(t, n, i, s) {
			return e.each(r, function() {
				n -= parseFloat(e.css(t, "padding" + this)) || 0, i && (n -= parseFloat(e.css(t, "border" + this + "Width")) || 0), s && (n -= parseFloat(e.css(t, "margin" + this)) || 0)
			}), n
		}
		var r = n === "Width" ? ["Left", "Right"] : ["Top", "Bottom"],
			i = n.toLowerCase(),
			s = {
				innerWidth: e.fn.innerWidth,
				innerHeight: e.fn.innerHeight,
				outerWidth: e.fn.outerWidth,
				outerHeight: e.fn.outerHeight
			};
		e.fn["inner" + n] = function(t) {
			return t === undefined ? s["inner" + n].call(this) : this.each(function() {
				e(this).css(i, o(this, t) + "px")
			})
		}, e.fn["outer" + n] = function(t, r) {
			return typeof t != "number" ? s["outer" + n].call(this, t) : this.each(function() {
				e(this).css(i, o(this, t, !0, r) + "px")
			})
		}
	}), e.fn.addBack || (e.fn.addBack = function(e) {
		return this.add(e == null ? this.prevObject : this.prevObject.filter(e))
	}), e("<a>").data("a-b", "a").removeData("a-b").data("a-b") && (e.fn.removeData = function(t) {
		return function(n) {
			return arguments.length ? t.call(this, e.camelCase(n)) : t.call(this)
		}
	}(e.fn.removeData)), e.ui.ie = !!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase()), e.fn.extend({
		focus: function(t) {
			return function(n, r) {
				return typeof n == "number" ? this.each(function() {
					var t = this;
					setTimeout(function() {
						e(t).focus(), r && r.call(t)
					}, n)
				}) : t.apply(this, arguments)
			}
		}(e.fn.focus),
		disableSelection: function() {
			var e = "onselectstart" in document.createElement("div") ? "selectstart" : "mousedown";
			return function() {
				return this.bind(e + ".ui-disableSelection", function(e) {
					e.preventDefault()
				})
			}
		}(),
		enableSelection: function() {
			return this.unbind(".ui-disableSelection")
		},
		zIndex: function(t) {
			if (t !== undefined) return this.css("zIndex", t);
			if (this.length) {
				var n = e(this[0]),
					r, i;
				while (n.length && n[0] !== document) {
					r = n.css("position");
					if (r === "absolute" || r === "relative" || r === "fixed") {
						i = parseInt(n.css("zIndex"), 10);
						if (!isNaN(i) && i !== 0) return i
					}
					n = n.parent()
				}
			}
			return 0
		}
	}), e.ui.plugin = {
		add: function(t, n, r) {
			var i, s = e.ui[t].prototype;
			for (i in r) s.plugins[i] = s.plugins[i] || [], s.plugins[i].push([n, r[i]])
		},
		call: function(e, t, n, r) {
			var i, s = e.plugins[t];
			if (!s) return;
			if (!r && (!e.element[0].parentNode || e.element[0].parentNode.nodeType === 11)) return;
			for (i = 0; i < s.length; i++) e.options[s[i][0]] && s[i][1].apply(e.element, n)
		}
	}
});