(self["webpackChunk"] = self["webpackChunk"] || []).push([["./js/vmobile"],{

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/notify.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/notify.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => __WEBPACK_DEFAULT_EXPORT__
/* harmony export */ });
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);


function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }

function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: "Notify",
  props: ["authuser", "authuserid"],
  components: {},
  data: function data() {
    return {
      totalnotification: 0,
      userNotifyChannel: null,
      onlineUsers: [],
      notifications: [],
      multiLine: true,
      snackbar: false,
      text: null,
      url: null,
      dialog: true
    };
  },
  mounted: function mounted() {
    this.initUserNotifyChannel();
    this.initUserOnlineListeners();
  },
  methods: {
    /**
     * Presence Broadcast Channel Listeners and Methods
     * Provided by Laravel.
     * Websockets with Pusher
     */
    initUserNotifyChannel: function initUserNotifyChannel() {
      var _this = this;

      return _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee() {
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                _this.userNotifyChannel = window.Echo.join("notify-channel");
                console.log(_this.userNotifyChannel);

              case 2:
              case "end":
                return _context.stop();
            }
          }
        }, _callee);
      }))();
    },
    initUserOnlineListeners: function initUserOnlineListeners() {
      var _this2 = this;

      this.userNotifyChannel.here(function (users) {
        //console.log("here")
        _this2.onlineUsers = users;
      });
      this.userNotifyChannel.joining(function (user) {
        //console.log("joining")
        //console.log(user)
        var joiningUserIndex = _this2.onlineUsers.findIndex(function (data) {
          return data.id === user.id;
        });

        if (joiningUserIndex < 0) {
          _this2.onlineUsers.push(user);
        }
      });
      this.userNotifyChannel.leaving(function (user) {
        var leavingUserIndex = _this2.onlineUsers.findIndex(function (data) {
          return data.id === user.id;
        });

        _this2.onlineUsers.splice(leavingUserIndex, 1);
      }); // listen to incomming call

      var _vm = this;

      this.userNotifyChannel.listen("Notify", function (_ref) {
        var data = _ref.data;
        console.log("Notify");
        console.log(data);
        console.log(_this2.authuserid);

        if (parseInt(data.user) === parseInt(_this2.authuserid)) {
          _this2.totalnotification = data.count_notification;
          var notify = data.notify;

          if (_this2.notifications.length) {
            _vm.text = data.message;
            _vm.snackbar = true;
            _vm.url = data.url;
            var notify = {
              message: data.message,
              sub_message: data.sub_message,
              url: data.url
            };
            _vm.dialog = true;

            _this2.notifications.unshift(notify);
          } else {
            _vm.text = data.message;
            _vm.snackbar = true;
            _vm.url = data.url;
            var notify = {
              message: data.message,
              sub_message: data.sub_message,
              url: data.url
            };

            _this2.notifications.unshift(notify);

            _vm.dialog = true;
          }
        }
      });
    },
    getUserOnlineStatus: function getUserOnlineStatus(id) {
      var onlineUserIndex = this.onlineUsers.findIndex(function (data) {
        return data.id === id;
      });

      if (onlineUserIndex < 0) {
        return "Offline";
      }

      return "Online";
    }
  }
});

/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9[0].rules[0].use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9[0].rules[0].use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/notify.vue?vue&type=style&index=0&id=8382c658&scoped=true&lang=css&":
/*!*********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9[0].rules[0].use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9[0].rules[0].use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/notify.vue?vue&type=style&index=0&id=8382c658&scoped=true&lang=css& ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => __WEBPACK_DEFAULT_EXPORT__
/* harmony export */ });
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__);
// Imports

