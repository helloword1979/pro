/** vim: et:ts=4:sw=4:sts=4
 * @license RequireJS 2.1.15 Copyright (c) 2010-2014, The Dojo Foundation All Rights Reserved.
 * Available via the MIT or new BSD license.
 * see: http://github.com/jrburke/requirejs for details
 */

/*!
 * jQuery JavaScript Library v1.11.1
 * http://jquery.com/
 *
 * Includes Sizzle.js
 * http://sizzlejs.com/
 *
 * Copyright 2005, 2014 jQuery Foundation, Inc. and other contributors
 * Released under the MIT license
 * http://jquery.org/license
 *
 * Date: 2014-05-01T17:42Z
 */

/*!
 * Sizzle CSS Selector Engine v1.10.19
 * http://sizzlejs.com/
 *
 * Copyright 2013 jQuery Foundation, Inc. and other contributors
 * Released under the MIT license
 * http://jquery.org/license
 *
 * Date: 2014-04-18
 */

var requirejs, require, define;
(function(global) {
	function isFunction(e) {
		return ostring.call(e) === "[object Function]"
	}

	function isArray(e) {
		return ostring.call(e) === "[object Array]"
	}

	function each(e, t) {
		if (e) {
			var n;
			for (n = 0; n < e.length; n += 1)
				if (e[n] && t(e[n], n, e)) break
		}
	}

	function eachReverse(e, t) {
		if (e) {
			var n;
			for (n = e.length - 1; n > -1; n -= 1)
				if (e[n] && t(e[n], n, e)) break
		}
	}

	function hasProp(e, t) {
		return hasOwn.call(e, t)
	}

	function getOwn(e, t) {
		return hasProp(e, t) && e[t]
	}

	function eachProp(e, t) {
		var n;
		for (n in e)
			if (hasProp(e, n) && t(e[n], n)) break
	}

	function mixin(e, t, n, r) {
		return t && eachProp(t, function(t, i) {
			if (n || !hasProp(e, i)) r && typeof t == "object" && t && !isArray(t) && !isFunction(t) && !(t instanceof RegExp) ? (e[i] || (e[i] = {}), mixin(e[i], t, n, r)) : e[i] = t
		}), e
	}

	function bind(e, t) {
		return function() {
			return t.apply(e, arguments)
		}
	}

	function scripts() {
		return document.getElementsByTagName("script")
	}

	function defaultOnError(e) {
		throw e
	}

	function getGlobal(e) {
		if (!e) return e;
		var t = global;
		return each(e.split("."), function(e) {
			t = t[e]
		}), t
	}

	function makeError(e, t, n, r) {
		var i = new Error(t + "\nhttp://requirejs.org/docs/errors.html#" + e);
		return i.requireType = e, i.requireModules = r, n && (i.originalError = n), i
	}

	function newContext(e) {
		function m(e) {
			var t, n;
			for (t = 0; t < e.length; t++) {
				n = e[t];
				if (n === ".") e.splice(t, 1), t -= 1;
				else if (n === "..") {
					if (t === 0 || t == 1 && e[2] === ".." || e[t - 1] === "..") continue;
					t > 0 && (e.splice(t - 1, 2), t -= 2)
				}
			}
		}

		function g(e, t, n) {
			var r, i, s, u, a, f, l, c, h, p, d, v, g = t && t.split("/"),
				y = o.map,
				b = y && y["*"];
			e && (e = e.split("/"), l = e.length - 1, o.nodeIdCompat && jsSuffixRegExp.test(e[l]) && (e[l] = e[l].replace(jsSuffixRegExp, "")), e[0].charAt(0) === "." && g && (v = g.slice(0, g.length - 1), e = v.concat(e)), m(e), e = e.join("/"));
			if (n && y && (g || b)) {
				s = e.split("/");
				e: for (u = s.length; u > 0; u -= 1) {
					f = s.slice(0, u).join("/");
					if (g)
						for (a = g.length; a > 0; a -= 1) {
							i = getOwn(y, g.slice(0, a).join("/"));
							if (i) {
								i = getOwn(i, f);
								if (i) {
									c = i, h = u;
									break e
								}
							}
						}!p && b && getOwn(b, f) && (p = getOwn(b, f), d = u)
				}!c && p && (c = p, h = d), c && (s.splice(0, h, c), e = s.join("/"))
			}
			return r = getOwn(o.pkgs, e), r ? r : e
		}

		function y(e) {
			isBrowser && each(scripts(), function(t) {
				if (t.getAttribute("data-requiremodule") === e && t.getAttribute("data-requirecontext") === r.contextName) return t.parentNode.removeChild(t), !0
			})
		}

		function b(e) {
			var t = getOwn(o.paths, e);
			if (t && isArray(t) && t.length > 1) return t.shift(), r.require.undef(e), r.makeRequire(null, {
				skipMap: !0
			})([e]), !0
		}

		function w(e) {
			var t, n = e ? e.indexOf("!") : -1;
			return n > -1 && (t = e.substring(0, n), e = e.substring(n + 1, e.length)), [t, e]
		}

		function E(e, t, n, i) {
			var s, o, u, a, f = null,
				l = t ? t.name : null,
				h = e,
				p = !0,
				m = "";
			return e || (p = !1, e = "_@r" + (d += 1)), a = w(e), f = a[0], e = a[1], f && (f = g(f, l, i), o = getOwn(c, f)), e && (f ? o && o.normalize ? m = o.normalize(e, function(e) {
				return g(e, l, i)
			}) : m = e.indexOf("!") === -1 ? g(e, l, i) : e : (m = g(e, l, i), a = w(m), f = a[0], m = a[1], n = !0, s = r.nameToUrl(m))), u = f && !o && !n ? "_unnormalized" + (v += 1) : "", {
				prefix: f,
				name: m,
				parentMap: t,
				unnormalized: !!u,
				url: s,
				originalName: h,
				isDefine: p,
				id: (f ? f + "!" + m : m) + u
			}
		}

		function S(e) {
			var t = e.id,
				n = getOwn(u, t);
			return n || (n = u[t] = new r.Module(e)), n
		}

		function x(e, t, n) {
			var r = e.id,
				i = getOwn(u, r);
			hasProp(c, r) && (!i || i.defineEmitComplete) ? t === "defined" && n(c[r]) : (i = S(e), i.error && t === "error" ? n(i.error) : i.on(t, n))
		}

		function T(e, t) {
			var n = e.requireModules,
				r = !1;
			t ? t(e) : (each(n, function(t) {
				var n = getOwn(u, t);
				n && (n.error = e, n.events.error && (r = !0, n.emit("error", e)))
			}), r || req.onError(e))
		}

		function N() {
			globalDefQueue.length && (apsp.apply(l, [l.length, 0].concat(globalDefQueue)), globalDefQueue = [])
		}

		function C(e) {
			delete u[e], delete a[e]
		}

		function k(e, t, n) {
			var r = e.map.id;
			e.error ? e.emit("error", e.error) : (t[r] = !0, each(e.depMaps, function(r, i) {
				var s = r.id,
					o = getOwn(u, s);
				o && !e.depMatched[i] && !n[s] && (getOwn(t, s) ? (e.defineDep(i, c[s]), e.check()) : k(o, t, n))
			}), n[r] = !0)
		}

		function L() {
			var e, n, i = o.waitSeconds * 1e3,
				u = i && r.startTime + i < (new Date).getTime(),
				f = [],
				l = [],
				c = !1,
				h = !0;
			if (t) return;
			t = !0, eachProp(a, function(e) {
				var t = e.map,
					r = t.id;
				if (!e.enabled) return;
				t.isDefine || l.push(e);
				if (!e.error)
					if (!e.inited && u) b(r) ? (n = !0, c = !0) : (f.push(r), y(r));
					else if (!e.inited && e.fetched && t.isDefine) {
					c = !0;
					if (!t.prefix) return h = !1
				}
			});
			if (u && f.length) return e = makeError("timeout", "Load timeout for modules: " + f, null, f), e.contextName = r.contextName, T(e);
			h && each(l, function(e) {
				k(e, {}, {})
			}), (!u || n) && c && (isBrowser || isWebWorker) && !s && (s = setTimeout(function() {
				s = 0, L()
			}, 50)), t = !1
		}

		function A(e) {
			hasProp(c, e[0]) || S(E(e[0], null, !0)).init(e[1], e[2])
		}

		function O(e, t, n, r) {
			e.detachEvent && !isOpera ? r && e.detachEvent(r, t) : e.removeEventListener(n, t, !1)
		}

		function M(e) {
			var t = e.currentTarget || e.srcElement;
			return O(t, r.onScriptLoad, "load", "onreadystatechange"), O(t, r.onScriptError, "error"), {
				node: t,
				id: t && t.getAttribute("data-requiremodule")
			}
		}

		function _() {
			var e;
			N();
			while (l.length) {
				e = l.shift();
				if (e[0] === null) return T(makeError("mismatch", "Mismatched anonymous define() module2: " + e[e.length - 1]));
				A(e)
			}
		}
		var t, n, r, i, s, o = {
				waitSeconds: 7,
				baseUrl: "./",
				paths: {},
				bundles: {},
				pkgs: {},
				shim: {},
				config: {}
			},
			u = {},
			a = {},
			f = {},
			l = [],
			c = {},
			h = {},
			p = {},
			d = 1,
			v = 1;
		return i = {
			require: function(e) {
				return e.require ? e.require : e.require = r.makeRequire(e.map)
			},
			exports: function(e) {
				e.usingExports = !0;
				if (e.map.isDefine) return e.exports ? c[e.map.id] = e.exports : e.exports = c[e.map.id] = {}
			},
			module: function(e) {
				return e.module ? e.module : e.module = {
					id: e.map.id,
					uri: e.map.url,
					config: function() {
						return getOwn(o.config, e.map.id) || {}
					},
					exports: e.exports || (e.exports = {})
				}
			}
		}, n = function(e) {
			this.events = getOwn(f, e.id) || {}, this.map = e, this.shim = getOwn(o.shim, e.id), this.depExports = [], this.depMaps = [], this.depMatched = [], this.pluginMaps = {}, this.depCount = 0
		}, n.prototype = {
			init: function(e, t, n, r) {
				r = r || {};
				if (this.inited) return;
				this.factory = t, n ? this.on("error", n) : this.events.error && (n = bind(this, function(e) {
					this.emit("error", e)
				})), this.depMaps = e && e.slice(0), this.errback = n, this.inited = !0, this.ignore = r.ignore, r.enabled || this.enabled ? this.enable() : this.check()
			},
			defineDep: function(e, t) {
				this.depMatched[e] || (this.depMatched[e] = !0, this.depCount -= 1, this.depExports[e] = t)
			},
			fetch: function() {
				if (this.fetched) return;
				this.fetched = !0, r.startTime = (new Date).getTime();
				var e = this.map;
				if (!this.shim) return e.prefix ? this.callPlugin() : this.load();
				r.makeRequire(this.map, {
					enableBuildCallback: !0
				})(this.shim.deps || [], bind(this, function() {
					return e.prefix ? this.callPlugin() : this.load()
				}))
			},
			load: function() {
				var e = this.map.url;
				h[e] || (h[e] = !0, r.load(this.map.id, e))
			},
			check: function() {
				if (!this.enabled || this.enabling) return;
				var e, t, n = this.map.id,
					i = this.depExports,
					s = this.exports,
					o = this.factory;
				if (!this.inited) this.fetch();
				else if (this.error) this.emit("error", this.error);
				else if (!this.defining) {
					this.defining = !0;
					if (this.depCount < 1 && !this.defined) {
						if (isFunction(o)) {
							if (this.events.error && this.map.isDefine || req.onError !== defaultOnError) try {
								s = r.execCb(n, o, i, s)
							} catch (u) {
								e = u
							} else s = r.execCb(n, o, i, s);
							this.map.isDefine && s === undefined && (t = this.module, t ? s = t.exports : this.usingExports && (s = this.exports));
							if (e) return e.requireMap = this.map, e.requireModules = this.map.isDefine ? [this.map.id] : null, e.requireType = this.map.isDefine ? "define" : "require", T(this.error = e)
						} else s = o;
						this.exports = s, this.map.isDefine && !this.ignore && (c[n] = s, req.onResourceLoad && req.onResourceLoad(r, this.map, this.depMaps)), C(n), this.defined = !0
					}
					this.defining = !1, this.defined && !this.defineEmitted && (this.defineEmitted = !0, this.emit("defined", this.exports), this.defineEmitComplete = !0)
				}
			},
			callPlugin: function() {
				var e = this.map,
					t = e.id,
					n = E(e.prefix);
				this.depMaps.push(n), x(n, "defined", bind(this, function(n) {
					var i, s, a, f = getOwn(p, this.map.id),
						l = this.map.name,
						c = this.map.parentMap ? this.map.parentMap.name : null,
						h = r.makeRequire(e.parentMap, {
							enableBuildCallback: !0
						});
					if (this.map.unnormalized) {
						n.normalize && (l = n.normalize(l, function(e) {
							return g(e, c, !0)
						}) || ""), s = E(e.prefix + "!" + l, this.map.parentMap), x(s, "defined", bind(this, function(e) {
							this.init([], function() {
								return e
							}, null, {
								enabled: !0,
								ignore: !0
							})
						})), a = getOwn(u, s.id), a && (this.depMaps.push(s), this.events.error && a.on("error", bind(this, function(e) {
							this.emit("error", e)
						})), a.enable());
						return
					}
					if (f) {
						this.map.url = r.nameToUrl(f), this.load();
						return
					}
					i = bind(this, function(e) {
						this.init([], function() {
							return e
						}, null, {
							enabled: !0
						})
					}), i.error = bind(this, function(e) {
						this.inited = !0, this.error = e, e.requireModules = [t], eachProp(u, function(e) {
							e.map.id.indexOf(t + "_unnormalized") === 0 && C(e.map.id)
						}), T(e)
					}), i.fromText = bind(this, function(n, s) {
						var u = e.name,
							a = E(u),
							f = useInteractive;
						s && (n = s), f && (useInteractive = !1), S(a), hasProp(o.config, t) && (o.config[u] = o.config[t]);
						try {
							req.exec(n)
						} catch (l) {
							return T(makeError("fromtexteval", "fromText eval for " + t + " failed: " + l, l, [t]))
						}
						f && (useInteractive = !0), this.depMaps.push(a), r.completeLoad(u), h([u], i)
					}), n.load(e.name, h, i, o)
				})), r.enable(n, this), this.pluginMaps[n.id] = n
			},
			enable: function() {
				a[this.map.id] = this, this.enabled = !0, this.enabling = !0, each(this.depMaps, bind(this, function(e, t) {
					var n, s, o;
					if (typeof e == "string") {
						e = E(e, this.map.isDefine ? this.map : this.map.parentMap, !1, !this.skipMap), this.depMaps[t] = e, o = getOwn(i, e.id);
						if (o) {
							this.depExports[t] = o(this);
							return
						}
						this.depCount += 1, x(e, "defined", bind(this, function(e) {
							this.defineDep(t, e), this.check()
						})), this.errback && x(e, "error", bind(this, this.errback))
					}
					n = e.id, s = u[n], !hasProp(i, n) && s && !s.enabled && r.enable(e, this)
				})), eachProp(this.pluginMaps, bind(this, function(e) {
					var t = getOwn(u, e.id);
					t && !t.enabled && r.enable(e, this)
				})), this.enabling = !1, this.check()
			},
			on: function(e, t) {
				var n = this.events[e];
				n || (n = this.events[e] = []), n.push(t)
			},
			emit: function(e, t) {
				each(this.events[e], function(e) {
					e(t)
				}), e === "error" && delete this.events[e]
			}
		}, r = {
			config: o,
			contextName: e,
			registry: u,
			defined: c,
			urlFetched: h,
			defQueue: l,
			Module: n,
			makeModuleMap: E,
			nextTick: req.nextTick,
			onError: T,
			configure: function(e) {
				e.baseUrl && e.baseUrl.charAt(e.baseUrl.length - 1) !== "/" && (e.baseUrl += "/");
				var t = o.shim,
					n = {
						paths: !0,
						bundles: !0,
						config: !0,
						map: !0
					};
				eachProp(e, function(e, t) {
					n[t] ? (o[t] || (o[t] = {}), mixin(o[t], e, !0, !0)) : o[t] = e
				}), e.bundles && eachProp(e.bundles, function(e, t) {
					each(e, function(e) {
						e !== t && (p[e] = t)
					})
				}), e.shim && (eachProp(e.shim, function(e, n) {
					isArray(e) && (e = {
						deps: e
					}), (e.exports || e.init) && !e.exportsFn && (e.exportsFn = r.makeShimExports(e)), t[n] = e
				}), o.shim = t), e.packages && each(e.packages, function(e) {
					var t, n;
					e = typeof e == "string" ? {
						name: e
					} : e, n = e.name, t = e.location, t && (o.paths[n] = e.location), o.pkgs[n] = e.name + "/" + (e.main || "main").replace(currDirRegExp, "").replace(jsSuffixRegExp, "")
				}), eachProp(u, function(e, t) {
					!e.inited && !e.map.unnormalized && (e.map = E(t))
				}), (e.deps || e.callback) && r.require(e.deps || [], e.callback)
			},
			makeShimExports: function(e) {
				function t() {
					var t;
					return e.init && (t = e.init.apply(global, arguments)), t || e.exports && getGlobal(e.exports)
				}
				return t
			},
			makeRequire: function(t, n) {
				function s(o, a, f) {
					var l, h, p;
					return n.enableBuildCallback && a && isFunction(a) && (a.__requireJsBuild = !0), typeof o == "string" ? isFunction(a) ? T(makeError("requireargs", "Invalid require call"), f) : t && hasProp(i, o) ? i[o](u[t.id]) : req.get ? req.get(r, o, t, s) : (h = E(o, t, !1, !0), l = h.id, hasProp(c, l) ? c[l] : T(makeError("notloaded", 'Module name "' + l + '" has not been loaded yet for context: ' + e + (t ? "" : ". Use require([])")))) : (_(), r.nextTick(function() {
						_(), p = S(E(null, t)), p.skipMap = n.skipMap, p.init(o, a, f, {
							enabled: !0
						}), L()
					}), s)
				}
				return n = n || {}, mixin(s, {
					isBrowser: isBrowser,
					toUrl: function(e) {
						var n, i = e.lastIndexOf("."),
							s = e.split("/")[0],
							o = s === "." || s === "..";
						return i !== -1 && (!o || i > 1) && (n = e.substring(i, e.length), e = e.substring(0, i)), r.nameToUrl(g(e, t && t.id, !0), n, !0)
					},
					defined: function(e) {
						return hasProp(c, E(e, t, !1, !0).id)
					},
					specified: function(e) {
						return e = E(e, t, !1, !0).id, hasProp(c, e) || hasProp(u, e)
					}
				}), t || (s.undef = function(e) {
					N();
					var n = E(e, t, !0),
						r = getOwn(u, e);
					y(e), delete c[e], delete h[n.url], delete f[e], eachReverse(l, function(t, n) {
						t[0] === e && l.splice(n, 1)
					}), r && (r.events.defined && (f[e] = r.events), C(e))
				}), s
			},
			enable: function(e) {
				var t = getOwn(u, e.id);
				t && S(e).enable()
			},
			completeLoad: function(e) {
				var t, n, r, i = getOwn(o.shim, e) || {},
					s = i.exports;
				N();
				while (l.length) {
					n = l.shift();
					if (n[0] === null) {
						n[0] = e;
						if (t) break;
						t = !0
					} else n[0] === e && (t = !0);
					A(n)
				}
				r = getOwn(u, e);
				if (!t && !hasProp(c, e) && r && !r.inited) {
					if (o.enforceDefine && (!s || !getGlobal(s))) {
						if (b(e)) return;
						return T(makeError("nodefine", "No define call for " + e, null, [e]))
					}
					A([e, i.deps || [], i.exportsFn])
				}
				L()
			},
			nameToUrl: function(e, t, n) {
				var i, s, u, a, f, l, c, h = getOwn(o.pkgs, e);
				h && (e = h), c = getOwn(p, e);
				if (c) return r.nameToUrl(c, t, n);
				if (req.jsExtRegExp.test(e)) f = e + (t || "");
				else {
					i = o.paths, s = e.split("/");
					for (u = s.length; u > 0; u -= 1) {
						a = s.slice(0, u).join("/"), l = getOwn(i, a);
						if (l) {
							isArray(l) && (l = l[0]), s.splice(0, u, l);
							break
						}
					}
					f = s.join("/"), f += t || (/^data\:|\?/.test(f) || n ? "" : ".js"),

					f = (f.charAt(0) === "/" || f.match(/^[\w\+\.\-]+:/) ? "" : o.baseUrl) + f
				}
				return o.urlArgs ? f + ((f.indexOf("?") === -1 ? "?" : "&") + o.urlArgs) : f
			},
			load: function(e, t) {
				req.load(r, e, t)
			},
			execCb: function(e, t, n, r) {
				return t.apply(r, n)
			},
			onScriptLoad: function(e) {
				if (e.type === "load" || readyRegExp.test((e.currentTarget || e.srcElement).readyState)) {
					interactiveScript = null;
					var t = M(e);
					r.completeLoad(t.id)
				}
			},
			onScriptError: function(e) {
				var t = M(e);
				if (!b(t.id)) return T(makeError("scripterror", "Script error for: " + t.id, e, [t.id]))
			}
		}, r.require = r.makeRequire(), r
	}

	function getInteractiveScript() {
		return interactiveScript && interactiveScript.readyState === "interactive" ? interactiveScript : (eachReverse(scripts(), function(e) {
			if (e.readyState === "interactive") return interactiveScript = e
		}), interactiveScript)
	}
	var req, s, head, baseElement, dataMain, src, interactiveScript, currentlyAddingScript, mainScript, subPath, version = "2.1.15",
		commentRegExp = /(\/\*([\s\S]*?)\*\/|([^:]|^)\/\/(.*)$)/mg,
		cjsRequireRegExp = /[^.]\s*require\s*\(\s*["']([^'"\s]+)["']\s*\)/g,
		jsSuffixRegExp = /\.js$/,
		currDirRegExp = /^\.\//,
		op = Object.prototype,
		ostring = op.toString,
		hasOwn = op.hasOwnProperty,
		ap = Array.prototype,
		apsp = ap.splice,
		isBrowser = typeof window != "undefined" && typeof navigator != "undefined" && !!window.document,
		isWebWorker = !isBrowser && typeof importScripts != "undefined",
		readyRegExp = isBrowser && navigator.platform === "PLAYSTATION 3" ? /^complete$/ : /^(complete|loaded)$/,
		defContextName = "_",
		isOpera = typeof opera != "undefined" && opera.toString() === "[object Opera]",
		contexts = {},
		cfg = {},
		globalDefQueue = [],
		useInteractive = !1;
	if (typeof define != "undefined") return;
	if (typeof requirejs != "undefined") {
		if (isFunction(requirejs)) return;
		cfg = requirejs, requirejs = undefined
	}
	typeof require != "undefined" && !isFunction(require) && (cfg = require, require = undefined), req = requirejs = function(e, t, n, r) {
		var i, s, o = defContextName;
		return !isArray(e) && typeof e != "string" && (s = e, isArray(t) ? (e = t, t = n, n = r) : e = []), s && s.betContext && (o = s.betContext), i = getOwn(contexts, o), i || (i = contexts[o] = req.s.newContext(o)), s && i.configure(s), i.require(e, t, n)
	}, req.config = function(e) {
		return req(e)
	}, req.nextTick = typeof setTimeout != "undefined" ? function(e) {
		setTimeout(e, 4)
	} : function(e) {
		e()
	}, require || (require = req), req.version = version, req.jsExtRegExp = /^\/|:|\?|\.js$/, req.isBrowser = isBrowser, s = req.s = {
		contexts: contexts,
		newContext: newContext
	}, req({}), each(["toUrl", "undef", "defined", "specified"], function(e) {
		req[e] = function() {
			var t = contexts[defContextName];
			return t.require[e].apply(t, arguments)
		}
	}), isBrowser && (head = s.head = document.getElementsByTagName("head")[0], baseElement = document.getElementsByTagName("base")[0], baseElement && (head = s.head = baseElement.parentNode)), req.onError = defaultOnError, req.createNode = function(e, t, n) {
		var r = e.xhtml ? document.createElementNS("http://www.w3.org/1999/xhtml", "html:script") : document.createElement("script");
		return r.type = e.scriptType || "text/javascript", r.charset = "utf-8", r.async = !0, r
	}, req.load = function(e, t, n) {
		var r = e && e.config || {},
			i;
		if (isBrowser) return i = req.createNode(r, t, n), i.setAttribute("data-requirecontext", e.contextName), i.setAttribute("data-requiremodule", t), i.attachEvent && !(i.attachEvent.toString && i.attachEvent.toString().indexOf("[native code") < 0) && !isOpera ? (useInteractive = !0, i.attachEvent("onreadystatechange", e.onScriptLoad)) : (i.addEventListener("load", e.onScriptLoad, !1), i.addEventListener("error", e.onScriptError, !1)), i.src = n, currentlyAddingScript = i, baseElement ? head.insertBefore(i, baseElement) : head.appendChild(i), currentlyAddingScript = null, i;
		if (isWebWorker) try {
			importScripts(n), e.completeLoad(t)
		} catch (s) {
			e.onError(makeError("importscripts", "importScripts failed for " + t + " at " + n, s, [t]))
		}
	}, isBrowser && !cfg.skipDataMain && eachReverse(scripts(), function(e) {
		head || (head = e.parentNode), dataMain = e.getAttribute("data-main");
		if (dataMain) return mainScript = dataMain, cfg.baseUrl || (src = mainScript.split("/"), mainScript = src.pop(), subPath = src.length ? src.join("/") + "/" : "./", cfg.baseUrl = subPath), mainScript = mainScript.replace(jsSuffixRegExp, ""), req.jsExtRegExp.test(mainScript) && (mainScript = dataMain), cfg.deps = cfg.deps ? cfg.deps.concat(mainScript) : [mainScript], !0
	}), define = function(e, t, n) {
		var r, i;
		typeof e != "string" && (n = t, t = e, e = null), isArray(t) || (n = t, t = null), !t && isFunction(n) && (t = [], n.length && (n.toString().replace(commentRegExp, "").replace(cjsRequireRegExp, function(e, n) {
			t.push(n)
		}), t = (n.length === 1 ? ["require"] : ["require", "exports", "module"]).concat(t))), useInteractive && (r = currentlyAddingScript || getInteractiveScript(), r && (e || (e = r.getAttribute("data-requiremodule")), i = contexts[r.getAttribute("data-requirecontext")])), (i ? i.defQueue : globalDefQueue).push([e, t, n])
	}, define.amd = {
		jQuery: !0
	}, req.exec = function(text) {
		return eval(text)
	}, req(cfg)
})(this),
function() {
	"use strict";
	window.console || (window.console = {
		log: function() {}
	});
	var e = [];
	window.JSON || e.push("json"), requirejs.config({
		baseUrl: "/assets/dist/scripts/lib",
		deps: e,
		paths: {
			app: "../app",
			jquery: "jquery/dist/jquery",
			json: "json3/lib/json3",
			underscore: "underscore/underscore",
			dialog: "art-dialog/index",
			cookie: "jquery-cookie/jquery.cookie",
			mustache: "mustache/mustache"
		},
		map: {
			"*": {
				css: "require-css/css"
			}
		},
		urlArgs: 1450712701938,
		waitSeconds: 50
	})
}(),
function(e, t) {
	typeof module == "object" && typeof module.exports == "object" ? module.exports = e.document ? t(e, !0) : function(e) {
		if (!e.document) throw new Error("jQuery requires a window with a document");
		return t(e)
	} : t(e)
}(typeof window != "undefined" ? window : this, function(e, t) {
	function g(e) {
		var t = e.length,
			n = h.type(e);
		return n === "function" || h.isWindow(e) ? !1 : e.nodeType === 1 && t ? !0 : n === "array" || t === 0 || typeof t == "number" && t > 0 && t - 1 in e
	}

	function S(e, t, n) {
		if (h.isFunction(t)) return h.grep(e, function(e, r) {
			return !!t.call(e, r, e) !== n
		});
		if (t.nodeType) return h.grep(e, function(e) {
			return e === t !== n
		});
		if (typeof t == "string") {
			if (E.test(t)) return h.filter(t, e, n);
			t = h.filter(t, e)
		}
		return h.grep(e, function(e) {
			return h.inArray(e, t) >= 0 !== n
		})
	}

	function A(e, t) {
		do e = e[t]; while (e && e.nodeType !== 1);
		return e
	}

	function _(e) {
		var t = M[e] = {};
		return h.each(e.match(O) || [], function(e, n) {
			t[n] = !0
		}), t
	}

	function P() {
		T.addEventListener ? (T.removeEventListener("DOMContentLoaded", H, !1), e.removeEventListener("load", H, !1)) : (T.detachEvent("onreadystatechange", H), e.detachEvent("onload", H))
	}

	function H() {
		if (T.addEventListener || event.type === "load" || T.readyState === "complete") P(), h.ready()
	}

	function q(e, t, n) {
		if (n === undefined && e.nodeType === 1) {
			var r = "data-" + t.replace(I, "-$1").toLowerCase();
			n = e.getAttribute(r);
			if (typeof n == "string") {
				try {
					n = n === "true" ? !0 : n === "false" ? !1 : n === "null" ? null : +n + "" === n ? +n : F.test(n) ? h.parseJSON(n) : n
				} catch (i) {}
				h.data(e, t, n)
			} else n = undefined
		}
		return n
	}

	function R(e) {
		var t;
		for (t in e) {
			if (t === "data" && h.isEmptyObject(e[t])) continue;
			if (t !== "toJSON") return !1
		}
		return !0
	}

	function U(e, t, r, i) {
		if (!h.acceptData(e)) return;
		var s, o, u = h.expando,
			a = e.nodeType,
			f = a ? h.cache : e,
			l = a ? e[u] : e[u] && u;
		if ((!l || !f[l] || !i && !f[l].data) && r === undefined && typeof t == "string") return;
		l || (a ? l = e[u] = n.pop() || h.guid++ : l = u), f[l] || (f[l] = a ? {} : {
			toJSON: h.noop
		});
		if (typeof t == "object" || typeof t == "function") i ? f[l] = h.extend(f[l], t) : f[l].data = h.extend(f[l].data, t);
		return o = f[l], i || (o.data || (o.data = {}), o = o.data), r !== undefined && (o[h.camelCase(t)] = r), typeof t == "string" ? (s = o[t], s == null && (s = o[h.camelCase(t)])) : s = o, s
	}

	function z(e, t, n) {
		if (!h.acceptData(e)) return;
		var r, i, s = e.nodeType,
			o = s ? h.cache : e,
			u = s ? e[h.expando] : h.expando;
		if (!o[u]) return;
		if (t) {
			r = n ? o[u] : o[u].data;
			if (r) {
				h.isArray(t) ? t = t.concat(h.map(t, h.camelCase)) : t in r ? t = [t] : (t = h.camelCase(t), t in r ? t = [t] : t = t.split(" ")), i = t.length;
				while (i--) delete r[t[i]];
				if (n ? !R(r) : !h.isEmptyObject(r)) return
			}
		}
		if (!n) {
			delete o[u].data;
			if (!R(o[u])) return
		}
		s ? h.cleanData([e], !0) : l.deleteExpando || o != o.window ? delete o[u] : o[u] = null
	}

	function et() {
		return !0
	}

	function tt() {
		return !1
	}

	function nt() {
		try {
			return T.activeElement
		} catch (e) {}
	}

	function rt(e) {
		var t = it.split("|"),
			n = e.createDocumentFragment();
		if (n.createElement)
			while (t.length) n.createElement(t.pop());
		return n
	}

	function wt(e, t) {
		var n, r, i = 0,
			s = typeof e.getElementsByTagName !== B ? e.getElementsByTagName(t || "*") : typeof e.querySelectorAll !== B ? e.querySelectorAll(t || "*") : undefined;
		if (!s)
			for (s = [], n = e.childNodes || e;
				(r = n[i]) != null; i++) !t || h.nodeName(r, t) ? s.push(r) : h.merge(s, wt(r, t));
		return t === undefined || t && h.nodeName(e, t) ? h.merge([e], s) : s
	}

	function Et(e) {
		J.test(e.type) && (e.defaultChecked = e.checked)
	}

	function St(e, t) {
		return h.nodeName(e, "table") && h.nodeName(t.nodeType !== 11 ? t : t.firstChild, "tr") ? e.getElementsByTagName("tbody")[0] || e.appendChild(e.ownerDocument.createElement("tbody")) : e
	}

	function xt(e) {
		return e.type = (h.find.attr(e, "type") !== null) + "/" + e.type, e
	}

	function Tt(e) {
		var t = vt.exec(e.type);
		return t ? e.type = t[1] : e.removeAttribute("type"), e
	}

	function Nt(e, t) {
		var n, r = 0;
		for (;
			(n = e[r]) != null; r++) h._data(n, "globalEval", !t || h._data(t[r], "globalEval"))
	}

	function Ct(e, t) {
		if (t.nodeType !== 1 || !h.hasData(e)) return;
		var n, r, i, s = h._data(e),
			o = h._data(t, s),
			u = s.events;
		if (u) {
			delete o.handle, o.events = {};
			for (n in u)
				for (r = 0, i = u[n].length; r < i; r++) h.event.add(t, n, u[n][r])
		}
		o.data && (o.data = h.extend({}, o.data))
	}

	function kt(e, t) {
		var n, r, i;
		if (t.nodeType !== 1) return;
		n = t.nodeName.toLowerCase();
		if (!l.noCloneEvent && t[h.expando]) {
			i = h._data(t);
			for (r in i.events) h.removeEvent(t, r, i.handle);
			t.removeAttribute(h.expando)
		}
		if (n === "script" && t.text !== e.text) xt(t).text = e.text, Tt(t);
		else if (n === "object") t.parentNode && (t.outerHTML = e.outerHTML), l.html5Clone && e.innerHTML && !h.trim(t.innerHTML) && (t.innerHTML = e.innerHTML);
		else if (n === "input" && J.test(e.type)) t.defaultChecked = t.checked = e.checked, t.value !== e.value && (t.value = e.value);
		else if (n === "option") t.defaultSelected = t.selected = e.defaultSelected;
		else if (n === "input" || n === "textarea") t.defaultValue = e.defaultValue
	}

	function Ot(t, n) {
		var r, i = h(n.createElement(t)).appendTo(n.body),
			s = e.getDefaultComputedStyle && (r = e.getDefaultComputedStyle(i[0])) ? r.display : h.css(i[0], "display");
		return i.detach(), s
	}

	function Mt(e) {
		var t = T,
			n = At[e];
		if (!n) {
			n = Ot(e, t);
			if (n === "none" || !n) Lt = (Lt || h("<iframe frameborder='0' width='0' height='0'/>")).appendTo(t.documentElement), t = (Lt[0].contentWindow || Lt[0].contentDocument).document, t.write(), t.close(), n = Ot(e, t), Lt.detach();
			At[e] = n
		}
		return n
	}

	function jt(e, t) {
		return {
			get: function() {
				var n = e();
				if (n == null) return;
				if (n) {
					delete this.get;
					return
				}
				return (this.get = t).apply(this, arguments)
			}
		}
	}

	function Vt(e, t) {
		if (t in e) return t;
		var n = t.charAt(0).toUpperCase() + t.slice(1),
			r = t,
			i = Xt.length;
		while (i--) {
			t = Xt[i] + n;
			if (t in e) return t
		}
		return r
	}

	function $t(e, t) {
		var n, r, i, s = [],
			o = 0,
			u = e.length;
		for (; o < u; o++) {
			r = e[o];
			if (!r.style) continue;
			s[o] = h._data(r, "olddisplay"), n = r.style.display, t ? (!s[o] && n === "none" && (r.style.display = ""), r.style.display === "" && V(r) && (s[o] = h._data(r, "olddisplay", Mt(r.nodeName)))) : (i = V(r), (n && n !== "none" || !i) && h._data(r, "olddisplay", i ? n : h.css(r, "display")))
		}
		for (o = 0; o < u; o++) {
			r = e[o];
			if (!r.style) continue;
			if (!t || r.style.display === "none" || r.style.display === "") r.style.display = t ? s[o] || "" : "none"
		}
		return e
	}

	function Jt(e, t, n) {
		var r = Rt.exec(t);
		return r ? Math.max(0, r[1] - (n || 0)) + (r[2] || "px") : t
	}

	function Kt(e, t, n, r, i) {
		var s = n === (r ? "border" : "content") ? 4 : t === "width" ? 1 : 0,
			o = 0;
		for (; s < 4; s += 2) n === "margin" && (o += h.css(e, n + X[s], !0, i)), r ? (n === "content" && (o -= h.css(e, "padding" + X[s], !0, i)), n !== "margin" && (o -= h.css(e, "border" + X[s] + "Width", !0, i))) : (o += h.css(e, "padding" + X[s], !0, i), n !== "padding" && (o += h.css(e, "border" + X[s] + "Width", !0, i)));
		return o
	}

	function Qt(e, t, n) {
		var r = !0,
			i = t === "width" ? e.offsetWidth : e.offsetHeight,
			s = Pt(e),
			o = l.boxSizing && h.css(e, "boxSizing", !1, s) === "border-box";
		if (i <= 0 || i == null) {
			i = Ht(e, t, s);
			if (i < 0 || i == null) i = e.style[t];
			if (Dt.test(i)) return i;
			r = o && (l.boxSizingReliable() || i === e.style[t]), i = parseFloat(i) || 0
		}
		return i + Kt(e, t, n || (o ? "border" : "content"), r, s) + "px"
	}

	function Gt(e, t, n, r, i) {
		return new Gt.prototype.init(e, t, n, r, i)
	}

	function on() {
		return setTimeout(function() {
			Yt = undefined
		}), Yt = h.now()
	}

	function un(e, t) {
		var n, r = {
				height: e
			},
			i = 0;
		t = t ? 1 : 0;
		for (; i < 4; i += 2 - t) n = X[i], r["margin" + n] = r["padding" + n] = e;
		return t && (r.opacity = r.width = e), r
	}

	function an(e, t, n) {
		var r, i = (sn[t] || []).concat(sn["*"]),
			s = 0,
			o = i.length;
		for (; s < o; s++)
			if (r = i[s].call(n, t, e)) return r
	}

	function fn(e, t, n) {
		var r, i, s, o, u, a, f, c, p = this,
			d = {},
			v = e.style,
			m = e.nodeType && V(e),
			g = h._data(e, "fxshow");
		n.queue || (u = h._queueHooks(e, "fx"), u.unqueued == null && (u.unqueued = 0, a = u.empty.fire, u.empty.fire = function() {
			u.unqueued || a()
		}), u.unqueued++, p.always(function() {
			p.always(function() {
				u.unqueued--, h.queue(e, "fx").length || u.empty.fire()
			})
		})), e.nodeType === 1 && ("height" in t || "width" in t) && (n.overflow = [v.overflow, v.overflowX, v.overflowY], f = h.css(e, "display"), c = f === "none" ? h._data(e, "olddisplay") || Mt(e.nodeName) : f, c === "inline" && h.css(e, "float") === "none" && (!l.inlineBlockNeedsLayout || Mt(e.nodeName) === "inline" ? v.display = "inline-block" : v.zoom = 1)), n.overflow && (v.overflow = "hidden", l.shrinkWrapBlocks() || p.always(function() {
			v.overflow = n.overflow[0], v.overflowX = n.overflow[1], v.overflowY = n.overflow[2]
		}));
		for (r in t) {
			i = t[r];
			if (en.exec(i)) {
				delete t[r], s = s || i === "toggle";
				if (i === (m ? "hide" : "show")) {
					if (i !== "show" || !g || g[r] === undefined) continue;
					m = !0
				}
				d[r] = g && g[r] || h.style(e, r)
			} else f = undefined
		}
		if (!h.isEmptyObject(d)) {
			g ? "hidden" in g && (m = g.hidden) : g = h._data(e, "fxshow", {}), s && (g.hidden = !m), m ? h(e).show() : p.done(function() {
				h(e).hide()
			}), p.done(function() {
				var t;
				h._removeData(e, "fxshow");
				for (t in d) h.style(e, t, d[t])
			});
			for (r in d) o = an(m ? g[r] : 0, r, p), r in g || (g[r] = o.start, m && (o.end = o.start, o.start = r === "width" || r === "height" ? 1 : 0))
		} else(f === "none" ? Mt(e.nodeName) : f) === "inline" && (v.display = f)
	}

	function ln(e, t) {
		var n, r, i, s, o;
		for (n in e) {
			r = h.camelCase(n), i = t[r], s = e[n], h.isArray(s) && (i = s[1], s = e[n] = s[0]), n !== r && (e[r] = s, delete e[n]), o = h.cssHooks[r];
			if (o && "expand" in o) {
				s = o.expand(s), delete e[r];
				for (n in s) n in e || (e[n] = s[n], t[n] = i)
			} else t[r] = i
		}
	}

	function cn(e, t, n) {
		var r, i, s = 0,
			o = rn.length,
			u = h.Deferred().always(function() {
				delete a.elem
			}),
			a = function() {
				if (i) return !1;
				var t = Yt || on(),
					n = Math.max(0, f.startTime + f.duration - t),
					r = n / f.duration || 0,
					s = 1 - r,
					o = 0,
					a = f.tweens.length;
				for (; o < a; o++) f.tweens[o].run(s);
				return u.notifyWith(e, [f, s, n]), s < 1 && a ? n : (u.resolveWith(e, [f]), !1)
			},
			f = u.promise({
				elem: e,
				props: h.extend({}, t),
				opts: h.extend(!0, {
					specialEasing: {}
				}, n),
				originalProperties: t,
				originalOptions: n,
				startTime: Yt || on(),
				duration: n.duration,
				tweens: [],
				createTween: function(t, n) {
					var r = h.Tween(e, f.opts, t, n, f.opts.specialEasing[t] || f.opts.easing);
					return f.tweens.push(r), r
				},
				stop: function(t) {
					var n = 0,
						r = t ? f.tweens.length : 0;
					if (i) return this;
					i = !0;
					for (; n < r; n++) f.tweens[n].run(1);
					return t ? u.resolveWith(e, [f, t]) : u.rejectWith(e, [f, t]), this
				}
			}),
			l = f.props;
		ln(l, f.opts.specialEasing);
		for (; s < o; s++) {
			r = rn[s].call(f, e, l, f.opts);
			if (r) return r
		}
		return h.map(l, an, f), h.isFunction(f.opts.start) && f.opts.start.call(e, f), h.fx.timer(h.extend(a, {
			elem: e,
			anim: f,
			queue: f.opts.queue
		})), f.progress(f.opts.progress).done(f.opts.done, f.opts.complete).fail(f.opts.fail).always(f.opts.always)
	}

	function Fn(e) {
		return function(t, n) {
			typeof t != "string" && (n = t, t = "*");
			var r, i = 0,
				s = t.toLowerCase().match(O) || [];
			if (h.isFunction(n))
				while (r = s[i++]) r.charAt(0) === "+" ? (r = r.slice(1) || "*", (e[r] = e[r] || []).unshift(n)) : (e[r] = e[r] || []).push(n)
		}
	}

	function In(e, t, n, r) {
		function o(u) {
			var a;
			return i[u] = !0, h.each(e[u] || [], function(e, u) {
				var f = u(t, n, r);
				if (typeof f == "string" && !s && !i[f]) return t.dataTypes.unshift(f), o(f), !1;
				if (s) return !(a = f)
			}), a
		}
		var i = {},
			s = e === Hn;
		return o(t.dataTypes[0]) || !i["*"] && o("*")
	}

	function qn(e, t) {
		var n, r, i = h.ajaxSettings.flatOptions || {};
		for (r in t) t[r] !== undefined && ((i[r] ? e : n || (n = {}))[r] = t[r]);
		return n && h.extend(!0, e, n), e
	}

	function Rn(e, t, n) {
		var r, i, s, o, u = e.contents,
			a = e.dataTypes;
		while (a[0] === "*") a.shift(), i === undefined && (i = e.mimeType || t.getResponseHeader("Content-Type"));
		if (i)
			for (o in u)
				if (u[o] && u[o].test(i)) {
					a.unshift(o);
					break
				}
		if (a[0] in n) s = a[0];
		else {
			for (o in n) {
				if (!a[0] || e.converters[o + " " + a[0]]) {
					s = o;
					break
				}
				r || (r = o)
			}
			s = s || r
		}
		if (s) return s !== a[0] && a.unshift(s), n[s]
	}

	function Un(e, t, n, r) {
		var i, s, o, u, a, f = {},
			l = e.dataTypes.slice();
		if (l[1])
			for (o in e.converters) f[o.toLowerCase()] = e.converters[o];
		s = l.shift();
		while (s) {
			e.responseFields[s] && (n[e.responseFields[s]] = t), !a && r && e.dataFilter && (t = e.dataFilter(t, e.dataType)), a = s, s = l.shift();
			if (s)
				if (s === "*") s = a;
				else if (a !== "*" && a !== s) {
				o = f[a + " " + s] || f["* " + s];
				if (!o)
					for (i in f) {
						u = i.split(" ");
						if (u[1] === s) {
							o = f[a + " " + u[0]] || f["* " + u[0]];
							if (o) {
								o === !0 ? o = f[i] : f[i] !== !0 && (s = u[0], l.unshift(u[1]));
								break
							}
						}
					}
				if (o !== !0)
					if (o && e["throws"]) t = o(t);
					else try {
						t = o(t)
					} catch (c) {
						return {
							state: "parsererror",
							error: o ? c : "No conversion from " + a + " to " + s
						}
					}
			}
		}
		return {
			state: "success",
			data: t
		}
	}

	function Jn(e, t, n, r) {
		var i;
		if (h.isArray(t)) h.each(t, function(t, i) {
			n || Wn.test(e) ? r(e, i) : Jn(e + "[" + (typeof i == "object" ? t : "") + "]", i, n, r)
		});
		else if (!n && h.type(t) === "object")
			for (i in t) Jn(e + "[" + i + "]", t[i], n, r);
		else r(e, t)
	}

	function Yn() {
		try {
			return new e.XMLHttpRequest
		} catch (t) {}
	}

	function Zn() {
		try {
			return new e.ActiveXObject("Microsoft.XMLHTTP")
		} catch (t) {}
	}

	function ir(e) {
		return h.isWindow(e) ? e : e.nodeType === 9 ? e.defaultView || e.parentWindow : !1
	}
	var n = [],
		r = n.slice,
		i = n.concat,
		s = n.push,
		o = n.indexOf,
		u = {},
		a = u.toString,
		f = u.hasOwnProperty,
		l = {},
		c = "1.11.1",
		h = function(e, t) {
			return new h.fn.init(e, t)
		},
		p = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,
		d = /^-ms-/,
		v = /-([\da-z])/gi,
		m = function(e, t) {
			return t.toUpperCase()
		};
	h.fn = h.prototype = {
		jquery: c,
		constructor: h,
		selector: "",
		length: 0,
		toArray: function() {
			return r.call(this)
		},
		get: function(e) {
			return e != null ? e < 0 ? this[e + this.length] : this[e] : r.call(this)
		},
		pushStack: function(e) {
			var t = h.merge(this.constructor(), e);
			return t.prevObject = this, t.betContext = this.context, t
		},
		each: function(e, t) {
			return h.each(this, e, t)
		},
		map: function(e) {
			return this.pushStack(h.map(this, function(t, n) {
				return e.call(t, n, t)
			}))
		},
		slice: function() {
			return this.pushStack(r.apply(this, arguments))
		},
		first: function() {
			return this.eq(0)
		},
		last: function() {
			return this.eq(-1)
		},
		eq: function(e) {
			var t = this.length,
				n = +e + (e < 0 ? t : 0);
			return this.pushStack(n >= 0 && n < t ? [this[n]] : [])
		},
		end: function() {
			return this.prevObject || this.constructor(null)
		},
		push: s,
		sort: n.sort,
		splice: n.splice
	}, h.extend = h.fn.extend = function() {
		var e, t, n, r, i, s, o = arguments[0] || {},
			u = 1,
			a = arguments.length,
			f = !1;
		typeof o == "boolean" && (f = o, o = arguments[u] || {}, u++), typeof o != "object" && !h.isFunction(o) && (o = {}), u === a && (o = this, u--);
		for (; u < a; u++)
			if ((i = arguments[u]) != null)
				for (r in i) {
					e = o[r], n = i[r];
					if (o === n) continue;
					f && n && (h.isPlainObject(n) || (t = h.isArray(n))) ? (t ? (t = !1, s = e && h.isArray(e) ? e : []) : s = e && h.isPlainObject(e) ? e : {}, o[r] = h.extend(f, s, n)) : n !== undefined && (o[r] = n)
				}
			return o
	}, h.extend({
		expando: "jQuery" + (c + Math.random()).replace(/\D/g, ""),
		isReady: !0,
		error: function(e) {
			throw new Error(e)
		},
		noop: function() {},
		isFunction: function(e) {
			return h.type(e) === "function"
		},
		isArray: Array.isArray || function(e) {
			return h.type(e) === "array"
		},
		isWindow: function(e) {
			return e != null && e == e.window
		},
		isNumeric: function(e) {
			return !h.isArray(e) && e - parseFloat(e) >= 0
		},
		isEmptyObject: function(e) {
			var t;
			for (t in e) return !1;
			return !0
		},
		isPlainObject: function(e) {
			var t;
			if (!e || h.type(e) !== "object" || e.nodeType || h.isWindow(e)) return !1;
			try {
				if (e.constructor && !f.call(e, "constructor") && !f.call(e.constructor.prototype, "isPrototypeOf")) return !1
			} catch (n) {
				return !1
			}
			if (l.ownLast)
				for (t in e) return f.call(e, t);
			for (t in e);
			return t === undefined || f.call(e, t)
		},
		type: function(e) {
			return e == null ? e + "" : typeof e == "object" || typeof e == "function" ? u[a.call(e)] || "object" : typeof e
		},
		globalEval: function(t) {
			t && h.trim(t) && (e.execScript || function(t) {
				e.eval.call(e, t)
			})(t)
		},
		camelCase: function(e) {
			return e.replace(d, "ms-").replace(v, m)
		},
		nodeName: function(e, t) {
			return e.nodeName && e.nodeName.toLowerCase() === t.toLowerCase()
		},
		each: function(e, t, n) {
			var r, i = 0,
				s = e.length,
				o = g(e);
			if (n)
				if (o)
					for (; i < s; i++) {
						r = t.apply(e[i], n);
						if (r === !1) break
					} else
						for (i in e) {
							r = t.apply(e[i], n);
							if (r === !1) break
						} else if (o)
							for (; i < s; i++) {
								r = t.call(e[i], i, e[i]);
								if (r === !1) break
							} else
								for (i in e) {
									r = t.call(e[i], i, e[i]);
									if (r === !1) break
								}
						return e
		},
		trim: function(e) {
			return e == null ? "" : (e + "").replace(p, "")
		},
		makeArray: function(e, t) {
			var n = t || [];
			return e != null && (g(Object(e)) ? h.merge(n, typeof e == "string" ? [e] : e) : s.call(n, e)), n
		},
		inArray: function(e, t, n) {
			var r;
			if (t) {
				if (o) return o.call(t, e, n);
				r = t.length, n = n ? n < 0 ? Math.max(0, r + n) : n : 0;
				for (; n < r; n++)
					if (n in t && t[n] === e) return n
			}
			return -1
		},
		merge: function(e, t) {
			var n = +t.length,
				r = 0,
				i = e.length;
			while (r < n) e[i++] = t[r++];
			if (n !== n)
				while (t[r] !== undefined) e[i++] = t[r++];
			return e.length = i, e
		},
		grep: function(e, t, n) {
			var r, i = [],
				s = 0,
				o = e.length,
				u = !n;
			for (; s < o; s++) r = !t(e[s], s), r !== u && i.push(e[s]);
			return i
		},
		map: function(e, t, n) {
			var r, s = 0,
				o = e.length,
				u = g(e),
				a = [];
			if (u)
				for (; s < o; s++) r = t(e[s], s, n), r != null && a.push(r);
			else
				for (s in e) r = t(e[s], s, n), r != null && a.push(r);
			return i.apply([], a)
		},
		guid: 1,
		proxy: function(e, t) {
			var n, i, s;
			return typeof t == "string" && (s = e[t], t = e, e = s), h.isFunction(e) ? (n = r.call(arguments, 2), i = function() {
				return e.apply(t || this, n.concat(r.call(arguments)))
			}, i.guid = e.guid = e.guid || h.guid++, i) : undefined
		},
		now: function() {
			return +(new Date)
		},
		support: l
	}), h.each("Boolean Number String Function Array Date RegExp Object Error".split(" "), function(e, t) {
		u["[object " + t + "]"] = t.toLowerCase()
	});
	var y = function(e) {
		function st(e, t, r, i) {
			var s, u, f, l, c, d, g, y, S, x;
			(t ? t.ownerDocument || t : E) !== p && h(t), t = t || p, r = r || [];
			if (!e || typeof e != "string") return r;
			if ((l = t.nodeType) !== 1 && l !== 9) return [];
			if (v && !i) {
				if (s = Z.exec(e))
					if (f = s[1]) {
						if (l === 9) {
							u = t.getElementById(f);
							if (!u || !u.parentNode) return r;
							if (u.id === f) return r.push(u), r
						} else if (t.ownerDocument && (u = t.ownerDocument.getElementById(f)) && b(t, u) && u.id === f) return r.push(u), r
					} else {
						if (s[2]) return P.apply(r, t.getElementsByTagName(e)), r;
						if ((f = s[3]) && n.getElementsByClassName && t.getElementsByClassName) return P.apply(r, t.getElementsByClassName(f)), r
					}
				if (n.qsa && (!m || !m.test(e))) {
					y = g = w, S = t, x = l === 9 && e;
					if (l === 1 && t.nodeName.toLowerCase() !== "object") {
						d = o(e), (g = t.getAttribute("id")) ? y = g.replace(tt, "\\$&") : t.setAttribute("id", y), y = "[id='" + y + "'] ", c = d.length;
						while (c--) d[c] = y + mt(d[c]);
						S = et.test(e) && dt(t.parentNode) || t, x = d.join(",")
					}
					if (x) try {
						return P.apply(r, S.querySelectorAll(x)), r
					} catch (T) {} finally {
						g || t.removeAttribute("id")
					}
				}
			}
			return a(e.replace(z, "$1"), t, r, i)
		}

		function ot() {
			function t(n, i) {
				return e.push(n + " ") > r.cacheLength && delete t[e.shift()], t[n + " "] = i
			}
			var e = [];
			return t
		}

		function ut(e) {
			return e[w] = !0, e
		}

		function at(e) {
			var t = p.createElement("div");
			try {
				return !!e(t)
			} catch (n) {
				return !1
			} finally {
				t.parentNode && t.parentNode.removeChild(t), t = null
			}
		}

		function ft(e, t) {
			var n = e.split("|"),
				i = e.length;
			while (i--) r.attrHandle[n[i]] = t
		}

		function lt(e, t) {
			var n = t && e,
				r = n && e.nodeType === 1 && t.nodeType === 1 && (~t.sourceIndex || A) - (~e.sourceIndex || A);
			if (r) return r;
			if (n)
				while (n = n.nextSibling)
					if (n === t) return -1;
			return e ? 1 : -1
		}

		function ct(e) {
			return function(t) {
				var n = t.nodeName.toLowerCase();
				return n === "input" && t.type === e
			}
		}

		function ht(e) {
			return function(t) {
				var n = t.nodeName.toLowerCase();
				return (n === "input" || n === "button") && t.type === e
			}
		}

		function pt(e) {
			return ut(function(t) {
				return t = +t, ut(function(n, r) {
					var i, s = e([], n.length, t),
						o = s.length;
					while (o--) n[i = s[o]] && (n[i] = !(r[i] = n[i]))
				})
			})
		}

		function dt(e) {
			return e && typeof e.getElementsByTagName !== L && e
		}

		function vt() {}

		function mt(e) {
			var t = 0,
				n = e.length,
				r = "";
			for (; t < n; t++) r += e[t].value;
			return r
		}

		function gt(e, t, n) {
			var r = t.dir,
				i = n && r === "parentNode",
				s = x++;
			return t.first ? function(t, n, s) {
				while (t = t[r])
					if (t.nodeType === 1 || i) return e(t, n, s)
			} : function(t, n, o) {
				var u, a, f = [S, s];
				if (o) {
					while (t = t[r])
						if (t.nodeType === 1 || i)
							if (e(t, n, o)) return !0
				} else
					while (t = t[r])
						if (t.nodeType === 1 || i) {
							a = t[w] || (t[w] = {});
							if ((u = a[r]) && u[0] === S && u[1] === s) return f[2] = u[2];
							a[r] = f;
							if (f[2] = e(t, n, o)) return !0
						}
			}
		}

		function yt(e) {
			return e.length > 1 ? function(t, n, r) {
				var i = e.length;
				while (i--)
					if (!e[i](t, n, r)) return !1;
				return !0
			} : e[0]
		}

		function bt(e, t, n) {
			var r = 0,
				i = t.length;
			for (; r < i; r++) st(e, t[r], n);
			return n
		}

		function wt(e, t, n, r, i) {
			var s, o = [],
				u = 0,
				a = e.length,
				f = t != null;
			for (; u < a; u++)
				if (s = e[u])
					if (!n || n(s, r, i)) o.push(s), f && t.push(u);
			return o
		}

		function Et(e, t, n, r, i, s) {
			return r && !r[w] && (r = Et(r)), i && !i[w] && (i = Et(i, s)), ut(function(s, o, u, a) {
				var f, l, c, h = [],
					p = [],
					d = o.length,
					v = s || bt(t || "*", u.nodeType ? [u] : u, []),
					m = e && (s || !t) ? wt(v, h, e, u, a) : v,
					g = n ? i || (s ? e : d || r) ? [] : o : m;
				n && n(m, g, u, a);
				if (r) {
					f = wt(g, p), r(f, [], u, a), l = f.length;
					while (l--)
						if (c = f[l]) g[p[l]] = !(m[p[l]] = c)
				}
				if (s) {
					if (i || e) {
						if (i) {
							f = [], l = g.length;
							while (l--)(c = g[l]) && f.push(m[l] = c);
							i(null, g = [], f, a)
						}
						l = g.length;
						while (l--)(c = g[l]) && (f = i ? B.call(s, c) : h[l]) > -1 && (s[f] = !(o[f] = c))
					}
				} else g = wt(g === o ? g.splice(d, g.length) : g), i ? i(null, o, g, a) : P.apply(o, g)
			})
		}

		function St(e) {
			var t, n, i, s = e.length,
				o = r.relative[e[0].type],
				u = o || r.relative[" "],
				a = o ? 1 : 0,
				l = gt(function(e) {
					return e === t
				}, u, !0),
				c = gt(function(e) {
					return B.call(t, e) > -1
				}, u, !0),
				h = [function(e, n, r) {
					return !o && (r || n !== f) || ((t = n).nodeType ? l(e, n, r) : c(e, n, r))
				}];
			for (; a < s; a++)
				if (n = r.relative[e[a].type]) h = [gt(yt(h), n)];
				else {
					n = r.filter[e[a].type].apply(null, e[a].matches);
					if (n[w]) {
						i = ++a;
						for (; i < s; i++)
							if (r.relative[e[i].type]) break;
						return Et(a > 1 && yt(h), a > 1 && mt(e.slice(0, a - 1).concat({
							value: e[a - 2].type === " " ? "*" : ""
						})).replace(z, "$1"), n, a < i && St(e.slice(a, i)), i < s && St(e = e.slice(i)), i < s && mt(e))
					}
					h.push(n)
				}
			return yt(h)
		}

		function xt(e, t) {
			var n = t.length > 0,
				i = e.length > 0,
				s = function(s, o, u, a, l) {
					var c, h, d, v = 0,
						m = "0",
						g = s && [],
						y = [],
						b = f,
						w = s || i && r.find.TAG("*", l),
						E = S += b == null ? 1 : Math.random() || .1,
						x = w.length;
					l && (f = o !== p && o);
					for (; m !== x && (c = w[m]) != null; m++) {
						if (i && c) {
							h = 0;
							while (d = e[h++])
								if (d(c, o, u)) {
									a.push(c);
									break
								}
							l && (S = E)
						}
						n && ((c = !d && c) && v--, s && g.push(c))
					}
					v += m;
					if (n && m !== v) {
						h = 0;
						while (d = t[h++]) d(g, y, o, u);
						if (s) {
							if (v > 0)
								while (m--) !g[m] && !y[m] && (y[m] = _.call(a));
							y = wt(y)
						}
						P.apply(a, y), l && !s && y.length > 0 && v + t.length > 1 && st.uniqueSort(a)
					}
					return l && (S = E, f = b), g
				};
			return n ? ut(s) : s
		}
		var t, n, r, i, s, o, u, a, f, l, c, h, p, d, v, m, g, y, b, w = "sizzle" + -(new Date),
			E = e.document,
			S = 0,
			x = 0,
			T = ot(),
			N = ot(),
			C = ot(),
			k = function(e, t) {
				return e === t && (c = !0), 0
			},
			L = typeof undefined,
			A = 1 << 31,
			O = {}.hasOwnProperty,
			M = [],
			_ = M.pop,
			D = M.push,
			P = M.push,
			H = M.slice,
			B = M.indexOf || function(e) {
				var t = 0,
					n = this.length;
				for (; t < n; t++)
					if (this[t] === e) return t;
				return -1
			},
			j = "checked|selected|async|autofocus|autoplay|controls|defer|disabled|hidden|ismap|loop|multiple|open|readonly|required|scoped",
			F = "[\\x20\\t\\r\\n\\f]",
			I = "(?:\\\\.|[\\w-]|[^\\x00-\\xa0])+",
			q = I.replace("w", "w#"),
			R = "\\[" + F + "*(" + I + ")(?:" + F + "*([*^$|!~]?=)" + F + "*(?:'((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\"|(" + q + "))|)" + F + "*\\]",
			U = ":(" + I + ")(?:\\((" + "('((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\")|" + "((?:\\\\.|[^\\\\()[\\]]|" + R + ")*)|" + ".*" + ")\\)|)",
			z = new RegExp("^" + F + "+|((?:^|[^\\\\])(?:\\\\.)*)" + F + "+$", "g"),
			W = new RegExp("^" + F + "*," + F + "*"),
			X = new RegExp("^" + F + "*([>+~]|" + F + ")" + F + "*"),
			V = new RegExp("=" + F + "*([^\\]'\"]*?)" + F + "*\\]", "g"),
			$ = new RegExp(U),
			J = new RegExp("^" + q + "$"),
			K = {
				ID: new RegExp("^#(" + I + ")"),
				CLASS: new RegExp("^\\.(" + I + ")"),
				TAG: new RegExp("^(" + I.replace("w", "w*") + ")"),
				ATTR: new RegExp("^" + R),
				PSEUDO: new RegExp("^" + U),
				CHILD: new RegExp("^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\(" + F + "*(even|odd|(([+-]|)(\\d*)n|)" + F + "*(?:([+-]|)" + F + "*(\\d+)|))" + F + "*\\)|)", "i"),
				bool: new RegExp("^(?:" + j + ")$", "i"),
				needsContext: new RegExp("^" + F + "*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\(" + F + "*((?:-\\d)?\\d*)" + F + "*\\)|)(?=[^-]|$)", "i")
			},
			Q = /^(?:input|select|textarea|button)$/i,
			G = /^h\d$/i,
			Y = /^[^{]+\{\s*\[native \w/,
			Z = /^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,
			et = /[+~]/,
			tt = /'|\\/g,
			nt = new RegExp("\\\\([\\da-f]{1,6}" + F + "?|(" + F + ")|.)", "ig"),
			rt = function(e, t, n) {
				var r = "0x" + t - 65536;
				return r !== r || n ? t : r < 0 ? String.fromCharCode(r + 65536) : String.fromCharCode(r >> 10 | 55296, r & 1023 | 56320)
			};
		try {
			P.apply(M = H.call(E.childNodes), E.childNodes), M[E.childNodes.length].nodeType
		} catch (it) {
			P = {
				apply: M.length ? function(e, t) {
					D.apply(e, H.call(t))
				} : function(e, t) {
					var n = e.length,
						r = 0;
					while (e[n++] = t[r++]);
					e.length = n - 1
				}
			}
		}
		n = st.support = {}, s = st.isXML = function(e) {
			var t = e && (e.ownerDocument || e).documentElement;
			return t ? t.nodeName !== "HTML" : !1
		}, h = st.setDocument = function(e) {
			var t, i = e ? e.ownerDocument || e : E,
				o = i.defaultView;
			if (i === p || i.nodeType !== 9 || !i.documentElement) return p;
			p = i, d = i.documentElement, v = !s(i), o && o !== o.top && (o.addEventListener ? o.addEventListener("unload", function() {
				h()
			}, !1) : o.attachEvent && o.attachEvent("onunload", function() {
				h()
			})), n.attributes = at(function(e) {
				return e.className = "i", !e.getAttribute("className")
			}), n.getElementsByTagName = at(function(e) {
				return e.appendChild(i.createComment("")), !e.getElementsByTagName("*").length
			}), n.getElementsByClassName = Y.test(i.getElementsByClassName) && at(function(e) {
				return e.innerHTML = "<div class='a'></div><div class='a i'></div>", e.firstChild.className = "i", e.getElementsByClassName("i").length === 2
			}), n.getById = at(function(e) {
				return d.appendChild(e).id = w, !i.getElementsByName || !i.getElementsByName(w).length
			}), n.getById ? (r.find.ID = function(e, t) {
				if (typeof t.getElementById !== L && v) {
					var n = t.getElementById(e);
					return n && n.parentNode ? [n] : []
				}
			}, r.filter.ID = function(e) {
				var t = e.replace(nt, rt);
				return function(e) {
					return e.getAttribute("id") === t
				}
			}) : (delete r.find.ID, r.filter.ID = function(e) {
				var t = e.replace(nt, rt);
				return function(e) {
					var n = typeof e.getAttributeNode !== L && e.getAttributeNode("id");
					return n && n.value === t
				}
			}), r.find.TAG = n.getElementsByTagName ? function(e, t) {
				if (typeof t.getElementsByTagName !== L) return t.getElementsByTagName(e)
			} : function(e, t) {
				var n, r = [],
					i = 0,
					s = t.getElementsByTagName(e);
				if (e === "*") {
					while (n = s[i++]) n.nodeType === 1 && r.push(n);
					return r
				}
				return s
			}, r.find.CLASS = n.getElementsByClassName && function(e, t) {
				if (typeof t.getElementsByClassName !== L && v) return t.getElementsByClassName(e)
			}, g = [], m = [];
			if (n.qsa = Y.test(i.querySelectorAll)) at(function(e) {
				e.innerHTML = "<select msallowclip=''><option selected=''></option></select>", e.querySelectorAll("[msallowclip^='']").length && m.push("[*^$]=" + F + "*(?:''|\"\")"), e.querySelectorAll("[selected]").length || m.push("\\[" + F + "*(?:value|" + j + ")"), e.querySelectorAll(":checked").length || m.push(":checked")
			}), at(function(e) {
				var t = i.createElement("input");
				t.setAttribute("type", "hidden"), e.appendChild(t).setAttribute("name", "D"), e.querySelectorAll("[name=d]").length && m.push("name" + F + "*[*^$|!~]?="), e.querySelectorAll(":enabled").length || m.push(":enabled", ":disabled"), e.querySelectorAll("*,:x"), m.push(",.*:")
			});
			return (n.matchesSelector = Y.test(y = d.matches || d.webkitMatchesSelector || d.mozMatchesSelector || d.oMatchesSelector || d.msMatchesSelector)) && at(function(e) {
				n.disconnectedMatch = y.call(e, "div"), y.call(e, "[s!='']:x"), g.push("!=", U)
			}), m = m.length && new RegExp(m.join("|")), g = g.length && new RegExp(g.join("|")), t = Y.test(d.compareDocumentPosition), b = t || Y.test(d.contains) ? function(e, t) {
				var n = e.nodeType === 9 ? e.documentElement : e,
					r = t && t.parentNode;
				return e === r || !!r && r.nodeType === 1 && !!(n.contains ? n.contains(r) : e.compareDocumentPosition && e.compareDocumentPosition(r) & 16)
			} : function(e, t) {
				if (t)
					while (t = t.parentNode)
						if (t === e) return !0;
				return !1
			}, k = t ? function(e, t) {
				if (e === t) return c = !0, 0;
				var r = !e.compareDocumentPosition - !t.compareDocumentPosition;
				return r ? r : (r = (e.ownerDocument || e) === (t.ownerDocument || t) ? e.compareDocumentPosition(t) : 1, r & 1 || !n.sortDetached && t.compareDocumentPosition(e) === r ? e === i || e.ownerDocument === E && b(E, e) ? -1 : t === i || t.ownerDocument === E && b(E, t) ? 1 : l ? B.call(l, e) - B.call(l, t) : 0 : r & 4 ? -1 : 1)
			} : function(e, t) {
				if (e === t) return c = !0, 0;
				var n, r = 0,
					s = e.parentNode,
					o = t.parentNode,
					u = [e],
					a = [t];
				if (!s || !o) return e === i ? -1 : t === i ? 1 : s ? -1 : o ? 1 : l ? B.call(l, e) - B.call(l, t) : 0;
				if (s === o) return lt(e, t);
				n = e;
				while (n = n.parentNode) u.unshift(n);
				n = t;
				while (n = n.parentNode) a.unshift(n);
				while (u[r] === a[r]) r++;
				return r ? lt(u[r], a[r]) : u[r] === E ? -1 : a[r] === E ? 1 : 0
			}, i
		}, st.matches = function(e, t) {
			return st(e, null, null, t)
		}, st.matchesSelector = function(e, t) {
			(e.ownerDocument || e) !== p && h(e), t = t.replace(V, "='$1']");
			if (n.matchesSelector && v && (!g || !g.test(t)) && (!m || !m.test(t))) try {
				var r = y.call(e, t);
				if (r || n.disconnectedMatch || e.document && e.document.nodeType !== 11) return r
			} catch (i) {}
			return st(t, p, null, [e]).length > 0
		}, st.contains = function(e, t) {
			return (e.ownerDocument || e) !== p && h(e), b(e, t)
		}, st.attr = function(e, t) {
			(e.ownerDocument || e) !== p && h(e);
			var i = r.attrHandle[t.toLowerCase()],
				s = i && O.call(r.attrHandle, t.toLowerCase()) ? i(e, t, !v) : undefined;
			return s !== undefined ? s : n.attributes || !v ? e.getAttribute(t) : (s = e.getAttributeNode(t)) && s.specified ? s.value : null
		}, st.error = function(e) {
			throw new Error("Syntax error, unrecognized expression: " + e)
		}, st.uniqueSort = function(e) {
			var t, r = [],
				i = 0,
				s = 0;
			c = !n.detectDuplicates, l = !n.sortStable && e.slice(0), e.sort(k);
			if (c) {
				while (t = e[s++]) t === e[s] && (i = r.push(s));
				while (i--) e.splice(r[i], 1)
			}
			return l = null, e
		}, i = st.getText = function(e) {
			var t, n = "",
				r = 0,
				s = e.nodeType;
			if (!s)
				while (t = e[r++]) n += i(t);
			else if (s === 1 || s === 9 || s === 11) {
				if (typeof e.textContent == "string") return e.textContent;
				for (e = e.firstChild; e; e = e.nextSibling) n += i(e)
			} else if (s === 3 || s === 4) return e.nodeValue;
			return n
		}, r = st.selectors = {
			cacheLength: 50,
			createPseudo: ut,
			match: K,
			attrHandle: {},
			find: {},
			relative: {
				">": {
					dir: "parentNode",
					first: !0
				},
				" ": {
					dir: "parentNode"
				},
				"+": {
					dir: "previousSibling",
					first: !0
				},
				"~": {
					dir: "previousSibling"
				}
			},
			preFilter: {
				ATTR: function(e) {
					return e[1] = e[1].replace(nt, rt), e[3] = (e[3] || e[4] || e[5] || "").replace(nt, rt), e[2] === "~=" && (e[3] = " " + e[3] + " "), e.slice(0, 4)
				},
				CHILD: function(e) {
					return e[1] = e[1].toLowerCase(), e[1].slice(0, 3) === "nth" ? (e[3] || st.error(e[0]), e[4] = +(e[4] ? e[5] + (e[6] || 1) : 2 * (e[3] === "even" || e[3] === "odd")), e[5] = +(e[7] + e[8] || e[3] === "odd")) : e[3] && st.error(e[0]), e
				},
				PSEUDO: function(e) {
					var t, n = !e[6] && e[2];
					return K.CHILD.test(e[0]) ? null : (e[3] ? e[2] = e[4] || e[5] || "" : n && $.test(n) && (t = o(n, !0)) && (t = n.indexOf(")", n.length - t) - n.length) && (e[0] = e[0].slice(0, t), e[2] = n.slice(0, t)), e.slice(0, 3))
				}
			},
			filter: {
				TAG: function(e) {
					var t = e.replace(nt, rt).toLowerCase();
					return e === "*" ? function() {
						return !0
					} : function(e) {
						return e.nodeName && e.nodeName.toLowerCase() === t
					}
				},
				CLASS: function(e) {
					var t = T[e + " "];
					return t || (t = new RegExp("(^|" + F + ")" + e + "(" + F + "|$)")) && T(e, function(e) {
						return t.test(typeof e.className == "string" && e.className || typeof e.getAttribute !== L && e.getAttribute("class") || "")
					})
				},
				ATTR: function(e, t, n) {
					return function(r) {
						var i = st.attr(r, e);
						return i == null ? t === "!=" : t ? (i += "", t === "=" ? i === n : t === "!=" ? i !== n : t === "^=" ? n && i.indexOf(n) === 0 : t === "*=" ? n && i.indexOf(n) > -1 : t === "$=" ? n && i.slice(-n.length) === n : t === "~=" ? (" " + i + " ").indexOf(n) > -1 : t === "|=" ? i === n || i.slice(0, n.length + 1) === n + "-" : !1) : !0
					}
				},
				CHILD: function(e, t, n, r, i) {
					var s = e.slice(0, 3) !== "nth",
						o = e.slice(-4) !== "last",
						u = t === "of-type";
					return r === 1 && i === 0 ? function(e) {
						return !!e.parentNode
					} : function(t, n, a) {
						var f, l, c, h, p, d, v = s !== o ? "nextSibling" : "previousSibling",
							m = t.parentNode,
							g = u && t.nodeName.toLowerCase(),
							y = !a && !u;
						if (m) {
							if (s) {
								while (v) {
									c = t;
									while (c = c[v])
										if (u ? c.nodeName.toLowerCase() === g : c.nodeType === 1) return !1;
									d = v = e === "only" && !d && "nextSibling"
								}
								return !0
							}
							d = [o ? m.firstChild : m.lastChild];
							if (o && y) {
								l = m[w] || (m[w] = {}), f = l[e] || [], p = f[0] === S && f[1], h = f[0] === S && f[2], c = p && m.childNodes[p];
								while (c = ++p && c && c[v] || (h = p = 0) || d.pop())
									if (c.nodeType === 1 && ++h && c === t) {
										l[e] = [S, p, h];
										break
									}
							} else if (y && (f = (t[w] || (t[w] = {}))[e]) && f[0] === S) h = f[1];
							else
								while (c = ++p && c && c[v] || (h = p = 0) || d.pop())
									if ((u ? c.nodeName.toLowerCase() === g : c.nodeType === 1) && ++h) {
										y && ((c[w] || (c[w] = {}))[e] = [S, h]);
										if (c === t) break
									} return h -= i, h === r || h % r === 0 && h / r >= 0
						}
					}
				},
				PSEUDO: function(e, t) {
					var n, i = r.pseudos[e] || r.setFilters[e.toLowerCase()] || st.error("unsupported pseudo: " + e);
					return i[w] ? i(t) : i.length > 1 ? (n = [e, e, "", t], r.setFilters.hasOwnProperty(e.toLowerCase()) ? ut(function(e, n) {
						var r, s = i(e, t),
							o = s.length;
						while (o--) r = B.call(e, s[o]), e[r] = !(n[r] = s[o])
					}) : function(e) {
						return i(e, 0, n)
					}) : i
				}
			},
			pseudos: {
				not: ut(function(e) {
					var t = [],
						n = [],
						r = u(e.replace(z, "$1"));
					return r[w] ? ut(function(e, t, n, i) {
						var s, o = r(e, null, i, []),
							u = e.length;
						while (u--)
							if (s = o[u]) e[u] = !(t[u] = s)
					}) : function(e, i, s) {
						return t[0] = e, r(t, null, s, n), !n.pop()
					}
				}),
				has: ut(function(e) {
					return function(t) {
						return st(e, t).length > 0
					}
				}),
				contains: ut(function(e) {
					return function(t) {
						return (t.textContent || t.innerText || i(t)).indexOf(e) > -1
					}
				}),
				lang: ut(function(e) {
					return J.test(e || "") || st.error("unsupported lang: " + e), e = e.replace(nt, rt).toLowerCase(),
						function(t) {
							var n;
							do
								if (n = v ? t.lang : t.getAttribute("xml:lang") || t.getAttribute("lang")) return n = n.toLowerCase(), n === e || n.indexOf(e + "-") === 0;
							while ((t = t.parentNode) && t.nodeType === 1);
							return !1
						}
				}),
				target: function(t) {
					var n = e.location && e.location.hash;
					return n && n.slice(1) === t.id
				},
				root: function(e) {
					return e === d
				},
				focus: function(e) {
					return e === p.activeElement && (!p.hasFocus || p.hasFocus()) && !!(e.type || e.href || ~e.tabIndex)
				},
				enabled: function(e) {
					return e.disabled === !1
				},
				disabled: function(e) {
					return e.disabled === !0
				},
				checked: function(e) {
					var t = e.nodeName.toLowerCase();
					return t === "input" && !!e.checked || t === "option" && !!e.selected
				},
				selected: function(e) {
					return e.parentNode && e.parentNode.selectedIndex, e.selected === !0
				},
				empty: function(e) {
					for (e = e.firstChild; e; e = e.nextSibling)
						if (e.nodeType < 6) return !1;
					return !0
				},
				parent: function(e) {
					return !r.pseudos.empty(e)
				},
				header: function(e) {
					return G.test(e.nodeName)
				},
				input: function(e) {
					return Q.test(e.nodeName)
				},
				button: function(e) {
					var t = e.nodeName.toLowerCase();
					return t === "input" && e.type === "button" || t === "button"
				},
				text: function(e) {
					var t;
					return e.nodeName.toLowerCase() === "input" && e.type === "text" && ((t = e.getAttribute("type")) == null || t.toLowerCase() === "text")
				},
				first: pt(function() {
					return [0]
				}),
				last: pt(function(e, t) {
					return [t - 1]
				}),
				eq: pt(function(e, t, n) {
					return [n < 0 ? n + t : n]
				}),
				even: pt(function(e, t) {
					var n = 0;
					for (; n < t; n += 2) e.push(n);
					return e
				}),
				odd: pt(function(e, t) {
					var n = 1;
					for (; n < t; n += 2) e.push(n);
					return e
				}),
				lt: pt(function(e, t, n) {
					var r = n < 0 ? n + t : n;
					for (; --r >= 0;) e.push(r);
					return e
				}),
				gt: pt(function(e, t, n) {
					var r = n < 0 ? n + t : n;
					for (; ++r < t;) e.push(r);
					return e
				})
			}
		}, r.pseudos.nth = r.pseudos.eq;
		for (t in {
				radio: !0,
				checkbox: !0,
				file: !0,
				password: !0,
				image: !0
			}) r.pseudos[t] = ct(t);
		for (t in {
				submit: !0,
				reset: !0
			}) r.pseudos[t] = ht(t);
		return vt.prototype = r.filters = r.pseudos, r.setFilters = new vt, o = st.tokenize = function(e, t) {
			var n, i, s, o, u, a, f, l = N[e + " "];
			if (l) return t ? 0 : l.slice(0);
			u = e, a = [], f = r.preFilter;
			while (u) {
				if (!n || (i = W.exec(u))) i && (u = u.slice(i[0].length) || u), a.push(s = []);
				n = !1;
				if (i = X.exec(u)) n = i.shift(), s.push({
					value: n,
					type: i[0].replace(z, " ")
				}), u = u.slice(n.length);
				for (o in r.filter)(i = K[o].exec(u)) && (!f[o] || (i = f[o](i))) && (n = i.shift(), s.push({
					value: n,
					type: o,
					matches: i
				}), u = u.slice(n.length));
				if (!n) break
			}
			return t ? u.length : u ? st.error(e) : N(e, a).slice(0)
		}, u = st.compile = function(e, t) {
			var n, r = [],
				i = [],
				s = C[e + " "];
			if (!s) {
				t || (t = o(e)), n = t.length;
				while (n--) s = St(t[n]), s[w] ? r.push(s) : i.push(s);
				s = C(e, xt(i, r)), s.selector = e
			}
			return s
		}, a = st.select = function(e, t, i, s) {
			var a, f, l, c, h, p = typeof e == "function" && e,
				d = !s && o(e = p.selector || e);
			i = i || [];
			if (d.length === 1) {
				f = d[0] = d[0].slice(0);
				if (f.length > 2 && (l = f[0]).type === "ID" && n.getById && t.nodeType === 9 && v && r.relative[f[1].type]) {
					t = (r.find.ID(l.matches[0].replace(nt, rt), t) || [])[0];
					if (!t) return i;
					p && (t = t.parentNode), e = e.slice(f.shift().value.length)
				}
				a = K.needsContext.test(e) ? 0 : f.length;
				while (a--) {
					l = f[a];
					if (r.relative[c = l.type]) break;
					if (h = r.find[c])
						if (s = h(l.matches[0].replace(nt, rt), et.test(f[0].type) && dt(t.parentNode) || t)) {
							f.splice(a, 1), e = s.length && mt(f);
							if (!e) return P.apply(i, s), i;
							break
						}
				}
			}
			return (p || u(e, d))(s, t, !v, i, et.test(e) && dt(t.parentNode) || t), i
		}, n.sortStable = w.split("").sort(k).join("") === w, n.detectDuplicates = !!c, h(), n.sortDetached = at(function(e) {
			return e.compareDocumentPosition(p.createElement("div")) & 1
		}), at(function(e) {
			return e.innerHTML = "<a href='#'></a>", e.firstChild.getAttribute("href") === "#"
		}) || ft("type|href|height|width", function(e, t, n) {
			if (!n) return e.getAttribute(t, t.toLowerCase() === "type" ? 1 : 2)
		}), (!n.attributes || !at(function(e) {
			return e.innerHTML = "<input/>", e.firstChild.setAttribute("value", ""), e.firstChild.getAttribute("value") === ""
		})) && ft("value", function(e, t, n) {
			if (!n && e.nodeName.toLowerCase() === "input") return e.defaultValue
		}), at(function(e) {
			return e.getAttribute("disabled") == null
		}) || ft(j, function(e, t, n) {
			var r;
			if (!n) return e[t] === !0 ? t.toLowerCase() : (r = e.getAttributeNode(t)) && r.specified ? r.value : null
		}), st
	}(e);
	h.find = y, h.expr = y.selectors, h.expr[":"] = h.expr.pseudos, h.unique = y.uniqueSort, h.text = y.getText, h.isXMLDoc = y.isXML, h.contains = y.contains;
	var b = h.expr.match.needsContext,
		w = /^<(\w+)\s*\/?>(?:<\/\1>|)$/,
		E = /^.[^:#\[\.,]*$/;
	h.filter = function(e, t, n) {
		var r = t[0];
		return n && (e = ":not(" + e + ")"), t.length === 1 && r.nodeType === 1 ? h.find.matchesSelector(r, e) ? [r] : [] : h.find.matches(e, h.grep(t, function(e) {
			return e.nodeType === 1
		}))
	}, h.fn.extend({
		find: function(e) {
			var t, n = [],
				r = this,
				i = r.length;
			if (typeof e != "string") return this.pushStack(h(e).filter(function() {
				for (t = 0; t < i; t++)
					if (h.contains(r[t], this)) return !0
			}));
			for (t = 0; t < i; t++) h.find(e, r[t], n);
			return n = this.pushStack(i > 1 ? h.unique(n) : n), n.selector = this.selector ? this.selector + " " + e : e, n
		},
		filter: function(e) {
			return this.pushStack(S(this, e || [], !1))
		},
		not: function(e) {
			return this.pushStack(S(this, e || [], !0))
		},
		is: function(e) {
			return !!S(this, typeof e == "string" && b.test(e) ? h(e) : e || [], !1).length
		}
	});
	var x, T = e.document,
		N = /^(?:\s*(<[\w\W]+>)[^>]*|#([\w-]*))$/,
		C = h.fn.init = function(e, t) {
			var n, r;
			if (!e) return this;
			if (typeof e == "string") {
				e.charAt(0) === "<" && e.charAt(e.length - 1) === ">" && e.length >= 3 ? n = [null, e, null] : n = N.exec(e);
				if (n && (n[1] || !t)) {
					if (n[1]) {
						t = t instanceof h ? t[0] : t, h.merge(this, h.parseHTML(n[1], t && t.nodeType ? t.ownerDocument || t : T, !0));
						if (w.test(n[1]) && h.isPlainObject(t))
							for (n in t) h.isFunction(this[n]) ? this[n](t[n]) : this.attr(n, t[n]);
						return this
					}
					r = T.getElementById(n[2]);
					if (r && r.parentNode) {
						if (r.id !== n[2]) return x.find(e);
						this.length = 1, this[0] = r
					}
					return this.context = T, this.selector = e, this
				}
				return !t || t.jquery ? (t || x).find(e) : this.constructor(t).find(e)
			}
			return e.nodeType ? (this.context = this[0] = e, this.length = 1, this) : h.isFunction(e) ? typeof x.ready != "undefined" ? x.ready(e) : e(h) : (e.selector !== undefined && (this.selector = e.selector, this.context = e.betContext), h.makeArray(e, this))
		};
	C.prototype = h.fn, x = h(T);
	var k = /^(?:parents|prev(?:Until|All))/,
		L = {
			children: !0,
			contents: !0,
			next: !0,
			prev: !0
		};
	h.extend({
		dir: function(e, t, n) {
			var r = [],
				i = e[t];
			while (i && i.nodeType !== 9 && (n === undefined || i.nodeType !== 1 || !h(i).is(n))) i.nodeType === 1 && r.push(i), i = i[t];
			return r
		},
		sibling: function(e, t) {
			var n = [];
			for (; e; e = e.nextSibling) e.nodeType === 1 && e !== t && n.push(e);
			return n
		}
	}), h.fn.extend({
		has: function(e) {
			var t, n = h(e, this),
				r = n.length;
			return this.filter(function() {
				for (t = 0; t < r; t++)
					if (h.contains(this, n[t])) return !0
			})
		},
		closest: function(e, t) {
			var n, r = 0,
				i = this.length,
				s = [],
				o = b.test(e) || typeof e != "string" ? h(e, t || this.context) : 0;
			for (; r < i; r++)
				for (n = this[r]; n && n !== t; n = n.parentNode)
					if (n.nodeType < 11 && (o ? o.index(n) > -1 : n.nodeType === 1 && h.find.matchesSelector(n, e))) {
						s.push(n);
						break
					}
			return this.pushStack(s.length > 1 ? h.unique(s) : s)
		},
		index: function(e) {
			return e ? typeof e == "string" ? h.inArray(this[0], h(e)) : h.inArray(e.jquery ? e[0] : e, this) : this[0] && this[0].parentNode ? this.first().prevAll().length : -1
		},
		add: function(e, t) {
			return this.pushStack(h.unique(h.merge(this.get(), h(e, t))))
		},
		addBack: function(e) {
			return this.add(e == null ? this.prevObject : this.prevObject.filter(e))
		}
	}), h.each({
		parent: function(e) {
			var t = e.parentNode;
			return t && t.nodeType !== 11 ? t : null
		},
		parents: function(e) {
			return h.dir(e, "parentNode")
		},
		parentsUntil: function(e, t, n) {
			return h.dir(e, "parentNode", n)
		},
		next: function(e) {
			return A(e, "nextSibling")
		},
		prev: function(e) {
			return A(e, "previousSibling")
		},
		nextAll: function(e) {
			return h.dir(e, "nextSibling")
		},
		prevAll: function(e) {
			return h.dir(e, "previousSibling")
		},
		nextUntil: function(e, t, n) {
			return h.dir(e, "nextSibling", n)
		},
		prevUntil: function(e, t, n) {
			return h.dir(e, "previousSibling", n)
		},
		siblings: function(e) {
			return h.sibling((e.parentNode || {}).firstChild, e)
		},
		children: function(e) {
			return h.sibling(e.firstChild)
		},
		contents: function(e) {
			return h.nodeName(e, "iframe") ? e.contentDocument || e.contentWindow.document : h.merge([], e.childNodes)
		}
	}, function(e, t) {
		h.fn[e] = function(n, r) {
			var i = h.map(this, t, n);
			return e.slice(-5) !== "Until" && (r = n), r && typeof r == "string" && (i = h.filter(r, i)), this.length > 1 && (L[e] || (i = h.unique(i)), k.test(e) && (i = i.reverse())), this.pushStack(i)
		}
	});
	var O = /\S+/g,
		M = {};
	h.Callbacks = function(e) {
		e = typeof e == "string" ? M[e] || _(e) : h.extend({}, e);
		var t, n, r, i, s, o, u = [],
			a = !e.once && [],
			f = function(c) {
				n = e.memory && c, r = !0, s = o || 0, o = 0, i = u.length, t = !0;
				for (; u && s < i; s++)
					if (u[s].apply(c[0], c[1]) === !1 && e.stopOnFalse) {
						n = !1;
						break
					}
				t = !1, u && (a ? a.length && f(a.shift()) : n ? u = [] : l.disable())
			},
			l = {
				add: function() {
					if (u) {
						var r = u.length;
						(function s(t) {
							h.each(t, function(t, n) {
								var r = h.type(n);
								r === "function" ? (!e.unique || !l.has(n)) && u.push(n) : n && n.length && r !== "string" && s(n)
							})
						})(arguments), t ? i = u.length : n && (o = r, f(n))
					}
					return this
				},
				remove: function() {
					return u && h.each(arguments, function(e, n) {
						var r;
						while ((r = h.inArray(n, u, r)) > -1) u.splice(r, 1), t && (r <= i && i--, r <= s && s--)
					}), this
				},
				has: function(e) {
					return e ? h.inArray(e, u) > -1 : !!u && !!u.length
				},
				empty: function() {
					return u = [], i = 0, this
				},
				disable: function() {
					return u = a = n = undefined, this
				},
				disabled: function() {
					return !u
				},
				lock: function() {
					return a = undefined, n || l.disable(), this
				},
				locked: function() {
					return !a
				},
				fireWith: function(e, n) {
					return u && (!r || a) && (n = n || [], n = [e, n.slice ? n.slice() : n], t ? a.push(n) : f(n)), this
				},
				fire: function() {
					return l.fireWith(this, arguments), this
				},
				fired: function() {
					return !!r
				}
			};
		return l
	}, h.extend({
		Deferred: function(e) {
			var t = [
					["resolve", "done", h.Callbacks("once memory"), "resolved"],
					["reject", "fail", h.Callbacks("once memory"), "rejected"],
					["notify", "progress", h.Callbacks("memory")]
				],
				n = "pending",
				r = {
					state: function() {
						return n
					},
					always: function() {
						return i.done(arguments).fail(arguments), this
					},
					then: function() {
						var e = arguments;
						return h.Deferred(function(n) {
							h.each(t, function(t, s) {
								var o = h.isFunction(e[t]) && e[t];
								i[s[1]](function() {
									var e = o && o.apply(this, arguments);
									e && h.isFunction(e.promise) ? e.promise().done(n.resolve).fail(n.reject).progress(n.notify) : n[s[0] + "With"](this === r ? n.promise() : this, o ? [e] : arguments)
								})
							}), e = null
						}).promise()
					},
					promise: function(e) {
						return e != null ? h.extend(e, r) : r
					}
				},
				i = {};
			return r.pipe = r.then, h.each(t, function(e, s) {
				var o = s[2],
					u = s[3];
				r[s[1]] = o.add, u && o.add(function() {
					n = u
				}, t[e ^ 1][2].disable, t[2][2].lock), i[s[0]] = function() {
					return i[s[0] + "With"](this === i ? r : this, arguments), this
				}, i[s[0] + "With"] = o.fireWith
			}), r.promise(i), e && e.call(i, i), i
		},
		when: function(e) {
			var t = 0,
				n = r.call(arguments),
				i = n.length,
				s = i !== 1 || e && h.isFunction(e.promise) ? i : 0,
				o = s === 1 ? e : h.Deferred(),
				u = function(e, t, n) {
					return function(i) {
						t[e] = this, n[e] = arguments.length > 1 ? r.call(arguments) : i, n === a ? o.notifyWith(t, n) : --s || o.resolveWith(t, n)
					}
				},
				a, f, l;
			if (i > 1) {
				a = new Array(i), f = new Array(i), l = new Array(i);
				for (; t < i; t++) n[t] && h.isFunction(n[t].promise) ? n[t].promise().done(u(t, l, n)).fail(o.reject).progress(u(t, f, a)) : --s
			}
			return s || o.resolveWith(l, n), o.promise()
		}
	});
	var D;
	h.fn.ready = function(e) {
		return h.ready.promise().done(e), this
	}, h.extend({
		isReady: !1,
		readyWait: 1,
		holdReady: function(e) {
			e ? h.readyWait++ : h.ready(!0)
		},
		ready: function(e) {
			if (e === !0 ? --h.readyWait : h.isReady) return;
			if (!T.body) return setTimeout(h.ready);
			h.isReady = !0;
			if (e !== !0 && --h.readyWait > 0) return;
			D.resolveWith(T, [h]), h.fn.triggerHandler && (h(T).triggerHandler("ready"), h(T).off("ready"))
		}
	}), h.ready.promise = function(t) {
		if (!D) {
			D = h.Deferred();
			if (T.readyState === "complete") setTimeout(h.ready);
			else if (T.addEventListener) T.addEventListener("DOMContentLoaded", H, !1), e.addEventListener("load", H, !1);
			else {
				T.attachEvent("onreadystatechange", H), e.attachEvent("onload", H);
				var n = !1;
				try {
					n = e.frameElement == null && T.documentElement
				} catch (r) {}
				n && n.doScroll && function i() {
					if (!h.isReady) {
						try {
							n.doScroll("left")
						} catch (e) {
							return setTimeout(i, 50)
						}
						P(), h.ready()
					}
				}()
			}
		}
		return D.promise(t)
	};
	var B = typeof undefined,
		j;
	for (j in h(l)) break;
	l.ownLast = j !== "0", l.inlineBlockNeedsLayout = !1, h(function() {
			var e, t, n, r;
			n = T.getElementsByTagName("body")[0];
			if (!n || !n.style) return;
			t = T.createElement("div"), r = T.createElement("div"), r.style.cssText = "position:absolute;border:0;width:0;height:0;top:0;left:-9999px", n.appendChild(r).appendChild(t), typeof t.style.zoom !== B && (t.style.cssText = "display:inline;margin:0;border:0;padding:1px;width:1px;zoom:1", l.inlineBlockNeedsLayout = e = t.offsetWidth === 3, e && (n.style.zoom = 1)), n.removeChild(r)
		}),
		function() {
			var e = T.createElement("div");
			if (l.deleteExpando == null) {
				l.deleteExpando = !0;
				try {
					delete e.test
				} catch (t) {
					l.deleteExpando = !1
				}
			}
			e = null
		}(), h.acceptData = function(e) {
			var t = h.noData[(e.nodeName + " ").toLowerCase()],
				n = +e.nodeType || 1;
			return n !== 1 && n !== 9 ? !1 : !t || t !== !0 && e.getAttribute("classid") === t
		};
	var F = /^(?:\{[\w\W]*\}|\[[\w\W]*\])$/,
		I = /([A-Z])/g;
	h.extend({
		cache: {},
		noData: {
			"applet ": !0,
			"embed ": !0,
			"object ": "clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
		},
		hasData: function(e) {
			return e = e.nodeType ? h.cache[e[h.expando]] : e[h.expando], !!e && !R(e)
		},
		data: function(e, t, n) {
			return U(e, t, n)
		},
		removeData: function(e, t) {
			return z(e, t)
		},
		_data: function(e, t, n) {
			return U(e, t, n, !0)
		},
		_removeData: function(e, t) {
			return z(e, t, !0)
		}
	}), h.fn.extend({
		data: function(e, t) {
			var n, r, i, s = this[0],
				o = s && s.attributes;
			if (e === undefined) {
				if (this.length) {
					i = h.data(s);
					if (s.nodeType === 1 && !h._data(s, "parsedAttrs")) {
						n = o.length;
						while (n--) o[n] && (r = o[n].name, r.indexOf("data-") === 0 && (r = h.camelCase(r.slice(5)), q(s, r, i[r])));
						h._data(s, "parsedAttrs", !0)
					}
				}
				return i
			}
			return typeof e == "object" ? this.each(function() {
				h.data(this, e)
			}) : arguments.length > 1 ? this.each(function() {
				h.data(this, e, t)
			}) : s ? q(s, e, h.data(s, e)) : undefined
		},
		removeData: function(e) {
			return this.each(function() {
				h.removeData(this, e)
			})
		}
	}), h.extend({
		queue: function(e, t, n) {
			var r;
			if (e) return t = (t || "fx") + "queue", r = h._data(e, t), n && (!r || h.isArray(n) ? r = h._data(e, t, h.makeArray(n)) : r.push(n)), r || []
		},
		dequeue: function(e, t) {
			t = t || "fx";
			var n = h.queue(e, t),
				r = n.length,
				i = n.shift(),
				s = h._queueHooks(e, t),
				o = function() {
					h.dequeue(e, t)
				};
			i === "inprogress" && (i = n.shift(), r--), i && (t === "fx" && n.unshift("inprogress"), delete s.stop, i.call(e, o, s)), !r && s && s.empty.fire()
		},
		_queueHooks: function(e, t) {
			var n = t + "queueHooks";
			return h._data(e, n) || h._data(e, n, {
				empty: h.Callbacks("once memory").add(function() {
					h._removeData(e, t + "queue"), h._removeData(e, n)
				})
			})
		}
	}), h.fn.extend({
		queue: function(e, t) {
			var n = 2;
			return typeof e != "string" && (t = e, e = "fx", n--), arguments.length < n ? h.queue(this[0], e) : t === undefined ? this : this.each(function() {
				var n = h.queue(this, e, t);
				h._queueHooks(this, e), e === "fx" && n[0] !== "inprogress" && h.dequeue(this, e)
			})
		},
		dequeue: function(e) {
			return this.each(function() {
				h.dequeue(this, e)
			})
		},
		clearQueue: function(e) {
			return this.queue(e || "fx", [])
		},
		promise: function(e, t) {
			var n, r = 1,
				i = h.Deferred(),
				s = this,
				o = this.length,
				u = function() {
					--r || i.resolveWith(s, [s])
				};
			typeof e != "string" && (t = e, e = undefined), e = e || "fx";
			while (o--) n = h._data(s[o], e + "queueHooks"), n && n.empty && (r++, n.empty.add(u));
			return u(), i.promise(t)
		}
	});
	var W = /[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source,
		X = ["Top", "Right", "Bottom", "Left"],
		V = function(e, t) {
			return e = t || e, h.css(e, "display") === "none" || !h.contains(e.ownerDocument, e)
		},
		$ = h.access = function(e, t, n, r, i, s, o) {
			var u = 0,
				a = e.length,
				f = n == null;
			if (h.type(n) === "object") {
				i = !0;
				for (u in n) h.access(e, t, u, n[u], !0, s, o)
			} else if (r !== undefined) {
				i = !0, h.isFunction(r) || (o = !0), f && (o ? (t.call(e, r), t = null) : (f = t, t = function(e, t, n) {
					return f.call(h(e), n)
				}));
				if (t)
					for (; u < a; u++) t(e[u], n, o ? r : r.call(e[u], u, t(e[u], n)))
			}
			return i ? e : f ? t.call(e) : a ? t(e[0], n) : s
		},
		J = /^(?:checkbox|radio)$/i;
	(function() {
		var e = T.createElement("input"),
			t = T.createElement("div"),
			n = T.createDocumentFragment();
		t.innerHTML = "  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>", l.leadingWhitespace = t.firstChild.nodeType === 3, l.tbody = !t.getElementsByTagName("tbody").length, l.htmlSerialize = !!t.getElementsByTagName("link").length, l.html5Clone = T.createElement("nav").cloneNode(!0).outerHTML !== "<:nav></:nav>", e.type = "checkbox", e.checked = !0, n.appendChild(e), l.appendChecked = e.checked, t.innerHTML = "<textarea>x</textarea>", l.noCloneChecked = !!t.cloneNode(!0).lastChild.defaultValue, n.appendChild(t), t.innerHTML = "<input type='radio' checked='checked' name='t'/>", l.checkClone = t.cloneNode(!0).cloneNode(!0).lastChild.checked, l.noCloneEvent = !0, t.attachEvent && (t.attachEvent("onclick", function() {
			l.noCloneEvent = !1
		}), t.cloneNode(!0).click());
		if (l.deleteExpando == null) {
			l.deleteExpando = !0;
			try {
				delete t.test
			} catch (r) {
				l.deleteExpando = !1
			}
		}
	})(),
	function() {
		var t, n, r = T.createElement("div");
		for (t in {
				submit: !0,
				change: !0,
				focusin: !0
			}) n = "on" + t, (l[t + "Bubbles"] = n in e) || (r.setAttribute(n, "t"), l[t + "Bubbles"] = r.attributes[n].expando === !1);
		r = null
	}();
	var K = /^(?:input|select|textarea)$/i,
		Q = /^key/,
		G = /^(?:mouse|pointer|contextmenu)|click/,
		Y = /^(?:focusinfocus|focusoutblur)$/,
		Z = /^([^.]*)(?:\.(.+)|)$/;
	h.event = {
		global: {},
		add: function(e, t, n, r, i) {
			var s, o, u, a, f, l, c, p, d, v, m, g = h._data(e);
			if (!g) return;
			n.handler && (a = n, n = a.handler, i = a.selector), n.guid || (n.guid = h.guid++), (o = g.events) || (o = g.events = {}), (l = g.handle) || (l = g.handle = function(e) {
				return typeof h === B || !!e && h.event.triggered === e.type ? undefined : h.event.dispatch.apply(l.elem, arguments)
			}, l.elem = e), t = (t || "").match(O) || [""], u = t.length;
			while (u--) {
				s = Z.exec(t[u]) || [], d = m = s[1], v = (s[2] || "").split(".").sort();
				if (!d) continue;
				f = h.event.special[d] || {}, d = (i ? f.delegateType : f.bindType) || d, f = h.event.special[d] || {}, c = h.extend({
					type: d,
					origType: m,
					data: r,
					handler: n,
					guid: n.guid,
					selector: i,
					needsContext: i && h.expr.match.needsContext.test(i),
					namespace: v.join(".")
				}, a);
				if (!(p = o[d])) {
					p = o[d] = [], p.delegateCount = 0;
					if (!f.setup || f.setup.call(e, r, v, l) === !1) e.addEventListener ? e.addEventListener(d, l, !1) : e.attachEvent && e.attachEvent("on" + d, l)
				}
				f.add && (f.add.call(e, c), c.handler.guid || (c.handler.guid = n.guid)), i ? p.splice(p.delegateCount++, 0, c) : p.push(c), h.event.global[d] = !0
			}
			e = null
		},
		remove: function(e, t, n, r, i) {
			var s, o, u, a, f, l, c, p, d, v, m, g = h.hasData(e) && h._data(e);
			if (!g || !(l = g.events)) return;
			t = (t || "").match(O) || [""], f = t.length;
			while (f--) {
				u = Z.exec(t[f]) || [], d = m = u[1], v = (u[2] || "").split(".").sort();
				if (!d) {
					for (d in l) h.event.remove(e, d + t[f], n, r, !0);
					continue
				}
				c = h.event.special[d] || {}, d = (r ? c.delegateType : c.bindType) || d, p = l[d] || [], u = u[2] && new RegExp("(^|\\.)" + v.join("\\.(?:.*\\.|)") + "(\\.|$)"), a = s = p.length;
				while (s--) o = p[s], (i || m === o.origType) && (!n || n.guid === o.guid) && (!u || u.test(o.namespace)) && (!r || r === o.selector || r === "**" && o.selector) && (p.splice(s, 1), o.selector && p.delegateCount--, c.remove && c.remove.call(e, o));
				a && !p.length && ((!c.teardown || c.teardown.call(e, v, g.handle) === !1) && h.removeEvent(e, d, g.handle), delete l[d])
			}
			h.isEmptyObject(l) && (delete g.handle, h._removeData(e, "events"))
		},
		trigger: function(t, n, r, i) {
			var s, o, u, a, l, c, p, d = [r || T],
				v = f.call(t, "type") ? t.type : t,
				m = f.call(t, "namespace") ? t.namespace.split(".") : [];
			u = c = r = r || T;
			if (r.nodeType === 3 || r.nodeType === 8) return;
			if (Y.test(v + h.event.triggered)) return;
			v.indexOf(".") >= 0 && (m = v.split("."), v = m.shift(), m.sort()), o = v.indexOf(":") < 0 && "on" + v, t = t[h.expando] ? t : new h.Event(v, typeof t == "object" && t), t.isTrigger = i ? 2 : 3, t.namespace = m.join("."), t.namespace_re = t.namespace ? new RegExp("(^|\\.)" + m.join("\\.(?:.*\\.|)") + "(\\.|$)") : null, t.result = undefined, t.target || (t.target = r), n = n == null ? [t] : h.makeArray(n, [t]), l = h.event.special[v] || {};
			if (!i && l.trigger && l.trigger.apply(r, n) === !1) return;
			if (!i && !l.noBubble && !h.isWindow(r)) {
				a = l.delegateType || v, Y.test(a + v) || (u = u.parentNode);
				for (; u; u = u.parentNode) d.push(u), c = u;
				c === (r.ownerDocument || T) && d.push(c.defaultView || c.parentWindow || e)
			}
			p = 0;
			while ((u = d[p++]) && !t.isPropagationStopped()) t.type = p > 1 ? a : l.bindType || v, s = (h._data(u, "events") || {})[t.type] && h._data(u, "handle"), s && s.apply(u, n), s = o && u[o], s && s.apply && h.acceptData(u) && (t.result = s.apply(u, n), t.result === !1 && t.preventDefault());
			t.type = v;
			if (!i && !t.isDefaultPrevented() && (!l._default || l._default.apply(d.pop(), n) === !1) && h.acceptData(r) && o && r[v] && !h.isWindow(r)) {
				c = r[o], c && (r[o] = null), h.event.triggered = v;
				try {
					r[v]()
				} catch (g) {}
				h.event.triggered = undefined, c && (r[o] = c)
			}
			return t.result
		},
		dispatch: function(e) {
			e = h.event.fix(e);
			var t, n, i, s, o, u = [],
				a = r.call(arguments),
				f = (h._data(this, "events") || {})[e.type] || [],
				l = h.event.special[e.type] || {};
			a[0] = e, e.delegateTarget = this;
			if (l.preDispatch && l.preDispatch.call(this, e) === !1) return;
			u = h.event.handlers.call(this, e, f), t = 0;
			while ((s = u[t++]) && !e.isPropagationStopped()) {
				e.currentTarget = s.elem, o = 0;
				while ((i = s.handlers[o++]) && !e.isImmediatePropagationStopped())
					if (!e.namespace_re || e.namespace_re.test(i.namespace)) e.handleObj = i, e.data = i.data, n = ((h.event.special[i.origType] || {}).handle || i.handler).apply(s.elem, a), n !== undefined && (e.result = n) === !1 && (e.preventDefault(), e.stopPropagation())
			}
			return l.postDispatch && l.postDispatch.call(this, e), e.result
		},
		handlers: function(e, t) {
			var n, r, i, s, o = [],
				u = t.delegateCount,
				a = e.target;
			if (u && a.nodeType && (!e.button || e.type !== "click"))
				for (; a != this; a = a.parentNode || this)
					if (a.nodeType === 1 && (a.disabled !== !0 || e.type !== "click")) {
						i = [];
						for (s = 0; s < u; s++) r = t[s], n = r.selector + " ", i[n] === undefined && (i[n] = r.needsContext ? h(n, this).index(a) >= 0 : h.find(n, this, null, [a]).length), i[n] && i.push(r);
						i.length && o.push({
							elem: a,
							handlers: i
						})
					}
			return u < t.length && o.push({
				elem: this,
				handlers: t.slice(u)
			}), o
		},
		fix: function(e) {
			if (e[h.expando]) return e;
			var t, n, r, i = e.type,
				s = e,
				o = this.fixHooks[i];
			o || (this.fixHooks[i] = o = G.test(i) ? this.mouseHooks : Q.test(i) ? this.keyHooks : {}), r = o.props ? this.props.concat(o.props) : this.props, e = new h.Event(s), t = r.length;
			while (t--) n = r[t], e[n] = s[n];
			return e.target || (e.target = s.srcElement || T), e.target.nodeType === 3 && (e.target = e.target.parentNode), e.metaKey = !!e.metaKey, o.filter ? o.filter(e, s) : e
		},
		props: "altKey bubbles cancelable ctrlKey currentTarget eventPhase metaKey relatedTarget shiftKey target timeStamp view which".split(" "),
		fixHooks: {},
		keyHooks: {
			props: "char charCode key keyCode".split(" "),
			filter: function(e, t) {
				return e.which == null && (e.which = t.charCode != null ? t.charCode : t.keyCode), e
			}
		},
		mouseHooks: {
			props: "button buttons clientX clientY fromElement offsetX offsetY pageX pageY screenX screenY toElement".split(" "),
			filter: function(e, t) {
				var n, r, i, s = t.button,
					o = t.fromElement;
				return e.pageX == null && t.clientX != null && (r = e.target.ownerDocument || T, i = r.documentElement, n = r.body, e.pageX = t.clientX + (i && i.scrollLeft || n && n.scrollLeft || 0) - (i && i.clientLeft || n && n.clientLeft || 0), e.pageY = t.clientY + (i && i.scrollTop || n && n.scrollTop || 0) - (i && i.clientTop || n && n.clientTop || 0)), !e.relatedTarget && o && (e.relatedTarget = o === e.target ? t.toElement : o), !e.which && s !== undefined && (e.which = s & 1 ? 1 : s & 2 ? 3 : s & 4 ? 2 : 0), e
			}
		},
		special: {
			load: {
				noBubble: !0
			},
			focus: {
				trigger: function() {
					if (this !== nt() && this.focus) try {
						return this.focus(), !1
					} catch (e) {}
				},
				delegateType: "focusin"
			},
			blur: {
				trigger: function() {
					if (this === nt() && this.blur) return this.blur(), !1
				},
				delegateType: "focusout"
			},
			click: {
				trigger: function() {
					if (h.nodeName(this, "input") && this.type === "checkbox" && this.click) return this.click(), !1
				},
				_default: function(e) {
					return h.nodeName(e.target, "a")
				}
			},
			beforeunload: {
				postDispatch: function(e) {
					e.result !== undefined && e.originalEvent && (e.originalEvent.returnValue = e.result)
				}
			}
		},
		simulate: function(e, t, n, r) {
			var i = h.extend(new h.Event, n, {
				type: e,
				isSimulated: !0,
				originalEvent: {}
			});
			r ? h.event.trigger(i, null, t) : h.event.dispatch.call(t, i), i.isDefaultPrevented() && n.preventDefault()
		}
	}, h.removeEvent = T.removeEventListener ? function(e, t, n) {
		e.removeEventListener && e.removeEventListener(t, n, !1)
	} : function(e, t, n) {
		var r = "on" + t;
		e.detachEvent && (typeof e[r] === B && (e[r] = null), e.detachEvent(r, n))
	}, h.Event = function(e, t) {
		if (!(this instanceof h.Event)) return new h.Event(e, t);
		e && e.type ? (this.originalEvent = e, this.type = e.type, this.isDefaultPrevented = e.defaultPrevented || e.defaultPrevented === undefined && e.returnValue === !1 ? et : tt) : this.type = e, t && h.extend(this, t), this.timeStamp = e && e.timeStamp || h.now(), this[h.expando] = !0
	}, h.Event.prototype = {
		isDefaultPrevented: tt,
		isPropagationStopped: tt,
		isImmediatePropagationStopped: tt,
		preventDefault: function() {
			var e = this.originalEvent;
			this.isDefaultPrevented = et;
			if (!e) return;
			e.preventDefault ? e.preventDefault() : e.returnValue = !1
		},
		stopPropagation: function() {
			var e = this.originalEvent;
			this.isPropagationStopped = et;
			if (!e) return;
			e.stopPropagation && e.stopPropagation(), e.cancelBubble = !0
		},
		stopImmediatePropagation: function() {
			var e = this.originalEvent;
			this.isImmediatePropagationStopped = et, e && e.stopImmediatePropagation && e.stopImmediatePropagation(), this.stopPropagation()
		}
	}, h.each({
		mouseenter: "mouseover",
		mouseleave: "mouseout",
		pointerenter: "pointerover",
		pointerleave: "pointerout"
	}, function(e, t) {
		h.event.special[e] = {
			delegateType: t,
			bindType: t,
			handle: function(e) {
				var n, r = this,
					i = e.relatedTarget,
					s = e.handleObj;
				if (!i || i !== r && !h.contains(r, i)) e.type = s.origType, n = s.handler.apply(this, arguments), e.type = t;
				return n
			}
		}
	}), l.submitBubbles || (h.event.special.submit = {
		setup: function() {
			if (h.nodeName(this, "form")) return !1;
			h.event.add(this, "click._submit keypress._submit", function(e) {
				var t = e.target,
					n = h.nodeName(t, "input") || h.nodeName(t, "button") ? t.form : undefined;
				n && !h._data(n, "submitBubbles") && (h.event.add(n, "submit._submit", function(e) {
					e._submit_bubble = !0
				}), h._data(n, "submitBubbles", !0))
			})
		},
		postDispatch: function(e) {
			e._submit_bubble && (delete e._submit_bubble, this.parentNode && !e.isTrigger && h.event.simulate("submit", this.parentNode, e, !0))
		},
		teardown: function() {
			if (h.nodeName(this, "form")) return !1;
			h.event.remove(this, "._submit")
		}
	}), l.changeBubbles || (h.event.special.change = {
		setup: function() {
			if (K.test(this.nodeName)) {
				if (this.type === "checkbox" || this.type === "radio") h.event.add(this, "propertychange._change", function(e) {
					e.originalEvent.propertyName === "checked" && (this._just_changed = !0)
				}), h.event.add(this, "click._change", function(e) {
					this._just_changed && !e.isTrigger && (this._just_changed = !1), h.event.simulate("change", this, e, !0)
				});
				return !1
			}
			h.event.add(this, "beforeactivate._change", function(e) {
				var t = e.target;
				K.test(t.nodeName) && !h._data(t, "changeBubbles") && (h.event.add(t, "change._change", function(e) {
					this.parentNode && !e.isSimulated && !e.isTrigger && h.event.simulate("change", this.parentNode, e, !0)
				}), h._data(t, "changeBubbles", !0))
			})
		},
		handle: function(e) {
			var t = e.target;
			if (this !== t || e.isSimulated || e.isTrigger || t.type !== "radio" && t.type !== "checkbox") return e.handleObj.handler.apply(this, arguments)
		},
		teardown: function() {
			return h.event.remove(this, "._change"), !K.test(this.nodeName)
		}
	}), l.focusinBubbles || h.each({
		focus: "focusin",
		blur: "focusout"
	}, function(e, t) {
		var n = function(e) {
			h.event.simulate(t, e.target, h.event.fix(e), !0)
		};
		h.event.special[t] = {
			setup: function() {
				var r = this.ownerDocument || this,
					i = h._data(r, t);
				i || r.addEventListener(e, n, !0), h._data(r, t, (i || 0) + 1)
			},
			teardown: function() {
				var r = this.ownerDocument || this,
					i = h._data(r, t) - 1;
				i ? h._data(r, t, i) : (r.removeEventListener(e, n, !0), h._removeData(r, t))
			}
		}
	}), h.fn.extend({
		on: function(e, t, n, r, i) {
			var s, o;
			if (typeof e == "object") {
				typeof t != "string" && (n = n || t, t = undefined);
				for (s in e) this.on(s, t, n, e[s], i);
				return this
			}
			n == null && r == null ? (r = t, n = t = undefined) : r == null && (typeof t == "string" ? (r = n, n = undefined) : (r = n, n = t, t = undefined));
			if (r === !1) r = tt;
			else if (!r) return this;
			return i === 1 && (o = r, r = function(e) {
				return h().off(e), o.apply(this, arguments)
			}, r.guid = o.guid || (o.guid = h.guid++)), this.each(function() {
				h.event.add(this, e, r, n, t)
			})
		},
		one: function(e, t, n, r) {
			return this.on(e, t, n, r, 1)
		},
		off: function(e, t, n) {
			var r, i;
			if (e && e.preventDefault && e.handleObj) return r = e.handleObj, h(e.delegateTarget).off(r.namespace ? r.origType + "." + r.namespace : r.origType, r.selector, r.handler), this;
			if (typeof e == "object") {
				for (i in e) this.off(i, t, e[i]);
				return this
			}
			if (t === !1 || typeof t == "function") n = t, t = undefined;
			return n === !1 && (n = tt), this.each(function() {
				h.event.remove(this, e, n, t)
			})
		},
		trigger: function(e, t) {
			return this.each(function() {
				h.event.trigger(e, t, this)
			})
		},
		triggerHandler: function(e, t) {
			var n = this[0];
			if (n) return h.event.trigger(e, t, n, !0)
		}
	});
	var it = "abbr|article|aside|audio|bdi|canvas|data|datalist|details|figcaption|figure|footer|header|hgroup|mark|meter|nav|output|progress|section|summary|time|video",
		st = / jQuery\d+="(?:null|\d+)"/g,
		ot = new RegExp("<(?:" + it + ")[\\s/>]", "i"),
		ut = /^\s+/,
		at = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/gi,
		ft = /<([\w:]+)/,
		lt = /<tbody/i,
		ct = /<|&#?\w+;/,
		ht = /<(?:script|style|link)/i,
		pt = /checked\s*(?:[^=]|=\s*.checked.)/i,
		dt = /^$|\/(?:java|ecma)script/i,
		vt = /^true\/(.*)/,
		mt = /^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g,
		gt = {
			option: [1, "<select multiple='multiple'>", "</select>"],
			legend: [1, "<fieldset>", "</fieldset>"],
			area: [1, "<map>", "</map>"],
			param: [1, "<object>", "</object>"],
			thead: [1, "<table>", "</table>"],
			tr: [2, "<table><tbody>", "</tbody></table>"],
			col: [2, "<table><tbody></tbody><colgroup>", "</colgroup></table>"],
			td: [3, "<table><tbody><tr>", "</tr></tbody></table>"],
			_default: l.htmlSerialize ? [0, "", ""] : [1, "X<div>", "</div>"]
		},
		yt = rt(T),
		bt = yt.appendChild(T.createElement("div"));
	gt.optgroup = gt.option, gt.tbody = gt.tfoot = gt.colgroup = gt.caption = gt.thead, gt.th = gt.td, h.extend({
		clone: function(e, t, n) {
			var r, i, s, o, u, a = h.contains(e.ownerDocument, e);
			l.html5Clone || h.isXMLDoc(e) || !ot.test("<" + e.nodeName + ">") ? s = e.cloneNode(!0) : (bt.innerHTML = e.outerHTML, bt.removeChild(s = bt.firstChild));
			if ((!l.noCloneEvent || !l.noCloneChecked) && (e.nodeType === 1 || e.nodeType === 11) && !h.isXMLDoc(e)) {
				r = wt(s), u = wt(e);
				for (o = 0;
					(i = u[o]) != null; ++o) r[o] && kt(i, r[o])
			}
			if (t)
				if (n) {
					u = u || wt(e), r = r || wt(s);
					for (o = 0;
						(i = u[o]) != null; o++) Ct(i, r[o])
				} else Ct(e, s);
			return r = wt(s, "script"), r.length > 0 && Nt(r, !a && wt(e, "script")), r = u = i = null, s
		},
		buildFragment: function(e, t, n, r) {
			var i, s, o, u, a, f, c, p = e.length,
				d = rt(t),
				v = [],
				m = 0;
			for (; m < p; m++) {
				s = e[m];
				if (s || s === 0)
					if (h.type(s) === "object") h.merge(v, s.nodeType ? [s] : s);
					else if (!ct.test(s)) v.push(t.createTextNode(s));
				else {
					u = u || d.appendChild(t.createElement("div")), a = (ft.exec(s) || ["", ""])[1].toLowerCase(), c = gt[a] || gt._default, u.innerHTML = c[1] + s.replace(at, "<$1></$2>") + c[2], i = c[0];
					while (i--) u = u.lastChild;
					!l.leadingWhitespace && ut.test(s) && v.push(t.createTextNode(ut.exec(s)[0]));
					if (!l.tbody) {
						s = a === "table" && !lt.test(s) ? u.firstChild : c[1] === "<table>" && !lt.test(s) ? u : 0, i = s && s.childNodes.length;
						while (i--) h.nodeName(f = s.childNodes[i], "tbody") && !f.childNodes.length && s.removeChild(f)
					}
					h.merge(v, u.childNodes), u.textContent = "";
					while (u.firstChild) u.removeChild(u.firstChild);
					u = d.lastChild
				}
			}
			u && d.removeChild(u), l.appendChecked || h.grep(wt(v, "input"), Et), m = 0;
			while (s = v[m++]) {
				if (r && h.inArray(s, r) !== -1) continue;
				o = h.contains(s.ownerDocument, s), u = wt(d.appendChild(s), "script"), o && Nt(u);
				if (n) {
					i = 0;
					while (s = u[i++]) dt.test(s.type || "") && n.push(s)
				}
			}
			return u = null, d
		},
		cleanData: function(e, t) {
			var r, i, s, o, u = 0,
				a = h.expando,
				f = h.cache,
				c = l.deleteExpando,
				p = h.event.special;
			for (;
				(r = e[u]) != null; u++)
				if (t || h.acceptData(r)) {
					s = r[a], o = s && f[s];
					if (o) {
						if (o.events)
							for (i in o.events) p[i] ? h.event.remove(r, i) : h.removeEvent(r, i, o.handle);
						f[s] && (delete f[s], c ? delete r[a] : typeof r.removeAttribute !== B ? r.removeAttribute(a) : r[a] = null, n.push(s))
					}
				}
		}
	}), h.fn.extend({
		text: function(e) {
			return $(this, function(e) {
				return e === undefined ? h.text(this) : this.empty().append((this[0] && this[0].ownerDocument || T).createTextNode(e))
			}, null, e, arguments.length)
		},
		append: function() {
			return this.domManip(arguments, function(e) {
				if (this.nodeType === 1 || this.nodeType === 11 || this.nodeType === 9) {
					var t = St(this, e);
					t.appendChild(e)
				}
			})
		},
		prepend: function() {
			return this.domManip(arguments, function(e) {
				if (this.nodeType === 1 || this.nodeType === 11 || this.nodeType === 9) {
					var t = St(this, e);
					t.insertBefore(e, t.firstChild)
				}
			})
		},
		before: function() {
			return this.domManip(arguments, function(e) {
				this.parentNode && this.parentNode.insertBefore(e, this)
			})
		},
		after: function() {
			return this.domManip(arguments, function(e) {
				this.parentNode && this.parentNode.insertBefore(e, this.nextSibling)
			})
		},
		remove: function(e, t) {
			var n, r = e ? h.filter(e, this) : this,
				i = 0;
			for (;
				(n = r[i]) != null; i++) !t && n.nodeType === 1 && h.cleanData(wt(n)), n.parentNode && (t && h.contains(n.ownerDocument, n) && Nt(wt(n, "script")), n.parentNode.removeChild(n));
			return this
		},
		empty: function() {
			var e, t = 0;
			for (;
				(e = this[t]) != null; t++) {
				e.nodeType === 1 && h.cleanData(wt(e, !1));
				while (e.firstChild) e.removeChild(e.firstChild);
				e.options && h.nodeName(e, "select") && (e.options.length = 0)
			}
			return this
		},
		clone: function(e, t) {
			return e = e == null ? !1 : e, t = t == null ? e : t, this.map(function() {
				return h.clone(this, e, t)
			})
		},
		html: function(e) {
			return $(this, function(e) {
				var t = this[0] || {},
					n = 0,
					r = this.length;
				if (e === undefined) return t.nodeType === 1 ? t.innerHTML.replace(st, "") : undefined;
				if (typeof e == "string" && !ht.test(e) && (l.htmlSerialize || !ot.test(e)) && (l.leadingWhitespace || !ut.test(e)) && !gt[(ft.exec(e) || ["", ""])[1].toLowerCase()]) {
					e = e.replace(at, "<$1></$2>");
					try {
						for (; n < r; n++) t = this[n] || {}, t.nodeType === 1 && (h.cleanData(wt(t, !1)), t.innerHTML = e);
						t = 0
					} catch (i) {}
				}
				t && this.empty().append(e)
			}, null, e, arguments.length)
		},
		replaceWith: function() {
			var e = arguments[0];
			return this.domManip(arguments, function(t) {
				e = this.parentNode, h.cleanData(wt(this)), e && e.replaceChild(t, this)
			}), e && (e.length || e.nodeType) ? this : this.remove()
		},
		detach: function(e) {
			return this.remove(e, !0)
		},
		domManip: function(e, t) {
			e = i.apply([], e);
			var n, r, s, o, u, a, f = 0,
				c = this.length,
				p = this,
				d = c - 1,
				v = e[0],
				m = h.isFunction(v);
			if (m || c > 1 && typeof v == "string" && !l.checkClone && pt.test(v)) return this.each(function(n) {
				var r = p.eq(n);
				m && (e[0] = v.call(this, n, r.html())), r.domManip(e, t)
			});
			if (c) {
				a = h.buildFragment(e, this[0].ownerDocument, !1, this), n = a.firstChild, a.childNodes.length === 1 && (a = n);
				if (n) {
					o = h.map(wt(a, "script"), xt), s = o.length;
					for (; f < c; f++) r = a, f !== d && (r = h.clone(r, !0, !0), s && h.merge(o, wt(r, "script"))), t.call(this[f], r, f);
					if (s) {
						u = o[o.length - 1].ownerDocument, h.map(o, Tt);
						for (f = 0; f < s; f++) r = o[f], dt.test(r.type || "") && !h._data(r, "globalEval") && h.contains(u, r) && (r.src ? h._evalUrl && h._evalUrl(r.src) : h.globalEval((r.text || r.textContent || r.innerHTML || "").replace(mt, "")))
					}
					a = n = null
				}
			}
			return this
		}
	}), h.each({
		appendTo: "append",
		prependTo: "prepend",
		insertBefore: "before",
		insertAfter: "after",
		replaceAll: "replaceWith"
	}, function(e, t) {
		h.fn[e] = function(e) {
			var n, r = 0,
				i = [],
				o = h(e),
				u = o.length - 1;
			for (; r <= u; r++) n = r === u ? this : this.clone(!0), h(o[r])[t](n), s.apply(i, n.get());
			return this.pushStack(i)
		}
	});
	var Lt, At = {};
	(function() {
		var e;
		l.shrinkWrapBlocks = function() {
			if (e != null) return e;
			e = !1;
			var t, n, r;
			n = T.getElementsByTagName("body")[0];
			if (!n || !n.style) return;
			return t = T.createElement("div"), r = T.createElement("div"), r.style.cssText = "position:absolute;border:0;width:0;height:0;top:0;left:-9999px", n.appendChild(r).appendChild(t), typeof t.style.zoom !== B && (t.style.cssText = "-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;display:block;margin:0;border:0;padding:1px;width:1px;zoom:1", t.appendChild(T.createElement("div")).style.width = "5px", e = t.offsetWidth !== 3), n.removeChild(r), e
		}
	})();
	var _t = /^margin/,
		Dt = new RegExp("^(" + W + ")(?!px)[a-z%]+$", "i"),
		Pt, Ht, Bt = /^(top|right|bottom|left)$/;
	e.getComputedStyle ? (Pt = function(e) {
			return e.ownerDocument.defaultView.getComputedStyle(e, null)
		}, Ht = function(e, t, n) {
			var r, i, s, o, u = e.style;
			return n = n || Pt(e), o = n ? n.getPropertyValue(t) || n[t] : undefined, n && (o === "" && !h.contains(e.ownerDocument, e) && (o = h.style(e, t)), Dt.test(o) && _t.test(t) && (r = u.width, i = u.minWidth, s = u.maxWidth, u.minWidth = u.maxWidth = u.width = o, o = n.width, u.width = r, u.minWidth = i, u.maxWidth = s)), o === undefined ? o : o + ""
		}) : T.documentElement.currentStyle && (Pt = function(e) {
			return e.currentStyle
		}, Ht = function(e, t, n) {
			var r, i, s, o, u = e.style;
			return n = n || Pt(e), o = n ? n[t] : undefined, o == null && u && u[t] && (o = u[t]), Dt.test(o) && !Bt.test(t) && (r = u.left, i = e.runtimeStyle, s = i && i.left, s && (i.left = e.currentStyle.left), u.left = t === "fontSize" ? "1em" : o, o = u.pixelLeft + "px", u.left = r, s && (i.left = s)), o === undefined ? o : o + "" || "auto"
		}),
		function() {
			function a() {
				var t, n, r, a;
				n = T.getElementsByTagName("body")[0];
				if (!n || !n.style) return;
				t = T.createElement("div"), r = T.createElement("div"), r.style.cssText = "position:absolute;border:0;width:0;height:0;top:0;left:-9999px", n.appendChild(r).appendChild(t), t.style.cssText = "-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;display:block;margin-top:1%;top:1%;border:1px;padding:1px;width:4px;position:absolute", i = s = !1, u = !0, e.getComputedStyle && (i = (e.getComputedStyle(t, null) || {}).top !== "1%", s = (e.getComputedStyle(t, null) || {
					width: "4px"
				}).width === "4px", a = t.appendChild(T.createElement("div")), a.style.cssText = t.style.cssText = "-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;display:block;margin:0;border:0;padding:0", a.style.marginRight = a.style.width = "0", t.style.width = "1px", u = !parseFloat((e.getComputedStyle(a, null) || {}).marginRight)), t.innerHTML = "<table><tr><td></td><td>t</td></tr></table>", a = t.getElementsByTagName("td"), a[0].style.cssText = "margin:0;border:0;padding:0;display:none", o = a[0].offsetHeight === 0, o && (a[0].style.display = "", a[1].style.display = "none", o = a[0].offsetHeight === 0), n.removeChild(r)
			}
			var t, n, r, i, s, o, u;
			t = T.createElement("div"), t.innerHTML = "  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>", r = t.getElementsByTagName("a")[0], n = r && r.style;
			if (!n) return;
			n.cssText = "float:left;opacity:.5", l.opacity = n.opacity === "0.5", l.cssFloat = !!n.cssFloat, t.style.backgroundClip = "content-box", t.cloneNode(!0).style.backgroundClip = "", l.clearCloneStyle = t.style.backgroundClip === "content-box", l.boxSizing = n.boxSizing === "" || n.MozBoxSizing === "" || n.WebkitBoxSizing === "", h.extend(l, {
				reliableHiddenOffsets: function() {
					return o == null && a(), o
				},
				boxSizingReliable: function() {
					return s == null && a(), s
				},
				pixelPosition: function() {
					return i == null && a(), i
				},
				reliableMarginRight: function() {
					return u == null && a(), u
				}
			})
		}(), h.swap = function(e, t, n, r) {
			var i, s, o = {};
			for (s in t) o[s] = e.style[s], e.style[s] = t[s];
			i = n.apply(e, r || []);
			for (s in t) e.style[s] = o[s];
			return i
		};
	var Ft = /alpha\([^)]*\)/i,
		It = /opacity\s*=\s*([^)]*)/,
		qt = /^(none|table(?!-c[ea]).+)/,
		Rt = new RegExp("^(" + W + ")(.*)$", "i"),
		Ut = new RegExp("^([+-])=(" + W + ")", "i"),
		zt = {
			position: "absolute",
			visibility: "hidden",
			display: "block"
		},
		Wt = {
			letterSpacing: "0",
			fontWeight: "400"
		},
		Xt = ["Webkit", "O", "Moz", "ms"];
	h.extend({
		cssHooks: {
			opacity: {
				get: function(e, t) {
					if (t) {
						var n = Ht(e, "opacity");
						return n === "" ? "1" : n
					}
				}
			}
		},
		cssNumber: {
			columnCount: !0,
			fillOpacity: !0,
			flexGrow: !0,
			flexShrink: !0,
			fontWeight: !0,
			lineHeight: !0,
			opacity: !0,
			order: !0,
			orphans: !0,
			widows: !0,
			zIndex: !0,
			zoom: !0
		},
		cssProps: {
			"float": l.cssFloat ? "cssFloat" : "styleFloat"
		},
		style: function(e, t, n, r) {
			if (!e || e.nodeType === 3 || e.nodeType === 8 || !e.style) return;
			var i, s, o, u = h.camelCase(t),
				a = e.style;
			t = h.cssProps[u] || (h.cssProps[u] = Vt(a, u)), o = h.cssHooks[t] || h.cssHooks[u];
			if (n === undefined) return o && "get" in o && (i = o.get(e, !1, r)) !== undefined ? i : a[t];
			s = typeof n, s === "string" && (i = Ut.exec(n)) && (n = (i[1] + 1) * i[2] + parseFloat(h.css(e, t)), s = "number");
			if (n == null || n !== n) return;
			s === "number" && !h.cssNumber[u] && (n += "px"), !l.clearCloneStyle && n === "" && t.indexOf("background") === 0 && (a[t] = "inherit");
			if (!o || !("set" in o) || (n = o.set(e, n, r)) !== undefined) try {
				a[t] = n
			} catch (f) {}
		},
		css: function(e, t, n, r) {
			var i, s, o, u = h.camelCase(t);
			return t = h.cssProps[u] || (h.cssProps[u] = Vt(e.style, u)), o = h.cssHooks[t] || h.cssHooks[u], o && "get" in o && (s = o.get(e, !0, n)), s === undefined && (s = Ht(e, t, r)), s === "normal" && t in Wt && (s = Wt[t]), n === "" || n ? (i = parseFloat(s), n === !0 || h.isNumeric(i) ? i || 0 : s) : s
		}
	}), h.each(["height", "width"], function(e, t) {
		h.cssHooks[t] = {
			get: function(e, n, r) {
				if (n) return qt.test(h.css(e, "display")) && e.offsetWidth === 0 ? h.swap(e, zt, function() {
					return Qt(e, t, r)
				}) : Qt(e, t, r)
			},
			set: function(e, n, r) {
				var i = r && Pt(e);
				return Jt(e, n, r ? Kt(e, t, r, l.boxSizing && h.css(e, "boxSizing", !1, i) === "border-box", i) : 0)
			}
		}
	}), l.opacity || (h.cssHooks.opacity = {
		get: function(e, t) {
			return It.test((t && e.currentStyle ? e.currentStyle.filter : e.style.filter) || "") ? .01 * parseFloat(RegExp.$1) + "" : t ? "1" : ""
		},
		set: function(e, t) {
			var n = e.style,
				r = e.currentStyle,
				i = h.isNumeric(t) ? "alpha(opacity=" + t * 100 + ")" : "",
				s = r && r.filter || n.filter || "";
			n.zoom = 1;
			if ((t >= 1 || t === "") && h.trim(s.replace(Ft, "")) === "" && n.removeAttribute) {
				n.removeAttribute("filter");
				if (t === "" || r && !r.filter) return
			}
			n.filter = Ft.test(s) ? s.replace(Ft, i) : s + " " + i
		}
	}), h.cssHooks.marginRight = jt(l.reliableMarginRight, function(e, t) {
		if (t) return h.swap(e, {
			display: "inline-block"
		}, Ht, [e, "marginRight"])
	}), h.each({
		margin: "",
		padding: "",
		border: "Width"
	}, function(e, t) {
		h.cssHooks[e + t] = {
			expand: function(n) {
				var r = 0,
					i = {},
					s = typeof n == "string" ? n.split(" ") : [n];
				for (; r < 4; r++) i[e + X[r] + t] = s[r] || s[r - 2] || s[0];
				return i
			}
		}, _t.test(e) || (h.cssHooks[e + t].set = Jt)
	}), h.fn.extend({
		css: function(e, t) {
			return $(this, function(e, t, n) {
				var r, i, s = {},
					o = 0;
				if (h.isArray(t)) {
					r = Pt(e), i = t.length;
					for (; o < i; o++) s[t[o]] = h.css(e, t[o], !1, r);
					return s
				}
				return n !== undefined ? h.style(e, t, n) : h.css(e, t)
			}, e, t, arguments.length > 1)
		},
		show: function() {
			return $t(this, !0)
		},
		hide: function() {
			return $t(this)
		},
		toggle: function(e) {
			return typeof e == "boolean" ? e ? this.show() : this.hide() : this.each(function() {
				V(this) ? h(this).show() : h(this).hide()
			})
		}
	}), h.Tween = Gt, Gt.prototype = {
		constructor: Gt,
		init: function(e, t, n, r, i, s) {
			this.elem = e, this.prop = n, this.easing = i || "swing", this.options = t, this.start = this.now = this.cur(), this.end = r, this.unit = s || (h.cssNumber[n] ? "" : "px")
		},
		cur: function() {
			var e = Gt.propHooks[this.prop];
			return e && e.get ? e.get(this) : Gt.propHooks._default.get(this)
		},
		run: function(e) {
			var t, n = Gt.propHooks[this.prop];
			return this.options.duration ? this.pos = t = h.easing[this.easing](e, this.options.duration * e, 0, 1, this.options.duration) : this.pos = t = e, this.now = (this.end - this.start) * t + this.start, this.options.step && this.options.step.call(this.elem, this.now, this), n && n.set ? n.set(this) : Gt.propHooks._default.set(this), this
		}
	}, Gt.prototype.init.prototype = Gt.prototype, Gt.propHooks = {
		_default: {
			get: function(e) {
				var t;
				return e.elem[e.prop] == null || !!e.elem.style && e.elem.style[e.prop] != null ? (t = h.css(e.elem, e.prop, ""), !t || t === "auto" ? 0 : t) : e.elem[e.prop]
			},
			set: function(e) {
				h.fx.step[e.prop] ? h.fx.step[e.prop](e) : e.elem.style && (e.elem.style[h.cssProps[e.prop]] != null || h.cssHooks[e.prop]) ? h.style(e.elem, e.prop, e.now + e.unit) : e.elem[e.prop] = e.now
			}
		}
	}, Gt.propHooks.scrollTop = Gt.propHooks.scrollLeft = {
		set: function(e) {
			e.elem.nodeType && e.elem.parentNode && (e.elem[e.prop] = e.now)
		}
	}, h.easing = {
		linear: function(e) {
			return e
		},
		swing: function(e) {
			return .5 - Math.cos(e * Math.PI) / 2
		}
	}, h.fx = Gt.prototype.init, h.fx.step = {};
	var Yt, Zt, en = /^(?:toggle|show|hide)$/,
		tn = new RegExp("^(?:([+-])=|)(" + W + ")([a-z%]*)$", "i"),
		nn = /queueHooks$/,
		rn = [fn],
		sn = {
			"*": [function(e, t) {
				var n = this.createTween(e, t),
					r = n.cur(),
					i = tn.exec(t),
					s = i && i[3] || (h.cssNumber[e] ? "" : "px"),
					o = (h.cssNumber[e] || s !== "px" && +r) && tn.exec(h.css(n.elem, e)),
					u = 1,
					a = 20;
				if (o && o[3] !== s) {
					s = s || o[3], i = i || [], o = +r || 1;
					do u = u || ".5", o /= u, h.style(n.elem, e, o + s); while (u !== (u = n.cur() / r) && u !== 1 && --a)
				}
				return i && (o = n.start = +o || +r || 0, n.unit = s, n.end = i[1] ? o + (i[1] + 1) * i[2] : +i[2]), n
			}]
		};
	h.Animation = h.extend(cn, {
			tweener: function(e, t) {
				h.isFunction(e) ? (t = e, e = ["*"]) : e = e.split(" ");
				var n, r = 0,
					i = e.length;
				for (; r < i; r++) n = e[r], sn[n] = sn[n] || [], sn[n].unshift(t)
			},
			prefilter: function(e, t) {
				t ? rn.unshift(e) : rn.push(e)
			}
		}), h.speed = function(e, t, n) {
			var r = e && typeof e == "object" ? h.extend({}, e) : {
				complete: n || !n && t || h.isFunction(e) && e,
				duration: e,
				easing: n && t || t && !h.isFunction(t) && t
			};
			r.duration = h.fx.off ? 0 : typeof r.duration == "number" ? r.duration : r.duration in h.fx.speeds ? h.fx.speeds[r.duration] : h.fx.speeds._default;
			if (r.queue == null || r.queue === !0) r.queue = "fx";
			return r.old = r.complete, r.complete = function() {
				h.isFunction(r.old) && r.old.call(this), r.queue && h.dequeue(this, r.queue)
			}, r
		}, h.fn.extend({
			fadeTo: function(e, t, n, r) {
				return this.filter(V).css("opacity", 0).show().end().animate({
					opacity: t
				}, e, n, r)
			},
			animate: function(e, t, n, r) {
				var i = h.isEmptyObject(e),
					s = h.speed(t, n, r),
					o = function() {
						var t = cn(this, h.extend({}, e), s);
						(i || h._data(this, "finish")) && t.stop(!0)
					};
				return o.finish = o, i || s.queue === !1 ? this.each(o) : this.queue(s.queue, o)
			},
			stop: function(e, t, n) {
				var r = function(e) {
					var t = e.stop;
					delete e.stop, t(n)
				};
				return typeof e != "string" && (n = t, t = e, e = undefined), t && e !== !1 && this.queue(e || "fx", []), this.each(function() {
					var t = !0,
						i = e != null && e + "queueHooks",
						s = h.timers,
						o = h._data(this);
					if (i) o[i] && o[i].stop && r(o[i]);
					else
						for (i in o) o[i] && o[i].stop && nn.test(i) && r(o[i]);
					for (i = s.length; i--;) s[i].elem === this && (e == null || s[i].queue === e) && (s[i].anim.stop(n), t = !1, s.splice(i, 1));
					(t || !n) && h.dequeue(this, e)
				})
			},
			finish: function(e) {
				return e !== !1 && (e = e || "fx"), this.each(function() {
					var t, n = h._data(this),
						r = n[e + "queue"],
						i = n[e + "queueHooks"],
						s = h.timers,
						o = r ? r.length : 0;
					n.finish = !0, h.queue(this, e, []), i && i.stop && i.stop.call(this, !0);
					for (t = s.length; t--;) s[t].elem === this && s[t].queue === e && (s[t].anim.stop(!0), s.splice(t, 1));
					for (t = 0; t < o; t++) r[t] && r[t].finish && r[t].finish.call(this);
					delete n.finish
				})
			}
		}), h.each(["toggle", "show", "hide"], function(e, t) {
			var n = h.fn[t];
			h.fn[t] = function(e, r, i) {
				return e == null || typeof e == "boolean" ? n.apply(this, arguments) : this.animate(un(t, !0), e, r, i)
			}
		}), h.each({
			slideDown: un("show"),
			slideUp: un("hide"),
			slideToggle: un("toggle"),
			fadeIn: {
				opacity: "show"
			},
			fadeOut: {
				opacity: "hide"
			},
			fadeToggle: {
				opacity: "toggle"
			}
		}, function(e, t) {
			h.fn[e] = function(e, n, r) {
				return this.animate(t, e, n, r)
			}
		}), h.timers = [], h.fx.tick = function() {
			var e, t = h.timers,
				n = 0;
			Yt = h.now();
			for (; n < t.length; n++) e = t[n], !e() && t[n] === e && t.splice(n--, 1);
			t.length || h.fx.stop(), Yt = undefined
		}, h.fx.timer = function(e) {
			h.timers.push(e), e() ? h.fx.start() : h.timers.pop()
		}, h.fx.interval = 13, h.fx.start = function() {
			Zt || (Zt = setInterval(h.fx.tick, h.fx.interval))
		}, h.fx.stop = function() {
			clearInterval(Zt), Zt = null
		}, h.fx.speeds = {
			slow: 600,
			fast: 200,
			_default: 400
		}, h.fn.delay = function(e, t) {
			return e = h.fx ? h.fx.speeds[e] || e : e, t = t || "fx", this.queue(t, function(t, n) {
				var r = setTimeout(t, e);
				n.stop = function() {
					clearTimeout(r)
				}
			})
		},
		function() {
			var e, t, n, r, i;
			t = T.createElement("div"), t.setAttribute("className", "t"), t.innerHTML = "  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>", r = t.getElementsByTagName("a")[0], n = T.createElement("select"), i = n.appendChild(T.createElement("option")), e = t.getElementsByTagName("input")[0], r.style.cssText = "top:1px", l.getSetAttribute = t.className !== "t", l.style = /top/.test(r.getAttribute("style")), l.hrefNormalized = r.getAttribute("href") === "/a", l.checkOn = !!e.value, l.optSelected = i.selected, l.enctype = !!T.createElement("form").enctype, n.disabled = !0, l.optDisabled = !i.disabled, e = T.createElement("input"), e.setAttribute("value", ""), l.input = e.getAttribute("value") === "", e.value = "t", e.setAttribute("type", "radio"), l.radioValue = e.value === "t"
		}();
	var hn = /\r/g;
	h.fn.extend({
		val: function(e) {
			var t, n, r, i = this[0];
			if (!arguments.length) {
				if (i) return t = h.valHooks[i.type] || h.valHooks[i.nodeName.toLowerCase()], t && "get" in t && (n = t.get(i, "value")) !== undefined ? n : (n = i.value, typeof n == "string" ? n.replace(hn, "") : n == null ? "" : n);
				return
			}
			return r = h.isFunction(e), this.each(function(n) {
				var i;
				if (this.nodeType !== 1) return;
				r ? i = e.call(this, n, h(this).val()) : i = e, i == null ? i = "" : typeof i == "number" ? i += "" : h.isArray(i) && (i = h.map(i, function(e) {
					return e == null ? "" : e + ""
				})), t = h.valHooks[this.type] || h.valHooks[this.nodeName.toLowerCase()];
				if (!t || !("set" in t) || t.set(this, i, "value") === undefined) this.value = i
			})
		}
	}), h.extend({
		valHooks: {
			option: {
				get: function(e) {
					var t = h.find.attr(e, "value");
					return t != null ? t : h.trim(h.text(e))
				}
			},
			select: {
				get: function(e) {
					var t, n, r = e.options,
						i = e.selectedIndex,
						s = e.type === "select-one" || i < 0,
						o = s ? null : [],
						u = s ? i + 1 : r.length,
						a = i < 0 ? u : s ? i : 0;
					for (; a < u; a++) {
						n = r[a];
						if ((n.selected || a === i) && (l.optDisabled ? !n.disabled : n.getAttribute("disabled") === null) && (!n.parentNode.disabled || !h.nodeName(n.parentNode, "optgroup"))) {
							t = h(n).val();
							if (s) return t;
							o.push(t)
						}
					}
					return o
				},
				set: function(e, t) {
					var n, r, i = e.options,
						s = h.makeArray(t),
						o = i.length;
					while (o--) {
						r = i[o];
						if (h.inArray(h.valHooks.option.get(r), s) >= 0) try {
							r.selected = n = !0
						} catch (u) {
							r.scrollHeight
						} else r.selected = !1
					}
					return n || (e.selectedIndex = -1), i
				}
			}
		}
	}), h.each(["radio", "checkbox"], function() {
		h.valHooks[this] = {
			set: function(e, t) {
				if (h.isArray(t)) return e.checked = h.inArray(h(e).val(), t) >= 0
			}
		}, l.checkOn || (h.valHooks[this].get = function(e) {
			return e.getAttribute("value") === null ? "on" : e.value
		})
	});
	var pn, dn, vn = h.expr.attrHandle,
		mn = /^(?:checked|selected)$/i,
		gn = l.getSetAttribute,
		yn = l.input;
	h.fn.extend({
		attr: function(e, t) {
			return $(this, h.attr, e, t, arguments.length > 1)
		},
		removeAttr: function(e) {
			return this.each(function() {
				h.removeAttr(this, e)
			})
		}
	}), h.extend({
		attr: function(e, t, n) {
			var r, i, s = e.nodeType;
			if (!e || s === 3 || s === 8 || s === 2) return;
			if (typeof e.getAttribute === B) return h.prop(e, t, n);
			if (s !== 1 || !h.isXMLDoc(e)) t = t.toLowerCase(), r = h.attrHooks[t] || (h.expr.match.bool.test(t) ? dn : pn);
			if (n === undefined) return r && "get" in r && (i = r.get(e, t)) !== null ? i : (i = h.find.attr(e, t), i == null ? undefined : i);
			if (n !== null) return r && "set" in r && (i = r.set(e, n, t)) !== undefined ? i : (e.setAttribute(t, n + ""), n);
			h.removeAttr(e, t)
		},
		removeAttr: function(e, t) {
			var n, r, i = 0,
				s = t && t.match(O);
			if (s && e.nodeType === 1)
				while (n = s[i++]) r = h.propFix[n] || n, h.expr.match.bool.test(n) ? yn && gn || !mn.test(n) ? e[r] = !1 : e[h.camelCase("default-" + n)] = e[r] = !1 : h.attr(e, n, ""), e.removeAttribute(gn ? n : r)
		},
		attrHooks: {
			type: {
				set: function(e, t) {
					if (!l.radioValue && t === "radio" && h.nodeName(e, "input")) {
						var n = e.value;
						return e.setAttribute("type", t), n && (e.value = n), t
					}
				}
			}
		}
	}), dn = {
		set: function(e, t, n) {
			return t === !1 ? h.removeAttr(e, n) : yn && gn || !mn.test(n) ? e.setAttribute(!gn && h.propFix[n] || n, n) : e[h.camelCase("default-" + n)] = e[n] = !0, n
		}
	}, h.each(h.expr.match.bool.source.match(/\w+/g), function(e, t) {
		var n = vn[t] || h.find.attr;
		vn[t] = yn && gn || !mn.test(t) ? function(e, t, r) {
			var i, s;
			return r || (s = vn[t], vn[t] = i, i = n(e, t, r) != null ? t.toLowerCase() : null, vn[t] = s), i
		} : function(e, t, n) {
			if (!n) return e[h.camelCase("default-" + t)] ? t.toLowerCase() : null
		}
	});
	if (!yn || !gn) h.attrHooks.value = {
		set: function(e, t, n) {
			if (!h.nodeName(e, "input")) return pn && pn.set(e, t, n);
			e.defaultValue = t
		}
	};
	gn || (pn = {
		set: function(e, t, n) {
			var r = e.getAttributeNode(n);
			r || e.setAttributeNode(r = e.ownerDocument.createAttribute(n)), r.value = t += "";
			if (n === "value" || t === e.getAttribute(n)) return t
		}
	}, vn.id = vn.name = vn.coords = function(e, t, n) {
		var r;
		if (!n) return (r = e.getAttributeNode(t)) && r.value !== "" ? r.value : null
	}, h.valHooks.button = {
		get: function(e, t) {
			var n = e.getAttributeNode(t);
			if (n && n.specified) return n.value
		},
		set: pn.set
	}, h.attrHooks.contenteditable = {
		set: function(e, t, n) {
			pn.set(e, t === "" ? !1 : t, n)
		}
	}, h.each(["width", "height"], function(e, t) {
		h.attrHooks[t] = {
			set: function(e, n) {
				if (n === "") return e.setAttribute(t, "auto"), n
			}
		}
	})), l.style || (h.attrHooks.style = {
		get: function(e) {
			return e.style.cssText || undefined
		},
		set: function(e, t) {
			return e.style.cssText = t + ""
		}
	});
	var bn = /^(?:input|select|textarea|button|object)$/i,
		wn = /^(?:a|area)$/i;
	h.fn.extend({
		prop: function(e, t) {
			return $(this, h.prop, e, t, arguments.length > 1)
		},
		removeProp: function(e) {
			return e = h.propFix[e] || e, this.each(function() {
				try {
					this[e] = undefined, delete this[e]
				} catch (t) {}
			})
		}
	}), h.extend({
		propFix: {
			"for": "htmlFor",
			"class": "className"
		},
		prop: function(e, t, n) {
			var r, i, s, o = e.nodeType;
			if (!e || o === 3 || o === 8 || o === 2) return;
			return s = o !== 1 || !h.isXMLDoc(e), s && (t = h.propFix[t] || t, i = h.propHooks[t]), n !== undefined ? i && "set" in i && (r = i.set(e, n, t)) !== undefined ? r : e[t] = n : i && "get" in i && (r = i.get(e, t)) !== null ? r : e[t]
		},
		propHooks: {
			tabIndex: {
				get: function(e) {
					var t = h.find.attr(e, "tabindex");
					return t ? parseInt(t, 10) : bn.test(e.nodeName) || wn.test(e.nodeName) && e.href ? 0 : -1
				}
			}
		}
	}), l.hrefNormalized || h.each(["href", "src"], function(e, t) {
		h.propHooks[t] = {
			get: function(e) {
				return e.getAttribute(t, 4)
			}
		}
	}), l.optSelected || (h.propHooks.selected = {
		get: function(e) {
			var t = e.parentNode;
			return t && (t.selectedIndex, t.parentNode && t.parentNode.selectedIndex), null
		}
	}), h.each(["tabIndex", "readOnly", "maxLength", "cellSpacing", "cellPadding", "rowSpan", "colSpan", "useMap", "frameBorder", "contentEditable"], function() {
		h.propFix[this.toLowerCase()] = this
	}), l.enctype || (h.propFix.enctype = "encoding");
	var En = /[\t\r\n\f]/g;
	h.fn.extend({
		addClass: function(e) {
			var t, n, r, i, s, o, u = 0,
				a = this.length,
				f = typeof e == "string" && e;
			if (h.isFunction(e)) return this.each(function(t) {
				h(this).addClass(e.call(this, t, this.className))
			});
			if (f) {
				t = (e || "").match(O) || [];
				for (; u < a; u++) {
					n = this[u], r = n.nodeType === 1 && (n.className ? (" " + n.className + " ").replace(En, " ") : " ");
					if (r) {
						s = 0;
						while (i = t[s++]) r.indexOf(" " + i + " ") < 0 && (r += i + " ");
						o = h.trim(r), n.className !== o && (n.className = o)
					}
				}
			}
			return this
		},
		removeClass: function(e) {
			var t, n, r, i, s, o, u = 0,
				a = this.length,
				f = arguments.length === 0 || typeof e == "string" && e;
			if (h.isFunction(e)) return this.each(function(t) {
				h(this).removeClass(e.call(this, t, this.className))
			});
			if (f) {
				t = (e || "").match(O) || [];
				for (; u < a; u++) {
					n = this[u], r = n.nodeType === 1 && (n.className ? (" " + n.className + " ").replace(En, " ") : "");
					if (r) {
						s = 0;
						while (i = t[s++])
							while (r.indexOf(" " + i + " ") >= 0) r = r.replace(" " + i + " ", " ");
						o = e ? h.trim(r) : "", n.className !== o && (n.className = o)
					}
				}
			}
			return this
		},
		toggleClass: function(e, t) {
			var n = typeof e;
			return typeof t == "boolean" && n === "string" ? t ? this.addClass(e) : this.removeClass(e) : h.isFunction(e) ? this.each(function(n) {
				h(this).toggleClass(e.call(this, n, this.className, t), t)
			}) : this.each(function() {
				if (n === "string") {
					var t, r = 0,
						i = h(this),
						s = e.match(O) || [];
					while (t = s[r++]) i.hasClass(t) ? i.removeClass(t) : i.addClass(t)
				} else if (n === B || n === "boolean") this.className && h._data(this, "__className__", this.className), this.className = this.className || e === !1 ? "" : h._data(this, "__className__") || ""
			})
		},
		hasClass: function(e) {
			var t = " " + e + " ",
				n = 0,
				r = this.length;
			for (; n < r; n++)
				if (this[n].nodeType === 1 && (" " + this[n].className + " ").replace(En, " ").indexOf(t) >= 0) return !0;
			return !1
		}
	}), h.each("blur focus focusin focusout load resize scroll unload click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup error contextmenu".split(" "), function(e, t) {
		h.fn[t] = function(e, n) {
			return arguments.length > 0 ? this.on(t, null, e, n) : this.trigger(t)
		}
	}), h.fn.extend({
		hover: function(e, t) {
			return this.mouseenter(e).mouseleave(t || e)
		},
		bind: function(e, t, n) {
			return this.on(e, null, t, n)
		},
		unbind: function(e, t) {
			return this.off(e, null, t)
		},
		delegate: function(e, t, n, r) {
			return this.on(t, e, n, r)
		},
		undelegate: function(e, t, n) {
			return arguments.length === 1 ? this.off(e, "**") : this.off(t, e || "**", n)
		}
	});
	var Sn = h.now(),
		xn = /\?/,
		Tn = /(,)|(\[|{)|(}|])|"(?:[^"\\\r\n]|\\["\\\/bfnrt]|\\u[\da-fA-F]{4})*"\s*:?|true|false|null|-?(?!0\d)\d+(?:\.\d+|)(?:[eE][+-]?\d+|)/g;
	h.parseJSON = function(t) {
		if (e.JSON && e.JSON.parse) return e.JSON.parse(t + "");
		var n, r = null,
			i = h.trim(t + "");
		return i && !h.trim(i.replace(Tn, function(e, t, i, s) {
			return n && t && (r = 0), r === 0 ? e : (n = i || t, r += !s - !i, "")
		})) ? Function("return " + i)() : h.error("Invalid JSON: " + t)
	}, h.parseXML = function(t) {
		var n, r;
		if (!t || typeof t != "string") return null;
		try {
			e.DOMParser ? (r = new DOMParser, n = r.parseFromString(t, "text/xml")) : (n = new ActiveXObject("Microsoft.XMLDOM"), n.async = "false", n.loadXML(t))
		} catch (i) {
			n = undefined
		}
		return (!n || !n.documentElement || n.getElementsByTagName("parsererror").length) && h.error("Invalid XML: " + t), n
	};
	var Nn, Cn, kn = /#.*$/,
		Ln = /([?&])_=[^&]*/,
		An = /^(.*?):[ \t]*([^\r\n]*)\r?$/mg,
		On = /^(?:about|app|app-storage|.+-extension|file|res|widget):$/,
		Mn = /^(?:GET|HEAD)$/,
		_n = /^\/\//,
		Dn = /^([\w.+-]+:)(?:\/\/(?:[^\/?#]*@|)([^\/?#:]*)(?::(\d+)|)|)/,
		Pn = {},
		Hn = {},
		Bn = "*/".concat("*");
	try {
		Cn = location.href
	} catch (jn) {
		Cn = T.createElement("a"), Cn.href = "", Cn = Cn.href
	}
	Nn = Dn.exec(Cn.toLowerCase()) || [], h.extend({
		active: 0,
		lastModified: {},
		etag: {},
		ajaxSettings: {
			url: Cn,
			type: "GET",
			isLocal: On.test(Nn[1]),
			global: !0,
			processData: !0,
			async: !0,
			contentType: "application/x-www-form-urlencoded; charset=UTF-8",
			accepts: {
				"*": Bn,
				text: "text/plain",
				html: "text/html",
				xml: "application/xml, text/xml",
				json: "application/json, text/javascript"
			},
			contents: {
				xml: /xml/,
				html: /html/,
				json: /json/
			},
			responseFields: {
				xml: "responseXML",
				text: "responseText",
				json: "responseJSON"
			},
			converters: {
				"* text": String,
				"text html": !0,
				"text json": h.parseJSON,
				"text xml": h.parseXML
			},
			flatOptions: {
				url: !0,
				betContext: !0
			}
		},
		ajaxSetup: function(e, t) {
			return t ? qn(qn(e, h.ajaxSettings), t) : qn(h.ajaxSettings, e)
		},
		ajaxPrefilter: Fn(Pn),
		ajaxTransport: Fn(Hn),
		ajax: function(e, t) {
			function x(e, t, n, r) {
				var f, g, y, w, S, x = t;
				if (b === 2) return;
				b = 2, o && clearTimeout(o), a = undefined, s = r || "", E.readyState = e > 0 ? 4 : 0, f = e >= 200 && e < 300 || e === 304, n && (w = Rn(l, E, n)), w = Un(l, w, E, f);
				if (f) l.ifModified && (S = E.getResponseHeader("Last-Modified"), S && (h.lastModified[i] = S), S = E.getResponseHeader("etag"), S && (h.etag[i] = S)), e === 204 || l.type === "HEAD" ? x = "nocontent" : e === 304 ? x = "notmodified" : (x = w.state, g = w.data, y = w.error, f = !y);
				else {
					y = x;
					if (e || !x) x = "error", e < 0 && (e = 0)
				}
				E.status = e, E.statusText = (t || x) + "", f ? d.resolveWith(c, [g, x, E]) : d.rejectWith(c, [E, x, y]), E.statusCode(m), m = undefined, u && p.trigger(f ? "ajaxSuccess" : "ajaxError", [E, l, f ? g : y]), v.fireWith(c, [E, x]), u && (p.trigger("ajaxComplete", [E, l]), --h.active || h.event.trigger("ajaxStop"))
			}
			typeof e == "object" && (t = e, e = undefined), t = t || {};
			var n, r, i, s, o, u, a, f, l = h.ajaxSetup({}, t),
				c = l.betContext || l,
				p = l.betContext && (c.nodeType || c.jquery) ? h(c) : h.event,
				d = h.Deferred(),
				v = h.Callbacks("once memory"),
				m = l.statusCode || {},
				g = {},
				y = {},
				b = 0,
				w = "canceled",
				E = {
					readyState: 0,
					getResponseHeader: function(e) {
						var t;
						if (b === 2) {
							if (!f) {
								f = {};
								while (t = An.exec(s)) f[t[1].toLowerCase()] = t[2]
							}
							t = f[e.toLowerCase()]
						}
						return t == null ? null : t
					},
					getAllResponseHeaders: function() {
						return b === 2 ? s : null
					},
					setRequestHeader: function(e, t) {
						var n = e.toLowerCase();
						return b || (e = y[n] = y[n] || e, g[e] = t), this
					},
					overrideMimeType: function(e) {
						return b || (l.mimeType = e), this
					},
					statusCode: function(e) {
						var t;
						if (e)
							if (b < 2)
								for (t in e) m[t] = [m[t], e[t]];
							else E.always(e[E.status]);
						return this
					},
					abort: function(e) {
						var t = e || w;
						return a && a.abort(t), x(0, t), this
					}
				};
			d.promise(E).complete = v.add, E.success = E.done, E.error = E.fail, l.url = ((e || l.url || Cn) + "").replace(kn, "").replace(_n, Nn[1] + "//"), l.type = t.method || t.type || l.method || l.type, l.dataTypes = h.trim(l.dataType || "*").toLowerCase().match(O) || [""], l.crossDomain == null && (n = Dn.exec(l.url.toLowerCase()), l.crossDomain = !(!n || n[1] === Nn[1] && n[2] === Nn[2] && (n[3] || (n[1] === "http:" ? "80" : "443")) === (Nn[3] || (Nn[1] === "http:" ? "80" : "443")))), l.data && l.processData && typeof l.data != "string" && (l.data = h.param(l.data, l.traditional)), In(Pn, l, t, E);
			if (b === 2) return E;
			u = l.global, u && h.active++ === 0 && h.event.trigger("ajaxStart"), l.type = l.type.toUpperCase(), l.hasContent = !Mn.test(l.type), i = l.url, l.hasContent || (l.data && (i = l.url += (xn.test(i) ? "&" : "?") + l.data, delete l.data), l.cache === !1 && (l.url = Ln.test(i) ? i.replace(Ln, "$1_=" + Sn++) : i + (xn.test(i) ? "&" : "?") + "_=" + Sn++)), l.ifModified && (h.lastModified[i] && E.setRequestHeader("If-Modified-Since", h.lastModified[i]), h.etag[i] && E.setRequestHeader("If-None-Match", h.etag[i])), (l.data && l.hasContent && l.contentType !== !1 || t.contentType) && E.setRequestHeader("Content-Type", l.contentType), E.setRequestHeader("Accept", l.dataTypes[0] && l.accepts[l.dataTypes[0]] ? l.accepts[l.dataTypes[0]] + (l.dataTypes[0] !== "*" ? ", " + Bn + "; q=0.01" : "") : l.accepts["*"]);
			for (r in l.headers) E.setRequestHeader(r, l.headers[r]);
			if (!l.beforeSend || l.beforeSend.call(c, E, l) !== !1 && b !== 2) {
				w = "abort";
				for (r in {
						success: 1,
						error: 1,
						complete: 1
					}) E[r](l[r]);
				a = In(Hn, l, t, E);
				if (!a) x(-1, "No Transport");
				else {
					E.readyState = 1, u && p.trigger("ajaxSend", [E, l]), l.async && l.timeout > 0 && (o = setTimeout(function() {
						E.abort("timeout")
					}, l.timeout));
					try {
						b = 1, a.send(g, x)
					} catch (S) {
						if (!(b < 2)) throw S;
						x(-1, S)
					}
				}
				return E
			}
			return E.abort()
		},
		getJSON: function(e, t, n) {
			return h.get(e, t, n, "json")
		},
		getScript: function(e, t) {
			return h.get(e, undefined, t, "script")
		}
	}), h.each(["get", "post"], function(e, t) {
		h[t] = function(e, n, r, i) {
			return h.isFunction(n) && (i = i || r, r = n, n = undefined), h.ajax({
				url: e,
				type: t,
				dataType: i,
				data: n,
				success: r
			})
		}
	}), h.each(["ajaxStart", "ajaxStop", "ajaxComplete", "ajaxError", "ajaxSuccess", "ajaxSend"], function(e, t) {
		h.fn[t] = function(e) {
			return this.on(t, e)
		}
	}), h._evalUrl = function(e) {
		return h.ajax({
			url: e,
			type: "GET",
			dataType: "script",
			async: !1,
			global: !1,
			"throws": !0
		})
	}, h.fn.extend({
		wrapAll: function(e) {
			if (h.isFunction(e)) return this.each(function(t) {
				h(this).wrapAll(e.call(this, t))
			});
			if (this[0]) {
				var t = h(e, this[0].ownerDocument).eq(0).clone(!0);
				this[0].parentNode && t.insertBefore(this[0]), t.map(function() {
					var e = this;
					while (e.firstChild && e.firstChild.nodeType === 1) e = e.firstChild;
					return e
				}).append(this)
			}
			return this
		},
		wrapInner: function(e) {
			return h.isFunction(e) ? this.each(function(t) {
				h(this).wrapInner(e.call(this, t))
			}) : this.each(function() {
				var t = h(this),
					n = t.contents();
				n.length ? n.wrapAll(e) : t.append(e)
			})
		},
		wrap: function(e) {
			var t = h.isFunction(e);
			return this.each(function(n) {
				h(this).wrapAll(t ? e.call(this, n) : e)
			})
		},
		unwrap: function() {
			return this.parent().each(function() {
				h.nodeName(this, "body") || h(this).replaceWith(this.childNodes)
			}).end()
		}
	}), h.expr.filters.hidden = function(e) {
		return e.offsetWidth <= 0 && e.offsetHeight <= 0 || !l.reliableHiddenOffsets() && (e.style && e.style.display || h.css(e, "display")) === "none"
	}, h.expr.filters.visible = function(e) {
		return !h.expr.filters.hidden(e)
	};
	var zn = /%20/g,
		Wn = /\[\]$/,
		Xn = /\r?\n/g,
		Vn = /^(?:submit|button|image|reset|file)$/i,
		$n = /^(?:input|select|textarea|keygen)/i;
	h.param = function(e, t) {
		var n, r = [],
			i = function(e, t) {
				t = h.isFunction(t) ? t() : t == null ? "" : t, r[r.length] = encodeURIComponent(e) + "=" + encodeURIComponent(t)
			};
		t === undefined && (t = h.ajaxSettings && h.ajaxSettings.traditional);
		if (h.isArray(e) || e.jquery && !h.isPlainObject(e)) h.each(e, function() {
			i(this.name, this.value)
		});
		else
			for (n in e) Jn(n, e[n], t, i);
		return r.join("&").replace(zn, "+")
	}, h.fn.extend({
		serialize: function() {
			return h.param(this.serializeArray())
		},
		serializeArray: function() {
			return this.map(function() {
				var e = h.prop(this, "elements");
				return e ? h.makeArray(e) : this
			}).filter(function() {
				var e = this.type;
				return this.name && !h(this).is(":disabled") && $n.test(this.nodeName) && !Vn.test(e) && (this.checked || !J.test(e))
			}).map(function(e, t) {
				var n = h(this).val();
				return n == null ? null : h.isArray(n) ? h.map(n, function(e) {
					return {
						name: t.name,
						value: e.replace(Xn, "\r\n")
					}
				}) : {
					name: t.name,
					value: n.replace(Xn, "\r\n")
				}
			}).get()
		}
	}), h.ajaxSettings.xhr = e.ActiveXObject !== undefined ? function() {
		return !this.isLocal && /^(get|post|head|put|delete|options)$/i.test(this.type) && Yn() || Zn()
	} : Yn;
	var Kn = 0,
		Qn = {},
		Gn = h.ajaxSettings.xhr();
	e.ActiveXObject && h(e).on("unload", function() {
		for (var e in Qn) Qn[e](undefined, !0)
	}), l.cors = !!Gn && "withCredentials" in Gn, Gn = l.ajax = !!Gn, Gn && h.ajaxTransport(function(e) {
		if (!e.crossDomain || l.cors) {
			var t;
			return {
				send: function(n, r) {
					var i, s = e.xhr(),
						o = ++Kn;
					s.open(e.type, e.url, e.async, e.username, e.password);
					if (e.xhrFields)
						for (i in e.xhrFields) s[i] = e.xhrFields[i];
					e.mimeType && s.overrideMimeType && s.overrideMimeType(e.mimeType), !e.crossDomain && !n["X-Requested-With"] && (n["X-Requested-With"] = "XMLHttpRequest");
					for (i in n) n[i] !== undefined && s.setRequestHeader(i, n[i] + "");
					s.send(e.hasContent && e.data || null), t = function(n, i) {
						var u, a, f;
						if (t && (i || s.readyState === 4)) {
							delete Qn[o], t = undefined, s.onreadystatechange = h.noop;
							if (i) s.readyState !== 4 && s.abort();
							else {
								f = {}, u = s.status, typeof s.responseText == "string" && (f.text = s.responseText);
								try {
									a = s.statusText
								} catch (l) {
									a = ""
								}!u && e.isLocal && !e.crossDomain ? u = f.text ? 200 : 404 : u === 1223 && (u = 204)
							}
						}
						f && r(u, a, f, s.getAllResponseHeaders())
					}, e.async ? s.readyState === 4 ? setTimeout(t) : s.onreadystatechange = Qn[o] = t : t()
				},
				abort: function() {
					t && t(undefined, !0)
				}
			}
		}
	}), h.ajaxSetup({
		accepts: {
			script: "text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"
		},
		contents: {
			script: /(?:java|ecma)script/
		},
		converters: {
			"text script": function(e) {
				return h.globalEval(e), e
			}
		}
	}), h.ajaxPrefilter("script", function(e) {
		e.cache === undefined && (e.cache = !1), e.crossDomain && (e.type = "GET", e.global = !1)
	}), h.ajaxTransport("script", function(e) {
		if (e.crossDomain) {
			var t, n = T.head || h("head")[0] || T.documentElement;
			return {
				send: function(r, i) {
					t = T.createElement("script"), t.async = !0, e.scriptCharset && (t.charset = e.scriptCharset), t.src = e.url, t.onload = t.onreadystatechange = function(e, n) {
						if (n || !t.readyState || /loaded|complete/.test(t.readyState)) t.onload = t.onreadystatechange = null, t.parentNode && t.parentNode.removeChild(t), t = null, n || i(200, "success")
					}, n.insertBefore(t, n.firstChild)
				},
				abort: function() {
					t && t.onload(undefined, !0)
				}
			}
		}
	});
	var er = [],
		tr = /(=)\?(?=&|$)|\?\?/;
	h.ajaxSetup({
		jsonp: "callback",
		jsonpCallback: function() {
			var e = er.pop() || h.expando + "_" + Sn++;
			return this[e] = !0, e
		}
	}), h.ajaxPrefilter("json jsonp", function(t, n, r) {
		var i, s, o, u = t.jsonp !== !1 && (tr.test(t.url) ? "url" : typeof t.data == "string" && !(t.contentType || "").indexOf("application/x-www-form-urlencoded") && tr.test(t.data) && "data");
		if (u || t.dataTypes[0] === "jsonp") return i = t.jsonpCallback = h.isFunction(t.jsonpCallback) ? t.jsonpCallback() : t.jsonpCallback, u ? t[u] = t[u].replace(tr, "$1" + i) : t.jsonp !== !1 && (t.url += (xn.test(t.url) ? "&" : "?") + t.jsonp + "=" + i), t.converters["script json"] = function() {
			return o || h.error(i + " was not called"), o[0]
		}, t.dataTypes[0] = "json", s = e[i], e[i] = function() {
			o = arguments
		}, r.always(function() {
			e[i] = s, t[i] && (t.jsonpCallback = n.jsonpCallback, er.push(i)), o && h.isFunction(s) && s(o[0]), o = s = undefined
		}), "script"
	}), h.parseHTML = function(e, t, n) {
		if (!e || typeof e != "string") return null;
		typeof t == "boolean" && (n = t, t = !1), t = t || T;
		var r = w.exec(e),
			i = !n && [];
		return r ? [t.createElement(r[1])] : (r = h.buildFragment([e], t, i), i && i.length && h(i).remove(), h.merge([], r.childNodes))
	};
	var nr = h.fn.load;
	h.fn.load = function(e, t, n) {
		if (typeof e != "string" && nr) return nr.apply(this, arguments);
		var r, i, s, o = this,
			u = e.indexOf(" ");
		return u >= 0 && (r = h.trim(e.slice(u, e.length)), e = e.slice(0, u)), h.isFunction(t) ? (n = t, t = undefined) : t && typeof t == "object" && (s = "POST"), o.length > 0 && h.ajax({
			url: e,
			type: s,
			dataType: "html",
			data: t
		}).done(function(e) {
			i = arguments, o.html(r ? h("<div>").append(h.parseHTML(e)).find(r) : e)
		}).complete(n && function(e, t) {
			o.each(n, i || [e.responseText, t, e])
		}), this
	}, h.expr.filters.animated = function(e) {
		return h.grep(h.timers, function(t) {
			return e === t.elem
		}).length
	};
	var rr = e.document.documentElement;
	h.offset = {
		setOffset: function(e, t, n) {
			var r, i, s, o, u, a, f, l = h.css(e, "position"),
				c = h(e),
				p = {};
			l === "static" && (e.style.position = "relative"), u = c.offset(), s = h.css(e, "top"), a = h.css(e, "left"), f = (l === "absolute" || l === "fixed") && h.inArray("auto", [s, a]) > -1, f ? (r = c.position(), o = r.top, i = r.left) : (o = parseFloat(s) || 0, i = parseFloat(a) || 0), h.isFunction(t) && (t = t.call(e, n, u)), t.top != null && (p.top = t.top - u.top + o), t.left != null && (p.left = t.left - u.left + i), "using" in t ? t.using.call(e, p) : c.css(p)
		}
	}, h.fn.extend({
		offset: function(e) {
			if (arguments.length) return e === undefined ? this : this.each(function(t) {
				h.offset.setOffset(this, e, t)
			});
			var t, n, r = {
					top: 0,
					left: 0
				},
				i = this[0],
				s = i && i.ownerDocument;
			if (!s) return;
			return t = s.documentElement, h.contains(t, i) ? (typeof i.getBoundingClientRect !== B && (r = i.getBoundingClientRect()), n = ir(s), {
				top: r.top + (n.pageYOffset || t.scrollTop) - (t.clientTop || 0),
				left: r.left + (n.pageXOffset || t.scrollLeft) - (t.clientLeft || 0)
			}) : r
		},
		position: function() {
			if (!this[0]) return;
			var e, t, n = {
					top: 0,
					left: 0
				},
				r = this[0];
			return h.css(r, "position") === "fixed" ? t = r.getBoundingClientRect() : (e = this.offsetParent(), t = this.offset(), h.nodeName(e[0], "html") || (n = e.offset()), n.top += h.css(e[0], "borderTopWidth", !0), n.left += h.css(e[0], "borderLeftWidth", !0)), {
				top: t.top - n.top - h.css(r, "marginTop", !0),
				left: t.left - n.left - h.css(r, "marginLeft", !0)
			}
		},
		offsetParent: function() {
			return this.map(function() {
				var e = this.offsetParent || rr;
				while (e && !h.nodeName(e, "html") && h.css(e, "position") === "static") e = e.offsetParent;
				return e || rr
			})
		}
	}), h.each({
		scrollLeft: "pageXOffset",
		scrollTop: "pageYOffset"
	}, function(e, t) {
		var n = /Y/.test(t);
		h.fn[e] = function(r) {
			return $(this, function(e, r, i) {
				var s = ir(e);
				if (i === undefined) return s ? t in s ? s[t] : s.document.documentElement[r] : e[r];
				s ? s.scrollTo(n ? h(s).scrollLeft() : i, n ? i : h(s).scrollTop()) : e[r] = i
			}, e, r, arguments.length, null)
		}
	}), h.each(["top", "left"], function(e, t) {
		h.cssHooks[t] = jt(l.pixelPosition, function(e, n) {
			if (n) return n = Ht(e, t), Dt.test(n) ? h(e).position()[t] + "px" : n
		})
	}), h.each({
		Height: "height",
		Width: "width"
	}, function(e, t) {
		h.each({
			padding: "inner" + e,
			content: t,
			"": "outer" + e
		}, function(n, r) {
			h.fn[r] = function(r, i) {
				var s = arguments.length && (n || typeof r != "boolean"),
					o = n || (r === !0 || i === !0 ? "margin" : "border");
				return $(this, function(t, n, r) {
					var i;
					return h.isWindow(t) ? t.document.documentElement["client" + e] : t.nodeType === 9 ? (i = t.documentElement, Math.max(t.body["scroll" + e], i["scroll" + e], t.body["offset" + e], i["offset" + e], i["client" + e])) : r === undefined ? h.css(t, n, o) : h.style(t, n, r, o)
				}, t, s ? r : undefined, s, null)
			}
		})
	}), h.fn.size = function() {
		return this.length
	}, h.fn.andSelf = h.fn.addBack, typeof define == "function" && define.amd && define("jquery", [], function() {
		return h
	});
	var sr = e.jQuery,
		or = e.$;
	return h.noConflict = function(t) {
		return e.$ === h && (e.$ = or), t && e.jQuery === h && (e.jQuery = sr), h
	}, typeof t === B && (e.jQuery = e.$ = h), h
});