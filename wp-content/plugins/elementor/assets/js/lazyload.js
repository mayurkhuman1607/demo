/*! elementor - v3.8.0 - 08-11-2022 */
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};
/*!*************************************************!*\
  !*** ../modules/lazyload/assets/js/frontend.js ***!
  \*************************************************/


document.addEventListener('DOMContentLoaded', function () {
  var dataAttribute = 'data-e-bg-lazyload';
  var lazyloadBackgrounds = document.querySelectorAll("[".concat(dataAttribute, "]:not(.lazyloaded)"));
  var lazyloadBackgroundObserver = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        var _ref = [entry.target, entry.target],
          lazyloadBackground = _ref[0],
          element = _ref[1];
        var lazyloadSelector = lazyloadBackground.getAttribute(dataAttribute);
        if (lazyloadSelector) {
          lazyloadBackground = lazyloadSelector;
        }
        lazyloadBackground.classList.add('lazyloaded');
        lazyloadBackgroundObserver.unobserve(element);
      }
    });
  });
  lazyloadBackgrounds.forEach(function (lazyloadBackground) {
    lazyloadBackgroundObserver.observe(lazyloadBackground);
  });
});
/******/ })()
;
//# sourceMappingURL=lazyload.js.map