var ___CSS_LOADER_EXPORT___ = _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default()(function(i){return i[1]});
// Module
___CSS_LOADER_EXPORT___.push([module.id, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n/* CSS used here will be applied after bootstrap.css */\n.dropdown[data-v-8382c658] {\n    display: inline-block;\n    margin-left: 20px;\n    padding: 10px;\n}\n.glyphicon-bell[data-v-8382c658] {\n    font-size: 1.5rem;\n}\n.notifications[data-v-8382c658] {\n    min-width: 420px;\n}\n.notifications-wrapper[data-v-8382c658] {\n    overflow: auto;\n    max-height: 250px;\n}\n.menu-title[data-v-8382c658] {\n    color: #ff7788;\n    font-size: 14px;\n    display: inline-block;\n}\n.glyphicon-circle-arrow-right[data-v-8382c658] {\n    margin-left: 10px;\n}\n.notification-heading[data-v-8382c658],\n.notification-footer[data-v-8382c658] {\n    padding: 2px 10px;\n}\n.dropdown-menu.divider[data-v-8382c658] {\n    margin: 5px 0;\n}\n.item-title[data-v-8382c658] {\n    font-size: 1.3rem;\n    color: #000;\n}\n.notifications a.content[data-v-8382c658] {\n    text-decoration: none;\n    background: #ccc;\n}\n.notification-item[data-v-8382c658] {\n    padding: 10px;\n    margin: 5px;\n    background: #ccc;\n    border-radius: 4px;\n}\n.notificationdiv .dropdown-menu[data-v-8382c658]{\n    left: -300px;\n}\nli.divider[data-v-8382c658] {\n    list-style: none;\n}\n\n\n\n", ""]);
// Exports
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9[0].rules[0].use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9[0].rules[0].use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/notify.vue?vue&type=style&index=0&id=8382c658&scoped=true&lang=css&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9[0].rules[0].use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9[0].rules[0].use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/notify.vue?vue&type=style&index=0&id=8382c658&scoped=true&lang=css& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => __WEBPACK_DEFAULT_EXPORT__
/* harmony export */ });
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !../../../node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js */ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_0_rules_0_use_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_0_rules_0_use_2_node_modules_vue_loader_lib_index_js_vue_loader_options_notify_vue_vue_type_style_index_0_id_8382c658_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-9[0].rules[0].use[1]!../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9[0].rules[0].use[2]!../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./notify.vue?vue&type=style&index=0&id=8382c658&scoped=true&lang=css& */ "./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9[0].rules[0].use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9[0].rules[0].use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/notify.vue?vue&type=style&index=0&id=8382c658&scoped=true&lang=css&");

            

var options = {};

options.insert = "head";
options.singleton = false;

var update = _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default()(_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_0_rules_0_use_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_0_rules_0_use_2_node_modules_vue_loader_lib_index_js_vue_loader_options_notify_vue_vue_type_style_index_0_id_8382c658_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_1__.default, options);



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_0_rules_0_use_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_0_rules_0_use_2_node_modules_vue_loader_lib_index_js_vue_loader_options_notify_vue_vue_type_style_index_0_id_8382c658_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_1__.default.locals || {});

/***/ }),

/***/ "./resources/js/components/notify.vue":
/*!********************************************!*\
  !*** ./resources/js/components/notify.vue ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => __WEBPACK_DEFAULT_EXPORT__
/* harmony export */ });
/* harmony import */ var _notify_vue_vue_type_template_id_8382c658_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./notify.vue?vue&type=template&id=8382c658&scoped=true& */ "./resources/js/components/notify.vue?vue&type=template&id=8382c658&scoped=true&");
/* harmony import */ var _notify_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./notify.vue?vue&type=script&lang=js& */ "./resources/js/components/notify.vue?vue&type=script&lang=js&");
/* harmony import */ var _notify_vue_vue_type_style_index_0_id_8382c658_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./notify.vue?vue&type=style&index=0&id=8382c658&scoped=true&lang=css& */ "./resources/js/components/notify.vue?vue&type=style&index=0&id=8382c658&scoped=true&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! !../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");



;


/* normalize component */

var component = (0,_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__.default)(
  _notify_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__.default,
  _notify_vue_vue_type_template_id_8382c658_scoped_true___WEBPACK_IMPORTED_MODULE_0__.render,
  _notify_vue_vue_type_template_id_8382c658_scoped_true___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns,
  false,
  null,
  "8382c658",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/notify.vue"
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (component.exports);

/***/ }),

