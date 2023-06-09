(function(e, a) { for(var i in a) e[i] = a[i]; }(this, /******/ (function(modules) { // webpackBootstrap
    /******/ 	// The module cache
    /******/ 	var installedModules = {};
    /******/
    /******/ 	// The require function
    /******/ 	function __webpack_require__(moduleId) {
        /******/
        /******/ 		// Check if module is in cache
        /******/ 		if(installedModules[moduleId]) {
            /******/ 			return installedModules[moduleId].exports;
            /******/ 		}
        /******/ 		// Create a new module (and put it into the cache)
        /******/ 		var module = installedModules[moduleId] = {
            /******/ 			i: moduleId,
            /******/ 			l: false,
            /******/ 			exports: {}
            /******/ 		};
        /******/
        /******/ 		// Execute the module function
        /******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
        /******/
        /******/ 		// Flag the module as loaded
        /******/ 		module.l = true;
        /******/
        /******/ 		// Return the exports of the module
        /******/ 		return module.exports;
        /******/ 	}
    /******/
    /******/
    /******/ 	// expose the modules object (__webpack_modules__)
    /******/ 	__webpack_require__.m = modules;
    /******/
    /******/ 	// expose the module cache
    /******/ 	__webpack_require__.c = installedModules;
    /******/
    /******/ 	// define getter function for harmony exports
    /******/ 	__webpack_require__.d = function(exports, name, getter) {
        /******/ 		if(!__webpack_require__.o(exports, name)) {
            /******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
            /******/ 		}
        /******/ 	};
    /******/
    /******/ 	// define __esModule on exports
    /******/ 	__webpack_require__.r = function(exports) {
        /******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
            /******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
            /******/ 		}
        /******/ 		Object.defineProperty(exports, '__esModule', { value: true });
        /******/ 	};
    /******/
    /******/ 	// create a fake namespace object
    /******/ 	// mode & 1: value is a module id, require it
    /******/ 	// mode & 2: merge all properties of value into the ns
    /******/ 	// mode & 4: return value when already ns object
    /******/ 	// mode & 8|1: behave like require
    /******/ 	__webpack_require__.t = function(value, mode) {
        /******/ 		if(mode & 1) value = __webpack_require__(value);
        /******/ 		if(mode & 8) return value;
        /******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
        /******/ 		var ns = Object.create(null);
        /******/ 		__webpack_require__.r(ns);
        /******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
        /******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
        /******/ 		return ns;
        /******/ 	};
    /******/
    /******/ 	// getDefaultExport function for compatibility with non-harmony modules
    /******/ 	__webpack_require__.n = function(module) {
        /******/ 		var getter = module && module.__esModule ?
            /******/ 			function getDefault() { return module['default']; } :
            /******/ 			function getModuleExports() { return module; };
        /******/ 		__webpack_require__.d(getter, 'a', getter);
        /******/ 		return getter;
        /******/ 	};
    /******/
    /******/ 	// Object.prototype.hasOwnProperty.call
    /******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
    /******/
    /******/ 	// __webpack_public_path__
    /******/ 	__webpack_require__.p = "";
    /******/
    /******/
    /******/ 	// Load entry module and return exports
    /******/ 	return __webpack_require__(__webpack_require__.s = "./docs/examples/extensions/table-column/js/index.js");
    /******/ })
    /************************************************************************/
    /******/ ({

        /***/ "./docs/examples/extensions/table-column/js/index.js":
        /*!***********************************************************!*\
          !*** ./docs/examples/extensions/table-column/js/index.js ***!
          \***********************************************************/
        /*! no exports provided */
        /***/ (function(module, __webpack_exports__, __webpack_require__) {

            "use strict";
            __webpack_require__.r(__webpack_exports__);
            /* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "./node_modules/@babel/runtime/helpers/toConsumableArray.js");
            /* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__);
            /* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
            /* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__);
            /* harmony import */ var _woocommerce_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @woocommerce/components */ "@woocommerce/components");
            /* harmony import */ var _woocommerce_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_components__WEBPACK_IMPORTED_MODULE_2__);



            Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__["addFilter"])('woocommerce_admin_orders_report_charts', 'plugin-domain', function (charts) {
                return [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(charts), [{
                    key: "cog_profit",
                    label: 'Profit',
                    order: "desc",
                    orderby: "date",
                    type: "currency"
                }, {
                    key: "cog_cost",
                    label: 'Cost',
                    order: "desc",
                    orderby: "date",
                    type: "currency"
                }, {
                    key: 'gross_sales',
                    label: 'Gross Sales',
                    type: 'currency'
                }, {
                    key: 'shipping',
                    label: 'Shipping',
                    type: 'currency'
                }]);
            });
            Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__["addFilter"])('woocommerce_admin_report_table', 'plugin-domain', function (reportTableData) {
                if (reportTableData.endpoint !== 'orders') {
                    return reportTableData;
                }

                reportTableData.headers = [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(reportTableData.headers), [{
                    label: 'Profit',
                    // wds
                    key: 'cog_profit' // wds

                }, {
                    label: 'Cost',
                    // wds
                    key: 'cog_cost' // wds

                }]);

                if (!reportTableData.items || !reportTableData.items.data || !reportTableData.items.data.length) {
                    return reportTableData;
                }

                var newRows = reportTableData.rows.map(function (row, index) {
                    var order = reportTableData.items.data[index];
                    var profit = order.cog_profit;
                    var cost = order.cog_cost;
                    var currency =  window.wc.wcSettings.CURRENCY.symbol;

                    var newRow = [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(row), [{
                        display: currency + "" + profit.toFixed(2),
                        value: profit
                    }, {
                        display: currency + cost.toFixed(2),
                        value: cost
                    }]);
                    return newRow;
                });
                reportTableData.rows = newRows;
                return reportTableData;
            });

            /***/ }),

        /***/ "./node_modules/@babel/runtime/helpers/arrayLikeToArray.js":
        /*!*****************************************************************!*\
          !*** ./node_modules/@babel/runtime/helpers/arrayLikeToArray.js ***!
          \*****************************************************************/
        /*! no static exports found */
        /***/ (function(module, exports) {

            function _arrayLikeToArray(arr, len) {
                if (len == null || len > arr.length) len = arr.length;

                for (var i = 0, arr2 = new Array(len); i < len; i++) {
                    arr2[i] = arr[i];
                }

                return arr2;
            }

            module.exports = _arrayLikeToArray;

            /***/ }),

        /***/ "./node_modules/@babel/runtime/helpers/arrayWithoutHoles.js":
        /*!******************************************************************!*\
          !*** ./node_modules/@babel/runtime/helpers/arrayWithoutHoles.js ***!
          \******************************************************************/
        /*! no static exports found */
        /***/ (function(module, exports, __webpack_require__) {

            var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray */ "./node_modules/@babel/runtime/helpers/arrayLikeToArray.js");

            function _arrayWithoutHoles(arr) {
                if (Array.isArray(arr)) return arrayLikeToArray(arr);
            }

            module.exports = _arrayWithoutHoles;

            /***/ }),

        /***/ "./node_modules/@babel/runtime/helpers/iterableToArray.js":
        /*!****************************************************************!*\
          !*** ./node_modules/@babel/runtime/helpers/iterableToArray.js ***!
          \****************************************************************/
        /*! no static exports found */
        /***/ (function(module, exports) {

            function _iterableToArray(iter) {
                if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter);
            }

            module.exports = _iterableToArray;

            /***/ }),

        /***/ "./node_modules/@babel/runtime/helpers/nonIterableSpread.js":
        /*!******************************************************************!*\
          !*** ./node_modules/@babel/runtime/helpers/nonIterableSpread.js ***!
          \******************************************************************/
        /*! no static exports found */
        /***/ (function(module, exports) {

            function _nonIterableSpread() {
                throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
            }

            module.exports = _nonIterableSpread;

            /***/ }),

        /***/ "./node_modules/@babel/runtime/helpers/toConsumableArray.js":
        /*!******************************************************************!*\
          !*** ./node_modules/@babel/runtime/helpers/toConsumableArray.js ***!
          \******************************************************************/
        /*! no static exports found */
        /***/ (function(module, exports, __webpack_require__) {

            var arrayWithoutHoles = __webpack_require__(/*! ./arrayWithoutHoles */ "./node_modules/@babel/runtime/helpers/arrayWithoutHoles.js");

            var iterableToArray = __webpack_require__(/*! ./iterableToArray */ "./node_modules/@babel/runtime/helpers/iterableToArray.js");

            var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray */ "./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js");

            var nonIterableSpread = __webpack_require__(/*! ./nonIterableSpread */ "./node_modules/@babel/runtime/helpers/nonIterableSpread.js");

            function _toConsumableArray(arr) {
                return arrayWithoutHoles(arr) || iterableToArray(arr) || unsupportedIterableToArray(arr) || nonIterableSpread();
            }

            module.exports = _toConsumableArray;

            /***/ }),

        /***/ "./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js":
        /*!***************************************************************************!*\
          !*** ./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js ***!
          \***************************************************************************/
        /*! no static exports found */
        /***/ (function(module, exports, __webpack_require__) {

            var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray */ "./node_modules/@babel/runtime/helpers/arrayLikeToArray.js");

            function _unsupportedIterableToArray(o, minLen) {
                if (!o) return;
                if (typeof o === "string") return arrayLikeToArray(o, minLen);
                var n = Object.prototype.toString.call(o).slice(8, -1);
                if (n === "Object" && o.constructor) n = o.constructor.name;
                if (n === "Map" || n === "Set") return Array.from(o);
                if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return arrayLikeToArray(o, minLen);
            }

            module.exports = _unsupportedIterableToArray;

            /***/ }),

        /***/ "@woocommerce/components":
        /*!*********************************************!*\
          !*** external {"this":["wc","components"]} ***!
          \*********************************************/
        /*! no static exports found */
        /***/ (function(module, exports) {

            (function() { module.exports = this["wc"]["components"]; }());

            /***/ }),

        /***/ "@wordpress/hooks":
        /*!****************************************!*\
          !*** external {"this":["wp","hooks"]} ***!
          \****************************************/
        /*! no static exports found */
        /***/ (function(module, exports) {

            (function() { module.exports = this["wp"]["hooks"]; }());

            /***/ })

        /******/ })));
//# sourceMappingURL=index.js.map