/*! UIkit 3.21.6 | https://www.getuikit.com | (c) 2014 - 2024 YOOtheme | MIT License */

(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
    typeof define === 'function' && define.amd ? define('uikit', factory) :
    (global = typeof globalThis !== 'undefined' ? globalThis : global || self, global.UIkit = factory());
})(this, (function () { 'use strict';

    const { hasOwnProperty, toString } = Object.prototype;
    function hasOwn(obj, key) {
      return hasOwnProperty.call(obj, key);
    }
    const hyphenateRe = /\B([A-Z])/g;
    const hyphenate = memoize((str) => str.replace(hyphenateRe, "-$1").toLowerCase());
    const camelizeRe = /-(\w)/g;
    const camelize = memoize(
      (str) => (str.charAt(0).toLowerCase() + str.slice(1)).replace(camelizeRe, (_, c) => c.toUpperCase())
    );
    const ucfirst = memoize((str) => str.charAt(0).toUpperCase() + str.slice(1));
    function startsWith(str, search) {
      var _a;
      return (_a = str == null ? void 0 : str.startsWith) == null ? void 0 : _a.call(str, search);
    }
    function endsWith(str, search) {
      var _a;
      return (_a = str == null ? void 0 : str.endsWith) == null ? void 0 : _a.call(str, search);
    }
    function includes(obj, search) {
      var _a;
      return (_a = obj == null ? void 0 : obj.includes) == null ? void 0 : _a.call(obj, search);
    }
    function findIndex(array, predicate) {
      var _a;
      return (_a = array == null ? void 0 : array.findIndex) == null ? void 0 : _a.call(array, predicate);
    }
    const { isArray, from: toArray } = Array;
    const { assign } = Object;
    function isFunction(obj) {
      return typeof obj === "function";
    }
    function isObject(obj) {
      return obj !== null && typeof obj === "object";
    }
    function isPlainObject(obj) {
      return toString.call(obj) === "[object Object]";
    }
    function isWindow(obj) {
      return isObject(obj) && obj === obj.window;
    }
    function isDocument(obj) {
      return nodeType(obj) === 9;
    }
    function isNode(obj) {
      return nodeType(obj) >= 1;
    }
    function isElement(obj) {
      return nodeType(obj) === 1;
    }
    function nodeType(obj) {
      return !isWindow(obj) && isObject(obj) && obj.nodeType;
    }
    function isBoolean(value) {
      return typeof value === "boolean";
    }
    function isString(value) {
      return typeof value === "string";
    }
    function isNumber(value) {
      return typeof value === "number";
    }
    function isNumeric(value) {
      return isNumber(value) || isString(value) && !isNaN(value - parseFloat(value));
    }
    function isEmpty(obj) {
      return !(isArray(obj) ? obj.length : isObject(obj) ? Object.keys(obj).length : false);
    }
    function isUndefined(value) {
      return value === void 0;
    }
    function toBoolean(value) {
      return isBoolean(value) ? value : value === "true" || value === "1" || value === "" ? true : value === "false" || value === "0" ? false : value;
    }
    function toNumber(value) {
      const number = Number(value);
      return isNaN(number) ? false : number;
    }
    function toFloat(value) {
      return parseFloat(value) || 0;
    }
    function toNode(element) {
      return element && toNodes(element)[0];
    }
    function toNodes(element) {
      return isNode(element) ? [element] : Array.from(element || []).filter(isNode);
    }
    function toWindow(element) {
      if (isWindow(element)) {
        return element;
      }
      element = toNode(element);
      const document = isDocument(element) ? element : element == null ? void 0 : element.ownerDocument;
      return (document == null ? void 0 : document.defaultView) || window;
    }
    function isEqual(value, other) {
      return value === other || isObject(value) && isObject(other) && Object.keys(value).length === Object.keys(other).length && each(value, (val, key) => val === other[key]);
    }
    function swap(value, a, b) {
      return value.replace(new RegExp(`${a}|${b}`, "g"), (match) => match === a ? b : a);
    }
    function last(array) {
      return array[array.length - 1];
    }
    function each(obj, cb) {
      for (const key in obj) {
        if (false === cb(obj[key], key)) {
          return false;
        }
      }
      return true;
    }
    function sortBy(array, prop) {
      return array.slice().sort(
        ({ [prop]: propA = 0 }, { [prop]: propB = 0 }) => propA > propB ? 1 : propB > propA ? -1 : 0
      );
    }
    function sumBy(array, iteratee) {
      return array.reduce(
        (sum, item) => sum + toFloat(isFunction(iteratee) ? iteratee(item) : item[iteratee]),
        0
      );
    }
    function uniqueBy(array, prop) {
      const seen = /* @__PURE__ */ new Set();
      return array.filter(({ [prop]: check }) => seen.has(check) ? false : seen.add(check));
    }
    function pick(obj, props) {
      return props.reduce((res, prop) => ({ ...res, [prop]: obj[prop] }), {});
    }
    function clamp(number, min = 0, max = 1) {
      return Math.min(Math.max(toNumber(number) || 0, min), max);
    }
    function noop() {
    }
    function intersectRect(...rects) {
      return [
        ["bottom", "top"],
        ["right", "left"]
      ].every(
        ([minProp, maxProp]) => Math.min(...rects.map(({ [minProp]: min }) => min)) - Math.max(...rects.map(({ [maxProp]: max }) => max)) > 0
      );
    }
    function pointInRect(point, rect) {
      return point.x <= rect.right && point.x >= rect.left && point.y <= rect.bottom && point.y >= rect.top;
    }
    function ratio(dimensions, prop, value) {
      const aProp = prop === "width" ? "height" : "width";
      return {
        [aProp]: dimensions[prop] ? Math.round(value * dimensions[aProp] / dimensions[prop]) : dimensions[aProp],
        [prop]: value
      };
    }
    function contain(dimensions, maxDimensions) {
      dimensions = { ...dimensions };
      for (const prop in dimensions) {
        dimensions = dimensions[prop] > maxDimensions[prop] ? ratio(dimensions, prop, maxDimensions[prop]) : dimensions;
      }
      return dimensions;
    }
    function cover$1(dimensions, maxDimensions) {
      dimensions = contain(dimensions, maxDimensions);
      for (const prop in dimensions) {
        dimensions = dimensions[prop] < maxDimensions[prop] ? ratio(dimensions, prop, maxDimensions[prop]) : dimensions;
      }
      return dimensions;
    }
    const Dimensions = { ratio, contain, cover: cover$1 };
    function getIndex(i, elements, current = 0, finite = false) {
      elements = toNodes(elements);
      const { length } = elements;
      if (!length) {
        return -1;
      }
      i = isNumeric(i) ? toNumber(i) : i === "next" ? current + 1 : i === "previous" ? current - 1 : i === "last" ? length - 1 : elements.indexOf(toNode(i));
      if (finite) {
        return clamp(i, 0, length - 1);
      }
      i %= length;
      return i < 0 ? i + length : i;
    }
    function memoize(fn) {
      const cache = /* @__PURE__ */ Object.create(null);
      return (key, ...args) => cache[key] || (cache[key] = fn(key, ...args));
    }

    function addClass(element, ...classes) {
      for (const node of toNodes(element)) {
        const add = toClasses(classes).filter((cls) => !hasClass(node, cls));
        if (add.length) {
          node.classList.add(...add);
        }
      }
    }
    function removeClass(element, ...classes) {
      for (const node of toNodes(element)) {
        const remove = toClasses(classes).filter((cls) => hasClass(node, cls));
        if (remove.length) {
          node.classList.remove(...remove);
        }
      }
    }
    function replaceClass(element, oldClass, newClass) {
      newClass = toClasses(newClass);
      oldClass = toClasses(oldClass).filter((cls) => !includes(newClass, cls));
      removeClass(element, oldClass);
      addClass(element, newClass);
    }
    function hasClass(element, cls) {
      [cls] = toClasses(cls);
      return toNodes(element).some((node) => node.classList.contains(cls));
    }
    function toggleClass(element, cls, force) {
      const classes = toClasses(cls);
      if (!isUndefined(force)) {
        force = !!force;
      }
      for (const node of toNodes(element)) {
        for (const cls2 of classes) {
          node.classList.toggle(cls2, force);
        }
      }
    }
    function toClasses(str) {
      return str ? isArray(str) ? str.map(toClasses).flat() : String(str).split(" ").filter(Boolean) : [];
    }

    function attr(element, name, value) {
      var _a;
      if (isObject(name)) {
        for (const key in name) {
          attr(element, key, name[key]);
        }
        return;
      }
      if (isUndefined(value)) {
        return (_a = toNode(element)) == null ? void 0 : _a.getAttribute(name);
      } else {
        for (const el of toNodes(element)) {
          if (isFunction(value)) {
            value = value.call(el, attr(el, name));
          }
          if (value === null) {
            removeAttr(el, name);
          } else {
            el.setAttribute(name, value);
          }
        }
      }
    }
    function hasAttr(element, name) {
      return toNodes(element).some((element2) => element2.hasAttribute(name));
    }
    function removeAttr(element, name) {
      toNodes(element).forEach((element2) => element2.removeAttribute(name));
    }
    function data(element, attribute) {
      for (const name of [attribute, `data-${attribute}`]) {
        if (hasAttr(element, name)) {
          return attr(element, name);
        }
      }
    }

    const inBrowser = typeof window !== "undefined";
    const isRtl = inBrowser && document.dir === "rtl";
    const hasTouch = inBrowser && "ontouchstart" in window;
    const hasPointerEvents = inBrowser && window.PointerEvent;
    const pointerDown = hasPointerEvents ? "pointerdown" : hasTouch ? "touchstart" : "mousedown";
    const pointerMove = hasPointerEvents ? "pointermove" : hasTouch ? "touchmove" : "mousemove";
    const pointerUp = hasPointerEvents ? "pointerup" : hasTouch ? "touchend" : "mouseup";
    const pointerEnter = hasPointerEvents ? "pointerenter" : hasTouch ? "" : "mouseenter";
    const pointerLeave = hasPointerEvents ? "pointerleave" : hasTouch ? "" : "mouseleave";
    const pointerCancel = hasPointerEvents ? "pointercancel" : "touchcancel";

    const voidElements = {
      area: true,
      base: true,
      br: true,
      col: true,
      embed: true,
      hr: true,
      img: true,
      input: true,
      keygen: true,
      link: true,
      meta: true,
      param: true,
      source: true,
      track: true,
      wbr: true
    };
    function isVoidElement(element) {
      return toNodes(element).some((element2) => voidElements[element2.tagName.toLowerCase()]);
    }
    const isVisibleFn = inBrowser && Element.prototype.checkVisibility || function() {
      return this.offsetWidth || this.offsetHeight || this.getClientRects().length;
    };
    function isVisible(element) {
      return toNodes(element).some((element2) => isVisibleFn.call(element2));
    }
    const selInput = "input,select,textarea,button";
    function isInput(element) {
      return toNodes(element).some((element2) => matches(element2, selInput));
    }
    const selFocusable = `${selInput},a[href],[tabindex]`;
    function isFocusable(element) {
      return matches(element, selFocusable);
    }
    function parent(element) {
      var _a;
      return (_a = toNode(element)) == null ? void 0 : _a.parentElement;
    }
    function filter(element, selector) {
      return toNodes(element).filter((element2) => matches(element2, selector));
    }
    function matches(element, selector) {
      return toNodes(element).some((element2) => element2.matches(selector));
    }
    function parents(element, selector) {
      const elements = [];
      while (element = parent(element)) {
        if (!selector || matches(element, selector)) {
          elements.push(element);
        }
      }
      return elements;
    }
    function children(element, selector) {
      element = toNode(element);
      const children2 = element ? toArray(element.children) : [];
      return selector ? filter(children2, selector) : children2;
    }
    function index(element, ref) {
      return ref ? toNodes(element).indexOf(toNode(ref)) : children(parent(element)).indexOf(element);
    }
    function isSameSiteAnchor(el) {
      el = toNode(el);
      return el && ["origin", "pathname", "search"].every((part) => el[part] === location[part]);
    }
    function getTargetedElement(el) {
      if (isSameSiteAnchor(el)) {
        const { hash, ownerDocument } = toNode(el);
        const id = decodeURIComponent(hash).slice(1);
        return ownerDocument.getElementById(id) || ownerDocument.getElementsByName(id)[0];
      }
    }

    function query(selector, context) {
      return find(selector, getContext(selector, context));
    }
    function queryAll(selector, context) {
      return findAll(selector, getContext(selector, context));
    }
    function find(selector, context) {
      return toNode(_query(selector, toNode(context), "querySelector"));
    }
    function findAll(selector, context) {
      return toNodes(_query(selector, toNode(context), "querySelectorAll"));
    }
    function getContext(selector, context = document) {
      return isString(selector) && parseSelector(selector).isContextSelector || isDocument(context) ? context : context.ownerDocument;
    }
    const addStarRe = /([!>+~-])(?=\s+[!>+~-]|\s*$)/g;
    const splitSelectorRe = /.*?[^\\](?![^(]*\))(?:,|$)/g;
    const trailingCommaRe = /\s*,$/;
    const parseSelector = memoize((selector) => {
      var _a;
      selector = selector.replace(addStarRe, "$1 *");
      let isContextSelector = false;
      const selectors = [];
      for (let sel of (_a = selector.match(splitSelectorRe)) != null ? _a : []) {
        sel = sel.replace(trailingCommaRe, "").trim();
        isContextSelector || (isContextSelector = ["!", "+", "~", "-", ">"].includes(sel[0]));
        selectors.push(sel);
      }
      return {
        selector: selectors.join(","),
        selectors,
        isContextSelector
      };
    });
    const parsePositionSelector = memoize((selector) => {
      selector = selector.slice(1).trim();
      const index2 = selector.indexOf(" ");
      return ~index2 ? [selector.slice(0, index2), selector.slice(index2 + 1)] : [selector, ""];
    });
    function _query(selector, context = document, queryFn) {
      if (!selector || !isString(selector)) {
        return selector;
      }
      const parsed = parseSelector(selector);
      if (!parsed.isContextSelector) {
        return _doQuery(context, queryFn, parsed.selector);
      }
      selector = "";
      const isSingle = parsed.selectors.length === 1;
      for (let sel of parsed.selectors) {
        let positionSel;
        let ctx = context;
        if (sel[0] === "!") {
          [positionSel, sel] = parsePositionSelector(sel);
          ctx = context.parentElement.closest(positionSel);
          if (!sel && isSingle) {
            return ctx;
          }
        }
        if (ctx && sel[0] === "-") {
          [positionSel, sel] = parsePositionSelector(sel);
          ctx = ctx.previousElementSibling;
          ctx = matches(ctx, positionSel) ? ctx : null;
          if (!sel && isSingle) {
            return ctx;
          }
        }
        if (!ctx) {
          continue;
        }
        if (isSingle) {
          if (sel[0] === "~" || sel[0] === "+") {
            sel = `:scope > :nth-child(${index(ctx) + 1}) ${sel}`;
            ctx = ctx.parentElement;
          } else if (sel[0] === ">") {
            sel = `:scope ${sel}`;
          }
          return _doQuery(ctx, queryFn, sel);
        }
        selector += `${selector ? "," : ""}${domPath(ctx)} ${sel}`;
      }
      if (!isDocument(context)) {
        context = context.ownerDocument;
      }
      return _doQuery(context, queryFn, selector);
    }
    function _doQuery(context, queryFn, selector) {
      try {
        return context[queryFn](selector);
      } catch (e) {
        return null;
      }
    }
    function domPath(element) {
      const names = [];
      while (element.parentNode) {
        const id = attr(element, "id");
        if (id) {
          names.unshift(`#${escape(id)}`);
          break;
        } else {
          let { tagName } = element;
          if (tagName !== "HTML") {
            tagName += `:nth-child(${index(element) + 1})`;
          }
          names.unshift(tagName);
          element = element.parentNode;
        }
      }
      return names.join(" > ");
    }
    function escape(css) {
      return isString(css) ? CSS.escape(css) : "";
    }

    function on(...args) {
      let [targets, types, selector, listener, useCapture = false] = getArgs(args);
      if (listener.length > 1) {
        listener = detail(listener);
      }
      if (useCapture == null ? void 0 : useCapture.self) {
        listener = selfFilter(listener);
      }
      if (selector) {
        listener = delegate(selector, listener);
      }
      for (const type of types) {
        for (const target of targets) {
          target.addEventListener(type, listener, useCapture);
        }
      }
      return () => off(targets, types, listener, useCapture);
    }
    function off(...args) {
      let [targets, types, , listener, useCapture = false] = getArgs(args);
      for (const type of types) {
        for (const target of targets) {
          target.removeEventListener(type, listener, useCapture);
        }
      }
    }
    function once(...args) {
      const [element, types, selector, listener, useCapture = false, condition] = getArgs(args);
      const off2 = on(
        element,
        types,
        selector,
        (e) => {
          const result = !condition || condition(e);
          if (result) {
            off2();
            listener(e, result);
          }
        },
        useCapture
      );
      return off2;
    }
    function trigger(targets, event, detail2) {
      return toEventTargets(targets).every(
        (target) => target.dispatchEvent(createEvent(event, true, true, detail2))
      );
    }
    function createEvent(e, bubbles = true, cancelable = false, detail2) {
      if (isString(e)) {
        e = new CustomEvent(e, { bubbles, cancelable, detail: detail2 });
      }
      return e;
    }
    function getArgs(args) {
      args[0] = toEventTargets(args[0]);
      if (isString(args[1])) {
        args[1] = args[1].split(" ");
      }
      if (isFunction(args[2])) {
        args.splice(2, 0, false);
      }
      return args;
    }
    function delegate(selector, listener) {
      return (e) => {
        const current = selector[0] === ">" ? findAll(selector, e.currentTarget).reverse().find((element) => element.contains(e.target)) : e.target.closest(selector);
        if (current) {
          e.current = current;
          listener.call(this, e);
          delete e.current;
        }
      };
    }
    function detail(listener) {
      return (e) => isArray(e.detail) ? listener(e, ...e.detail) : listener(e);
    }
    function selfFilter(listener) {
      return function(e) {
        if (e.target === e.currentTarget || e.target === e.current) {
          return listener.call(null, e);
        }
      };
    }
    function isEventTarget(target) {
      return target && "addEventListener" in target;
    }
    function toEventTarget(target) {
      return isEventTarget(target) ? target : toNode(target);
    }
    function toEventTargets(target) {
      return isArray(target) ? target.map(toEventTarget).filter(Boolean) : isString(target) ? findAll(target) : isEventTarget(target) ? [target] : toNodes(target);
    }
    function isTouch(e) {
      return e.pointerType === "touch" || !!e.touches;
    }
    function getEventPos(e) {
      var _a, _b;
      const { clientX: x, clientY: y } = ((_a = e.touches) == null ? void 0 : _a[0]) || ((_b = e.changedTouches) == null ? void 0 : _b[0]) || e;
      return { x, y };
    }

    const cssNumber = {
      "animation-iteration-count": true,
      "column-count": true,
      "fill-opacity": true,
      "flex-grow": true,
      "flex-shrink": true,
      "font-weight": true,
      "line-height": true,
      opacity: true,
      order: true,
      orphans: true,
      "stroke-dasharray": true,
      "stroke-dashoffset": true,
      widows: true,
      "z-index": true,
      zoom: true
    };
    function css(element, property, value, priority) {
      const elements = toNodes(element);
      for (const element2 of elements) {
        if (isString(property)) {
          property = propName(property);
          if (isUndefined(value)) {
            return getComputedStyle(element2).getPropertyValue(property);
          } else {
            element2.style.setProperty(
              property,
              isNumeric(value) && !cssNumber[property] ? `${value}px` : value || isNumber(value) ? value : "",
              priority
            );
          }
        } else if (isArray(property)) {
          const props = {};
          for (const prop of property) {
            props[prop] = css(element2, prop);
          }
          return props;
        } else if (isObject(property)) {
          for (const prop in property) {
            css(element2, prop, property[prop], value);
          }
        }
      }
      return elements[0];
    }
    const propName = memoize((name) => {
      if (startsWith(name, "--")) {
        return name;
      }
      name = hyphenate(name);
      const { style } = document.documentElement;
      if (name in style) {
        return name;
      }
      for (const prefix of ["webkit", "moz"]) {
        const prefixedName = `-${prefix}-${name}`;
        if (prefixedName in style) {
          return prefixedName;
        }
      }
    });

    const clsTransition = "uk-transition";
    const transitionEnd = "transitionend";
    const transitionCanceled = "transitioncanceled";
    function transition$1(element, props, duration = 400, timing = "linear") {
      duration = Math.round(duration);
      return Promise.all(
        toNodes(element).map(
          (element2) => new Promise((resolve, reject) => {
            for (const name in props) {
              css(element2, name);
            }
            const timer = setTimeout(() => trigger(element2, transitionEnd), duration);
            once(
              element2,
              [transitionEnd, transitionCanceled],
              ({ type }) => {
                clearTimeout(timer);
                removeClass(element2, clsTransition);
                css(element2, {
                  transitionProperty: "",
                  transitionDuration: "",
                  transitionTimingFunction: ""
                });
                type === transitionCanceled ? reject() : resolve(element2);
              },
              { self: true }
            );
            addClass(element2, clsTransition);
            css(element2, {
              transitionProperty: Object.keys(props).map(propName).join(","),
              transitionDuration: `${duration}ms`,
              transitionTimingFunction: timing,
              ...props
            });
          })
        )
      );
    }
    const Transition = {
      start: transition$1,
      async stop(element) {
        trigger(element, transitionEnd);
        await Promise.resolve();
      },
      async cancel(element) {
        trigger(element, transitionCanceled);
        await Promise.resolve();
      },
      inProgress(element) {
        return hasClass(element, clsTransition);
      }
    };
    const clsAnimation = "uk-animation";
    const animationEnd = "animationend";
    const animationCanceled = "animationcanceled";
    function animate$2(element, animation, duration = 200, origin, out) {
      return Promise.all(
        toNodes(element).map(
          (element2) => new Promise((resolve, reject) => {
            if (hasClass(element2, clsAnimation)) {
              trigger(element2, animationCanceled);
            }
            const classes = [
              animation,
              clsAnimation,
              `${clsAnimation}-${out ? "leave" : "enter"}`,
              origin && `uk-transform-origin-${origin}`,
              out && `${clsAnimation}-reverse`
            ];
            const timer = setTimeout(() => trigger(element2, animationEnd), duration);
            once(
              element2,
              [animationEnd, animationCanceled],
              ({ type }) => {
                clearTimeout(timer);
                type === animationCanceled ? reject() : resolve(element2);
                css(element2, "animationDuration", "");
                removeClass(element2, classes);
              },
              { self: true }
            );
            css(element2, "animationDuration", `${duration}ms`);
            addClass(element2, classes);
          })
        )
      );
    }
    const Animation = {
      in: animate$2,
      out(element, animation, duration, origin) {
        return animate$2(element, animation, duration, origin, true);
      },
      inProgress(element) {
        return hasClass(element, clsAnimation);
      },
      cancel(element) {
        trigger(element, animationCanceled);
      }
    };

    function ready(fn) {
      if (document.readyState !== "loading") {
        fn();
        return;
      }
      once(document, "DOMContentLoaded", fn);
    }
    function isTag(element, ...tagNames) {
      return tagNames.some((tagName) => {
        var _a;
        return ((_a = element == null ? void 0 : element.tagName) == null ? void 0 : _a.toLowerCase()) === tagName.toLowerCase();
      });
    }
    function empty(element) {
      element = $(element);
      element.innerHTML = "";
      return element;
    }
    function html(parent2, html2) {
      return isUndefined(html2) ? $(parent2).innerHTML : append(empty(parent2), html2);
    }
    const prepend = applyFn("prepend");
    const append = applyFn("append");
    const before = applyFn("before");
    const after = applyFn("after");
    function applyFn(fn) {
      return function(ref, element) {
        var _a;
        const nodes = toNodes(isString(element) ? fragment(element) : element);
        (_a = $(ref)) == null ? void 0 : _a[fn](...nodes);
        return unwrapSingle(nodes);
      };
    }
    function remove$1(element) {
      toNodes(element).forEach((element2) => element2.remove());
    }
    function wrapAll(element, structure) {
      structure = toNode(before(element, structure));
      while (structure.firstElementChild) {
        structure = structure.firstElementChild;
      }
      append(structure, element);
      return structure;
    }
    function wrapInner(element, structure) {
      return toNodes(
        toNodes(element).map(
          (element2) => element2.hasChildNodes() ? wrapAll(toArray(element2.childNodes), structure) : append(element2, structure)
        )
      );
    }
    function unwrap(element) {
      toNodes(element).map(parent).filter((value, index, self) => self.indexOf(value) === index).forEach((parent2) => parent2.replaceWith(...parent2.childNodes));
    }
    const singleTagRe = /^<(\w+)\s*\/?>(?:<\/\1>)?$/;
    function fragment(html2) {
      const matches = singleTagRe.exec(html2);
      if (matches) {
        return document.createElement(matches[1]);
      }
      const container = document.createElement("template");
      container.innerHTML = html2.trim();
      return unwrapSingle(container.content.childNodes);
    }
    function unwrapSingle(nodes) {
      return nodes.length > 1 ? nodes : nodes[0];
    }
    function apply(node, fn) {
      if (!isElement(node)) {
        return;
      }
      fn(node);
      node = node.firstElementChild;
      while (node) {
        apply(node, fn);
        node = node.nextElementSibling;
      }
    }
    function $(selector, context) {
      return isHtml(selector) ? toNode(fragment(selector)) : find(selector, context);
    }
    function $$(selector, context) {
      return isHtml(selector) ? toNodes(fragment(selector)) : findAll(selector, context);
    }
    function isHtml(str) {
      return isString(str) && startsWith(str.trim(), "<");
    }

    const dirs$1 = {
      width: ["left", "right"],
      height: ["top", "bottom"]
    };
    function dimensions(element) {
      const rect = isElement(element) ? toNode(element).getBoundingClientRect() : { height: height(element), width: width(element), top: 0, left: 0 };
      return {
        height: rect.height,
        width: rect.width,
        top: rect.top,
        left: rect.left,
        bottom: rect.top + rect.height,
        right: rect.left + rect.width
      };
    }
    function offset(element, coordinates) {
      if (coordinates) {
        css(element, { left: 0, top: 0 });
      }
      const currentOffset = dimensions(element);
      if (element) {
        const { scrollY, scrollX } = toWindow(element);
        const offsetBy = { height: scrollY, width: scrollX };
        for (const dir in dirs$1) {
          for (const prop of dirs$1[dir]) {
            currentOffset[prop] += offsetBy[dir];
          }
        }
      }
      if (!coordinates) {
        return currentOffset;
      }
      for (const prop of ["left", "top"]) {
        css(element, prop, coordinates[prop] - currentOffset[prop]);
      }
    }
    function position(element) {
      let { top, left } = offset(element);
      const {
        ownerDocument: { body, documentElement },
        offsetParent
      } = toNode(element);
      let parent = offsetParent || documentElement;
      while (parent && (parent === body || parent === documentElement) && css(parent, "position") === "static") {
        parent = parent.parentNode;
      }
      if (isElement(parent)) {
        const parentOffset = offset(parent);
        top -= parentOffset.top + toFloat(css(parent, "borderTopWidth"));
        left -= parentOffset.left + toFloat(css(parent, "borderLeftWidth"));
      }
      return {
        top: top - toFloat(css(element, "marginTop")),
        left: left - toFloat(css(element, "marginLeft"))
      };
    }
    function offsetPosition(element) {
      element = toNode(element);
      const offset2 = [element.offsetTop, element.offsetLeft];
      while (element = element.offsetParent) {
        offset2[0] += element.offsetTop + toFloat(css(element, `borderTopWidth`));
        offset2[1] += element.offsetLeft + toFloat(css(element, `borderLeftWidth`));
        if (css(element, "position") === "fixed") {
          const win = toWindow(element);
          offset2[0] += win.scrollY;
          offset2[1] += win.scrollX;
          return offset2;
        }
      }
      return offset2;
    }
    const height = dimension("height");
    const width = dimension("width");
    function dimension(prop) {
      const propName = ucfirst(prop);
      return (element, value) => {
        if (isUndefined(value)) {
          if (isWindow(element)) {
            return element[`inner${propName}`];
          }
          if (isDocument(element)) {
            const doc = element.documentElement;
            return Math.max(doc[`offset${propName}`], doc[`scroll${propName}`]);
          }
          element = toNode(element);
          value = css(element, prop);
          value = value === "auto" ? element[`offset${propName}`] : toFloat(value) || 0;
          return value - boxModelAdjust(element, prop);
        } else {
          return css(
            element,
            prop,
            !value && value !== 0 ? "" : +value + boxModelAdjust(element, prop) + "px"
          );
        }
      };
    }
    function boxModelAdjust(element, prop, sizing = "border-box") {
      return css(element, "boxSizing") === sizing ? sumBy(
        dirs$1[prop].map(ucfirst),
        (prop2) => toFloat(css(element, `padding${prop2}`)) + toFloat(css(element, `border${prop2}Width`))
      ) : 0;
    }
    function flipPosition(pos) {
      for (const dir in dirs$1) {
        for (const i in dirs$1[dir]) {
          if (dirs$1[dir][i] === pos) {
            return dirs$1[dir][1 - i];
          }
        }
      }
      return pos;
    }
    function toPx(value, property = "width", element = window, offsetDim = false) {
      if (!isString(value)) {
        return toFloat(value);
      }
      return sumBy(parseCalc(value), (value2) => {
        const unit = parseUnit(value2);
        return unit ? percent(
          unit === "vh" ? getViewportHeight() : unit === "vw" ? width(toWindow(element)) : offsetDim ? element[`offset${ucfirst(property)}`] : dimensions(element)[property],
          value2
        ) : value2;
      });
    }
    const calcRe = /-?\d+(?:\.\d+)?(?:v[wh]|%|px)?/g;
    const parseCalc = memoize((calc) => calc.toString().replace(/\s/g, "").match(calcRe) || []);
    const unitRe = /(?:v[hw]|%)$/;
    const parseUnit = memoize((str) => (str.match(unitRe) || [])[0]);
    function percent(base, value) {
      return base * toFloat(value) / 100;
    }
    let vh;
    let vhEl;
    function getViewportHeight() {
      if (vh) {
        return vh;
      }
      if (!vhEl) {
        vhEl = $("<div>");
        css(vhEl, {
          height: "100vh",
          position: "fixed"
        });
        on(window, "resize", () => vh = null);
      }
      append(document.body, vhEl);
      vh = vhEl.clientHeight;
      remove$1(vhEl);
      return vh;
    }

    const fastdom = { read, write, clear, flush };
    const reads = [];
    const writes = [];
    function read(task) {
      reads.push(task);
      scheduleFlush();
      return task;
    }
    function write(task) {
      writes.push(task);
      scheduleFlush();
      return task;
    }
    function clear(task) {
      remove(reads, task);
      remove(writes, task);
    }
    let scheduled = false;
    function flush() {
      runTasks(reads);
      runTasks(writes.splice(0));
      scheduled = false;
      if (reads.length || writes.length) {
        scheduleFlush();
      }
    }
    function scheduleFlush() {
      if (!scheduled) {
        scheduled = true;
        queueMicrotask(flush);
      }
    }
    function runTasks(tasks) {
      let task;
      while (task = tasks.shift()) {
        try {
          task();
        } catch (e) {
          console.error(e);
        }
      }
    }
    function remove(array, item) {
      const index = array.indexOf(item);
      return ~index && array.splice(index, 1);
    }

    class MouseTracker {
      init() {
        this.positions = [];
        let position;
        this.unbind = on(document, "mousemove", (e) => position = getEventPos(e));
        this.interval = setInterval(() => {
          if (!position) {
            return;
          }
          this.positions.push(position);
          if (this.positions.length > 5) {
            this.positions.shift();
          }
        }, 50);
      }
      cancel() {
        var _a;
        (_a = this.unbind) == null ? void 0 : _a.call(this);
        clearInterval(this.interval);
      }
      movesTo(target) {
        if (!this.positions || this.positions.length < 2) {
          return false;
        }
        const p = dimensions(target);
        const { left, right, top, bottom } = p;
        const [prevPosition] = this.positions;
        const position = last(this.positions);
        const path = [prevPosition, position];
        if (pointInRect(position, p)) {
          return false;
        }
        const diagonals = [
          [
            { x: left, y: top },
            { x: right, y: bottom }
          ],
          [
            { x: left, y: bottom },
            { x: right, y: top }
          ]
        ];
        return diagonals.some((diagonal) => {
          const intersection = intersect(path, diagonal);
          return intersection && pointInRect(intersection, p);
        });
      }
    }
    function intersect([{ x: x1, y: y1 }, { x: x2, y: y2 }], [{ x: x3, y: y3 }, { x: x4, y: y4 }]) {
      const denominator = (y4 - y3) * (x2 - x1) - (x4 - x3) * (y2 - y1);
      if (denominator === 0) {
        return false;
      }
      const ua = ((x4 - x3) * (y1 - y3) - (y4 - y3) * (x1 - x3)) / denominator;
      if (ua < 0) {
        return false;
      }
      return { x: x1 + ua * (x2 - x1), y: y1 + ua * (y2 - y1) };
    }

    function observeIntersection(targets, cb, options = {}, { intersecting = true } = {}) {
      const observer = new IntersectionObserver(
        intersecting ? (entries, observer2) => {
          if (entries.some((entry) => entry.isIntersecting)) {
            cb(entries, observer2);
          }
        } : cb,
        options
      );
      for (const el of toNodes(targets)) {
        observer.observe(el);
      }
      return observer;
    }
    const hasResizeObserver = inBrowser && window.ResizeObserver;
    function observeResize(targets, cb, options = { box: "border-box" }) {
      if (hasResizeObserver) {
        return observe$1(ResizeObserver, targets, cb, options);
      }
      const off = [on(window, "load resize", cb), on(document, "loadedmetadata load", cb, true)];
      return { disconnect: () => off.map((cb2) => cb2()) };
    }
    function observeViewportResize(cb) {
      return { disconnect: on([window, window.visualViewport], "resize", cb) };
    }
    function observeMutation(targets, cb, options) {
      return observe$1(MutationObserver, targets, cb, options);
    }
    function observe$1(Observer, targets, cb, options) {
      const observer = new Observer(cb);
      for (const el of toNodes(targets)) {
        observer.observe(el, options);
      }
      return observer;
    }

    function play(el) {
      if (isIFrame(el)) {
        call(el, { func: "playVideo", method: "play" });
      }
      if (isHTML5(el)) {
        el.play().catch(noop);
      }
    }
    function pause(el) {
      if (isIFrame(el)) {
        call(el, { func: "pauseVideo", method: "pause" });
      }
      if (isHTML5(el)) {
        el.pause();
      }
    }
    function mute(el) {
      if (isIFrame(el)) {
        call(el, { func: "mute", method: "setVolume", value: 0 });
      }
      if (isHTML5(el)) {
        el.muted = true;
      }
    }
    function isVideo(el) {
      return isHTML5(el) || isIFrame(el);
    }
    function isHTML5(el) {
      return isTag(el, "video");
    }
    function isIFrame(el) {
      return isTag(el, "iframe") && (isYoutube(el) || isVimeo(el));
    }
    function isYoutube(el) {
      return !!el.src.match(
        /\/\/.*?youtube(-nocookie)?\.[a-z]+\/(watch\?v=[^&\s]+|embed)|youtu\.be\/.*/
      );
    }
    function isVimeo(el) {
      return !!el.src.match(/vimeo\.com\/video\/.*/);
    }
    async function call(el, cmd) {
      await enableApi(el);
      post(el, cmd);
    }
    function post(el, cmd) {
      el.contentWindow.postMessage(JSON.stringify({ event: "command", ...cmd }), "*");
    }
    const stateKey = "_ukPlayer";
    let counter = 0;
    function enableApi(el) {
      if (el[stateKey]) {
        return el[stateKey];
      }
      const youtube = isYoutube(el);
      const vimeo = isVimeo(el);
      const id = ++counter;
      let poller;
      return el[stateKey] = new Promise((resolve) => {
        youtube && once(el, "load", () => {
          const listener = () => post(el, { event: "listening", id });
          poller = setInterval(listener, 100);
          listener();
        });
        once(window, "message", resolve, false, ({ data }) => {
          try {
            data = JSON.parse(data);
            return youtube && (data == null ? void 0 : data.id) === id && data.event === "onReady" || vimeo && Number(data == null ? void 0 : data.player_id) === id;
          } catch (e) {
          }
        });
        el.src = `${el.src}${includes(el.src, "?") ? "&" : "?"}${youtube ? "enablejsapi=1" : `api=1&player_id=${id}`}`;
      }).then(() => clearInterval(poller));
    }

    function isInView(element, offsetTop = 0, offsetLeft = 0) {
      if (!isVisible(element)) {
        return false;
      }
      return intersectRect(
        ...overflowParents(element).map((parent2) => {
          const { top, left, bottom, right } = offsetViewport(parent2);
          return {
            top: top - offsetTop,
            left: left - offsetLeft,
            bottom: bottom + offsetTop,
            right: right + offsetLeft
          };
        }).concat(offset(element))
      );
    }
    function scrollIntoView(element, { offset: offsetBy = 0 } = {}) {
      const parents2 = isVisible(element) ? scrollParents(element, false, ["hidden"]) : [];
      return parents2.reduce(
        (fn, scrollElement, i) => {
          const { scrollTop, scrollHeight, offsetHeight } = scrollElement;
          const viewport = offsetViewport(scrollElement);
          const maxScroll = scrollHeight - viewport.height;
          const { height: elHeight, top: elTop } = parents2[i - 1] ? offsetViewport(parents2[i - 1]) : offset(element);
          let top = Math.ceil(elTop - viewport.top - offsetBy + scrollTop);
          if (offsetBy > 0 && offsetHeight < elHeight + offsetBy) {
            top += offsetBy;
          } else {
            offsetBy = 0;
          }
          if (top > maxScroll) {
            offsetBy -= top - maxScroll;
            top = maxScroll;
          } else if (top < 0) {
            offsetBy -= top;
            top = 0;
          }
          return () => scrollTo(scrollElement, top - scrollTop, element, maxScroll).then(fn);
        },
        () => Promise.resolve()
      )();
      function scrollTo(element2, top, targetEl, maxScroll) {
        return new Promise((resolve) => {
          const scroll = element2.scrollTop;
          const duration = getDuration(Math.abs(top));
          const start = Date.now();
          const isScrollingElement = scrollingElement(element2) === element2;
          const targetTop = offset(targetEl).top + (isScrollingElement ? 0 : scroll);
          let prev = 0;
          let frames = 15;
          (function step() {
            const percent = ease(clamp((Date.now() - start) / duration));
            let diff = 0;
            if (parents2[0] === element2 && scroll + top < maxScroll) {
              diff = offset(targetEl).top + (isScrollingElement ? 0 : element2.scrollTop) - targetTop - dimensions(getCoveringElement(targetEl)).height;
            }
            element2.scrollTop = scroll + (top + diff) * percent;
            if (percent === 1 && (prev === diff || !frames--)) {
              resolve();
            } else {
              prev = diff;
              requestAnimationFrame(step);
            }
          })();
        });
      }
      function getDuration(dist) {
        return 40 * Math.pow(dist, 0.375);
      }
      function ease(k) {
        return 0.5 * (1 - Math.cos(Math.PI * k));
      }
    }
    function scrolledOver(element, startOffset = 0, endOffset = 0) {
      if (!isVisible(element)) {
        return 0;
      }
      const scrollElement = scrollParent(element, true);
      const { scrollHeight, scrollTop } = scrollElement;
      const { height: viewportHeight } = offsetViewport(scrollElement);
      const maxScroll = scrollHeight - viewportHeight;
      const elementOffsetTop = offsetPosition(element)[0] - offsetPosition(scrollElement)[0];
      const start = Math.max(0, elementOffsetTop - viewportHeight + startOffset);
      const end = Math.min(maxScroll, elementOffsetTop + element.offsetHeight - endOffset);
      return start < end ? clamp((scrollTop - start) / (end - start)) : 1;
    }
    function scrollParents(element, scrollable = false, props = []) {
      const scrollEl = scrollingElement(element);
      let ancestors = parents(element).reverse();
      ancestors = ancestors.slice(ancestors.indexOf(scrollEl) + 1);
      const fixedIndex = findIndex(ancestors, (el) => css(el, "position") === "fixed");
      if (~fixedIndex) {
        ancestors = ancestors.slice(fixedIndex);
      }
      return [scrollEl].concat(
        ancestors.filter(
          (parent2) => css(parent2, "overflow").split(" ").some((prop) => includes(["auto", "scroll", ...props], prop)) && (!scrollable || parent2.scrollHeight > offsetViewport(parent2).height)
        )
      ).reverse();
    }
    function scrollParent(...args) {
      return scrollParents(...args)[0];
    }
    function overflowParents(element) {
      return scrollParents(element, false, ["hidden", "clip"]);
    }
    function offsetViewport(scrollElement) {
      const window = toWindow(scrollElement);
      const documentScrollingElement = scrollingElement(scrollElement);
      const useWindow = scrollElement.contains(documentScrollingElement);
      if (useWindow && window.visualViewport) {
        let { height, width, scale, pageTop: top, pageLeft: left } = window.visualViewport;
        height = Math.round(height * scale);
        width = Math.round(width * scale);
        return { height, width, top, left, bottom: top + height, right: left + width };
      }
      let rect = offset(useWindow ? window : scrollElement);
      if (css(scrollElement, "display") === "inline") {
        return rect;
      }
      const { body, documentElement } = window.document;
      const viewportElement = useWindow ? documentScrollingElement === documentElement || // In quirks mode the scrolling element is body, even though the viewport is html
      documentScrollingElement.clientHeight < body.clientHeight ? documentScrollingElement : body : scrollElement;
      for (let [prop, dir, start, end] of [
        ["width", "x", "left", "right"],
        ["height", "y", "top", "bottom"]
      ]) {
        const subpixel = rect[prop] % 1;
        rect[start] += toFloat(css(viewportElement, `border-${start}-width`));
        rect[prop] = rect[dir] = viewportElement[`client${ucfirst(prop)}`] - (subpixel ? subpixel < 0.5 ? -subpixel : 1 - subpixel : 0);
        rect[end] = rect[prop] + rect[start];
      }
      return rect;
    }
    function getCoveringElement(target) {
      const { left, width, top } = dimensions(target);
      for (const position of top ? [0, top] : [0]) {
        let coverEl;
        for (const el of toWindow(target).document.elementsFromPoint(left + width / 2, position)) {
          if (!el.contains(target) && // If e.g. Offcanvas is not yet closed
          !hasClass(el, "uk-togglable-leave") && (hasPosition(el, "fixed") && zIndex(
            parents(target).reverse().find(
              (parent2) => !parent2.contains(el) && !hasPosition(parent2, "static")
            )
          ) < zIndex(el) || hasPosition(el, "sticky") && parent(el).contains(target)) && (!coverEl || dimensions(coverEl).height < dimensions(el).height)) {
            coverEl = el;
          }
        }
        if (coverEl) {
          return coverEl;
        }
      }
    }
    function zIndex(element) {
      return toFloat(css(element, "zIndex"));
    }
    function hasPosition(element, position) {
      return css(element, "position") === position;
    }
    function scrollingElement(element) {
      return toWindow(element).document.scrollingElement;
    }

    const dirs = [
      ["width", "x", "left", "right"],
      ["height", "y", "top", "bottom"]
    ];
    function positionAt(element, target, options) {
      options = {
        attach: {
          element: ["left", "top"],
          target: ["left", "top"],
          ...options.attach
        },
        offset: [0, 0],
        placement: [],
        ...options
      };
      if (!isArray(target)) {
        target = [target, target];
      }
      offset(element, getPosition(element, target, options));
    }
    function getPosition(element, target, options) {
      const position = attachTo(element, target, options);
      const { boundary, viewportOffset = 0, placement } = options;
      let offsetPosition = position;
      for (const [i, [prop, , start, end]] of Object.entries(dirs)) {
        const viewport = getViewport$2(element, target[i], viewportOffset, boundary, i);
        if (isWithin(position, viewport, i)) {
          continue;
        }
        let offsetBy = 0;
        if (placement[i] === "flip") {
          const attach = options.attach.target[i];
          if (attach === end && position[end] <= viewport[end] || attach === start && position[start] >= viewport[start]) {
            continue;
          }
          offsetBy = flip(element, target, options, i)[start] - position[start];
          const scrollArea = getScrollArea(element, target[i], viewportOffset, i);
          if (!isWithin(applyOffset(position, offsetBy, i), scrollArea, i)) {
            if (isWithin(position, scrollArea, i)) {
              continue;
            }
            if (options.recursion) {
              return false;
            }
            const newPos = flipAxis(element, target, options);
            if (newPos && isWithin(newPos, scrollArea, 1 - i)) {
              return newPos;
            }
            continue;
          }
        } else if (placement[i] === "shift") {
          const targetDim = offset(target[i]);
          const { offset: elOffset } = options;
          offsetBy = clamp(
            clamp(position[start], viewport[start], viewport[end] - position[prop]),
            targetDim[start] - position[prop] + elOffset[i],
            targetDim[end] - elOffset[i]
          ) - position[start];
        }
        offsetPosition = applyOffset(offsetPosition, offsetBy, i);
      }
      return offsetPosition;
    }
    function attachTo(element, target, options) {
      let { attach, offset: offsetBy } = {
        attach: {
          element: ["left", "top"],
          target: ["left", "top"],
          ...options.attach
        },
        offset: [0, 0],
        ...options
      };
      let elOffset = offset(element);
      for (const [i, [prop, , start, end]] of Object.entries(dirs)) {
        const targetOffset = attach.target[i] === attach.element[i] ? offsetViewport(target[i]) : offset(target[i]);
        elOffset = applyOffset(
          elOffset,
          targetOffset[start] - elOffset[start] + moveBy(attach.target[i], end, targetOffset[prop]) - moveBy(attach.element[i], end, elOffset[prop]) + +offsetBy[i],
          i
        );
      }
      return elOffset;
    }
    function applyOffset(position, offset2, i) {
      const [, dir, start, end] = dirs[i];
      const newPos = { ...position };
      newPos[start] = position[dir] = position[start] + offset2;
      newPos[end] += offset2;
      return newPos;
    }
    function moveBy(attach, end, dim) {
      return attach === "center" ? dim / 2 : attach === end ? dim : 0;
    }
    function getViewport$2(element, target, viewportOffset, boundary, i) {
      let viewport = getIntersectionArea(...commonScrollParents(element, target).map(offsetViewport));
      if (viewportOffset) {
        viewport[dirs[i][2]] += viewportOffset;
        viewport[dirs[i][3]] -= viewportOffset;
      }
      if (boundary) {
        viewport = getIntersectionArea(
          viewport,
          offset(isArray(boundary) ? boundary[i] : boundary)
        );
      }
      return viewport;
    }
    function getScrollArea(element, target, viewportOffset, i) {
      const [prop, axis, start, end] = dirs[i];
      const [scrollElement] = commonScrollParents(element, target);
      const viewport = offsetViewport(scrollElement);
      if (["auto", "scroll"].includes(css(scrollElement, `overflow-${axis}`))) {
        viewport[start] -= scrollElement[`scroll${ucfirst(start)}`];
        viewport[end] = viewport[start] + scrollElement[`scroll${ucfirst(prop)}`];
      }
      viewport[start] += viewportOffset;
      viewport[end] -= viewportOffset;
      return viewport;
    }
    function commonScrollParents(element, target) {
      return overflowParents(target).filter((parent) => parent.contains(element));
    }
    function getIntersectionArea(...rects) {
      let area = {};
      for (const rect of rects) {
        for (const [, , start, end] of dirs) {
          area[start] = Math.max(area[start] || 0, rect[start]);
          area[end] = Math.min(...[area[end], rect[end]].filter(Boolean));
        }
      }
      return area;
    }
    function isWithin(positionA, positionB, i) {
      const [, , start, end] = dirs[i];
      return positionA[start] >= positionB[start] && positionA[end] <= positionB[end];
    }
    function flip(element, target, { offset: offset2, attach }, i) {
      return attachTo(element, target, {
        attach: {
          element: flipAttach(attach.element, i),
          target: flipAttach(attach.target, i)
        },
        offset: flipOffset(offset2, i)
      });
    }
    function flipAxis(element, target, options) {
      return getPosition(element, target, {
        ...options,
        attach: {
          element: options.attach.element.map(flipAttachAxis).reverse(),
          target: options.attach.target.map(flipAttachAxis).reverse()
        },
        offset: options.offset.reverse(),
        placement: options.placement.reverse(),
        recursion: true
      });
    }
    function flipAttach(attach, i) {
      const newAttach = [...attach];
      const index = dirs[i].indexOf(attach[i]);
      if (~index) {
        newAttach[i] = dirs[i][1 - index % 2 + 2];
      }
      return newAttach;
    }
    function flipAttachAxis(prop) {
      for (let i = 0; i < dirs.length; i++) {
        const index = dirs[i].indexOf(prop);
        if (~index) {
          return dirs[1 - i][index % 2 + 2];
        }
      }
    }
    function flipOffset(offset2, i) {
      offset2 = [...offset2];
      offset2[i] *= -1;
      return offset2;
    }

    var util = /*#__PURE__*/Object.freeze({
        __proto__: null,
        $: $,
        $$: $$,
        Animation: Animation,
        Dimensions: Dimensions,
        MouseTracker: MouseTracker,
        Transition: Transition,
        addClass: addClass,
        after: after,
        append: append,
        apply: apply,
        assign: assign,
        attr: attr,
        before: before,
        boxModelAdjust: boxModelAdjust,
        camelize: camelize,
        children: children,
        clamp: clamp,
        createEvent: createEvent,
        css: css,
        data: data,
        dimensions: dimensions,
        each: each,
        empty: empty,
        endsWith: endsWith,
        escape: escape,
        fastdom: fastdom,
        filter: filter,
        find: find,
        findAll: findAll,
        findIndex: findIndex,
        flipPosition: flipPosition,
        fragment: fragment,
        getCoveringElement: getCoveringElement,
        getEventPos: getEventPos,
        getIndex: getIndex,
        getTargetedElement: getTargetedElement,
        hasAttr: hasAttr,
        hasClass: hasClass,
        hasOwn: hasOwn,
        hasTouch: hasTouch,
        height: height,
        html: html,
        hyphenate: hyphenate,
        inBrowser: inBrowser,
        includes: includes,
        index: index,
        intersectRect: intersectRect,
        isArray: isArray,
        isBoolean: isBoolean,
        isDocument: isDocument,
        isElement: isElement,
        isEmpty: isEmpty,
        isEqual: isEqual,
        isFocusable: isFocusable,
        isFunction: isFunction,
        isInView: isInView,
        isInput: isInput,
        isNode: isNode,
        isNumber: isNumber,
        isNumeric: isNumeric,
        isObject: isObject,
        isPlainObject: isPlainObject,
        isRtl: isRtl,
        isSameSiteAnchor: isSameSiteAnchor,
        isString: isString,
        isTag: isTag,
        isTouch: isTouch,
        isUndefined: isUndefined,
        isVideo: isVideo,
        isVisible: isVisible,
        isVoidElement: isVoidElement,
        isWindow: isWindow,
        last: last,
        matches: matches,
        memoize: memoize,
        mute: mute,
        noop: noop,
        observeIntersection: observeIntersection,
        observeMutation: observeMutation,
        observeResize: observeResize,
        observeViewportResize: observeViewportResize,
        off: off,
        offset: offset,
        offsetPosition: offsetPosition,
        offsetViewport: offsetViewport,
        on: on,
        once: once,
        overflowParents: overflowParents,
        parent: parent,
        parents: parents,
        pause: pause,
        pick: pick,
        play: play,
        pointInRect: pointInRect,
        pointerCancel: pointerCancel,
        pointerDown: pointerDown,
        pointerEnter: pointerEnter,
        pointerLeave: pointerLeave,
        pointerMove: pointerMove,
        pointerUp: pointerUp,
        position: position,
        positionAt: positionAt,
        prepend: prepend,
        propName: propName,
        query: query,
        queryAll: queryAll,
        ready: ready,
        remove: remove$1,
        removeAttr: removeAttr,
        removeClass: removeClass,
        replaceClass: replaceClass,
        scrollIntoView: scrollIntoView,
        scrollParent: scrollParent,
        scrollParents: scrollParents,
        scrolledOver: scrolledOver,
        selFocusable: selFocusable,
        selInput: selInput,
        sortBy: sortBy,
        startsWith: startsWith,
        sumBy: sumBy,
        swap: swap,
        toArray: toArray,
        toBoolean: toBoolean,
        toEventTargets: toEventTargets,
        toFloat: toFloat,
        toNode: toNode,
        toNodes: toNodes,
        toNumber: toNumber,
        toPx: toPx,
        toWindow: toWindow,
        toggleClass: toggleClass,
        trigger: trigger,
        ucfirst: ucfirst,
        uniqueBy: uniqueBy,
        unwrap: unwrap,
        width: width,
        wrapAll: wrapAll,
        wrapInner: wrapInner
    });

    function initUpdates(instance) {
      instance._data = {};
      instance._updates = [...instance.$options.update || []];
    }
    function prependUpdate(instance, update) {
      instance._updates.unshift(update);
    }
    function clearUpdateData(instance) {
      instance._data = null;
    }
    function callUpdate(instance, e = "update") {
      if (!instance._connected) {
        return;
      }
      if (!instance._updates.length) {
        return;
      }
      if (!instance._queued) {
        instance._queued = /* @__PURE__ */ new Set();
        fastdom.read(() => {
          if (instance._connected) {
            runUpdates(instance, instance._queued);
          }
          instance._queued = null;
        });
      }
      instance._queued.add(e.type || e);
    }
    function runUpdates(instance, types) {
      for (const { read, write, events = [] } of instance._updates) {
        if (!types.has("update") && !events.some((type) => types.has(type))) {
          continue;
        }
        let result;
        if (read) {
          result = read.call(instance, instance._data, types);
          if (result && isPlainObject(result)) {
            assign(instance._data, result);
          }
        }
        if (write && result !== false) {
          fastdom.write(() => {
            if (instance._connected) {
              write.call(instance, instance._data, types);
            }
          });
        }
      }
    }

    function initWatches(instance) {
      instance._watches = [];
      for (const watches of instance.$options.watch || []) {
        for (const [name, watch] of Object.entries(watches)) {
          registerWatch(instance, watch, name);
        }
      }
      instance._initial = true;
    }
    function registerWatch(instance, watch, name) {
      instance._watches.push({
        name,
        ...isPlainObject(watch) ? watch : { handler: watch }
      });
    }
    function runWatches(instance, values) {
      for (const { name, handler, immediate = true } of instance._watches) {
        if (instance._initial && immediate || hasOwn(values, name) && !isEqual(values[name], instance[name])) {
          handler.call(instance, instance[name], values[name]);
        }
      }
      instance._initial = false;
    }

    function initComputed(instance) {
      const { computed } = instance.$options;
      instance._computed = {};
      if (computed) {
        for (const key in computed) {
          registerComputed(instance, key, computed[key]);
        }
      }
    }
    const mutationOptions = { subtree: true, childList: true };
    function registerComputed(instance, key, cb) {
      instance._hasComputed = true;
      Object.defineProperty(instance, key, {
        enumerable: true,
        get() {
          const { _computed, $props, $el } = instance;
          if (!hasOwn(_computed, key)) {
            _computed[key] = (cb.get || cb).call(instance, $props, $el);
            if (cb.observe && instance._computedObserver) {
              const selector = cb.observe.call(instance, $props);
              instance._computedObserver.observe(
                ["~", "+", "-"].includes(selector[0]) ? $el.parentElement : $el.getRootNode(),
                mutationOptions
              );
            }
          }
          return _computed[key];
        },
        set(value) {
          const { _computed } = instance;
          _computed[key] = cb.set ? cb.set.call(instance, value) : value;
          if (isUndefined(_computed[key])) {
            delete _computed[key];
          }
        }
      });
    }
    function initComputedUpdates(instance) {
      if (!instance._hasComputed) {
        return;
      }
      prependUpdate(instance, {
        read: () => runWatches(instance, resetComputed(instance)),
        events: ["resize", "computed"]
      });
      instance._computedObserver = observeMutation(
        instance.$el,
        () => callUpdate(instance, "computed"),
        mutationOptions
      );
    }
    function disconnectComputedUpdates(instance) {
      var _a;
      (_a = instance._computedObserver) == null ? void 0 : _a.disconnect();
      delete instance._computedObserver;
      resetComputed(instance);
    }
    function resetComputed(instance) {
      const values = { ...instance._computed };
      instance._computed = {};
      return values;
    }

    function initEvents(instance) {
      instance._events = [];
      for (const event of instance.$options.events || []) {
        if (hasOwn(event, "handler")) {
          registerEvent(instance, event);
        } else {
          for (const key in event) {
            registerEvent(instance, event[key], key);
          }
        }
      }
    }
    function unbindEvents(instance) {
      instance._events.forEach((unbind) => unbind());
      delete instance._events;
    }
    function registerEvent(instance, event, key) {
      let { name, el, handler, capture, passive, delegate, filter, self } = isPlainObject(event) ? event : { name: key, handler: event };
      el = isFunction(el) ? el.call(instance, instance) : el || instance.$el;
      if (!el || isArray(el) && !el.length || filter && !filter.call(instance, instance)) {
        return;
      }
      instance._events.push(
        on(
          el,
          name,
          delegate ? isString(delegate) ? delegate : delegate.call(instance, instance) : null,
          isString(handler) ? instance[handler] : handler.bind(instance),
          { passive, capture, self }
        )
      );
    }

    function initObservers(instance) {
      instance._observers = [];
      for (const observer of instance.$options.observe || []) {
        registerObservable(instance, observer);
      }
    }
    function registerObserver(instance, ...observer) {
      instance._observers.push(...observer);
    }
    function disconnectObservers(instance) {
      for (const observer of instance._observers) {
        observer.disconnect();
      }
    }
    function registerObservable(instance, observable) {
      let { observe, target = instance.$el, handler, options, filter, args } = observable;
      if (filter && !filter.call(instance, instance)) {
        return;
      }
      const key = `_observe${instance._observers.length}`;
      if (isFunction(target) && !hasOwn(instance, key)) {
        registerComputed(instance, key, () => {
          const targets2 = target.call(instance, instance);
          return isArray(targets2) ? toNodes(targets2) : targets2;
        });
      }
      handler = isString(handler) ? instance[handler] : handler.bind(instance);
      if (isFunction(options)) {
        options = options.call(instance, instance);
      }
      const targets = hasOwn(instance, key) ? instance[key] : target;
      const observer = observe(targets, handler, options, args);
      if (isFunction(target) && isArray(instance[key])) {
        registerWatch(
          instance,
          { handler: updateTargets(observer, options), immediate: false },
          key
        );
      }
      registerObserver(instance, observer);
    }
    function updateTargets(observer, options) {
      return (targets, prev) => {
        for (const target of prev) {
          if (!includes(targets, target)) {
            if (observer.unobserve) {
              observer.unobserve(target);
            } else if (observer.observe) {
              observer.disconnect();
            }
          }
        }
        for (const target of targets) {
          if (!includes(prev, target) || !observer.unobserve) {
            observer.observe(target, options);
          }
        }
      };
    }

    const strats = {};
    strats.events = strats.watch = strats.observe = strats.created = strats.beforeConnect = strats.connected = strats.beforeDisconnect = strats.disconnected = strats.destroy = concatStrat;
    strats.args = function(parentVal, childVal) {
      return childVal !== false && concatStrat(childVal || parentVal);
    };
    strats.update = function(parentVal, childVal) {
      return sortBy(
        concatStrat(parentVal, isFunction(childVal) ? { read: childVal } : childVal),
        "order"
      );
    };
    strats.props = function(parentVal, childVal) {
      if (isArray(childVal)) {
        const value = {};
        for (const key of childVal) {
          value[key] = String;
        }
        childVal = value;
      }
      return strats.methods(parentVal, childVal);
    };
    strats.computed = strats.methods = function(parentVal, childVal) {
      return childVal ? parentVal ? { ...parentVal, ...childVal } : childVal : parentVal;
    };
    strats.i18n = strats.data = function(parentVal, childVal, vm) {
      if (!vm) {
        if (!childVal) {
          return parentVal;
        }
        if (!parentVal) {
          return childVal;
        }
        return function(vm2) {
          return mergeFnData(parentVal, childVal, vm2);
        };
      }
      return mergeFnData(parentVal, childVal, vm);
    };
    function mergeFnData(parentVal, childVal, vm) {
      return strats.computed(
        isFunction(parentVal) ? parentVal.call(vm, vm) : parentVal,
        isFunction(childVal) ? childVal.call(vm, vm) : childVal
      );
    }
    function concatStrat(parentVal, childVal) {
      parentVal = parentVal && !isArray(parentVal) ? [parentVal] : parentVal;
      return childVal ? parentVal ? parentVal.concat(childVal) : isArray(childVal) ? childVal : [childVal] : parentVal;
    }
    function defaultStrat(parentVal, childVal) {
      return isUndefined(childVal) ? parentVal : childVal;
    }
    function mergeOptions(parent, child, vm) {
      const options = {};
      if (isFunction(child)) {
        child = child.options;
      }
      if (child.extends) {
        parent = mergeOptions(parent, child.extends, vm);
      }
      if (child.mixins) {
        for (const mixin of child.mixins) {
          parent = mergeOptions(parent, mixin, vm);
        }
      }
      for (const key in parent) {
        mergeKey(key);
      }
      for (const key in child) {
        if (!hasOwn(parent, key)) {
          mergeKey(key);
        }
      }
      function mergeKey(key) {
        options[key] = (strats[key] || defaultStrat)(parent[key], child[key], vm);
      }
      return options;
    }
    function parseOptions(options, args = []) {
      try {
        return options ? startsWith(options, "{") ? JSON.parse(options) : args.length && !includes(options, ":") ? { [args[0]]: options } : options.split(";").reduce((options2, option) => {
          const [key, value] = option.split(/:(.*)/);
          if (key && !isUndefined(value)) {
            options2[key.trim()] = value.trim();
          }
          return options2;
        }, {}) : {};
      } catch (e) {
        return {};
      }
    }
    function coerce$1(type, value) {
      if (type === Boolean) {
        return toBoolean(value);
      } else if (type === Number) {
        return toNumber(value);
      } else if (type === "list") {
        return toList(value);
      } else if (type === Object && isString(value)) {
        return parseOptions(value);
      }
      return type ? type(value) : value;
    }
    const listRe = /,(?![^(]*\))/;
    function toList(value) {
      return isArray(value) ? value : isString(value) ? value.split(listRe).map((value2) => isNumeric(value2) ? toNumber(value2) : toBoolean(value2.trim())) : [value];
    }

    function initProps(instance) {
      const { $options, $props } = instance;
      const props = getProps($options);
      assign($props, props);
      const { computed, methods } = $options;
      for (let key in $props) {
        if (key in props && (!computed || !hasOwn(computed, key)) && (!methods || !hasOwn(methods, key))) {
          instance[key] = $props[key];
        }
      }
    }
    function getProps(opts) {
      const data$1 = {};
      const { args = [], props = {}, el, id } = opts;
      if (!props) {
        return data$1;
      }
      for (const key in props) {
        const prop = hyphenate(key);
        let value = data(el, prop);
        if (isUndefined(value)) {
          continue;
        }
        value = props[key] === Boolean && value === "" ? true : coerce$1(props[key], value);
        if (prop === "target" && startsWith(value, "_")) {
          continue;
        }
        data$1[key] = value;
      }
      const options = parseOptions(data(el, id), args);
      for (const key in options) {
        const prop = camelize(key);
        if (!isUndefined(props[prop])) {
          data$1[prop] = coerce$1(props[prop], options[key]);
        }
      }
      return data$1;
    }
    const getAttributes = memoize((id, props) => {
      const attributes = Object.keys(props);
      const filter = attributes.concat(id).map((key) => [hyphenate(key), `data-${hyphenate(key)}`]).flat();
      return { attributes, filter };
    });
    function initPropsObserver(instance) {
      const { $options, $props } = instance;
      const { id, props, el } = $options;
      if (!props) {
        return;
      }
      const { attributes, filter } = getAttributes(id, props);
      const observer = new MutationObserver((records) => {
        const data = getProps($options);
        if (records.some(({ attributeName }) => {
          const prop = attributeName.replace("data-", "");
          return (prop === id ? attributes : [camelize(prop), camelize(attributeName)]).some(
            (prop2) => !isUndefined(data[prop2]) && data[prop2] !== $props[prop2]
          );
        })) {
          instance.$reset();
        }
      });
      observer.observe(el, {
        attributes: true,
        attributeFilter: filter
      });
      registerObserver(instance, observer);
    }

    function callHook(instance, hook) {
      var _a;
      (_a = instance.$options[hook]) == null ? void 0 : _a.forEach((handler) => handler.call(instance));
    }
    function callConnected(instance) {
      if (instance._connected) {
        return;
      }
      initProps(instance);
      callHook(instance, "beforeConnect");
      instance._connected = true;
      initEvents(instance);
      initUpdates(instance);
      initWatches(instance);
      initObservers(instance);
      initPropsObserver(instance);
      initComputedUpdates(instance);
      callHook(instance, "connected");
      callUpdate(instance);
    }
    function callDisconnected(instance) {
      if (!instance._connected) {
        return;
      }
      callHook(instance, "beforeDisconnect");
      unbindEvents(instance);
      clearUpdateData(instance);
      disconnectObservers(instance);
      disconnectComputedUpdates(instance);
      callHook(instance, "disconnected");
      instance._connected = false;
    }

    let uid = 0;
    function init$1(instance, options = {}) {
      options.data = normalizeData(options, instance.constructor.options);
      instance.$options = mergeOptions(instance.constructor.options, options, instance);
      instance.$props = {};
      instance._uid = uid++;
      initData(instance);
      initMethods(instance);
      initComputed(instance);
      callHook(instance, "created");
      if (options.el) {
        instance.$mount(options.el);
      }
    }
    function initData(instance) {
      const { data = {} } = instance.$options;
      for (const key in data) {
        instance.$props[key] = instance[key] = data[key];
      }
    }
    function initMethods(instance) {
      const { methods } = instance.$options;
      if (methods) {
        for (const key in methods) {
          instance[key] = methods[key].bind(instance);
        }
      }
    }
    function normalizeData({ data = {} }, { args = [], props = {} }) {
      if (isArray(data)) {
        data = data.slice(0, args.length).reduce((data2, value, index) => {
          if (isPlainObject(value)) {
            assign(data2, value);
          } else {
            data2[args[index]] = value;
          }
          return data2;
        }, {});
      }
      for (const key in data) {
        if (isUndefined(data[key])) {
          delete data[key];
        } else if (props[key]) {
          data[key] = coerce$1(props[key], data[key]);
        }
      }
      return data;
    }

    const App = function(options) {
      init$1(this, options);
    };
    App.util = util;
    App.options = {};
    App.version = "3.21.6";

    const PREFIX = "uk-";
    const DATA = "__uikit__";
    const components$1 = {};
    function component(name, options) {
      var _a, _b;
      const id = PREFIX + hyphenate(name);
      if (!options) {
        if (!components$1[id].options) {
          components$1[id] = App.extend(components$1[id]);
        }
        return components$1[id];
      }
      name = camelize(name);
      App[name] = (element, data) => createComponent(name, element, data);
      const opt = (_a = options.options) != null ? _a : { ...options };
      opt.id = id;
      opt.name = name;
      (_b = opt.install) == null ? void 0 : _b.call(opt, App, opt, name);
      if (App._initialized && !opt.functional) {
        requestAnimationFrame(() => createComponent(name, `[${id}],[data-${id}]`));
      }
      return components$1[id] = opt;
    }
    function createComponent(name, element, data, ...args) {
      const Component = component(name);
      return Component.options.functional ? new Component({ data: isPlainObject(element) ? element : [element, data, ...args] }) : element ? findAll(element).map(init)[0] : init();
      function init(element2) {
        const instance = getComponent(element2, name);
        if (instance) {
          if (data) {
            instance.$destroy();
          } else {
            return instance;
          }
        }
        return new Component({ el: element2, data });
      }
    }
    function getComponents(element) {
      return (element == null ? void 0 : element[DATA]) || {};
    }
    function getComponent(element, name) {
      return getComponents(element)[name];
    }
    function attachToElement(element, instance) {
      if (!element[DATA]) {
        element[DATA] = {};
      }
      element[DATA][instance.$options.name] = instance;
    }
    function detachFromElement(element, instance) {
      var _a;
      (_a = element[DATA]) == null ? true : delete _a[instance.$options.name];
      if (isEmpty(element[DATA])) {
        delete element[DATA];
      }
    }

    function boot(App) {
      if (inBrowser && window.MutationObserver) {
        if (document.body) {
          requestAnimationFrame(() => init(App));
        } else {
          new MutationObserver((records, observer) => {
            if (document.body) {
              init(App);
              observer.disconnect();
            }
          }).observe(document.documentElement, { childList: true });
        }
      }
    }
    function init(App) {
      trigger(document, "uikit:init", App);
      if (document.body) {
        apply(document.body, connect);
      }
      new MutationObserver((records) => records.forEach(applyChildListMutation)).observe(document, {
        subtree: true,
        childList: true
      });
      new MutationObserver((records) => records.forEach(applyAttributeMutation)).observe(document, {
        subtree: true,
        attributes: true
      });
      App._initialized = true;
    }
    function applyChildListMutation({ addedNodes, removedNodes }) {
      for (const node of addedNodes) {
        apply(node, connect);
      }
      for (const node of removedNodes) {
        apply(node, disconnect);
      }
    }
    function applyAttributeMutation({ target, attributeName }) {
      var _a;
      const name = getComponentName(attributeName);
      if (name) {
        if (hasAttr(target, attributeName)) {
          createComponent(name, target);
        } else {
          (_a = getComponent(target, name)) == null ? void 0 : _a.$destroy();
        }
      }
    }
    function connect(node) {
      const components2 = getComponents(node);
      for (const name in components2) {
        callConnected(components2[name]);
      }
      for (const attributeName of node.getAttributeNames()) {
        const name = getComponentName(attributeName);
        name && createComponent(name, node);
      }
    }
    function disconnect(node) {
      const components2 = getComponents(node);
      for (const name in components2) {
        callDisconnected(components2[name]);
      }
    }
    function getComponentName(attribute) {
      if (startsWith(attribute, "data-")) {
        attribute = attribute.slice(5);
      }
      const cmp = components$1[attribute];
      return cmp && (cmp.options || cmp).name;
    }

    function globalApi(App) {
      App.component = component;
      App.getComponents = getComponents;
      App.getComponent = getComponent;
      App.update = update;
      App.use = function(plugin) {
        if (plugin.installed) {
          return;
        }
        plugin.call(null, this);
        plugin.installed = true;
        return this;
      };
      App.mixin = function(mixin, component2) {
        component2 = (isString(component2) ? this.component(component2) : component2) || this;
        component2.options = mergeOptions(component2.options, mixin);
      };
      App.extend = function(options) {
        options || (options = {});
        const Super = this;
        const Sub = function UIkitComponent(options2) {
          init$1(this, options2);
        };
        Sub.prototype = Object.create(Super.prototype);
        Sub.prototype.constructor = Sub;
        Sub.options = mergeOptions(Super.options, options);
        Sub.super = Super;
        Sub.extend = Super.extend;
        return Sub;
      };
      let container;
      Object.defineProperty(App, "container", {
        get() {
          return container || document.body;
        },
        set(element) {
          container = $(element);
        }
      });
    }
    function update(element, e) {
      element = element ? toNode(element) : document.body;
      for (const parentEl of parents(element).reverse()) {
        updateElement(parentEl, e);
      }
      apply(element, (element2) => updateElement(element2, e));
    }
    function updateElement(element, e) {
      const components = getComponents(element);
      for (const name in components) {
        callUpdate(components[name], e);
      }
    }

    function instanceApi(App) {
      App.prototype.$mount = function(el) {
        const instance = this;
        attachToElement(el, instance);
        instance.$options.el = el;
        if (document.contains(el)) {
          callConnected(instance);
        }
      };
      App.prototype.$destroy = function(removeEl = false) {
        const instance = this;
        const { el } = instance.$options;
        if (el) {
          callDisconnected(instance);
        }
        callHook(instance, "destroy");
        detachFromElement(el, instance);
        if (removeEl) {
          remove$1(instance.$el);
        }
      };
      App.prototype.$create = createComponent;
      App.prototype.$emit = function(e) {
        callUpdate(this, e);
      };
      App.prototype.$update = function(element = this.$el, e) {
        update(element, e);
      };
      App.prototype.$reset = function() {
        callDisconnected(this);
        callConnected(this);
      };
      App.prototype.$getComponent = getComponent;
      Object.defineProperties(App.prototype, {
        $el: {
          get() {
            return this.$options.el;
          }
        },
        $container: Object.getOwnPropertyDescriptor(App, "container")
      });
    }
    let id = 1;
    function generateId(instance, el = null) {
      return (el == null ? void 0 : el.id) || `${instance.$options.id}-${id++}`;
    }

    globalApi(App);
    instanceApi(App);

    function resize(options) {
      return observe(observeResize, options, "resize");
    }
    function intersection(options) {
      return observe(observeIntersection, options);
    }
    function mutation(options) {
      return observe(observeMutation, options);
    }
    function lazyload(options = {}) {
      return intersection({
        handler: function(entries, observer) {
          const { targets = this.$el, preload = 5 } = options;
          for (const el of toNodes(isFunction(targets) ? targets(this) : targets)) {
            $$('[loading="lazy"]', el).slice(0, preload - 1).forEach((el2) => removeAttr(el2, "loading"));
          }
          for (const el of entries.filter(({ isIntersecting }) => isIntersecting).map(({ target }) => target)) {
            observer.unobserve(el);
          }
        },
        ...options
      });
    }
    function viewport(options) {
      return observe((target, handler) => observeViewportResize(handler), options, "resize");
    }
    function scroll$1(options) {
      return observe(
        (target, handler) => ({
          disconnect: on(toScrollTargets(target), "scroll", handler, { passive: true })
        }),
        options,
        "scroll"
      );
    }
    function swipe(options) {
      return {
        observe(target, handler) {
          return {
            observe: noop,
            unobserve: noop,
            disconnect: on(target, pointerDown, handler, { passive: true })
          };
        },
        handler(e) {
          if (!isTouch(e)) {
            return;
          }
          const pos = getEventPos(e);
          const target = "tagName" in e.target ? e.target : parent(e.target);
          once(document, `${pointerUp} ${pointerCancel} scroll`, (e2) => {
            const { x, y } = getEventPos(e2);
            if (e2.type !== "scroll" && target && x && Math.abs(pos.x - x) > 100 || y && Math.abs(pos.y - y) > 100) {
              setTimeout(() => {
                trigger(target, "swipe");
                trigger(target, `swipe${swipeDirection(pos.x, pos.y, x, y)}`);
              });
            }
          });
        },
        ...options
      };
    }
    function observe(observe2, options, emit) {
      return {
        observe: observe2,
        handler() {
          callUpdate(this, emit);
        },
        ...options
      };
    }
    function swipeDirection(x1, y1, x2, y2) {
      return Math.abs(x1 - x2) >= Math.abs(y1 - y2) ? x1 - x2 > 0 ? "Left" : "Right" : y1 - y2 > 0 ? "Up" : "Down";
    }
    function toScrollTargets(elements) {
      return toNodes(elements).map((node) => {
        const { ownerDocument } = node;
        const parent2 = scrollParent(node, true);
        return parent2 === ownerDocument.scrollingElement ? ownerDocument : parent2;
      });
    }

    var Class = {
      connected() {
        addClass(this.$el, this.$options.id);
      }
    };

    var Togglable = {
      props: {
        cls: Boolean,
        animation: "list",
        duration: Number,
        velocity: Number,
        origin: String,
        transition: String
      },
      data: {
        cls: false,
        animation: [false],
        duration: 200,
        velocity: 0.2,
        origin: false,
        transition: "ease",
        clsEnter: "uk-togglable-enter",
        clsLeave: "uk-togglable-leave"
      },
      computed: {
        hasAnimation: ({ animation }) => !!animation[0],
        hasTransition: ({ animation }) => ["slide", "reveal"].some((transition) => startsWith(animation[0], transition))
      },
      methods: {
        async toggleElement(targets, toggle, animate) {
          try {
            await Promise.all(
              toNodes(targets).map((el) => {
                const show = isBoolean(toggle) ? toggle : !this.isToggled(el);
                if (!trigger(el, `before${show ? "show" : "hide"}`, [this])) {
                  return Promise.reject();
                }
                const promise = (isFunction(animate) ? animate : animate === false || !this.hasAnimation ? toggleInstant : this.hasTransition ? toggleTransition : toggleAnimation)(el, show, this);
                const cls = show ? this.clsEnter : this.clsLeave;
                addClass(el, cls);
                trigger(el, show ? "show" : "hide", [this]);
                const done = () => {
                  removeClass(el, cls);
                  trigger(el, show ? "shown" : "hidden", [this]);
                };
                return promise ? promise.then(done, () => {
                  removeClass(el, cls);
                  return Promise.reject();
                }) : done();
              })
            );
            return true;
          } catch (e) {
            return false;
          }
        },
        isToggled(el = this.$el) {
          el = toNode(el);
          return hasClass(el, this.clsEnter) ? true : hasClass(el, this.clsLeave) ? false : this.cls ? hasClass(el, this.cls.split(" ")[0]) : isVisible(el);
        },
        _toggle(el, toggled) {
          if (!el) {
            return;
          }
          toggled = Boolean(toggled);
          let changed;
          if (this.cls) {
            changed = includes(this.cls, " ") || toggled !== hasClass(el, this.cls);
            changed && toggleClass(el, this.cls, includes(this.cls, " ") ? void 0 : toggled);
          } else {
            changed = toggled === el.hidden;
            changed && (el.hidden = !toggled);
          }
          $$("[autofocus]", el).some((el2) => isVisible(el2) ? el2.focus() || true : el2.blur());
          if (changed) {
            trigger(el, "toggled", [toggled, this]);
          }
        }
      }
    };
    function toggleInstant(el, show, { _toggle }) {
      Animation.cancel(el);
      Transition.cancel(el);
      return _toggle(el, show);
    }
    async function toggleTransition(el, show, { animation, duration, velocity, transition, _toggle }) {
      var _a;
      const [mode = "reveal", startProp = "top"] = ((_a = animation[0]) == null ? void 0 : _a.split("-")) || [];
      const dirs = [
        ["left", "right"],
        ["top", "bottom"]
      ];
      const dir = dirs[includes(dirs[0], startProp) ? 0 : 1];
      const end = dir[1] === startProp;
      const props = ["width", "height"];
      const dimProp = props[dirs.indexOf(dir)];
      const marginProp = `margin-${dir[0]}`;
      const marginStartProp = `margin-${startProp}`;
      let currentDim = dimensions(el)[dimProp];
      const inProgress = Transition.inProgress(el);
      await Transition.cancel(el);
      if (show) {
        _toggle(el, true);
      }
      const prevProps = Object.fromEntries(
        [
          "padding",
          "border",
          "width",
          "height",
          "minWidth",
          "minHeight",
          "overflowY",
          "overflowX",
          marginProp,
          marginStartProp
        ].map((key) => [key, el.style[key]])
      );
      const dim = dimensions(el);
      const currentMargin = toFloat(css(el, marginProp));
      const marginStart = toFloat(css(el, marginStartProp));
      const endDim = dim[dimProp] + marginStart;
      if (!inProgress && !show) {
        currentDim += marginStart;
      }
      const [wrapper] = wrapInner(el, "<div>");
      css(wrapper, {
        boxSizing: "border-box",
        height: dim.height,
        width: dim.width,
        ...css(el, [
          "overflow",
          "padding",
          "borderTop",
          "borderRight",
          "borderBottom",
          "borderLeft",
          "borderImage",
          marginStartProp
        ])
      });
      css(el, {
        padding: 0,
        border: 0,
        minWidth: 0,
        minHeight: 0,
        [marginStartProp]: 0,
        width: dim.width,
        height: dim.height,
        overflow: "hidden",
        [dimProp]: currentDim
      });
      const percent = currentDim / endDim;
      duration = (velocity * endDim + duration) * (show ? 1 - percent : percent);
      const endProps = { [dimProp]: show ? endDim : 0 };
      if (end) {
        css(el, marginProp, endDim - currentDim + currentMargin);
        endProps[marginProp] = show ? currentMargin : endDim + currentMargin;
      }
      if (!end ^ mode === "reveal") {
        css(wrapper, marginProp, -endDim + currentDim);
        Transition.start(wrapper, { [marginProp]: show ? 0 : -endDim }, duration, transition);
      }
      try {
        await Transition.start(el, endProps, duration, transition);
      } finally {
        css(el, prevProps);
        unwrap(wrapper.firstChild);
        if (!show) {
          _toggle(el, false);
        }
      }
    }
    function toggleAnimation(el, show, cmp) {
      const { animation, duration, _toggle } = cmp;
      if (show) {
        _toggle(el, true);
        return Animation.in(el, animation[0], duration, cmp.origin);
      }
      return Animation.out(el, animation[1] || animation[0], duration, cmp.origin).then(
        () => _toggle(el, false)
      );
    }

    const keyMap = {
      TAB: 9,
      ESC: 27,
      SPACE: 32,
      END: 35,
      HOME: 36,
      LEFT: 37,
      UP: 38,
      RIGHT: 39,
      DOWN: 40
    };

    var Accordion = {
      mixins: [Class, Togglable],
      props: {
        animation: Boolean,
        targets: String,
        active: null,
        collapsible: Boolean,
        multiple: Boolean,
        toggle: String,
        content: String,
        offset: Number
      },
      data: {
        targets: "> *",
        active: false,
        animation: true,
        collapsible: true,
        multiple: false,
        clsOpen: "uk-open",
        toggle: "> .uk-accordion-title",
        content: "> .uk-accordion-content",
        offset: 0
      },
      computed: {
        items: ({ targets }, $el) => $$(targets, $el),
        toggles({ toggle }) {
          return this.items.map((item) => $(toggle, item));
        },
        contents({ content }) {
          return this.items.map((item) => {
            var _a;
            return ((_a = item._wrapper) == null ? void 0 : _a.firstElementChild) || $(content, item);
          });
        }
      },
      watch: {
        items(items, prev) {
          if (prev || hasClass(items, this.clsOpen)) {
            return;
          }
          const active = this.active !== false && items[Number(this.active)] || !this.collapsible && items[0];
          if (active) {
            this.toggle(active, false);
          }
        },
        toggles() {
          this.$emit();
        },
        contents(items) {
          for (const el of items) {
            const isOpen = hasClass(
              this.items.find((item) => item.contains(el)),
              this.clsOpen
            );
            hide(el, !isOpen);
          }
          this.$emit();
        }
      },
      observe: lazyload(),
      events: [
        {
          name: "click keydown",
          delegate: ({ targets, $props }) => `${targets} ${$props.toggle}`,
          async handler(e) {
            var _a;
            if (e.type === "keydown" && e.keyCode !== keyMap.SPACE) {
              return;
            }
            e.preventDefault();
            (_a = this._off) == null ? void 0 : _a.call(this);
            this._off = keepScrollPosition(e.target);
            await this.toggle(index(this.toggles, e.current));
            this._off();
          }
        },
        {
          name: "shown hidden",
          self: true,
          delegate: ({ targets }) => targets,
          handler() {
            this.$emit();
          }
        }
      ],
      update() {
        const activeItems = filter(this.items, `.${this.clsOpen}`);
        for (const index2 in this.items) {
          const toggle = this.toggles[index2];
          const content = this.contents[index2];
          if (!toggle || !content) {
            continue;
          }
          toggle.id = generateId(this, toggle);
          content.id = generateId(this, content);
          const active = includes(activeItems, this.items[index2]);
          attr(toggle, {
            role: isTag(toggle, "a") ? "button" : null,
            "aria-controls": content.id,
            "aria-expanded": active,
            "aria-disabled": !this.collapsible && activeItems.length < 2 && active
          });
          attr(content, { role: "region", "aria-labelledby": toggle.id });
          if (isTag(content, "ul")) {
            attr(children(content), "role", "presentation");
          }
        }
      },
      methods: {
        toggle(item, animate) {
          item = this.items[getIndex(item, this.items)];
          let items = [item];
          const activeItems = filter(this.items, `.${this.clsOpen}`);
          if (!this.multiple && !includes(activeItems, items[0])) {
            items = items.concat(activeItems);
          }
          if (!this.collapsible && activeItems.length < 2 && includes(activeItems, item)) {
            return;
          }
          return Promise.all(
            items.map(
              (el) => this.toggleElement(el, !includes(activeItems, el), (el2, show) => {
                toggleClass(el2, this.clsOpen, show);
                if (animate === false || !this.animation) {
                  hide($(this.content, el2), !show);
                  return;
                }
                return transition(el2, show, this);
              })
            )
          );
        }
      }
    };
    function hide(el, hide2) {
      el && (el.hidden = hide2);
    }
    async function transition(el, show, { content, duration, velocity, transition: transition2 }) {
      var _a;
      content = ((_a = el._wrapper) == null ? void 0 : _a.firstElementChild) || $(content, el);
      if (!el._wrapper) {
        el._wrapper = wrapAll(content, "<div>");
      }
      const wrapper = el._wrapper;
      css(wrapper, "overflow", "hidden");
      const currentHeight = toFloat(css(wrapper, "height"));
      await Transition.cancel(wrapper);
      hide(content, false);
      const endHeight = sumBy(["marginTop", "marginBottom"], (prop) => css(content, prop)) + dimensions(content).height;
      const percent = currentHeight / endHeight;
      duration = (velocity * endHeight + duration) * (show ? 1 - percent : percent);
      css(wrapper, "height", currentHeight);
      await Transition.start(wrapper, { height: show ? endHeight : 0 }, duration, transition2);
      unwrap(content);
      delete el._wrapper;
      if (!show) {
        hide(content, true);
      }
    }
    function keepScrollPosition(el) {
      const scrollElement = scrollParent(el, true);
      let frame;
      (function scroll() {
        frame = requestAnimationFrame(() => {
          const { top } = dimensions(el);
          if (top < 0) {
            scrollElement.scrollTop += top;
          }
          scroll();
        });
      })();
      return () => requestAnimationFrame(() => cancelAnimationFrame(frame));
    }

    var alert = {
      mixins: [Class, Togglable],
      args: "animation",
      props: {
        animation: Boolean,
        close: String
      },
      data: {
        animation: true,
        selClose: ".uk-alert-close",
        duration: 150
      },
      events: {
        name: "click",
        delegate: ({ selClose }) => selClose,
        handler(e) {
          e.preventDefault();
          this.close();
        }
      },
      methods: {
        async close() {
          await this.toggleElement(this.$el, false, animate$1);
          this.$destroy(true);
        }
      }
    };
    function animate$1(el, show, { duration, transition, velocity }) {
      const height = toFloat(css(el, "height"));
      css(el, "height", height);
      return Transition.start(
        el,
        {
          height: 0,
          marginTop: 0,
          marginBottom: 0,
          paddingTop: 0,
          paddingBottom: 0,
          borderTop: 0,
          borderBottom: 0,
          opacity: 0
        },
        velocity * height + duration,
        transition
      );
    }

    var Video = {
      args: "autoplay",
      props: {
        automute: Boolean,
        autoplay: Boolean
      },
      data: {
        automute: false,
        autoplay: true
      },
      beforeConnect() {
        if (this.autoplay === "inview" && !hasAttr(this.$el, "preload")) {
          this.$el.preload = "none";
        }
        if (isTag(this.$el, "iframe") && !hasAttr(this.$el, "allow")) {
          this.$el.allow = "autoplay";
        }
        if (this.autoplay === "hover") {
          if (isTag(this.$el, "video")) {
            this.$el.tabindex = 0;
          } else {
            this.autoplay = true;
          }
        }
        if (this.automute) {
          mute(this.$el);
        }
      },
      events: [
        {
          name: `${pointerEnter} focusin`,
          filter: ({ autoplay }) => includes(autoplay, "hover"),
          handler(e) {
            if (!isTouch(e) || !isPlaying(this.$el)) {
              play(this.$el);
            } else {
              pause(this.$el);
            }
          }
        },
        {
          name: `${pointerLeave} focusout`,
          filter: ({ autoplay }) => includes(autoplay, "hover"),
          handler(e) {
            if (!isTouch(e)) {
              pause(this.$el);
            }
          }
        }
      ],
      observe: [
        intersection({
          filter: ({ $el, autoplay }) => autoplay && autoplay !== "hover" && isVideo($el),
          handler([{ isIntersecting }]) {
            if (!document.fullscreenElement) {
              if (isIntersecting) {
                play(this.$el);
              } else {
                pause(this.$el);
              }
            }
          },
          args: { intersecting: false },
          options: ({ $el, autoplay }) => ({ root: autoplay === "inview" ? null : parent($el) })
        })
      ]
    };
    function isPlaying(videoEl) {
      return !videoEl.paused && !videoEl.ended;
    }

    var cover = {
      mixins: [Video],
      props: {
        width: Number,
        height: Number
      },
      data: {
        automute: true
      },
      created() {
        this.useObjectFit = isTag(this.$el, "img", "video");
      },
      observe: resize({
        target: ({ $el }) => getPositionedParent($el) || parent($el),
        filter: ({ useObjectFit }) => !useObjectFit
      }),
      update: {
        read() {
          if (this.useObjectFit) {
            return false;
          }
          const { ratio, cover } = Dimensions;
          const { $el, width, height } = this;
          let dim = { width, height };
          if (!width || !height) {
            const intrinsic = {
              width: $el.naturalWidth || $el.videoWidth || $el.clientWidth,
              height: $el.naturalHeight || $el.videoHeight || $el.clientHeight
            };
            if (width) {
              dim = ratio(intrinsic, "width", width);
            } else if (height) {
              dim = ratio(intrinsic, "height", height);
            } else {
              dim = intrinsic;
            }
          }
          const { offsetHeight: coverHeight, offsetWidth: coverWidth } = getPositionedParent($el) || parent($el);
          const coverDim = cover(dim, { width: coverWidth, height: coverHeight });
          if (!coverDim.width || !coverDim.height) {
            return false;
          }
          return coverDim;
        },
        write({ height, width }) {
          css(this.$el, { height, width });
        },
        events: ["resize"]
      }
    };
    function getPositionedParent(el) {
      while (el = parent(el)) {
        if (css(el, "position") !== "static") {
          return el;
        }
      }
    }

    var Container = {
      props: {
        container: Boolean
      },
      data: {
        container: true
      },
      computed: {
        container({ container }) {
          return container === true && this.$container || container && $(container);
        }
      }
    };

    var Position = {
      props: {
        pos: String,
        offset: Boolean,
        flip: Boolean,
        shift: Boolean,
        inset: Boolean
      },
      data: {
        pos: `bottom-${isRtl ? "right" : "left"}`,
        offset: false,
        flip: true,
        shift: true,
        inset: false
      },
      connected() {
        this.pos = this.$props.pos.split("-").concat("center").slice(0, 2);
        [this.dir, this.align] = this.pos;
        this.axis = includes(["top", "bottom"], this.dir) ? "y" : "x";
      },
      methods: {
        positionAt(element, target, boundary) {
          let offset = [this.getPositionOffset(element), this.getShiftOffset(element)];
          const placement = [this.flip && "flip", this.shift && "shift"];
          const attach = {
            element: [this.inset ? this.dir : flipPosition(this.dir), this.align],
            target: [this.dir, this.align]
          };
          if (this.axis === "y") {
            for (const prop in attach) {
              attach[prop].reverse();
            }
            offset.reverse();
            placement.reverse();
          }
          const restoreScrollPosition = storeScrollPosition(element);
          const elDim = dimensions(element);
          css(element, { top: -elDim.height, left: -elDim.width });
          positionAt(element, target, {
            attach,
            offset,
            boundary,
            placement,
            viewportOffset: this.getViewportOffset(element)
          });
          restoreScrollPosition();
        },
        getPositionOffset(element = this.$el) {
          return toPx(
            this.offset === false ? css(element, "--uk-position-offset") : this.offset,
            this.axis === "x" ? "width" : "height",
            element
          ) * (includes(["left", "top"], this.dir) ? -1 : 1) * (this.inset ? -1 : 1);
        },
        getShiftOffset(element = this.$el) {
          return this.align === "center" ? 0 : toPx(
            css(element, "--uk-position-shift-offset"),
            this.axis === "y" ? "width" : "height",
            element
          ) * (includes(["left", "top"], this.align) ? 1 : -1);
        },
        getViewportOffset(element) {
          return toPx(css(element, "--uk-position-viewport-offset"));
        }
      }
    };
    function storeScrollPosition(element) {
      const scrollElement = scrollParent(element);
      const { scrollTop } = scrollElement;
      return () => {
        if (scrollTop !== scrollElement.scrollTop) {
          scrollElement.scrollTop = scrollTop;
        }
      };
    }

    let prevented;
    function preventBackgroundScroll(el) {
      const off = on(
        el,
        "touchstart",
        (e) => {
          if (e.targetTouches.length !== 1 || matches(e.target, 'input[type="range"')) {
            return;
          }
          let prev = getEventPos(e).y;
          const offMove = on(
            el,
            "touchmove",
            (e2) => {
              const pos = getEventPos(e2).y;
              if (pos === prev) {
                return;
              }
              prev = pos;
              if (!scrollParents(e2.target).some((scrollParent) => {
                if (!el.contains(scrollParent)) {
                  return false;
                }
                let { scrollHeight, clientHeight } = scrollParent;
                return clientHeight < scrollHeight;
              })) {
                e2.preventDefault();
              }
            },
            { passive: false }
          );
          once(el, "scroll touchend touchcanel", offMove, { capture: true });
        },
        { passive: true }
      );
      if (prevented) {
        return off;
      }
      prevented = true;
      const { scrollingElement } = document;
      css(scrollingElement, {
        overflowY: CSS.supports("overflow", "clip") ? "clip" : "hidden",
        touchAction: "none",
        paddingRight: width(window) - scrollingElement.clientWidth || ""
      });
      return () => {
        prevented = false;
        off();
        css(scrollingElement, { overflowY: "", touchAction: "", paddingRight: "" });
      };
    }

    let active$1;
    var drop = {
      mixins: [Container, Position, Togglable],
      args: "pos",
      props: {
        mode: "list",
        toggle: Boolean,
        boundary: Boolean,
        boundaryX: Boolean,
        boundaryY: Boolean,
        target: Boolean,
        targetX: Boolean,
        targetY: Boolean,
        stretch: Boolean,
        delayShow: Number,
        delayHide: Number,
        autoUpdate: Boolean,
        clsDrop: String,
        animateOut: Boolean,
        bgScroll: Boolean,
        closeOnScroll: Boolean
      },
      data: {
        mode: ["click", "hover"],
        toggle: "- *",
        boundary: false,
        boundaryX: false,
        boundaryY: false,
        target: false,
        targetX: false,
        targetY: false,
        stretch: false,
        delayShow: 0,
        delayHide: 800,
        autoUpdate: true,
        clsDrop: false,
        animateOut: false,
        bgScroll: true,
        animation: ["uk-animation-fade"],
        cls: "uk-open",
        container: false,
        closeOnScroll: false
      },
      computed: {
        boundary({ boundary, boundaryX, boundaryY }, $el) {
          return [
            query(boundaryX || boundary, $el) || window,
            query(boundaryY || boundary, $el) || window
          ];
        },
        target({ target, targetX, targetY }, $el) {
          targetX || (targetX = target || this.targetEl);
          targetY || (targetY = target || this.targetEl);
          return [
            targetX === true ? window : query(targetX, $el),
            targetY === true ? window : query(targetY, $el)
          ];
        }
      },
      created() {
        this.tracker = new MouseTracker();
      },
      beforeConnect() {
        this.clsDrop = this.$props.clsDrop || this.$options.id;
      },
      connected() {
        addClass(this.$el, "uk-drop", this.clsDrop);
        if (this.toggle && !this.targetEl) {
          this.targetEl = createToggleComponent(this);
        }
        this._style = pick(this.$el.style, ["width", "height"]);
      },
      disconnected() {
        if (this.isActive()) {
          this.hide(false);
          active$1 = null;
        }
        css(this.$el, this._style);
      },
      events: [
        {
          name: "click",
          delegate: () => ".uk-drop-close",
          handler(e) {
            e.preventDefault();
            this.hide(false);
          }
        },
        {
          name: "click",
          delegate: () => 'a[href*="#"]',
          handler({ defaultPrevented, current }) {
            const { hash } = current;
            if (!defaultPrevented && hash && isSameSiteAnchor(current) && !this.$el.contains($(hash))) {
              this.hide(false);
            }
          }
        },
        {
          name: "beforescroll",
          handler() {
            this.hide(false);
          }
        },
        {
          name: "toggle",
          self: true,
          handler(e, toggle) {
            e.preventDefault();
            if (this.isToggled()) {
              this.hide(false);
            } else {
              this.show(toggle == null ? void 0 : toggle.$el, false);
            }
          }
        },
        {
          name: "toggleshow",
          self: true,
          handler(e, toggle) {
            e.preventDefault();
            this.show(toggle == null ? void 0 : toggle.$el);
          }
        },
        {
          name: "togglehide",
          self: true,
          handler(e) {
            e.preventDefault();
            if (!matches(this.$el, ":focus,:hover")) {
              this.hide();
            }
          }
        },
        {
          name: `${pointerEnter} focusin`,
          filter: ({ mode }) => includes(mode, "hover"),
          handler(e) {
            if (!isTouch(e)) {
              this.clearTimers();
            }
          }
        },
        {
          name: `${pointerLeave} focusout`,
          filter: ({ mode }) => includes(mode, "hover"),
          handler(e) {
            if (!isTouch(e) && e.relatedTarget) {
              this.hide();
            }
          }
        },
        {
          name: "toggled",
          self: true,
          handler(e, toggled) {
            if (toggled) {
              this.clearTimers();
              this.position();
            }
          }
        },
        {
          name: "show",
          self: true,
          handler() {
            active$1 = this;
            this.tracker.init();
            attr(this.targetEl, "aria-expanded", true);
            const handlers = [
              listenForResize(this),
              listenForEscClose$1(this),
              listenForBackgroundClose$1(this),
              this.autoUpdate && listenForScroll(this),
              this.closeOnScroll && listenForScrollClose(this)
            ];
            once(this.$el, "hide", () => handlers.forEach((handler) => handler && handler()), {
              self: true
            });
            if (!this.bgScroll) {
              once(this.$el, "hidden", preventBackgroundScroll(this.$el), { self: true });
            }
          }
        },
        {
          name: "beforehide",
          self: true,
          handler: "clearTimers"
        },
        {
          name: "hide",
          handler({ target }) {
            if (this.$el !== target) {
              active$1 = active$1 === null && this.$el.contains(target) && this.isToggled() ? this : active$1;
              return;
            }
            active$1 = this.isActive() ? null : active$1;
            this.tracker.cancel();
            attr(this.targetEl, "aria-expanded", null);
          }
        }
      ],
      update: {
        write() {
          if (this.isToggled() && !hasClass(this.$el, this.clsEnter)) {
            this.position();
          }
        }
      },
      methods: {
        show(target = this.targetEl, delay = true) {
          if (this.isToggled() && target && this.targetEl && target !== this.targetEl) {
            this.hide(false, false);
          }
          this.targetEl = target;
          this.clearTimers();
          if (this.isActive()) {
            return;
          }
          if (active$1) {
            if (delay && active$1.isDelaying()) {
              this.showTimer = setTimeout(() => matches(target, ":hover") && this.show(), 10);
              return;
            }
            let prev;
            while (active$1 && prev !== active$1 && !active$1.$el.contains(this.$el)) {
              prev = active$1;
              active$1.hide(false, false);
            }
          }
          if (this.container && parent(this.$el) !== this.container) {
            append(this.container, this.$el);
          }
          this.showTimer = setTimeout(
            () => this.toggleElement(this.$el, true),
            delay && this.delayShow || 0
          );
        },
        hide(delay = true, animate = true) {
          const hide = () => this.toggleElement(this.$el, false, this.animateOut && animate);
          this.clearTimers();
          this.isDelayedHide = delay;
          if (delay && this.isDelaying()) {
            this.hideTimer = setTimeout(this.hide, 50);
          } else if (delay && this.delayHide) {
            this.hideTimer = setTimeout(hide, this.delayHide);
          } else {
            hide();
          }
        },
        clearTimers() {
          clearTimeout(this.showTimer);
          clearTimeout(this.hideTimer);
          this.showTimer = null;
          this.hideTimer = null;
        },
        isActive() {
          return active$1 === this;
        },
        isDelaying() {
          return [this.$el, ...$$(".uk-drop", this.$el)].some((el) => this.tracker.movesTo(el));
        },
        position() {
          const restoreScrollPosition = storeScrollPosition(this.$el);
          removeClass(this.$el, "uk-drop-stack");
          css(this.$el, this._style);
          this.$el.hidden = true;
          const viewports = this.target.map((target) => getViewport$1(this.$el, target));
          const viewportOffset = this.getViewportOffset(this.$el);
          const dirs = [
            [0, ["x", "width", "left", "right"]],
            [1, ["y", "height", "top", "bottom"]]
          ];
          for (const [i, [axis, prop]] of dirs) {
            if (this.axis !== axis && includes([axis, true], this.stretch)) {
              css(this.$el, {
                [prop]: Math.min(
                  offset(this.boundary[i])[prop],
                  viewports[i][prop] - 2 * viewportOffset
                ),
                [`overflow-${axis}`]: "auto"
              });
            }
          }
          const maxWidth = viewports[0].width - 2 * viewportOffset;
          this.$el.hidden = false;
          css(this.$el, "maxWidth", "");
          if (this.$el.offsetWidth > maxWidth) {
            addClass(this.$el, "uk-drop-stack");
          }
          css(this.$el, "maxWidth", maxWidth);
          this.positionAt(this.$el, this.target, this.boundary);
          for (const [i, [axis, prop, start, end]] of dirs) {
            if (this.axis === axis && includes([axis, true], this.stretch)) {
              const positionOffset = Math.abs(this.getPositionOffset());
              const targetOffset = offset(this.target[i]);
              const elOffset = offset(this.$el);
              css(this.$el, {
                [prop]: (targetOffset[start] > elOffset[start] ? targetOffset[this.inset ? end : start] - Math.max(
                  offset(this.boundary[i])[start],
                  viewports[i][start] + viewportOffset
                ) : Math.min(
                  offset(this.boundary[i])[end],
                  viewports[i][end] - viewportOffset
                ) - targetOffset[this.inset ? start : end]) - positionOffset,
                [`overflow-${axis}`]: "auto"
              });
              this.positionAt(this.$el, this.target, this.boundary);
            }
          }
          restoreScrollPosition();
        }
      }
    };
    function getViewport$1(el, target) {
      return offsetViewport(overflowParents(target).find((parent2) => parent2.contains(el)));
    }
    function createToggleComponent(drop) {
      const { $el } = drop.$create("toggle", query(drop.toggle, drop.$el), {
        target: drop.$el,
        mode: drop.mode
      });
      attr($el, "aria-haspopup", true);
      return $el;
    }
    function listenForResize(drop) {
      const update = () => drop.$emit();
      const off = [
        observeViewportResize(update),
        observeResize(overflowParents(drop.$el).concat(drop.target), update)
      ];
      return () => off.map((observer) => observer.disconnect());
    }
    function listenForScroll(drop, fn = () => drop.$emit()) {
      return on([document, ...overflowParents(drop.$el)], "scroll", fn, {
        passive: true
      });
    }
    function listenForEscClose$1(drop) {
      return on(document, "keydown", (e) => {
        if (e.keyCode === keyMap.ESC) {
          drop.hide(false);
        }
      });
    }
    function listenForScrollClose(drop) {
      return listenForScroll(drop, () => drop.hide(false));
    }
    function listenForBackgroundClose$1(drop) {
      return on(document, pointerDown, ({ target }) => {
        if (drop.$el.contains(target)) {
          return;
        }
        once(
          document,
          `${pointerUp} ${pointerCancel} scroll`,
          ({ defaultPrevented, type, target: newTarget }) => {
            var _a;
            if (!defaultPrevented && type === pointerUp && target === newTarget && !((_a = drop.targetEl) == null ? void 0 : _a.contains(target))) {
              drop.hide(false);
            }
          },
          true
        );
      });
    }

    var Dropnav = {
      mixins: [Class, Container],
      props: {
        align: String,
        clsDrop: String,
        boundary: Boolean,
        dropbar: Boolean,
        dropbarAnchor: Boolean,
        duration: Number,
        mode: Boolean,
        offset: Boolean,
        stretch: Boolean,
        delayShow: Boolean,
        delayHide: Boolean,
        target: Boolean,
        targetX: Boolean,
        targetY: Boolean,
        animation: Boolean,
        animateOut: Boolean,
        closeOnScroll: Boolean
      },
      data: {
        align: isRtl ? "right" : "left",
        clsDrop: "uk-dropdown",
        clsDropbar: "uk-dropnav-dropbar",
        boundary: true,
        dropbar: false,
        dropbarAnchor: false,
        duration: 200,
        container: false,
        selNavItem: "> li > a, > ul > li > a"
      },
      computed: {
        dropbarAnchor: ({ dropbarAnchor }, $el) => query(dropbarAnchor, $el) || $el,
        dropbar({ dropbar }) {
          if (!dropbar) {
            return null;
          }
          dropbar = this._dropbar || query(dropbar, this.$el) || $(`+ .${this.clsDropbar}`, this.$el);
          return dropbar ? dropbar : this._dropbar = $("<div></div>");
        },
        dropContainer(_, $el) {
          return this.container || $el;
        },
        dropdowns({ clsDrop }, $el) {
          var _a;
          const dropdowns = $$(`.${clsDrop}`, $el);
          if (this.dropContainer !== $el) {
            for (const el of $$(`.${clsDrop}`, this.dropContainer)) {
              const target = (_a = this.getDropdown(el)) == null ? void 0 : _a.targetEl;
              if (!includes(dropdowns, el) && target && this.$el.contains(target)) {
                dropdowns.push(el);
              }
            }
          }
          return dropdowns;
        },
        items({ selNavItem }, $el) {
          return $$(selNavItem, $el);
        }
      },
      watch: {
        dropbar(dropbar) {
          addClass(
            dropbar,
            "uk-dropbar",
            "uk-dropbar-top",
            this.clsDropbar,
            `uk-${this.$options.name}-dropbar`
          );
        },
        dropdowns() {
          this.initializeDropdowns();
        }
      },
      connected() {
        this.initializeDropdowns();
      },
      disconnected() {
        remove$1(this._dropbar);
        delete this._dropbar;
      },
      events: [
        {
          name: "mouseover focusin",
          delegate: ({ selNavItem }) => selNavItem,
          handler({ current }) {
            const active2 = this.getActive();
            if (active2 && includes(active2.mode, "hover") && active2.targetEl && !current.contains(active2.targetEl) && !active2.isDelaying()) {
              active2.hide(false);
            }
          }
        },
        {
          name: "keydown",
          self: true,
          delegate: ({ selNavItem }) => selNavItem,
          handler(e) {
            var _a;
            const { current, keyCode } = e;
            const active2 = this.getActive();
            if (keyCode === keyMap.DOWN && (active2 == null ? void 0 : active2.targetEl) === current) {
              e.preventDefault();
              (_a = $(selFocusable, active2.$el)) == null ? void 0 : _a.focus();
            }
            handleNavItemNavigation(e, this.items, active2);
          }
        },
        {
          name: "keydown",
          el: ({ dropContainer }) => dropContainer,
          delegate: ({ clsDrop }) => `.${clsDrop}`,
          handler(e) {
            var _a;
            const { current, keyCode, target } = e;
            if (isInput(target) || !includes(this.dropdowns, current)) {
              return;
            }
            const active2 = this.getActive();
            let next = -1;
            if (keyCode === keyMap.HOME) {
              next = 0;
            } else if (keyCode === keyMap.END) {
              next = "last";
            } else if (keyCode === keyMap.UP) {
              next = "previous";
            } else if (keyCode === keyMap.DOWN) {
              next = "next";
            } else if (keyCode === keyMap.ESC) {
              (_a = active2.targetEl) == null ? void 0 : _a.focus();
            }
            if (~next) {
              e.preventDefault();
              const elements = $$(selFocusable, current);
              elements[getIndex(
                next,
                elements,
                findIndex(elements, (el) => matches(el, ":focus"))
              )].focus();
            }
            handleNavItemNavigation(e, this.items, active2);
          }
        },
        {
          name: "mouseleave",
          el: ({ dropbar }) => dropbar,
          filter: ({ dropbar }) => dropbar,
          handler() {
            const active2 = this.getActive();
            if (active2 && includes(active2.mode, "hover") && !this.dropdowns.some((el) => matches(el, ":hover"))) {
              active2.hide();
            }
          }
        },
        {
          name: "beforeshow",
          el: ({ dropContainer }) => dropContainer,
          filter: ({ dropbar }) => dropbar,
          handler({ target }) {
            if (!this.isDropbarDrop(target)) {
              return;
            }
            if (this.dropbar.previousElementSibling !== this.dropbarAnchor) {
              after(this.dropbarAnchor, this.dropbar);
            }
            addClass(target, `${this.clsDrop}-dropbar`);
          }
        },
        {
          name: "show",
          el: ({ dropContainer }) => dropContainer,
          filter: ({ dropbar }) => dropbar,
          handler({ target }) {
            if (!this.isDropbarDrop(target)) {
              return;
            }
            const drop = this.getDropdown(target);
            const adjustHeight = () => {
              const maxBottom = Math.max(
                ...parents(target, `.${this.clsDrop}`).concat(target).map((el) => offset(el).bottom)
              );
              offset(this.dropbar, {
                left: offset(this.dropbar).left,
                top: this.getDropbarOffset(drop.getPositionOffset())
              });
              this.transitionTo(
                maxBottom - offset(this.dropbar).top + toFloat(css(target, "marginBottom")),
                target
              );
            };
            this._observer = observeResize([drop.$el, ...drop.target], adjustHeight);
            adjustHeight();
          }
        },
        {
          name: "beforehide",
          el: ({ dropContainer }) => dropContainer,
          filter: ({ dropbar }) => dropbar,
          handler(e) {
            const active2 = this.getActive();
            if (matches(this.dropbar, ":hover") && active2.$el === e.target && this.isDropbarDrop(active2.$el) && includes(active2.mode, "hover") && active2.isDelayedHide && !this.items.some((el) => active2.targetEl !== el && matches(el, ":focus"))) {
              e.preventDefault();
            }
          }
        },
        {
          name: "hide",
          el: ({ dropContainer }) => dropContainer,
          filter: ({ dropbar }) => dropbar,
          handler({ target }) {
            var _a;
            if (!this.isDropbarDrop(target)) {
              return;
            }
            (_a = this._observer) == null ? void 0 : _a.disconnect();
            const active2 = this.getActive();
            if (!active2 || active2.$el === target) {
              this.transitionTo(0);
            }
          }
        }
      ],
      methods: {
        getActive() {
          var _a;
          return includes(this.dropdowns, (_a = active$1) == null ? void 0 : _a.$el) && active$1;
        },
        async transitionTo(newHeight, el) {
          const { dropbar } = this;
          const oldHeight = height(dropbar);
          el = oldHeight < newHeight && el;
          await Transition.cancel([el, dropbar]);
          if (el) {
            const diff = offset(el).top - offset(dropbar).top - oldHeight;
            if (diff > 0) {
              css(el, "transitionDelay", `${diff / newHeight * this.duration}ms`);
            }
          }
          css(el, "clipPath", `polygon(0 0,100% 0,100% ${oldHeight}px,0 ${oldHeight}px)`);
          height(dropbar, oldHeight);
          await Promise.all([
            Transition.start(dropbar, { height: newHeight }, this.duration),
            Transition.start(
              el,
              { clipPath: `polygon(0 0,100% 0,100% ${newHeight}px,0 ${newHeight}px)` },
              this.duration
            ).finally(() => css(el, { clipPath: "", transitionDelay: "" }))
          ]).catch(noop);
        },
        getDropdown(el) {
          return this.$getComponent(el, "drop") || this.$getComponent(el, "dropdown");
        },
        isDropbarDrop(el) {
          return includes(this.dropdowns, el) && hasClass(el, this.clsDrop);
        },
        getDropbarOffset(offsetTop) {
          const { $el, target, targetY } = this;
          const { top, height: height2 } = offset(query(targetY || target || $el, $el));
          return top + height2 + offsetTop;
        },
        initializeDropdowns() {
          this.$create(
            "drop",
            this.dropdowns.filter((el) => !this.getDropdown(el)),
            {
              ...this.$props,
              flip: false,
              shift: true,
              pos: `bottom-${this.align}`,
              boundary: this.boundary === true ? this.$el : this.boundary
            }
          );
        }
      }
    };
    function handleNavItemNavigation(e, toggles, active2) {
      var _a, _b, _c;
      const { current, keyCode } = e;
      let next = -1;
      if (keyCode === keyMap.HOME) {
        next = 0;
      } else if (keyCode === keyMap.END) {
        next = "last";
      } else if (keyCode === keyMap.LEFT) {
        next = "previous";
      } else if (keyCode === keyMap.RIGHT) {
        next = "next";
      } else if (keyCode === keyMap.TAB) {
        (_a = active2.targetEl) == null ? void 0 : _a.focus();
        (_b = active2.hide) == null ? void 0 : _b.call(active2, false);
      }
      if (~next) {
        e.preventDefault();
        (_c = active2.hide) == null ? void 0 : _c.call(active2, false);
        toggles[getIndex(next, toggles, toggles.indexOf(active2.targetEl || current))].focus();
      }
    }

    var formCustom = {
      mixins: [Class],
      args: "target",
      props: {
        target: Boolean
      },
      data: {
        target: false
      },
      computed: {
        input: (_, $el) => $(selInput, $el),
        state() {
          return this.input.nextElementSibling;
        },
        target({ target }, $el) {
          return target && (target === true && parent(this.input) === $el && this.input.nextElementSibling || $(target, $el));
        }
      },
      update() {
        var _a;
        const { target, input } = this;
        if (!target) {
          return;
        }
        let option;
        const prop = isInput(target) ? "value" : "textContent";
        const prev = target[prop];
        const value = ((_a = input.files) == null ? void 0 : _a[0]) ? input.files[0].name : matches(input, "select") && (option = $$("option", input).filter((el) => el.selected)[0]) ? option.textContent : input.value;
        if (prev !== value) {
          target[prop] = value;
        }
      },
      events: [
        {
          name: "change",
          handler() {
            this.$emit();
          }
        },
        {
          name: "reset",
          el: ({ $el }) => $el.closest("form"),
          handler() {
            this.$emit();
          }
        }
      ]
    };

    var Margin = {
      props: {
        margin: String,
        firstColumn: Boolean
      },
      data: {
        margin: "uk-margin-small-top",
        firstColumn: "uk-first-column"
      },
      observe: [
        mutation({
          options: {
            childList: true
          }
        }),
        mutation({
          options: {
            attributes: true,
            attributeFilter: ["style"]
          },
          target: ({ $el }) => [$el, ...children($el)]
        }),
        resize({
          target: ({ $el }) => [$el, ...children($el)]
        })
      ],
      update: {
        read() {
          return {
            rows: getRows(children(this.$el))
          };
        },
        write({ rows }) {
          for (const row of rows) {
            for (const el of row) {
              toggleClass(el, this.margin, rows[0] !== row);
              toggleClass(el, this.firstColumn, row[isRtl ? row.length - 1 : 0] === el);
            }
          }
        },
        events: ["resize"]
      }
    };
    function getRows(elements) {
      const sorted = [[]];
      const withOffset = elements.some(
        (el, i) => i && elements[i - 1].offsetParent !== el.offsetParent
      );
      for (const el of elements) {
        if (!isVisible(el)) {
          continue;
        }
        const offset = getOffset(el, withOffset);
        for (let i = sorted.length - 1; i >= 0; i--) {
          const current = sorted[i];
          if (!current[0]) {
            current.push(el);
            break;
          }
          const offsetCurrent = getOffset(current[0], withOffset);
          if (offset.top >= offsetCurrent.bottom - 1 && offset.top !== offsetCurrent.top) {
            sorted.push([el]);
            break;
          }
          if (offset.bottom - 1 > offsetCurrent.top || offset.top === offsetCurrent.top) {
            let j = current.length - 1;
            for (; j >= 0; j--) {
              const offsetCurrent2 = getOffset(current[j], withOffset);
              if (offset.left >= offsetCurrent2.left) {
                break;
              }
            }
            current.splice(j + 1, 0, el);
            break;
          }
          if (i === 0) {
            sorted.unshift([el]);
            break;
          }
        }
      }
      return sorted;
    }
    function getOffset(element, offset = false) {
      let { offsetTop, offsetLeft, offsetHeight, offsetWidth } = element;
      if (offset) {
        [offsetTop, offsetLeft] = offsetPosition(element);
      }
      return {
        top: offsetTop,
        left: offsetLeft,
        bottom: offsetTop + offsetHeight,
        right: offsetLeft + offsetWidth
      };
    }

    var grid = {
      extends: Margin,
      mixins: [Class],
      name: "grid",
      props: {
        masonry: Boolean,
        parallax: String,
        parallaxStart: String,
        parallaxEnd: String,
        parallaxJustify: Boolean
      },
      data: {
        margin: "uk-grid-margin",
        clsStack: "uk-grid-stack",
        masonry: false,
        parallax: 0,
        parallaxStart: 0,
        parallaxEnd: 0,
        parallaxJustify: false
      },
      connected() {
        this.masonry && addClass(this.$el, "uk-flex-top", "uk-flex-wrap-top");
      },
      observe: scroll$1({ filter: ({ parallax, parallaxJustify }) => parallax || parallaxJustify }),
      update: [
        {
          write({ rows }) {
            toggleClass(this.$el, this.clsStack, !rows.some((row) => row.length > 1));
          },
          events: ["resize"]
        },
        {
          read(data) {
            const { rows } = data;
            let { masonry, parallax, parallaxJustify, margin } = this;
            parallax = Math.max(0, toPx(parallax));
            if (!(masonry || parallax || parallaxJustify) || positionedAbsolute(rows) || rows[0].some(
              (el, i) => rows.some((row) => row[i] && row[i].offsetWidth !== el.offsetWidth)
            )) {
              return data.translates = data.scrollColumns = false;
            }
            let gutter = getGutter(rows, margin);
            let columns;
            let translates;
            if (masonry) {
              [columns, translates] = applyMasonry(rows, gutter, masonry === "next");
            } else {
              columns = transpose(rows);
            }
            const columnHeights = columns.map(
              (column) => sumBy(column, "offsetHeight") + gutter * (column.length - 1)
            );
            const height = Math.max(0, ...columnHeights);
            let scrollColumns;
            let parallaxStart;
            let parallaxEnd;
            if (parallax || parallaxJustify) {
              scrollColumns = columnHeights.map(
                (hgt, i) => parallaxJustify ? height - hgt + parallax : parallax / (i % 2 || 8)
              );
              if (!parallaxJustify) {
                parallax = Math.max(
                  ...columnHeights.map((hgt, i) => hgt + scrollColumns[i] - height)
                );
              }
              parallaxStart = toPx(this.parallaxStart, "height", this.$el, true);
              parallaxEnd = toPx(this.parallaxEnd, "height", this.$el, true);
            }
            return {
              columns,
              translates,
              scrollColumns,
              parallaxStart,
              parallaxEnd,
              padding: parallax,
              height: translates ? height : ""
            };
          },
          write({ height, padding }) {
            css(this.$el, "paddingBottom", padding || "");
            height !== false && css(this.$el, "height", height);
          },
          events: ["resize"]
        },
        {
          read({ rows, scrollColumns, parallaxStart, parallaxEnd }) {
            return {
              scrolled: scrollColumns && !positionedAbsolute(rows) ? scrolledOver(this.$el, parallaxStart, parallaxEnd) : false
            };
          },
          write({ columns, scrolled, scrollColumns, translates }) {
            if (!scrolled && !translates) {
              return;
            }
            columns.forEach(
              (column, i) => column.forEach((el, j) => {
                let [x, y] = translates && translates[i][j] || [0, 0];
                if (scrolled) {
                  y += scrolled * scrollColumns[i];
                }
                css(el, "transform", `translate(${x}px, ${y}px)`);
              })
            );
          },
          events: ["scroll", "resize"]
        }
      ]
    };
    function positionedAbsolute(rows) {
      return rows.flat().some((el) => css(el, "position") === "absolute");
    }
    function applyMasonry(rows, gutter, next) {
      const columns = [];
      const translates = [];
      const columnHeights = Array(rows[0].length).fill(0);
      let rowHeights = 0;
      for (let row of rows) {
        if (isRtl) {
          row = row.reverse();
        }
        let height = 0;
        for (const j in row) {
          const { offsetWidth, offsetHeight } = row[j];
          const index = next ? j : columnHeights.indexOf(Math.min(...columnHeights));
          push(columns, index, row[j]);
          push(translates, index, [
            (index - j) * offsetWidth * (isRtl ? -1 : 1),
            columnHeights[index] - rowHeights
          ]);
          columnHeights[index] += offsetHeight + gutter;
          height = Math.max(height, offsetHeight);
        }
        rowHeights += height + gutter;
      }
      return [columns, translates];
    }
    function getGutter(rows, cls) {
      const node = rows.flat().find((el) => hasClass(el, cls));
      return toFloat(node ? css(node, "marginTop") : css(rows[0][0], "paddingLeft"));
    }
    function transpose(rows) {
      const columns = [];
      for (const row of rows) {
        for (const i in row) {
          push(columns, i, row[i]);
        }
      }
      return columns;
    }
    function push(array, index, value) {
      if (!array[index]) {
        array[index] = [];
      }
      array[index].push(value);
    }

    var heightMatch = {
      args: "target",
      props: {
        target: String,
        row: Boolean
      },
      data: {
        target: "> *",
        row: true
      },
      computed: {
        elements: ({ target }, $el) => $$(target, $el)
      },
      observe: resize({
        target: ({ $el, elements }) => elements.reduce((elements2, el) => elements2.concat(el, ...el.children), [$el])
      }),
      events: {
        // Hidden elements may change height when fonts load
        name: "loadingdone",
        el: () => document.fonts,
        handler() {
          this.$emit("resize");
        }
      },
      update: {
        read() {
          return {
            rows: (this.row ? getRows(this.elements) : [this.elements]).map(match)
          };
        },
        write({ rows }) {
          for (const { heights, elements } of rows) {
            elements.forEach((el, i) => css(el, "minHeight", heights[i]));
          }
        },
        events: ["resize"]
      }
    };
    function match(elements) {
      if (elements.length < 2) {
        return { heights: [""], elements };
      }
      let heights = elements.map(getHeight);
      const max = Math.max(...heights);
      return {
        heights: elements.map((el, i) => heights[i].toFixed(2) === max.toFixed(2) ? "" : max),
        elements
      };
    }
    function getHeight(element) {
      const style = pick(element.style, ["display", "minHeight"]);
      if (!isVisible(element)) {
        css(element, "display", "block", "important");
      }
      css(element, "minHeight", "");
      const height = dimensions(element).height - boxModelAdjust(element, "height", "content-box");
      css(element, style);
      return height;
    }

    var heightPlaceholder = {
      args: "target",
      props: {
        target: String
      },
      data: {
        target: ""
      },
      computed: {
        target: {
          get: ({ target }, $el) => query(target, $el),
          observe: ({ target }) => target
        }
      },
      observe: resize({ target: ({ target }) => target }),
      update: {
        read() {
          return this.target ? { height: this.target.offsetHeight } : false;
        },
        write({ height }) {
          css(this.$el, { minHeight: height });
        },
        events: ["resize"]
      }
    };

    var heightViewport = {
      props: {
        expand: Boolean,
        offsetTop: Boolean,
        offsetBottom: Boolean,
        minHeight: Number
      },
      data: {
        expand: false,
        offsetTop: false,
        offsetBottom: false,
        minHeight: 0
      },
      // check for offsetTop change
      observe: [
        viewport({ filter: ({ expand }) => expand }),
        resize({ target: ({ $el }) => scrollParents($el) })
      ],
      update: {
        read() {
          if (!isVisible(this.$el)) {
            return false;
          }
          let minHeight = "";
          const box = boxModelAdjust(this.$el, "height", "content-box");
          const { body, scrollingElement } = document;
          const scrollElement = scrollParent(this.$el);
          const { height: viewportHeight } = offsetViewport(
            scrollElement === body ? scrollingElement : scrollElement
          );
          const isScrollingElement = scrollingElement === scrollElement || body === scrollElement;
          minHeight = `calc(${isScrollingElement ? "100vh" : `${viewportHeight}px`}`;
          if (this.expand) {
            const diff = dimensions(scrollElement).height - dimensions(this.$el).height;
            minHeight += ` - ${diff}px`;
          } else {
            if (this.offsetTop) {
              if (isScrollingElement) {
                const offsetTopEl = this.offsetTop === true ? this.$el : query(this.offsetTop, this.$el);
                const { top } = offset(offsetTopEl);
                minHeight += top > 0 && top < viewportHeight / 2 ? ` - ${top}px` : "";
              } else {
                minHeight += ` - ${boxModelAdjust(scrollElement, "height", css(scrollElement, "boxSizing"))}px`;
              }
            }
            if (this.offsetBottom === true) {
              minHeight += ` - ${dimensions(this.$el.nextElementSibling).height}px`;
            } else if (isNumeric(this.offsetBottom)) {
              minHeight += ` - ${this.offsetBottom}vh`;
            } else if (this.offsetBottom && endsWith(this.offsetBottom, "px")) {
              minHeight += ` - ${toFloat(this.offsetBottom)}px`;
            } else if (isString(this.offsetBottom)) {
              minHeight += ` - ${dimensions(query(this.offsetBottom, this.$el)).height}px`;
            }
          }
          minHeight += `${box ? ` - ${box}px` : ""})`;
          return { minHeight };
        },
        write({ minHeight }) {
          css(this.$el, "minHeight", `max(${this.minHeight || 0}px, ${minHeight})`);
        },
        events: ["resize"]
      }
    };

    var closeIcon = "<svg width=\"14\" height=\"14\" viewBox=\"0 0 14 14\"><line fill=\"none\" stroke=\"#000\" stroke-width=\"1.1\" x1=\"1\" y1=\"1\" x2=\"13\" y2=\"13\"/><line fill=\"none\" stroke=\"#000\" stroke-width=\"1.1\" x1=\"13\" y1=\"1\" x2=\"1\" y2=\"13\"/></svg>";

    var closeLarge = "<svg width=\"20\" height=\"20\" viewBox=\"0 0 20 20\"><line fill=\"none\" stroke=\"#000\" stroke-width=\"1.4\" x1=\"1\" y1=\"1\" x2=\"19\" y2=\"19\"/><line fill=\"none\" stroke=\"#000\" stroke-width=\"1.4\" x1=\"19\" y1=\"1\" x2=\"1\" y2=\"19\"/></svg>";

    var dropParentIcon = "<svg width=\"12\" height=\"12\" viewBox=\"0 0 12 12\"><polyline fill=\"none\" stroke=\"#000\" stroke-width=\"1.1\" points=\"1 3.5 6 8.5 11 3.5\"/></svg>";

    var marker = "<svg width=\"20\" height=\"20\" viewBox=\"0 0 20 20\"><rect width=\"1\" height=\"11\" x=\"9\" y=\"4\"/><rect width=\"11\" height=\"1\" x=\"4\" y=\"9\"/></svg>";

    var navParentIconLarge = "<svg width=\"14\" height=\"14\" viewBox=\"0 0 14 14\"><polyline fill=\"none\" stroke=\"#000\" stroke-width=\"1.1\" points=\"1 4 7 10 13 4\"/></svg>";

    var navParentIcon = "<svg width=\"12\" height=\"12\" viewBox=\"0 0 12 12\"><polyline fill=\"none\" stroke=\"#000\" stroke-width=\"1.1\" points=\"1 3.5 6 8.5 11 3.5\"/></svg>";

    var navbarParentIcon = "<svg width=\"12\" height=\"12\" viewBox=\"0 0 12 12\"><polyline fill=\"none\" stroke=\"#000\" stroke-width=\"1.1\" points=\"1 3.5 6 8.5 11 3.5\"/></svg>";

    var navbarToggleIcon = "<svg width=\"20\" height=\"20\" viewBox=\"0 0 20 20\"><style>.uk-navbar-toggle-icon svg&gt;[class*=&quot;line-&quot;]{transition:0.2s ease-in-out;transition-property:transform, opacity;transform-origin:center;opacity:1}.uk-navbar-toggle-icon svg&gt;.line-3{opacity:0}.uk-navbar-toggle-animate[aria-expanded=&quot;true&quot;] svg&gt;.line-3{opacity:1}.uk-navbar-toggle-animate[aria-expanded=&quot;true&quot;] svg&gt;.line-2{transform:rotate(45deg)}.uk-navbar-toggle-animate[aria-expanded=&quot;true&quot;] svg&gt;.line-3{transform:rotate(-45deg)}.uk-navbar-toggle-animate[aria-expanded=&quot;true&quot;] svg&gt;.line-1,.uk-navbar-toggle-animate[aria-expanded=&quot;true&quot;] svg&gt;.line-4{opacity:0}.uk-navbar-toggle-animate[aria-expanded=&quot;true&quot;] svg&gt;.line-1{transform:translateY(6px) scaleX(0)}.uk-navbar-toggle-animate[aria-expanded=&quot;true&quot;] svg&gt;.line-4{transform:translateY(-6px) scaleX(0)}</style><rect width=\"20\" height=\"2\" y=\"3\" class=\"line-1\"/><rect width=\"20\" height=\"2\" y=\"9\" class=\"line-2\"/><rect width=\"20\" height=\"2\" y=\"9\" class=\"line-3\"/><rect width=\"20\" height=\"2\" y=\"15\" class=\"line-4\"/></svg>";

    var overlayIcon = "<svg width=\"40\" height=\"40\" viewBox=\"0 0 40 40\"><rect width=\"1\" height=\"40\" x=\"19\" y=\"0\"/><rect width=\"40\" height=\"1\" x=\"0\" y=\"19\"/></svg>";

    var paginationNext = "<svg width=\"7\" height=\"12\" viewBox=\"0 0 7 12\"><polyline fill=\"none\" stroke=\"#000\" stroke-width=\"1.2\" points=\"1 1 6 6 1 11\"/></svg>";

    var paginationPrevious = "<svg width=\"7\" height=\"12\" viewBox=\"0 0 7 12\"><polyline fill=\"none\" stroke=\"#000\" stroke-width=\"1.2\" points=\"6 1 1 6 6 11\"/></svg>";

    var searchIcon = "<svg width=\"20\" height=\"20\" viewBox=\"0 0 20 20\"><circle fill=\"none\" stroke=\"#000\" stroke-width=\"1.1\" cx=\"9\" cy=\"9\" r=\"7\"/><path fill=\"none\" stroke=\"#000\" stroke-width=\"1.1\" d=\"M14,14 L18,18 L14,14 Z\"/></svg>";

    var searchLarge = "<svg width=\"40\" height=\"40\" viewBox=\"0 0 40 40\"><circle fill=\"none\" stroke=\"#000\" stroke-width=\"1.8\" cx=\"17.5\" cy=\"17.5\" r=\"16.5\"/><line fill=\"none\" stroke=\"#000\" stroke-width=\"1.8\" x1=\"38\" y1=\"39\" x2=\"29\" y2=\"30\"/></svg>";

    var searchMedium = "<svg width=\"24\" height=\"24\" viewBox=\"0 0 24 24\"><circle fill=\"none\" stroke=\"#000\" stroke-width=\"1.1\" cx=\"10.5\" cy=\"10.5\" r=\"9.5\"/><line fill=\"none\" stroke=\"#000\" stroke-width=\"1.1\" x1=\"23\" y1=\"23\" x2=\"17\" y2=\"17\"/></svg>";

    var slidenavNextLarge = "<svg width=\"25\" height=\"40\" viewBox=\"0 0 25 40\"><polyline fill=\"none\" stroke=\"#000\" stroke-width=\"2\" points=\"4.002,38.547 22.527,20.024 4,1.5\"/></svg>";

    var slidenavNext = "<svg width=\"14\" height=\"24\" viewBox=\"0 0 14 24\"><polyline fill=\"none\" stroke=\"#000\" stroke-width=\"1.4\" points=\"1.225,23 12.775,12 1.225,1\"/></svg>";

    var slidenavPreviousLarge = "<svg width=\"25\" height=\"40\" viewBox=\"0 0 25 40\"><polyline fill=\"none\" stroke=\"#000\" stroke-width=\"2\" points=\"20.527,1.5 2,20.024 20.525,38.547\"/></svg>";

    var slidenavPrevious = "<svg width=\"14\" height=\"24\" viewBox=\"0 0 14 24\"><polyline fill=\"none\" stroke=\"#000\" stroke-width=\"1.4\" points=\"12.775,1 1.225,12 12.775,23\"/></svg>";

    var spinner = "<svg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\"><circle fill=\"none\" stroke=\"#000\" cx=\"15\" cy=\"15\" r=\"14\"/></svg>";

    var totop = "<svg width=\"18\" height=\"10\" viewBox=\"0 0 18 10\"><polyline fill=\"none\" stroke=\"#000\" stroke-width=\"1.2\" points=\"1 9 9 1 17 9\"/></svg>";

    var I18n = {
      props: {
        i18n: Object
      },
      data: {
        i18n: null
      },
      methods: {
        t(key, ...params) {
          var _a, _b, _c;
          let i = 0;
          return ((_c = ((_a = this.i18n) == null ? void 0 : _a[key]) || ((_b = this.$options.i18n) == null ? void 0 : _b[key])) == null ? void 0 : _c.replace(
            /%s/g,
            () => params[i++] || ""
          )) || "";
        }
      }
    };

    var Svg = {
      args: "src",
      props: {
        width: Number,
        height: Number,
        ratio: Number
      },
      data: {
        ratio: 1
      },
      connected() {
        this.svg = this.getSvg().then((el) => {
          if (!this._connected) {
            return;
          }
          const svg = insertSVG(el, this.$el);
          if (this.svgEl && svg !== this.svgEl) {
            remove$1(this.svgEl);
          }
          applyWidthAndHeight.call(this, svg, el);
          return this.svgEl = svg;
        }, noop);
      },
      disconnected() {
        this.svg.then((svg) => {
          if (this._connected) {
            return;
          }
          if (isVoidElement(this.$el)) {
            this.$el.hidden = false;
          }
          remove$1(svg);
          this.svgEl = null;
        });
        this.svg = null;
      },
      methods: {
        async getSvg() {
        }
      }
    };
    function insertSVG(el, root) {
      if (isVoidElement(root) || isTag(root, "canvas")) {
        root.hidden = true;
        const next = root.nextElementSibling;
        return equals(el, next) ? next : after(root, el);
      }
      const last = root.lastElementChild;
      return equals(el, last) ? last : append(root, el);
    }
    function equals(el, other) {
      return isTag(el, "svg") && isTag(other, "svg") && el.innerHTML === other.innerHTML;
    }
    function applyWidthAndHeight(el, ref) {
      const props = ["width", "height"];
      let dimensions = props.map((prop) => this[prop]);
      if (!dimensions.some((val) => val)) {
        dimensions = props.map((prop) => attr(ref, prop));
      }
      const viewBox = attr(ref, "viewBox");
      if (viewBox && !dimensions.some((val) => val)) {
        dimensions = viewBox.split(" ").slice(2);
      }
      dimensions.forEach((val, i) => attr(el, props[i], toFloat(val) * this.ratio || null));
    }

    function getMaxPathLength(el) {
      return isVisible(el) ? Math.ceil(
        Math.max(0, ...$$("[stroke]", el).map((stroke) => {
          var _a;
          return ((_a = stroke.getTotalLength) == null ? void 0 : _a.call(stroke)) || 0;
        }))
      ) : 0;
    }

    var svg = {
      mixins: [Svg],
      args: "src",
      props: {
        src: String,
        icon: String,
        attributes: "list",
        strokeAnimation: Boolean
      },
      data: {
        strokeAnimation: false
      },
      observe: [
        mutation({
          async handler() {
            const svg = await this.svg;
            if (svg) {
              applyAttributes.call(this, svg);
            }
          },
          options: {
            attributes: true,
            attributeFilter: ["id", "class", "style"]
          }
        })
      ],
      async connected() {
        if (includes(this.src, "#")) {
          [this.src, this.icon] = this.src.split("#");
        }
        const svg = await this.svg;
        if (svg) {
          applyAttributes.call(this, svg);
          if (this.strokeAnimation) {
            applyAnimation(svg);
          }
        }
      },
      methods: {
        async getSvg() {
          if (isTag(this.$el, "img") && !this.$el.complete && this.$el.loading === "lazy") {
            await new Promise((resolve) => once(this.$el, "load", resolve));
          }
          return parseSVG(await loadSVG(this.src), this.icon) || Promise.reject("SVG not found.");
        }
      }
    };
    function applyAttributes(el) {
      const { $el } = this;
      addClass(el, attr($el, "class"), "uk-svg");
      for (let i = 0; i < $el.style.length; i++) {
        const prop = $el.style[i];
        css(el, prop, css($el, prop));
      }
      for (const attribute in this.attributes) {
        const [prop, value] = this.attributes[attribute].split(":", 2);
        attr(el, prop, value);
      }
      if (!this.$el.id) {
        removeAttr(el, "id");
      }
    }
    const loadSVG = memoize(async (src) => {
      if (src) {
        if (startsWith(src, "data:")) {
          return decodeURIComponent(src.split(",")[1]);
        } else {
          return (await fetch(src)).text();
        }
      } else {
        return Promise.reject();
      }
    });
    function parseSVG(svg, icon) {
      if (icon && includes(svg, "<symbol")) {
        svg = parseSymbols(svg)[icon] || svg;
      }
      return stringToSvg(svg);
    }
    const symbolRe = /<symbol([^]*?id=(['"])(.+?)\2[^]*?<\/)symbol>/g;
    const parseSymbols = memoize(function(svg) {
      const symbols = {};
      symbolRe.lastIndex = 0;
      let match;
      while (match = symbolRe.exec(svg)) {
        symbols[match[3]] = `<svg ${match[1]}svg>`;
      }
      return symbols;
    });
    function applyAnimation(el) {
      const length = getMaxPathLength(el);
      if (length) {
        css(el, "--uk-animation-stroke", length);
      }
    }
    function stringToSvg(string) {
      const container = document.createElement("template");
      container.innerHTML = string;
      return container.content.firstElementChild;
    }

    const icons = {
      spinner,
      totop,
      marker,
      "close-icon": closeIcon,
      "close-large": closeLarge,
      "drop-parent-icon": dropParentIcon,
      "nav-parent-icon": navParentIcon,
      "nav-parent-icon-large": navParentIconLarge,
      "navbar-parent-icon": navbarParentIcon,
      "navbar-toggle-icon": navbarToggleIcon,
      "overlay-icon": overlayIcon,
      "pagination-next": paginationNext,
      "pagination-previous": paginationPrevious,
      "search-icon": searchIcon,
      "search-medium": searchMedium,
      "search-large": searchLarge,
      "search-toggle-icon": searchIcon,
      "slidenav-next": slidenavNext,
      "slidenav-next-large": slidenavNextLarge,
      "slidenav-previous": slidenavPrevious,
      "slidenav-previous-large": slidenavPreviousLarge
    };
    const Icon = {
      install: install$1,
      mixins: [Svg],
      args: "icon",
      props: { icon: String },
      isIcon: true,
      beforeConnect() {
        addClass(this.$el, "uk-icon");
      },
      methods: {
        async getSvg() {
          const icon = getIcon(this.icon);
          if (!icon) {
            throw "Icon not found.";
          }
          return icon;
        }
      }
    };
    const IconComponent = {
      args: false,
      extends: Icon,
      data: (vm) => ({
        icon: hyphenate(vm.constructor.options.name)
      }),
      beforeConnect() {
        addClass(this.$el, this.$options.id);
      }
    };
    const NavParentIcon = {
      extends: IconComponent,
      beforeConnect() {
        const icon = this.$props.icon;
        this.icon = this.$el.closest(".uk-nav-primary") ? `${icon}-large` : icon;
      }
    };
    const Search = {
      extends: IconComponent,
      mixins: [I18n],
      i18n: { toggle: "Open Search", submit: "Submit Search" },
      beforeConnect() {
        const isToggle = hasClass(this.$el, "uk-search-toggle") || hasClass(this.$el, "uk-navbar-toggle");
        this.icon = isToggle ? "search-toggle-icon" : hasClass(this.$el, "uk-search-icon") && this.$el.closest(".uk-search-large") ? "search-large" : this.$el.closest(".uk-search-medium") ? "search-medium" : this.$props.icon;
        if (hasAttr(this.$el, "aria-label")) {
          return;
        }
        if (isToggle) {
          const label = this.t("toggle");
          attr(this.$el, "aria-label", label);
        } else {
          const button = this.$el.closest("a,button");
          if (button) {
            const label = this.t("submit");
            attr(button, "aria-label", label);
          }
        }
      }
    };
    const Spinner = {
      extends: IconComponent,
      beforeConnect() {
        attr(this.$el, "role", "status");
      },
      methods: {
        async getSvg() {
          const icon = await Icon.methods.getSvg.call(this);
          if (this.ratio !== 1) {
            css($("circle", icon), "strokeWidth", 1 / this.ratio);
          }
          return icon;
        }
      }
    };
    const ButtonComponent = {
      extends: IconComponent,
      mixins: [I18n],
      beforeConnect() {
        const button = this.$el.closest("a,button");
        attr(button, "role", this.role !== null && isTag(button, "a") ? "button" : this.role);
        const label = this.t("label");
        if (label && !hasAttr(button, "aria-label")) {
          attr(button, "aria-label", label);
        }
      }
    };
    const Slidenav = {
      extends: ButtonComponent,
      beforeConnect() {
        addClass(this.$el, "uk-slidenav");
        const icon = this.$props.icon;
        this.icon = hasClass(this.$el, "uk-slidenav-large") ? `${icon}-large` : icon;
      }
    };
    const NavbarToggleIcon = {
      extends: ButtonComponent,
      i18n: { label: "Open menu" }
    };
    const Close = {
      extends: ButtonComponent,
      i18n: { label: "Close" },
      beforeConnect() {
        this.icon = `close-${hasClass(this.$el, "uk-close-large") ? "large" : "icon"}`;
      }
    };
    const Marker = {
      extends: ButtonComponent,
      i18n: { label: "Open" }
    };
    const Totop = {
      extends: ButtonComponent,
      i18n: { label: "Back to top" }
    };
    const PaginationNext = {
      extends: ButtonComponent,
      i18n: { label: "Next page" },
      data: { role: null }
    };
    const PaginationPrevious = {
      extends: ButtonComponent,
      i18n: { label: "Previous page" },
      data: { role: null }
    };
    const parsed = {};
    function install$1(UIkit) {
      UIkit.icon.add = (name, svg) => {
        const added = isString(name) ? { [name]: svg } : name;
        each(added, (svg2, name2) => {
          icons[name2] = svg2;
          delete parsed[name2];
        });
        if (UIkit._initialized) {
          apply(
            document.body,
            (el) => each(UIkit.getComponents(el), (cmp) => {
              cmp.$options.isIcon && cmp.icon in added && cmp.$reset();
            })
          );
        }
      };
    }
    const aliases = { twitter: "x" };
    function getIcon(icon) {
      icon = aliases[icon] || icon;
      if (!icons[icon]) {
        return null;
      }
      if (!parsed[icon]) {
        parsed[icon] = stringToSvg(icons[applyRtl(icon)] || icons[icon]);
      }
      return parsed[icon].cloneNode(true);
    }
    function applyRtl(icon) {
      return isRtl ? swap(swap(icon, "left", "right"), "previous", "next") : icon;
    }

    var img = {
      args: "dataSrc",
      props: {
        dataSrc: String,
        sources: String,
        margin: String,
        target: String,
        loading: String
      },
      data: {
        dataSrc: "",
        sources: false,
        margin: "50%",
        target: false,
        loading: "lazy"
      },
      connected() {
        if (this.loading !== "lazy") {
          this.load();
        } else if (isImg(this.$el)) {
          this.$el.loading = "lazy";
          setSrcAttrs(this.$el);
        }
      },
      disconnected() {
        if (this.img) {
          this.img.onload = "";
        }
        delete this.img;
      },
      observe: intersection({
        handler(entries, observer) {
          this.load();
          observer.disconnect();
        },
        options: ({ margin }) => ({ rootMargin: margin }),
        filter: ({ loading }) => loading === "lazy",
        target: ({ $el, $props }) => $props.target ? [$el, ...queryAll($props.target, $el)] : $el
      }),
      methods: {
        load() {
          if (this.img) {
            return this.img;
          }
          const image = isImg(this.$el) ? this.$el : getImageFromElement(this.$el, this.dataSrc, this.sources);
          removeAttr(image, "loading");
          setSrcAttrs(this.$el, image.currentSrc);
          return this.img = image;
        }
      }
    };
    function setSrcAttrs(el, src) {
      if (isImg(el)) {
        const parentNode = parent(el);
        const elements = isTag(parentNode, "picture") ? children(parentNode) : [el];
        elements.forEach((el2) => setSourceProps(el2, el2));
      } else if (src) {
        const change = !includes(el.style.backgroundImage, src);
        if (change) {
          css(el, "backgroundImage", `url(${escape(src)})`);
          trigger(el, createEvent("load", false));
        }
      }
    }
    const srcProps = ["data-src", "data-srcset", "sizes"];
    function setSourceProps(sourceEl, targetEl) {
      for (const prop of srcProps) {
        const value = data(sourceEl, prop);
        if (value) {
          attr(targetEl, prop.replace(/^(data-)+/, ""), value);
        }
      }
    }
    function getImageFromElement(el, src, sources) {
      const img = new Image();
      wrapInPicture(img, sources);
      setSourceProps(el, img);
      img.onload = () => {
        setSrcAttrs(el, img.currentSrc);
      };
      attr(img, "src", src);
      return img;
    }
    function wrapInPicture(img, sources) {
      sources = parseSources(sources);
      if (sources.length) {
        const picture = fragment("<picture>");
        for (const attrs of sources) {
          const source = fragment("<source>");
          attr(source, attrs);
          append(picture, source);
        }
        append(picture, img);
      }
    }
    function parseSources(sources) {
      if (!sources) {
        return [];
      }
      if (startsWith(sources, "[")) {
        try {
          sources = JSON.parse(sources);
        } catch (e) {
          sources = [];
        }
      } else {
        sources = parseOptions(sources);
      }
      if (!isArray(sources)) {
        sources = [sources];
      }
      return sources.filter((source) => !isEmpty(source));
    }
    function isImg(el) {
      return isTag(el, "img");
    }

    var inverse = {
      props: {
        target: String,
        selActive: String
      },
      data: {
        target: false,
        selActive: false
      },
      computed: {
        target: ({ target }, $el) => target ? $$(target, $el) : $el
      },
      observe: [
        intersection({
          handler(entries) {
            this.isIntersecting = entries.some(({ isIntersecting }) => isIntersecting);
            this.$emit();
          },
          target: ({ target }) => target,
          args: { intersecting: false }
        }),
        mutation({
          target: ({ target }) => target,
          options: { attributes: true, attributeFilter: ["class"], attributeOldValue: true }
        }),
        {
          target: ({ target }) => target,
          observe: (target, handler) => {
            const observer = observeResize(
              [...toNodes(target), document.documentElement],
              handler
            );
            const listener = [
              on(document, "scroll itemshown itemhidden", handler, {
                passive: true,
                capture: true
              }),
              on(document, "show hide transitionstart", (e) => {
                handler();
                return observer.observe(e.target);
              }),
              on(document, "shown hidden transitionend transitioncancel", (e) => {
                handler();
                return observer.unobserve(e.target);
              })
            ];
            return {
              observe: observer.observe.bind(observer),
              unobserve: observer.unobserve.bind(observer),
              disconnect() {
                observer.disconnect();
                listener.map((off) => off());
              }
            };
          },
          handler() {
            this.$emit();
          }
        }
      ],
      update: {
        read() {
          if (!this.isIntersecting) {
            return false;
          }
          for (const target of toNodes(this.target)) {
            let color = !this.selActive || matches(target, this.selActive) ? findTargetColor(target) : "";
            if (color !== false) {
              replaceClass(target, "uk-light uk-dark", color);
            }
          }
        }
      }
    };
    function findTargetColor(target) {
      const dim = dimensions(target);
      const viewport = dimensions(window);
      if (!intersectRect(dim, viewport)) {
        return false;
      }
      const { left, top, height, width } = dim;
      let last;
      for (const percent of [0.25, 0.5, 0.75]) {
        const elements = target.ownerDocument.elementsFromPoint(
          Math.max(0, Math.min(left + width * percent, viewport.width - 1)),
          Math.max(0, Math.min(top + height / 2, viewport.height - 1))
        );
        for (const element of elements) {
          if (target.contains(element) || !checkVisibility(element) || element.closest('[class*="-leave"]') && elements.some((el) => element !== el && matches(el, '[class*="-enter"]'))) {
            continue;
          }
          const color = css(element, "--uk-inverse");
          if (color) {
            if (color === last) {
              return `uk-${color}`;
            }
            last = color;
            break;
          }
        }
      }
      return last ? `uk-${last}` : "";
    }
    function checkVisibility(element) {
      if (css(element, "visibility") !== "visible") {
        return false;
      }
      while (element) {
        if (css(element, "opacity") === "0") {
          return false;
        }
        element = parent(element);
      }
      return true;
    }

    var Media = {
      props: {
        media: Boolean
      },
      data: {
        media: false
      },
      connected() {
        const media = toMedia(this.media, this.$el);
        this.matchMedia = true;
        if (media) {
          this.mediaObj = window.matchMedia(media);
          const handler = () => {
            this.matchMedia = this.mediaObj.matches;
            trigger(this.$el, createEvent("mediachange", false, true, [this.mediaObj]));
          };
          this.offMediaObj = on(this.mediaObj, "change", () => {
            handler();
            this.$emit("resize");
          });
          handler();
        }
      },
      disconnected() {
        var _a;
        (_a = this.offMediaObj) == null ? void 0 : _a.call(this);
      }
    };
    function toMedia(value, element) {
      if (isString(value)) {
        if (startsWith(value, "@")) {
          value = toFloat(css(element, `--uk-breakpoint-${value.slice(1)}`));
        } else if (isNaN(value)) {
          return value;
        }
      }
      return value && isNumeric(value) ? `(min-width: ${value}px)` : "";
    }

    var leader = {
      mixins: [Class, Media],
      props: {
        fill: String
      },
      data: {
        fill: "",
        clsWrapper: "uk-leader-fill",
        clsHide: "uk-leader-hide",
        attrFill: "data-fill"
      },
      computed: {
        fill: ({ fill }, $el) => fill || css($el, "--uk-leader-fill-content")
      },
      connected() {
        [this.wrapper] = wrapInner(this.$el, `<span class="${this.clsWrapper}">`);
      },
      disconnected() {
        unwrap(this.wrapper.childNodes);
      },
      observe: resize(),
      update: {
        read() {
          const width = Math.trunc(this.$el.offsetWidth / 2);
          return {
            width,
            fill: this.fill,
            hide: !this.matchMedia
          };
        },
        write({ width, fill, hide }) {
          toggleClass(this.wrapper, this.clsHide, hide);
          attr(this.wrapper, this.attrFill, new Array(width).join(fill));
        },
        events: ["resize"]
      }
    };

    const active = [];
    var Modal = {
      mixins: [Class, Container, Togglable],
      props: {
        selPanel: String,
        selClose: String,
        escClose: Boolean,
        bgClose: Boolean,
        stack: Boolean,
        role: String
      },
      data: {
        cls: "uk-open",
        escClose: true,
        bgClose: true,
        overlay: true,
        stack: false,
        role: "dialog"
      },
      computed: {
        panel: ({ selPanel }, $el) => $(selPanel, $el),
        transitionElement() {
          return this.panel;
        },
        bgClose({ bgClose }) {
          return bgClose && this.panel;
        }
      },
      connected() {
        attr(this.panel || this.$el, "role", this.role);
        if (this.overlay) {
          attr(this.panel || this.$el, "aria-modal", true);
        }
      },
      beforeDisconnect() {
        if (includes(active, this)) {
          this.toggleElement(this.$el, false, false);
        }
      },
      events: [
        {
          name: "click",
          delegate: ({ selClose }) => `${selClose},a[href*="#"]`,
          handler(e) {
            const { current, defaultPrevented } = e;
            const { hash } = current;
            if (!defaultPrevented && hash && isSameSiteAnchor(current) && !this.$el.contains($(hash))) {
              this.hide();
            } else if (matches(current, this.selClose)) {
              e.preventDefault();
              this.hide();
            }
          }
        },
        {
          name: "toggle",
          self: true,
          handler(e) {
            if (e.defaultPrevented) {
              return;
            }
            e.preventDefault();
            if (this.isToggled() === includes(active, this)) {
              this.toggle();
            }
          }
        },
        {
          name: "beforeshow",
          self: true,
          handler(e) {
            if (includes(active, this)) {
              return false;
            }
            if (!this.stack && active.length) {
              Promise.all(active.map((modal) => modal.hide())).then(this.show);
              e.preventDefault();
            } else {
              active.push(this);
            }
          }
        },
        {
          name: "show",
          self: true,
          handler() {
            if (this.stack) {
              css(this.$el, "zIndex", toFloat(css(this.$el, "zIndex")) + active.length);
            }
            const handlers = [
              this.overlay && preventBackgroundFocus(this),
              this.overlay && preventBackgroundScroll(this.$el),
              this.bgClose && listenForBackgroundClose(this),
              this.escClose && listenForEscClose(this)
            ];
            once(
              this.$el,
              "hidden",
              () => handlers.forEach((handler) => handler && handler()),
              { self: true }
            );
            addClass(document.documentElement, this.clsPage);
          }
        },
        {
          name: "shown",
          self: true,
          handler() {
            if (!isFocusable(this.$el)) {
              attr(this.$el, "tabindex", "-1");
            }
            if (!matches(this.$el, ":focus-within")) {
              this.$el.focus();
            }
          }
        },
        {
          name: "hidden",
          self: true,
          handler() {
            if (includes(active, this)) {
              active.splice(active.indexOf(this), 1);
            }
            css(this.$el, "zIndex", "");
            if (!active.some((modal) => modal.clsPage === this.clsPage)) {
              removeClass(document.documentElement, this.clsPage);
            }
          }
        }
      ],
      methods: {
        toggle() {
          return this.isToggled() ? this.hide() : this.show();
        },
        show() {
          if (this.container && parent(this.$el) !== this.container) {
            append(this.container, this.$el);
            return new Promise(
              (resolve) => requestAnimationFrame(() => this.show().then(resolve))
            );
          }
          return this.toggleElement(this.$el, true, animate);
        },
        hide() {
          return this.toggleElement(this.$el, false, animate);
        }
      }
    };
    function animate(el, show, { transitionElement, _toggle }) {
      return new Promise(
        (resolve, reject) => once(el, "show hide", () => {
          var _a;
          (_a = el._reject) == null ? void 0 : _a.call(el);
          el._reject = reject;
          _toggle(el, show);
          const off = once(
            transitionElement,
            "transitionstart",
            () => {
              once(transitionElement, "transitionend transitioncancel", resolve, {
                self: true
              });
              clearTimeout(timer);
            },
            { self: true }
          );
          const timer = setTimeout(
            () => {
              off();
              resolve();
            },
            toMs(css(transitionElement, "transitionDuration"))
          );
        })
      ).then(() => delete el._reject);
    }
    function toMs(time) {
      return time ? endsWith(time, "ms") ? toFloat(time) : toFloat(time) * 1e3 : 0;
    }
    function preventBackgroundFocus(modal) {
      return on(document, "focusin", (e) => {
        if (last(active) === modal && !modal.$el.contains(e.target)) {
          modal.$el.focus();
        }
      });
    }
    function listenForBackgroundClose(modal) {
      return on(document, pointerDown, ({ target }) => {
        if (last(active) !== modal || modal.overlay && !modal.$el.contains(target) || modal.panel.contains(target)) {
          return;
        }
        once(
          document,
          `${pointerUp} ${pointerCancel} scroll`,
          ({ defaultPrevented, type, target: newTarget }) => {
            if (!defaultPrevented && type === pointerUp && target === newTarget) {
              modal.hide();
            }
          },
          true
        );
      });
    }
    function listenForEscClose(modal) {
      return on(document, "keydown", (e) => {
        if (e.keyCode === 27 && last(active) === modal) {
          modal.hide();
        }
      });
    }

    var modal = {
      install,
      mixins: [Modal],
      data: {
        clsPage: "uk-modal-page",
        selPanel: ".uk-modal-dialog",
        selClose: '[class*="uk-modal-close"]'
      },
      events: [
        {
          name: "fullscreenchange webkitendfullscreen",
          capture: true,
          handler(e) {
            if (isTag(e.target, "video") && this.isToggled() && !document.fullscreenElement) {
              this.hide();
            }
          }
        },
        {
          name: "show",
          self: true,
          handler() {
            if (hasClass(this.panel, "uk-margin-auto-vertical")) {
              addClass(this.$el, "uk-flex");
            } else {
              css(this.$el, "display", "block");
            }
            height(this.$el);
          }
        },
        {
          name: "hidden",
          self: true,
          handler() {
            css(this.$el, "display", "");
            removeClass(this.$el, "uk-flex");
          }
        }
      ]
    };
    function install({ modal }) {
      modal.dialog = function(content, options) {
        const dialog = modal($(`<div><div class="uk-modal-dialog">${content}</div></div>`), {
          stack: true,
          role: "alertdialog",
          ...options
        });
        dialog.show();
        on(
          dialog.$el,
          "hidden",
          async () => {
            await Promise.resolve();
            dialog.$destroy(true);
          },
          { self: true }
        );
        return dialog;
      };
      modal.alert = function(message, options) {
        return openDialog(
          ({ i18n }) => `<div class="uk-modal-body">${isString(message) ? message : html(message)}</div> <div class="uk-modal-footer uk-text-right"> <button class="uk-button uk-button-primary uk-modal-close" autofocus>${i18n.ok}</button> </div>`,
          options
        );
      };
      modal.confirm = function(message, options) {
        return openDialog(
          ({ i18n }) => `<form> <div class="uk-modal-body">${isString(message) ? message : html(message)}</div> <div class="uk-modal-footer uk-text-right"> <button class="uk-button uk-button-default uk-modal-close" type="button">${i18n.cancel}</button> <button class="uk-button uk-button-primary" autofocus>${i18n.ok}</button> </div> </form>`,
          options,
          () => Promise.reject()
        );
      };
      modal.prompt = function(message, value, options) {
        const promise = openDialog(
          ({ i18n }) => `<form class="uk-form-stacked"> <div class="uk-modal-body"> <label>${isString(message) ? message : html(message)}</label> <input class="uk-input" autofocus> </div> <div class="uk-modal-footer uk-text-right"> <button class="uk-button uk-button-default uk-modal-close" type="button">${i18n.cancel}</button> <button class="uk-button uk-button-primary">${i18n.ok}</button> </div> </form>`,
          options,
          () => null,
          () => input.value
        );
        const { $el } = promise.dialog;
        const input = $("input", $el);
        input.value = value || "";
        on($el, "show", () => input.select());
        return promise;
      };
      modal.i18n = {
        ok: "Ok",
        cancel: "Cancel"
      };
      function openDialog(tmpl, options, hideFn = noop, submitFn = noop) {
        options = {
          bgClose: false,
          escClose: true,
          ...options,
          i18n: { ...modal.i18n, ...options == null ? void 0 : options.i18n }
        };
        const dialog = modal.dialog(tmpl(options), options);
        return assign(
          new Promise((resolve) => {
            const off = on(dialog.$el, "hide", () => resolve(hideFn()));
            on(dialog.$el, "submit", "form", (e) => {
              e.preventDefault();
              resolve(submitFn(dialog));
              off();
              dialog.hide();
            });
          }),
          { dialog }
        );
      }
    }

    var nav = {
      extends: Accordion,
      data: {
        targets: "> .uk-parent",
        toggle: "> a",
        content: "> ul"
      }
    };

    const clsNavbarTransparent = "uk-navbar-transparent";
    var navbar = {
      extends: Dropnav,
      props: {
        dropbarTransparentMode: Boolean
      },
      data: {
        clsDrop: "uk-navbar-dropdown",
        selNavItem: ".uk-navbar-nav > li > a,a.uk-navbar-item,button.uk-navbar-item,.uk-navbar-item a,.uk-navbar-item button,.uk-navbar-toggle",
        // Simplify with :where() selector once browser target is Safari 14+
        dropbarTransparentMode: false
      },
      computed: {
        navbarContainer: (_, $el) => $el.closest(".uk-navbar-container")
      },
      watch: {
        items() {
          const justify = hasClass(this.$el, "uk-navbar-justify");
          const containers = $$(".uk-navbar-nav, .uk-navbar-left, .uk-navbar-right", this.$el);
          for (const container of containers) {
            const items = justify ? $$(".uk-navbar-nav > li > a, .uk-navbar-item, .uk-navbar-toggle", container).length : "";
            css(container, "flexGrow", items);
          }
        }
      },
      events: [
        {
          name: "show",
          el: ({ dropContainer }) => dropContainer,
          handler({ target }) {
            if (this.getTransparentMode(target) === "remove" && hasClass(this.navbarContainer, clsNavbarTransparent)) {
              removeClass(this.navbarContainer, clsNavbarTransparent);
              this._transparent = true;
            }
          }
        },
        {
          name: "hide",
          el: ({ dropContainer }) => dropContainer,
          async handler() {
            await awaitMacroTask();
            if (!this.getActive() && this._transparent) {
              addClass(this.navbarContainer, clsNavbarTransparent);
              this._transparent = null;
            }
          }
        }
      ],
      methods: {
        getTransparentMode(el) {
          if (!this.navbarContainer) {
            return;
          }
          if (this.dropbar && this.isDropbarDrop(el)) {
            return this.dropbarTransparentMode;
          }
          const drop = this.getDropdown(el);
          if (drop && hasClass(el, "uk-dropbar")) {
            return drop.inset ? "behind" : "remove";
          }
        },
        getDropbarOffset(offsetTop) {
          const { top, height } = offset(this.navbarContainer);
          return top + (this.dropbarTransparentMode === "behind" ? 0 : height + offsetTop);
        }
      }
    };
    function awaitMacroTask() {
      return new Promise((resolve) => setTimeout(resolve));
    }

    var offcanvas = {
      mixins: [Modal],
      args: "mode",
      props: {
        mode: String,
        flip: Boolean,
        overlay: Boolean,
        swiping: Boolean
      },
      data: {
        mode: "slide",
        flip: false,
        overlay: false,
        clsPage: "uk-offcanvas-page",
        clsContainer: "uk-offcanvas-container",
        selPanel: ".uk-offcanvas-bar",
        clsFlip: "uk-offcanvas-flip",
        clsContainerAnimation: "uk-offcanvas-container-animation",
        clsSidebarAnimation: "uk-offcanvas-bar-animation",
        clsMode: "uk-offcanvas",
        clsOverlay: "uk-offcanvas-overlay",
        selClose: ".uk-offcanvas-close",
        container: false,
        swiping: true
      },
      computed: {
        clsFlip: ({ flip, clsFlip }) => flip ? clsFlip : "",
        clsOverlay: ({ overlay, clsOverlay }) => overlay ? clsOverlay : "",
        clsMode: ({ mode, clsMode }) => `${clsMode}-${mode}`,
        clsSidebarAnimation: ({ mode, clsSidebarAnimation }) => mode === "none" || mode === "reveal" ? "" : clsSidebarAnimation,
        clsContainerAnimation: ({ mode, clsContainerAnimation }) => mode !== "push" && mode !== "reveal" ? "" : clsContainerAnimation,
        transitionElement({ mode }) {
          return mode === "reveal" ? parent(this.panel) : this.panel;
        }
      },
      observe: swipe({ filter: ({ swiping }) => swiping }),
      update: {
        read() {
          if (this.isToggled() && !isVisible(this.$el)) {
            this.hide();
          }
        },
        events: ["resize"]
      },
      events: [
        {
          name: "touchmove",
          self: true,
          passive: false,
          filter: ({ overlay }) => overlay,
          handler(e) {
            e.cancelable && e.preventDefault();
          }
        },
        {
          name: "show",
          self: true,
          handler() {
            if (this.mode === "reveal" && !hasClass(parent(this.panel), this.clsMode)) {
              wrapAll(this.panel, "<div>");
              addClass(parent(this.panel), this.clsMode);
            }
            const { body, scrollingElement } = document;
            addClass(body, this.clsContainer, this.clsFlip);
            css(body, "touch-action", "pan-y pinch-zoom");
            css(this.$el, "display", "block");
            css(this.panel, "maxWidth", scrollingElement.clientWidth);
            addClass(this.$el, this.clsOverlay);
            addClass(
              this.panel,
              this.clsSidebarAnimation,
              this.mode === "reveal" ? "" : this.clsMode
            );
            height(body);
            addClass(body, this.clsContainerAnimation);
            this.clsContainerAnimation && suppressUserScale();
          }
        },
        {
          name: "hide",
          self: true,
          handler() {
            removeClass(document.body, this.clsContainerAnimation);
            css(document.body, "touch-action", "");
          }
        },
        {
          name: "hidden",
          self: true,
          handler() {
            this.clsContainerAnimation && resumeUserScale();
            if (this.mode === "reveal") {
              unwrap(this.panel);
            }
            removeClass(this.panel, this.clsSidebarAnimation, this.clsMode);
            removeClass(this.$el, this.clsOverlay);
            css(this.$el, "display", "");
            css(this.panel, "maxWidth", "");
            removeClass(document.body, this.clsContainer, this.clsFlip);
          }
        },
        {
          name: "swipeLeft swipeRight",
          handler(e) {
            if (this.isToggled() && endsWith(e.type, "Left") ^ this.flip) {
              this.hide();
            }
          }
        }
      ]
    };
    function suppressUserScale() {
      getViewport().content += ",user-scalable=0";
    }
    function resumeUserScale() {
      const viewport = getViewport();
      viewport.content = viewport.content.replace(/,user-scalable=0$/, "");
    }
    function getViewport() {
      return $('meta[name="viewport"]', document.head) || append(document.head, '<meta name="viewport">');
    }

    var overflowAuto = {
      mixins: [Class],
      props: {
        selContainer: String,
        selContent: String,
        minHeight: Number
      },
      data: {
        selContainer: ".uk-modal",
        selContent: ".uk-modal-dialog",
        minHeight: 150
      },
      computed: {
        container: ({ selContainer }, $el) => $el.closest(selContainer),
        content: ({ selContent }, $el) => $el.closest(selContent)
      },
      observe: resize({
        target: ({ container, content }) => [container, content]
      }),
      update: {
        read() {
          if (!this.content || !this.container || !isVisible(this.$el)) {
            return false;
          }
          return {
            max: Math.max(
              this.minHeight,
              height(this.container) - (dimensions(this.content).height - height(this.$el))
            )
          };
        },
        write({ max }) {
          css(this.$el, { minHeight: this.minHeight, maxHeight: max });
        },
        events: ["resize"]
      }
    };

    var responsive = {
      props: ["width", "height"],
      connected() {
        addClass(this.$el, "uk-responsive-width");
        css(this.$el, "aspectRatio", `${this.width}/${this.height}`);
      }
    };

    var scroll = {
      props: {
        offset: Number
      },
      data: {
        offset: 0
      },
      connected() {
        registerClick(this);
      },
      disconnected() {
        unregisterClick(this);
      },
      methods: {
        async scrollTo(el) {
          el = el && $(el) || document.body;
          if (trigger(this.$el, "beforescroll", [this, el])) {
            await scrollIntoView(el, { offset: this.offset });
            trigger(this.$el, "scrolled", [this, el]);
          }
        }
      }
    };
    const instances = /* @__PURE__ */ new Set();
    function registerClick(cmp) {
      if (!instances.size) {
        on(document, "click", clickHandler);
      }
      instances.add(cmp);
    }
    function unregisterClick(cmp) {
      instances.delete(cmp);
      if (!instances.size) {
        off(document, "click", clickHandler);
      }
    }
    function clickHandler(e) {
      if (e.defaultPrevented) {
        return;
      }
      for (const instance of instances) {
        if (instance.$el.contains(e.target) && isSameSiteAnchor(instance.$el)) {
          e.preventDefault();
          if (window.location.href !== instance.$el.href) {
            window.history.pushState({}, "", instance.$el.href);
          }
          instance.scrollTo(getTargetedElement(instance.$el));
        }
      }
    }

    const clsInView = "uk-scrollspy-inview";
    var scrollspy = {
      args: "cls",
      props: {
        cls: String,
        target: String,
        hidden: Boolean,
        margin: String,
        repeat: Boolean,
        delay: Number
      },
      data: () => ({
        cls: "",
        target: false,
        hidden: true,
        margin: "-1px",
        repeat: false,
        delay: 0
      }),
      computed: {
        elements: ({ target }, $el) => target ? $$(target, $el) : [$el]
      },
      watch: {
        elements(elements) {
          if (this.hidden) {
            css(filter(elements, `:not(.${clsInView})`), "opacity", 0);
          }
        }
      },
      connected() {
        this.elementData = /* @__PURE__ */ new Map();
      },
      disconnected() {
        for (const [el, state] of this.elementData.entries()) {
          removeClass(el, clsInView, (state == null ? void 0 : state.cls) || "");
        }
        delete this.elementData;
      },
      observe: intersection({
        target: ({ elements }) => elements,
        handler(records) {
          const elements = this.elementData;
          for (const { target: el, isIntersecting } of records) {
            if (!elements.has(el)) {
              elements.set(el, {
                cls: data(el, "uk-scrollspy-class") || this.cls
              });
            }
            const state = elements.get(el);
            if (!this.repeat && state.show) {
              continue;
            }
            state.show = isIntersecting;
          }
          this.$emit();
        },
        options: ({ margin }) => ({ rootMargin: margin }),
        args: { intersecting: false }
      }),
      update: [
        {
          write(data) {
            for (const [el, state] of this.elementData.entries()) {
              if (state.show && !state.inview && !state.queued) {
                state.queued = true;
                data.promise = (data.promise || Promise.resolve()).then(() => new Promise((resolve) => setTimeout(resolve, this.delay))).then(() => {
                  this.toggle(el, true);
                  setTimeout(() => {
                    state.queued = false;
                    this.$emit();
                  }, 300);
                });
              } else if (!state.show && state.inview && !state.queued && this.repeat) {
                this.toggle(el, false);
              }
            }
          }
        }
      ],
      methods: {
        toggle(el, inview) {
          var _a, _b;
          const state = (_a = this.elementData) == null ? void 0 : _a.get(el);
          if (!state) {
            return;
          }
          (_b = state.off) == null ? void 0 : _b.call(state);
          css(el, "opacity", !inview && this.hidden ? 0 : "");
          toggleClass(el, clsInView, inview);
          toggleClass(el, state.cls);
          let match;
          if (match = state.cls.match(/\buk-animation-[\w-]+/g)) {
            const removeAnimationClasses = () => removeClass(el, match);
            if (inview) {
              state.off = once(el, "animationcancel animationend", removeAnimationClasses, {
                self: true
              });
            } else {
              removeAnimationClasses();
            }
          }
          trigger(el, inview ? "inview" : "outview");
          state.inview = inview;
        }
      }
    };

    var scrollspyNav = {
      props: {
        cls: String,
        closest: Boolean,
        scroll: Boolean,
        target: String,
        offset: Number
      },
      data: {
        cls: "uk-active",
        closest: false,
        scroll: false,
        target: 'a[href]:not([role="button"])',
        offset: 0
      },
      computed: {
        links: ({ target }, $el) => $$(target, $el).filter((el) => isSameSiteAnchor(el)),
        elements({ closest }) {
          return this.links.map((el) => el.closest(closest || "*"));
        }
      },
      watch: {
        links(links) {
          if (this.scroll) {
            this.$create("scroll", links, { offset: this.offset });
          }
        }
      },
      observe: [intersection(), scroll$1()],
      update: [
        {
          read() {
            const targets = this.links.map((el) => getTargetedElement(el) || el.ownerDocument);
            const { length } = targets;
            if (!length || !isVisible(this.$el)) {
              return false;
            }
            const scrollElement = scrollParent(targets, true);
            const { scrollTop, scrollHeight } = scrollElement;
            const viewport = offsetViewport(scrollElement);
            const max = scrollHeight - viewport.height;
            let active = false;
            if (scrollTop >= max) {
              active = length - 1;
            } else {
              const offsetBy = this.offset + dimensions(getCoveringElement()).height + viewport.height * 0.1;
              for (let i = 0; i < targets.length; i++) {
                if (offset(targets[i]).top - viewport.top - offsetBy > 0) {
                  break;
                }
                active = +i;
              }
            }
            return { active };
          },
          write({ active }) {
            const changed = active !== false && !hasClass(this.elements[active], this.cls);
            this.links.forEach((el) => el.blur());
            for (let i = 0; i < this.elements.length; i++) {
              toggleClass(this.elements[i], this.cls, +i === active);
            }
            if (changed) {
              trigger(this.$el, "active", [active, this.elements[active]]);
            }
          },
          events: ["scroll", "resize"]
        }
      ]
    };

    var sticky = {
      mixins: [Class, Media],
      props: {
        position: String,
        top: null,
        bottom: null,
        start: null,
        end: null,
        offset: String,
        overflowFlip: Boolean,
        animation: String,
        clsActive: String,
        clsInactive: String,
        clsFixed: String,
        clsBelow: String,
        selTarget: String,
        showOnUp: Boolean,
        targetOffset: Number
      },
      data: {
        position: "top",
        top: false,
        bottom: false,
        start: false,
        end: false,
        offset: 0,
        overflowFlip: false,
        animation: "",
        clsActive: "uk-active",
        clsInactive: "",
        clsFixed: "uk-sticky-fixed",
        clsBelow: "uk-sticky-below",
        selTarget: "",
        showOnUp: false,
        targetOffset: false
      },
      computed: {
        target: ({ selTarget }, $el) => selTarget && $(selTarget, $el) || $el
      },
      connected() {
        this.start = coerce(this.start || this.top);
        this.end = coerce(this.end || this.bottom);
        this.placeholder = $("+ .uk-sticky-placeholder", this.$el) || $('<div class="uk-sticky-placeholder"></div>');
        this.isFixed = false;
        this.setActive(false);
      },
      beforeDisconnect() {
        if (this.isFixed) {
          this.hide();
          removeClass(this.target, this.clsInactive);
        }
        reset(this.$el);
        remove$1(this.placeholder);
        this.placeholder = null;
      },
      observe: [
        viewport(),
        scroll$1({ target: () => document.scrollingElement }),
        resize({
          target: ({ $el }) => [$el, getVisibleParent($el), document.scrollingElement],
          handler(entries) {
            this.$emit(
              this._data.resized && entries.some(({ target }) => target === getVisibleParent(this.$el)) ? "update" : "resize"
            );
            this._data.resized = true;
          }
        })
      ],
      events: [
        {
          name: "load hashchange popstate",
          el: () => window,
          filter: ({ targetOffset }) => targetOffset !== false,
          handler() {
            const { scrollingElement } = document;
            if (!location.hash || scrollingElement.scrollTop === 0) {
              return;
            }
            setTimeout(() => {
              const targetOffset = offset($(location.hash));
              const elOffset = offset(this.$el);
              if (this.isFixed && intersectRect(targetOffset, elOffset)) {
                scrollingElement.scrollTop = Math.ceil(
                  targetOffset.top - elOffset.height - toPx(this.targetOffset, "height", this.placeholder) - toPx(this.offset, "height", this.placeholder)
                );
              }
            });
          }
        }
      ],
      update: [
        {
          read({ height: height$1, width, margin, sticky }, types) {
            this.inactive = !this.matchMedia || !isVisible(this.$el) || !this.$el.offsetHeight;
            if (this.inactive) {
              return;
            }
            const hide = this.isFixed && types.has("update");
            if (hide) {
              preventTransition(this.target);
              this.hide();
            }
            if (!this.active) {
              ({ height: height$1, width } = dimensions(this.$el));
              margin = css(this.$el, "margin");
            }
            if (hide) {
              this.show();
            }
            const viewport2 = toPx("100vh", "height");
            const dynamicViewport = height(window);
            const maxScrollHeight = Math.max(
              0,
              document.scrollingElement.scrollHeight - viewport2
            );
            let position = this.position;
            if (this.overflowFlip && height$1 > viewport2) {
              position = position === "top" ? "bottom" : "top";
            }
            const referenceElement = this.isFixed ? this.placeholder : this.$el;
            let offset$1 = toPx(this.offset, "height", sticky ? this.$el : referenceElement);
            if (position === "bottom" && (height$1 < dynamicViewport || this.overflowFlip)) {
              offset$1 += dynamicViewport - height$1;
            }
            const overflow = this.overflowFlip ? 0 : Math.max(0, height$1 + offset$1 - viewport2);
            const topOffset = offset(referenceElement).top;
            const elHeight = dimensions(this.$el).height;
            const start = (this.start === false ? topOffset : parseProp(this.start, this.$el, topOffset)) - offset$1;
            const end = this.end === false ? maxScrollHeight : Math.min(
              maxScrollHeight,
              parseProp(this.end, this.$el, topOffset + height$1, true) - elHeight - offset$1 + overflow
            );
            sticky = maxScrollHeight && !this.showOnUp && start + offset$1 === topOffset && end === Math.min(
              maxScrollHeight,
              parseProp(true, this.$el, 0, true) - elHeight - offset$1 + overflow
            ) && css(getVisibleParent(this.$el), "overflowY") === "visible";
            return {
              start,
              end,
              offset: offset$1,
              overflow,
              height: height$1,
              elHeight,
              width,
              margin,
              top: offsetPosition(referenceElement)[0],
              sticky,
              viewport: viewport2,
              maxScrollHeight
            };
          },
          write({ height, width, margin, offset, sticky }) {
            if (this.inactive || sticky || !this.isFixed) {
              reset(this.$el);
            }
            if (this.inactive) {
              return;
            }
            if (sticky) {
              height = width = margin = 0;
              css(this.$el, { position: "sticky", top: offset });
            }
            const { placeholder } = this;
            css(placeholder, { height, width, margin });
            if (parent(placeholder) !== parent(this.$el) || sticky ^ index(placeholder) < index(this.$el)) {
              (sticky ? before : after)(this.$el, placeholder);
              placeholder.hidden = true;
            }
          },
          events: ["resize"]
        },
        {
          read({
            scroll: prevScroll = 0,
            dir: prevDir = "down",
            overflow,
            overflowScroll = 0,
            start,
            end,
            elHeight,
            height,
            sticky,
            maxScrollHeight
          }) {
            const scroll2 = Math.min(document.scrollingElement.scrollTop, maxScrollHeight);
            const dir = prevScroll <= scroll2 ? "down" : "up";
            const referenceElement = this.isFixed ? this.placeholder : this.$el;
            return {
              dir,
              prevDir,
              scroll: scroll2,
              prevScroll,
              below: scroll2 > offset(referenceElement).top + (sticky ? Math.min(height, elHeight) : height),
              offsetParentTop: offset(referenceElement.offsetParent).top,
              overflowScroll: clamp(
                overflowScroll + clamp(scroll2, start, end) - clamp(prevScroll, start, end),
                0,
                overflow
              )
            };
          },
          write(data, types) {
            const isScrollUpdate = types.has("scroll");
            const {
              initTimestamp = 0,
              dir,
              prevDir,
              scroll: scroll2,
              prevScroll = 0,
              top,
              start,
              below
            } = data;
            if (scroll2 < 0 || scroll2 === prevScroll && isScrollUpdate || this.showOnUp && !isScrollUpdate && !this.isFixed) {
              return;
            }
            const now = Date.now();
            if (now - initTimestamp > 300 || dir !== prevDir) {
              data.initScroll = scroll2;
              data.initTimestamp = now;
            }
            if (this.showOnUp && !this.isFixed && Math.abs(data.initScroll - scroll2) <= 30 && Math.abs(prevScroll - scroll2) <= 10) {
              return;
            }
            if (this.inactive || scroll2 < start || this.showOnUp && (scroll2 <= start || dir === "down" && isScrollUpdate || dir === "up" && !this.isFixed && !below)) {
              if (!this.isFixed) {
                if (Animation.inProgress(this.$el) && top > scroll2) {
                  Animation.cancel(this.$el);
                  this.hide();
                }
                return;
              }
              if (this.animation && below) {
                if (hasClass(this.$el, "uk-animation-leave")) {
                  return;
                }
                Animation.out(this.$el, this.animation).then(() => this.hide(), noop);
              } else {
                this.hide();
              }
            } else if (this.isFixed) {
              this.update();
            } else if (this.animation && below) {
              this.show();
              Animation.in(this.$el, this.animation).catch(noop);
            } else {
              preventTransition(this.target);
              this.show();
            }
          },
          events: ["resize", "resizeViewport", "scroll"]
        }
      ],
      methods: {
        show() {
          this.isFixed = true;
          this.update();
          this.placeholder.hidden = false;
        },
        hide() {
          const { offset, sticky } = this._data;
          this.setActive(false);
          removeClass(this.$el, this.clsFixed, this.clsBelow);
          if (sticky) {
            css(this.$el, "top", offset);
          } else {
            css(this.$el, {
              position: "",
              top: "",
              width: "",
              marginTop: ""
            });
          }
          this.placeholder.hidden = true;
          this.isFixed = false;
        },
        update() {
          let {
            width,
            scroll: scroll2 = 0,
            overflow,
            overflowScroll = 0,
            start,
            end,
            offset,
            offsetParentTop,
            sticky,
            below
          } = this._data;
          const active = start !== 0 || scroll2 > start;
          if (!sticky) {
            let position = "fixed";
            if (scroll2 > end) {
              offset += end - offsetParentTop + overflowScroll - overflow;
              position = "absolute";
            }
            css(this.$el, { position, width, marginTop: 0 }, "important");
          }
          css(this.$el, "top", offset - overflowScroll);
          this.setActive(active);
          toggleClass(this.$el, this.clsBelow, below);
          addClass(this.$el, this.clsFixed);
        },
        setActive(active) {
          const prev = this.active;
          this.active = active;
          if (active) {
            replaceClass(this.target, this.clsInactive, this.clsActive);
            prev !== active && trigger(this.$el, "active");
          } else {
            replaceClass(this.target, this.clsActive, this.clsInactive);
            if (prev !== active) {
              preventTransition(this.target);
              trigger(this.$el, "inactive");
            }
          }
        }
      }
    };
    function parseProp(value, el, propOffset, padding) {
      if (!value) {
        return 0;
      }
      if (isNumeric(value) || isString(value) && value.match(/^-?\d/)) {
        return propOffset + toPx(value, "height", el, true);
      } else {
        const refElement = value === true ? getVisibleParent(el) : query(value, el);
        return offset(refElement).bottom - (padding && (refElement == null ? void 0 : refElement.contains(el)) ? toFloat(css(refElement, "paddingBottom")) : 0);
      }
    }
    function coerce(value) {
      if (value === "true") {
        return true;
      } else if (value === "false") {
        return false;
      }
      return value;
    }
    function reset(el) {
      css(el, { position: "", top: "", marginTop: "", width: "" });
    }
    const clsTransitionDisable = "uk-transition-disable";
    function preventTransition(element) {
      if (!hasClass(element, clsTransitionDisable)) {
        addClass(element, clsTransitionDisable);
        requestAnimationFrame(() => removeClass(element, clsTransitionDisable));
      }
    }
    function getVisibleParent(element) {
      while (element = parent(element)) {
        if (isVisible(element)) {
          return element;
        }
      }
    }

    const selDisabled = ".uk-disabled *, .uk-disabled, [disabled]";
    var Switcher = {
      mixins: [Togglable],
      args: "connect",
      props: {
        connect: String,
        toggle: String,
        itemNav: String,
        active: Number,
        followFocus: Boolean,
        swiping: Boolean
      },
      data: {
        connect: "~.uk-switcher",
        toggle: "> * > :first-child",
        itemNav: false,
        active: 0,
        cls: "uk-active",
        attrItem: "uk-switcher-item",
        selVertical: ".uk-nav",
        followFocus: false,
        swiping: true
      },
      computed: {
        connects: {
          get: ({ connect }, $el) => queryAll(connect, $el),
          observe: ({ connect }) => connect
        },
        connectChildren() {
          return this.connects.map((el) => children(el)).flat();
        },
        toggles: ({ toggle }, $el) => $$(toggle, $el),
        children(_, $el) {
          return children($el).filter(
            (child) => this.toggles.some((toggle) => child.contains(toggle))
          );
        }
      },
      watch: {
        connects(connects) {
          if (this.swiping) {
            css(connects, "touchAction", "pan-y pinch-zoom");
          }
          this.$emit();
        },
        connectChildren() {
          let index = Math.max(0, this.index());
          for (const el of this.connects) {
            children(el).forEach((child, i) => toggleClass(child, this.cls, i === index));
          }
          this.$emit();
        },
        toggles(toggles) {
          this.$emit();
          const active = this.index();
          this.show(~active ? active : toggles[this.active] || toggles[0]);
        }
      },
      connected() {
        attr(this.$el, "role", "tablist");
      },
      observe: [
        lazyload({ targets: ({ connectChildren }) => connectChildren }),
        swipe({ target: ({ connects }) => connects, filter: ({ swiping }) => swiping })
      ],
      events: [
        {
          name: "click keydown",
          delegate: ({ toggle }) => toggle,
          handler(e) {
            if (!matches(e.current, selDisabled) && (e.type === "click" || e.keyCode === keyMap.SPACE)) {
              e.preventDefault();
              this.show(e.current);
            }
          }
        },
        {
          name: "keydown",
          delegate: ({ toggle }) => toggle,
          handler(e) {
            const { current, keyCode } = e;
            const isVertical = matches(this.$el, this.selVertical);
            let i = keyCode === keyMap.HOME ? 0 : keyCode === keyMap.END ? "last" : keyCode === keyMap.LEFT && !isVertical || keyCode === keyMap.UP && isVertical ? "previous" : keyCode === keyMap.RIGHT && !isVertical || keyCode === keyMap.DOWN && isVertical ? "next" : -1;
            if (~i) {
              e.preventDefault();
              const toggles = this.toggles.filter((el) => !matches(el, selDisabled));
              const next = toggles[getIndex(i, toggles, toggles.indexOf(current))];
              next.focus();
              if (this.followFocus) {
                this.show(next);
              }
            }
          }
        },
        {
          name: "click",
          el: ({ $el, connects, itemNav }) => connects.concat(itemNav ? queryAll(itemNav, $el) : []),
          delegate: ({ attrItem }) => `[${attrItem}],[data-${attrItem}]`,
          handler(e) {
            if (e.target.closest("a,button")) {
              e.preventDefault();
              this.show(data(e.current, this.attrItem));
            }
          }
        },
        {
          name: "swipeRight swipeLeft",
          filter: ({ swiping }) => swiping,
          el: ({ connects }) => connects,
          handler({ type }) {
            this.show(endsWith(type, "Left") ? "next" : "previous");
          }
        }
      ],
      update() {
        var _a;
        for (const el of this.connects) {
          if (isTag(el, "ul")) {
            attr(el, "role", "presentation");
          }
        }
        attr(children(this.$el), "role", "presentation");
        for (const index in this.toggles) {
          const toggle = this.toggles[index];
          const item = (_a = this.connects[0]) == null ? void 0 : _a.children[index];
          attr(toggle, "role", "tab");
          if (!item) {
            continue;
          }
          toggle.id = generateId(this, toggle);
          item.id = generateId(this, item);
          attr(toggle, "aria-controls", item.id);
          attr(item, { role: "tabpanel", "aria-labelledby": toggle.id });
        }
        attr(this.$el, "aria-orientation", matches(this.$el, this.selVertical) ? "vertical" : null);
      },
      methods: {
        index() {
          return findIndex(this.children, (el) => hasClass(el, this.cls));
        },
        show(item) {
          const toggles = this.toggles.filter((el) => !matches(el, selDisabled));
          const prev = this.index();
          const next = getIndex(
            !isNode(item) || includes(toggles, item) ? item : 0,
            toggles,
            getIndex(this.toggles[prev], toggles)
          );
          const active = getIndex(toggles[next], this.toggles);
          this.children.forEach((child, i) => {
            toggleClass(child, this.cls, active === i);
            attr(this.toggles[i], {
              "aria-selected": active === i,
              tabindex: active === i ? null : -1
            });
          });
          const animate = prev >= 0 && prev !== next;
          this.connects.forEach(async ({ children: children2 }) => {
            const actives = toArray(children2).filter(
              (child, i) => i !== active && hasClass(child, this.cls)
            );
            if (await this.toggleElement(actives, false, animate)) {
              await this.toggleElement(children2[active], true, animate);
            }
          });
        }
      }
    };

    var tab = {
      mixins: [Class],
      extends: Switcher,
      props: {
        media: Boolean
      },
      data: {
        media: 960,
        attrItem: "uk-tab-item",
        selVertical: ".uk-tab-left,.uk-tab-right"
      },
      connected() {
        const cls = hasClass(this.$el, "uk-tab-left") ? "uk-tab-left" : hasClass(this.$el, "uk-tab-right") ? "uk-tab-right" : false;
        if (cls) {
          this.$create("toggle", this.$el, { cls, mode: "media", media: this.media });
        }
      }
    };

    const KEY_SPACE = 32;
    var toggle = {
      mixins: [Media, Togglable],
      args: "target",
      props: {
        href: String,
        target: null,
        mode: "list",
        queued: Boolean
      },
      data: {
        href: false,
        target: false,
        mode: "click",
        queued: true
      },
      computed: {
        target: {
          get: ({ target }, $el) => {
            target = queryAll(target || $el.hash, $el);
            return target.length ? target : [$el];
          },
          observe: ({ target }) => target
        }
      },
      connected() {
        if (!includes(this.mode, "media")) {
          if (!isFocusable(this.$el)) {
            attr(this.$el, "tabindex", "0");
          }
          if (!this.cls && isTag(this.$el, "a")) {
            attr(this.$el, "role", "button");
          }
        }
      },
      observe: lazyload({ targets: ({ target }) => target }),
      events: [
        {
          name: pointerDown,
          filter: ({ mode }) => includes(mode, "hover"),
          handler(e) {
            this._preventClick = null;
            if (!isTouch(e) || isBoolean(this._showState) || this.$el.disabled) {
              return;
            }
            trigger(this.$el, "focus");
            once(
              document,
              pointerDown,
              () => trigger(this.$el, "blur"),
              true,
              (e2) => !this.$el.contains(e2.target)
            );
            if (includes(this.mode, "click")) {
              this._preventClick = true;
            }
          }
        },
        {
          // mouseenter mouseleave are added because of Firefox bug,
          // where pointerleave is triggered immediately after pointerenter on scroll
          name: `mouseenter mouseleave ${pointerEnter} ${pointerLeave} focus blur`,
          filter: ({ mode }) => includes(mode, "hover"),
          handler(e) {
            if (isTouch(e) || this.$el.disabled) {
              return;
            }
            const show = includes(["mouseenter", pointerEnter, "focus"], e.type);
            const expanded = this.isToggled(this.target);
            if (!show && (!isBoolean(this._showState) || e.type !== "blur" && matches(this.$el, ":focus") || e.type === "blur" && matches(this.$el, ":hover"))) {
              if (expanded === this._showState) {
                this._showState = null;
              }
              return;
            }
            if (show && isBoolean(this._showState) && expanded !== this._showState) {
              return;
            }
            this._showState = show ? expanded : null;
            this.toggle(`toggle${show ? "show" : "hide"}`);
          }
        },
        {
          name: "keydown",
          filter: ({ $el, mode }) => includes(mode, "click") && !isTag($el, "input"),
          handler(e) {
            if (e.keyCode === KEY_SPACE) {
              e.preventDefault();
              this.$el.click();
            }
          }
        },
        {
          name: "click",
          filter: ({ mode }) => ["click", "hover"].some((m) => includes(mode, m)),
          handler(e) {
            let link;
            if (this._preventClick || e.target.closest('a[href="#"], a[href=""]') || (link = e.target.closest("a[href]")) && (!this.isToggled(this.target) || link.hash && matches(this.target, link.hash))) {
              e.preventDefault();
            }
            if (!this._preventClick && includes(this.mode, "click")) {
              this.toggle();
            }
          }
        },
        {
          name: "mediachange",
          filter: ({ mode }) => includes(mode, "media"),
          el: ({ target }) => target,
          handler(e, mediaObj) {
            if (mediaObj.matches ^ this.isToggled(this.target)) {
              this.toggle();
            }
          }
        }
      ],
      methods: {
        async toggle(type) {
          if (!trigger(this.target, type || "toggle", [this])) {
            return;
          }
          if (hasAttr(this.$el, "aria-expanded")) {
            attr(this.$el, "aria-expanded", !this.isToggled(this.target));
          }
          if (!this.queued) {
            return this.toggleElement(this.target);
          }
          const leaving = this.target.filter((el) => hasClass(el, this.clsLeave));
          if (leaving.length) {
            for (const el of this.target) {
              const isLeaving = includes(leaving, el);
              this.toggleElement(el, isLeaving, isLeaving);
            }
            return;
          }
          const toggled = this.target.filter(this.isToggled);
          if (await this.toggleElement(toggled, false)) {
            await this.toggleElement(
              this.target.filter((el) => !includes(toggled, el)),
              true
            );
          }
        }
      }
    };

    var components = /*#__PURE__*/Object.freeze({
        __proto__: null,
        Accordion: Accordion,
        Alert: alert,
        Close: Close,
        Cover: cover,
        Drop: drop,
        DropParentIcon: IconComponent,
        Dropdown: drop,
        Dropnav: Dropnav,
        FormCustom: formCustom,
        Grid: grid,
        HeightMatch: heightMatch,
        HeightPlaceholder: heightPlaceholder,
        HeightViewport: heightViewport,
        Icon: Icon,
        Img: img,
        Inverse: inverse,
        Leader: leader,
        Margin: Margin,
        Marker: Marker,
        Modal: modal,
        Nav: nav,
        NavParentIcon: NavParentIcon,
        Navbar: navbar,
        NavbarParentIcon: IconComponent,
        NavbarToggleIcon: NavbarToggleIcon,
        Offcanvas: offcanvas,
        OverflowAuto: overflowAuto,
        OverlayIcon: IconComponent,
        PaginationNext: PaginationNext,
        PaginationPrevious: PaginationPrevious,
        Responsive: responsive,
        Scroll: scroll,
        Scrollspy: scrollspy,
        ScrollspyNav: scrollspyNav,
        SearchIcon: Search,
        SlidenavNext: Slidenav,
        SlidenavPrevious: Slidenav,
        Spinner: Spinner,
        Sticky: sticky,
        Svg: svg,
        Switcher: Switcher,
        Tab: tab,
        Toggle: toggle,
        Totop: Totop,
        Video: Video
    });

    each(components, (component, name) => App.component(name, component));
    boot(App);

    return App;

}));