/***/ "./resources/js/components/notify.vue?vue&type=script&lang=js&":
/*!*********************************************************************!*\
  !*** ./resources/js/components/notify.vue?vue&type=script&lang=js& ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => __WEBPACK_DEFAULT_EXPORT__
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_0_rules_0_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_notify_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./notify.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/notify.vue?vue&type=script&lang=js&");
 /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_0_rules_0_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_notify_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__.default); 

/***/ }),

/***/ "./resources/js/components/notify.vue?vue&type=style&index=0&id=8382c658&scoped=true&lang=css&":
/*!*****************************************************************************************************!*\
  !*** ./resources/js/components/notify.vue?vue&type=style&index=0&id=8382c658&scoped=true&lang=css& ***!
  \*****************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_dist_cjs_js_node_modules_css_loader_dist_cjs_js_clonedRuleSet_9_0_rules_0_use_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_9_0_rules_0_use_2_node_modules_vue_loader_lib_index_js_vue_loader_options_notify_vue_vue_type_style_index_0_id_8382c658_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/style-loader/dist/cjs.js!../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-9[0].rules[0].use[1]!../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9[0].rules[0].use[2]!../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./notify.vue?vue&type=style&index=0&id=8382c658&scoped=true&lang=css& */ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-9[0].rules[0].use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-9[0].rules[0].use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/notify.vue?vue&type=style&index=0&id=8382c658&scoped=true&lang=css&");


/***/ }),

/***/ "./resources/js/components/notify.vue?vue&type=template&id=8382c658&scoped=true&":
/*!***************************************************************************************!*\
  !*** ./resources/js/components/notify.vue?vue&type=template&id=8382c658&scoped=true& ***!
  \***************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => /* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_notify_vue_vue_type_template_id_8382c658_scoped_true___WEBPACK_IMPORTED_MODULE_0__.render,
/* harmony export */   "staticRenderFns": () => /* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_notify_vue_vue_type_template_id_8382c658_scoped_true___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_notify_vue_vue_type_template_id_8382c658_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./notify.vue?vue&type=template&id=8382c658&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/notify.vue?vue&type=template&id=8382c658&scoped=true&");


/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/notify.vue?vue&type=template&id=8382c658&scoped=true&":
/*!******************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/notify.vue?vue&type=template&id=8382c658&scoped=true& ***!
  \******************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => /* binding */ render,
/* harmony export */   "staticRenderFns": () => /* binding */ staticRenderFns
/* harmony export */ });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("main", [
    _c(
      "div",
      { staticClass: "text-center" },
      [
        _c(
          "v-snackbar",
          {
            attrs: { "multi-line": _vm.multiLine, timeout: "-1", top: true },
            scopedSlots: _vm._u([
              {
                key: "action",
                fn: function(ref) {
                  var attrs = ref.attrs
                  return [
                    _c(
                      "v-btn",
                      _vm._b(
                        {
                          attrs: { color: "red", text: "" },
                          on: {
                            click: function($event) {
                              _vm.snackbar = false
                            }
                          }
                        },
                        "v-btn",
                        attrs,
                        false
                      ),
                      [_vm._v(" Close ")]
                    ),
                    _vm._v(" "),
                    _c(
                      "a",
                      { attrs: { href: _vm.url } },
                      [
                        _c(
                          "v-btn",
                          _vm._b(
                            { attrs: { color: "green", text: "" } },
                            "v-btn",
                            attrs,
                            false
                          ),
                          [_vm._v(" Open ")]
                        )
                      ],
                      1
                    )
                  ]
                }
              }
            ]),
            model: {
              value: _vm.snackbar,
              callback: function($$v) {
                _vm.snackbar = $$v
              },
              expression: "snackbar"
            }
          },
          [
            _c("a", { attrs: { href: _vm.url } }, [
              _vm._v(_vm._s(_vm.text) + " ")
            ])
          ]
        )
      ],
      1
    )
  ])
}
var staticRenderFns = []
render._withStripped = true



/***/ })

}]);