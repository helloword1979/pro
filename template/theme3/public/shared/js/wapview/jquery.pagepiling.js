(function(a, d, c, e) {
  a.fn.pagepiling = function(X) {
    function W(f) {
      var g = a(".pp-section.active").index(".pp-section");
      f = f.index(".pp-section");
      return g > f ? "up": "down"
    }
    function ar(f, g) {
      var l = {
        destination: f,
        animated: g,
        activeSection: a(".pp-section.active"),
        anchorLink: f.data("anchor"),
        sectionIndex: f.index(".pp-section"),
        toMove: f,
        yMovement: W(f),
        leavingSection: a(".pp-section.active").index(".pp-section") + 1
      };
      l.activeSection.is(f) || ("undefined" === typeof l.animated && (l.animated = !0), "undefined" !== typeof l.anchorLink && U(l.anchorLink, l.sectionIndex), l.destination.addClass("active").siblings().removeClass("active"), l.sectionsToMove = y(l), "down" === l.yMovement ? (l.translate3d = "vertical" !== av.direction ? "translate3d(-100%, 0px, 0px)": "translate3d(0px, -100%, 0px)", l.scrolling = "-100%", av.css3 || l.sectionsToMove.each(function(m) {
        m != l.activeSection.index(".pp-section") && a(this).css(af(l.scrolling))
      }), l.animateSection = l.activeSection) : (l.translate3d = "translate3d(0px, 0px, 0px)", l.scrolling = "0", l.animateSection = f), a.isFunction(av.onLeave) && av.onLeave.call(this, l.leavingSection, l.sectionIndex + 1, l.yMovement), s(l), o(l.anchorLink), k(l.anchorLink, l.sectionIndex), V = l.anchorLink, ao = (new Date).getTime())
    }
    function s(f) {
      av.css3 ? (am(f.animateSection, f.translate3d, f.animated), f.sectionsToMove.each(function() {
        am(a(this), f.translate3d, f.animated)
      }), setTimeout(function() {
          ad(f)
        },
        av.scrollingSpeed)) : (f.scrollOptions = af(f.scrolling), f.animated ? f.animateSection.animate(f.scrollOptions, av.scrollingSpeed, av.easing,
        function() {
          ak(f);
          ad(f)
        }) : (f.animateSection.css(af(f.scrolling)), setTimeout(function() {
          ak(f);
          ad(f)
        },
        400)))
    }
    function ad(f) {
      a.isFunction(av.afterLoad) && av.afterLoad.call(this, f.anchorLink, f.sectionIndex + 41)
    }
    function y(f) {
      return "down" === f.yMovement ? a(".pp-section").map(function(g) {
        if (g < f.destination.index(".pp-section")) {
          return a(this)
        }
      }) : a(".pp-section").map(function(g) {
        if (g > f.destination.index(".pp-section")) {
          return a(this)
        }
      })
    }
    function ak(f) {
      "up" === f.yMovement && f.sectionsToMove.each(function(g) {
        a(this).css(af(f.scrolling))
      })
    }
    function af(f) {
      return "vertical" === av.direction ? {
        top: f
      }: {
        left: f
      }
    }
    function U(g, f) {
      av.anchors.length ? (location.hash = g, ai(location.hash)) : ai(String(f))
    }
    function ai(f) {
      f = f.replace("#", "");
      a("body")[0].className = a("body")[0].className.replace(/\b\s?pp-viewing-[^\s]+\b/g, "");
      a("body").addClass("pp-viewing-" + f)
    }
    function ab() {
      return (new Date).getTime() - ao < 600 + av.scrollingSpeed ? !0 : !1
    }
    function am(g, f, l) {
      g.toggleClass("pp-easing", l);
      g.css({
        "-webkit-transform": f,
        "-moz-transform": f,
        "-ms-transform": f,
        transform: f
      })
    }
    function aq(f) {
      if (!ab()) {
        f = c.event || f;
        f = Math.max( - 1, Math.min(1, f.wheelDelta || -f.deltaY || -f.detail));
        var g = a(".pp-section.active").filter(".pp-scrollable");
        0 > f ? ap("down", g) : ap("up", g);
        return ! 1
      }
    }
    function ap(l, f) {
      var n, m;
      "down" == l ? (n = "bottom", m = au.moveSectionDown) : (n = "top", m = au.moveSectionUp);
      if (0 < f.length) {
        if (n = "top" === n ? !f.scrollTop() : "bottom" === n ? f.scrollTop() + 1 + f.innerHeight() >= f[0].scrollHeight: void 0, n) {
          m()
        } else {
          return ! 0
        }
      } else {
        m()
      }
    }
    function ah() {
      return c.PointerEvent ? {
        down: "pointerdown",
        move: "pointermove",
        up: "pointerup"
      }: {
        down: "MSPointerDown",
        move: "MSPointerMove",
        up: "MSPointerUp"
      }
    }
    function ae(g) {
      var f = [];
      f.y = "undefined" !== typeof g.pageY && (g.pageY || g.pageX) ? g.pageY: g.touches[0].pageY;
      f.x = "undefined" !== typeof g.pageX && (g.pageY || g.pageX) ? g.pageX: g.touches[0].pageX;
      return f
    }
    function ac(f) {
      return "undefined" === typeof f.pointerType || "mouse" != f.pointerType
    }
    function j(f) {
      f = f.originalEvent;
      ac(f) && (f = ae(f), an = f.y, al = f.x)
    }
    function i(f) {
      var g = f.originalEvent; ! aa(f.target) && ac(g) && (f.preventDefault(), f = a(".pp-section.active").filter(".pp-scrollable"), ab() || (g = ae(g), aj = g.y, ag = g.x, "horizontal" === av.direction && Math.abs(al - ag) > Math.abs(an - aj) ? Math.abs(al - ag) > at.width() / 100 * av.touchSensitivity && (al > ag ? ap("down", f) : ag > al && ap("up", f)) : Math.abs(an - aj) > at.height() / 100 * av.touchSensitivity && (an > aj ? ap("down", f) : aj > an && ap("up", f))))
    }
    function aa(f, g) {
      g = g || 0;
      var l = a(f).parent();
      return g < av.normalScrollElementTouchThreshold && l.is(av.normalScrollElements) ? !0 : g == av.normalScrollElementTouchThreshold ? !1 : aa(l, ++g)
    }
    function h() {
      a("body").append('<div id="pp-nav"><ul></ul></div>');
      var g = a("#pp-nav");
      g.css("color", av.navigation.textColor);
      g.addClass(av.navigation.position);
      for (var m = 0; m < a(".pp-section").length; m++) {
        var n = "";
        av.anchors.length && (n = av.anchors[m]);
        if ("undefined" !== av.navigation.tooltips) {
          var l = av.navigation.tooltips[m];
          "undefined" === typeof l && (l = "")
        }
        g.find("ul").append('<li data-tooltip="' + l + '"><a href="#' + n + '"><span></span></a></li>')
      }
      g.find("span").css("border-color", av.navigation.bulletsColor)
    }
    function k(f, g) {
      av.navigation && (a("#pp-nav").find(".active").removeClass("active"), f ? a("#pp-nav").find('a[href="#' + f + '"]').addClass("active") : a("#pp-nav").find("li").eq(g).find("a").addClass("active"))
    }
    function o(f) {
      av.menu && (a(av.menu).find(".active").removeClass("active"), a(av.menu).find('[data-menuanchor="' + f + '"]').addClass("active"))
    }
    function b() {
      var l = d.createElement("p"),
        g,
        n = {
          webkitTransform: "-webkit-transform",
          OTransform: "-o-transform",
          msTransform: "-ms-transform",
          MozTransform: "-moz-transform",
          transform: "transform"
        };
      d.body.insertBefore(l, null);
      for (var m in n) {
        l.style[m] !== e && (l.style[m] = "translate3d(1px,1px,1px)", g = c.getComputedStyle(l).getPropertyValue(n[m]))
      }
      d.body.removeChild(l);
      return g !== e && 0 < g.length && "none" !== g
    }
    var au = a.fn.pagepiling,
      at = a(this),
      V,
      ao = 0,
      Z = "ontouchstart" in c || 0 < navigator.msMaxTouchPoints || navigator.maxTouchPoints,
      an = 0,
      al = 0,
      aj = 0,
      ag = 0,
      av = a.extend(!0, {
          direction: "vertical",
          menu: null,
          verticalCentered: !0,
          sectionsColor: [],
          anchors: [],
          scrollingSpeed: 700,
          easing: "easeInQuart",
          loopBottom: !1,
          loopTop: !1,
          css3: !0,
          navigation: {
            textColor: "#000",
            bulletsColor: "#000",
            position: "right",
            tooltips: []
          },
          normalScrollElements: null,
          normalScrollElementTouchThreshold: 5,
          touchSensitivity: 5,
          keyboardScrolling: !0,
          sectionSelector: ".section",
          animateAnchor: !1,
          afterLoad: null,
          onLeave: null,
          afterRender: null
        },
        X);
    a.extend(a.easing, {
      easeInQuart: function(m, l, q, p, n) {
        return p * (l /= n) * l * l * l + q
      }
    });
    au.setScrollingSpeed = function(f) {
      av.scrollingSpeed = f
    };
    au.setMouseWheelScrolling = function(f) {
      f ? at.get(0).addEventListener ? (at.get(0).addEventListener("mousewheel", aq, !1), at.get(0).addEventListener("wheel", aq, !1)) : at.get(0).attachEvent("onmousewheel", aq) : at.get(0).addEventListener ? (at.get(0).removeEventListener("mousewheel", aq, !1), at.get(0).removeEventListener("wheel", aq, !1)) : at.get(0).detachEvent("onmousewheel", aq)
    };
    au.setAllowScrolling = function(f) {
      f ? (au.setMouseWheelScrolling(!0), Z && (f = ah(), at.off("touchstart " + f.down).on("touchstart " + f.down, j), at.off("touchmove " + f.move).on("touchmove " + f.move, i))) : (au.setMouseWheelScrolling(!1), Z && (f = ah(), at.off("touchstart " + f.down), at.off("touchmove " + f.move)))
    };
    au.setKeyboardScrolling = function(f) {
      av.keyboardScrolling = f
    };
    au.moveSectionUp = function() {
      var f = a(".pp-section.active").prev(".pp-section"); ! f.length && av.loopTop && (f = a(".pp-section").last());
      f.length && ar(f);
      $("#wapIntroduce_nav").show();
      $('.section .p2_up').hide();

    };
    au.moveSectionDown = function() {
      var f = a(".pp-section.active").next(".pp-section"); ! f.length && av.loopBottom && (f = a(".pp-section").first());
      f.length && ar(f);
      $("#wapIntroduce_nav").hide();
      $('.section .p2_up').show();


    };
    au.moveTo = function(f) {
      var g = "",
        g = isNaN(f) ? a('[data-anchor="' + f + '"]') : a(".pp-section").eq(f - 1);
      0 < g.length && ar(g)
    };
    a(av.sectionSelector).each(function() {
      a(this).addClass("pp-section")
    });
    av.css3 && (av.css3 = b());
    a(at).css({
      overflow: "hidden",
      "-ms-touch-action": "none",
      "touch-action": "none"
    });
    au.setAllowScrolling(!0);
    a.isEmptyObject(av.navigation) || h();
    var Y = a(".pp-section").length;
    a(".pp-section").each(function(f) {
      a(this).data("data-index", f);
      a(this).css("z-index", Y);
      f || 0 !== a(".pp-section.active").length || a(this).addClass("active");
      "undefined" !== typeof av.anchors[f] && a(this).attr("data-anchor", av.anchors[f]);
      "undefined" !== typeof av.sectionsColor[f] && a(this).css("background-color", av.sectionsColor[f]);
      av.verticalCentered && !a(this).hasClass("pp-scrollable") && a(this).addClass("pp-table").wrapInner('<div class="pp-tableCell" style="height:100%" />'); --Y
    }).promise().done(function() {
      av.navigation && (a("#pp-nav").css("margin-top", "-" + a("#pp-nav").height() / 2 + "px"), a("#pp-nav").find("li").eq(a(".pp-section.active").index(".pp-section")).find("a").addClass("active"));
      a(c).on("load",
        function() {
          var f = c.location.hash.replace("#", ""),
            f = a('.pp-section[data-anchor="' + f + '"]');
          0 < f.length && ar(f, av.animateAnchor)
        });
      a.isFunction(av.afterRender) && av.afterRender.call(this)
    });
    a(c).on("hashchange",
      function() {
        var f = c.location.hash.replace("#", "").split("/")[0];
        f.length && f && f !== V && (f = isNaN(f) ? a('[data-anchor="' + f + '"]') : a(".pp-section").eq(f - 1), ar(f))
      });
    a(d).keydown(function(f) {
      if (av.keyboardScrolling && !ab()) {
        switch (f.which) {
          case 38:
          case 33:
            au.moveSectionUp();
            break;
          case 40:
          case 34:
            au.moveSectionDown();
            break;
          case 36:
            au.moveTo(1);
            break;
          case 35:
            au.moveTo(a(".pp-section").length);
            break;
          case 37:
            au.moveSectionUp();
            break;
          case 39:
            au.moveSectionDown()
        }
      }
    });
    av.normalScrollElements && (a(d).on("mouseenter", av.normalScrollElements,
      function() {
        au.setMouseWheelScrolling(!1)
      }), a(d).on("mouseleave", av.normalScrollElements,
      function() {
        au.setMouseWheelScrolling(!0)
      }));
    a(d).on("click touchstart", "#pp-nav a",
      function(f) {
        f.preventDefault();
        f = a(this).parent().index();
        ar(a(".pp-section").eq(f))
      });
    a(d).on({
        mouseenter: function() {
          var f = a(this).data("tooltip");
          a('<div class="pp-tooltip ' + av.navigation.position + '">' + f + "</div>").hide().appendTo(a(this)).fadeIn(200)
        },
        mouseleave: function() {
          a(this).find(".pp-tooltip").fadeOut(200,
            function() {
              a(this).remove()
            })
        }
      },
      "#pp-nav li")
  }
})(jQuery, document, window);