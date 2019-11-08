(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["transforms"],{

/***/ "./assets/js/transforms.js":
/*!*********************************!*\
  !*** ./assets/js/transforms.js ***!
  \*********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_array_find__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.array.find */ "./node_modules/core-js/modules/es.array.find.js");
/* harmony import */ var core_js_modules_es_array_find__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_find__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_object_define_property__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.object.define-property */ "./node_modules/core-js/modules/es.object.define-property.js");
/* harmony import */ var core_js_modules_es_object_define_property__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_define_property__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_parse_int__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.parse-int */ "./node_modules/core-js/modules/es.parse-int.js");
/* harmony import */ var core_js_modules_es_parse_int__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_parse_int__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var select2__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! select2 */ "./node_modules/select2/dist/js/select2.js");
/* harmony import */ var select2__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(select2__WEBPACK_IMPORTED_MODULE_3__);




function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

__webpack_require__(/*! ../scss/transforms.scss */ "./assets/scss/transforms.scss");

var $ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");

 // @see https://symfony.com/doc/current/form/dynamic_form_modification.html#dynamic-generation-for-submitted-forms

$(function () {
  var $preview = $('#data-flow-preview');
  $('[data-run-flow-to-step]').on('click', function () {
    var step = parseInt($(this).data('run-flow-to-step'));
    $preview.attr('src', $preview.data('src') + '?steps=' + step);
  });
  $('#new-tranform-transformer').select2();
  var $transformer = $('#data_transform_transformer');
  $transformer.on('change', function () {
    var _data,
        _arguments = arguments;

    var $form = $(this).closest('form'); // Simulate form data, but only include the selected sport value.

    var data = (_data = {}, _defineProperty(_data, $transformer.attr('name'), $transformer.val()), _defineProperty(_data, "ajax", true), _data);
    var $target = $('#data_transform_transformerOptions');
    $target.html('<span class="loader">Loading …</loader>'); // Submit data via AJAX to the form's action path.

    $.ajax({
      url: $form.attr('action'),
      type: $form.attr('method'),
      data: data,
      //$form.serializeArray(),
      success: function success(html) {
        // Replace current position field ...
        $target.replaceWith( // ... with the returned one from the AJAX response.
        $(html).find('#data_transform_transformerOptions'));
      },
      error: function error(_error) {
        $target.replaceWith($('<div/>').html('xxx'));
        console.log(_arguments);
      }
    });
  });
});

/***/ }),

/***/ "./assets/scss/transforms.scss":
/*!*************************************!*\
  !*** ./assets/scss/transforms.scss ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ })

},[["./assets/js/transforms.js","runtime","vendors~base~transforms","vendors~transforms"]]]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvanMvdHJhbnNmb3Jtcy5qcyIsIndlYnBhY2s6Ly8vLi9hc3NldHMvc2Nzcy90cmFuc2Zvcm1zLnNjc3MiXSwibmFtZXMiOlsicmVxdWlyZSIsIiQiLCIkcHJldmlldyIsIm9uIiwic3RlcCIsInBhcnNlSW50IiwiZGF0YSIsImF0dHIiLCJzZWxlY3QyIiwiJHRyYW5zZm9ybWVyIiwiJGZvcm0iLCJjbG9zZXN0IiwidmFsIiwiJHRhcmdldCIsImh0bWwiLCJhamF4IiwidXJsIiwidHlwZSIsInN1Y2Nlc3MiLCJyZXBsYWNlV2l0aCIsImZpbmQiLCJlcnJvciIsImNvbnNvbGUiLCJsb2ciLCJhcmd1bWVudHMiXSwibWFwcGluZ3MiOiI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUFBQUEsbUJBQU8sQ0FBQyw4REFBRCxDQUFQOztBQUVBLElBQU1DLENBQUMsR0FBR0QsbUJBQU8sQ0FBQyxvREFBRCxDQUFqQjs7Q0FHQTs7QUFDQUMsQ0FBQyxDQUFDLFlBQU07QUFDSixNQUFNQyxRQUFRLEdBQUdELENBQUMsQ0FBQyxvQkFBRCxDQUFsQjtBQUNBQSxHQUFDLENBQUMseUJBQUQsQ0FBRCxDQUE2QkUsRUFBN0IsQ0FBZ0MsT0FBaEMsRUFBeUMsWUFBVztBQUNoRCxRQUFNQyxJQUFJLEdBQUdDLFFBQVEsQ0FBQ0osQ0FBQyxDQUFDLElBQUQsQ0FBRCxDQUFRSyxJQUFSLENBQWEsa0JBQWIsQ0FBRCxDQUFyQjtBQUNBSixZQUFRLENBQUNLLElBQVQsQ0FBYyxLQUFkLEVBQXFCTCxRQUFRLENBQUNJLElBQVQsQ0FBYyxLQUFkLElBQXFCLFNBQXJCLEdBQStCRixJQUFwRDtBQUNILEdBSEQ7QUFLQUgsR0FBQyxDQUFDLDJCQUFELENBQUQsQ0FBK0JPLE9BQS9CO0FBRUEsTUFBTUMsWUFBWSxHQUFHUixDQUFDLENBQUMsNkJBQUQsQ0FBdEI7QUFDQVEsY0FBWSxDQUFDTixFQUFiLENBQWdCLFFBQWhCLEVBQTBCLFlBQVc7QUFBQTtBQUFBOztBQUNqQyxRQUFNTyxLQUFLLEdBQUdULENBQUMsQ0FBQyxJQUFELENBQUQsQ0FBUVUsT0FBUixDQUFnQixNQUFoQixDQUFkLENBRGlDLENBRWpDOztBQUNBLFFBQU1MLElBQUksdUNBQ0xHLFlBQVksQ0FBQ0YsSUFBYixDQUFrQixNQUFsQixDQURLLEVBQ3VCRSxZQUFZLENBQUNHLEdBQWIsRUFEdkIsa0NBRUEsSUFGQSxTQUFWO0FBS0EsUUFBTUMsT0FBTyxHQUFHWixDQUFDLENBQUMsb0NBQUQsQ0FBakI7QUFDQVksV0FBTyxDQUFDQyxJQUFSLENBQWEseUNBQWIsRUFUaUMsQ0FVakM7O0FBQ0FiLEtBQUMsQ0FBQ2MsSUFBRixDQUFPO0FBQ0hDLFNBQUcsRUFBR04sS0FBSyxDQUFDSCxJQUFOLENBQVcsUUFBWCxDQURIO0FBRUhVLFVBQUksRUFBRVAsS0FBSyxDQUFDSCxJQUFOLENBQVcsUUFBWCxDQUZIO0FBR0hELFVBQUksRUFBR0EsSUFISjtBQUdVO0FBQ2JZLGFBQU8sRUFBRSxpQkFBQ0osSUFBRCxFQUFVO0FBQ2Y7QUFDQUQsZUFBTyxDQUFDTSxXQUFSLEVBQ0k7QUFDQWxCLFNBQUMsQ0FBQ2EsSUFBRCxDQUFELENBQVFNLElBQVIsQ0FBYSxvQ0FBYixDQUZKO0FBSUgsT0FWRTtBQVdIQyxXQUFLLEVBQUUsZUFBQ0EsTUFBRCxFQUFXO0FBQ2RSLGVBQU8sQ0FBQ00sV0FBUixDQUFvQmxCLENBQUMsQ0FBQyxRQUFELENBQUQsQ0FBWWEsSUFBWixDQUFpQixLQUFqQixDQUFwQjtBQUNBUSxlQUFPLENBQUNDLEdBQVIsQ0FBWUMsVUFBWjtBQUNIO0FBZEUsS0FBUDtBQWdCSCxHQTNCRDtBQTRCSCxDQXRDQSxDQUFELEM7Ozs7Ozs7Ozs7O0FDTkEsdUMiLCJmaWxlIjoidHJhbnNmb3Jtcy5qcyIsInNvdXJjZXNDb250ZW50IjpbInJlcXVpcmUoJy4uL3Njc3MvdHJhbnNmb3Jtcy5zY3NzJylcblxuY29uc3QgJCA9IHJlcXVpcmUoJ2pxdWVyeScpXG5pbXBvcnQgJ3NlbGVjdDInXG5cbi8vIEBzZWUgaHR0cHM6Ly9zeW1mb255LmNvbS9kb2MvY3VycmVudC9mb3JtL2R5bmFtaWNfZm9ybV9tb2RpZmljYXRpb24uaHRtbCNkeW5hbWljLWdlbmVyYXRpb24tZm9yLXN1Ym1pdHRlZC1mb3Jtc1xuJCgoKSA9PiB7XG4gICAgY29uc3QgJHByZXZpZXcgPSAkKCcjZGF0YS1mbG93LXByZXZpZXcnKVxuICAgICQoJ1tkYXRhLXJ1bi1mbG93LXRvLXN0ZXBdJykub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICAgIGNvbnN0IHN0ZXAgPSBwYXJzZUludCgkKHRoaXMpLmRhdGEoJ3J1bi1mbG93LXRvLXN0ZXAnKSlcbiAgICAgICAgJHByZXZpZXcuYXR0cignc3JjJywgJHByZXZpZXcuZGF0YSgnc3JjJykrJz9zdGVwcz0nK3N0ZXApXG4gICAgfSlcblxuICAgICQoJyNuZXctdHJhbmZvcm0tdHJhbnNmb3JtZXInKS5zZWxlY3QyKClcblxuICAgIGNvbnN0ICR0cmFuc2Zvcm1lciA9ICQoJyNkYXRhX3RyYW5zZm9ybV90cmFuc2Zvcm1lcicpXG4gICAgJHRyYW5zZm9ybWVyLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgY29uc3QgJGZvcm0gPSAkKHRoaXMpLmNsb3Nlc3QoJ2Zvcm0nKVxuICAgICAgICAvLyBTaW11bGF0ZSBmb3JtIGRhdGEsIGJ1dCBvbmx5IGluY2x1ZGUgdGhlIHNlbGVjdGVkIHNwb3J0IHZhbHVlLlxuICAgICAgICBjb25zdCBkYXRhID0ge1xuICAgICAgICAgICAgWyR0cmFuc2Zvcm1lci5hdHRyKCduYW1lJyldOiAkdHJhbnNmb3JtZXIudmFsKCksXG4gICAgICAgICAgICBhamF4OiB0cnVlXG4gICAgICAgIH1cblxuICAgICAgICBjb25zdCAkdGFyZ2V0ID0gJCgnI2RhdGFfdHJhbnNmb3JtX3RyYW5zZm9ybWVyT3B0aW9ucycpXG4gICAgICAgICR0YXJnZXQuaHRtbCgnPHNwYW4gY2xhc3M9XCJsb2FkZXJcIj5Mb2FkaW5nIOKApjwvbG9hZGVyPicpXG4gICAgICAgIC8vIFN1Ym1pdCBkYXRhIHZpYSBBSkFYIHRvIHRoZSBmb3JtJ3MgYWN0aW9uIHBhdGguXG4gICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICB1cmwgOiAkZm9ybS5hdHRyKCdhY3Rpb24nKSxcbiAgICAgICAgICAgIHR5cGU6ICRmb3JtLmF0dHIoJ21ldGhvZCcpLFxuICAgICAgICAgICAgZGF0YSA6IGRhdGEsIC8vJGZvcm0uc2VyaWFsaXplQXJyYXkoKSxcbiAgICAgICAgICAgIHN1Y2Nlc3M6IChodG1sKSA9PiB7XG4gICAgICAgICAgICAgICAgLy8gUmVwbGFjZSBjdXJyZW50IHBvc2l0aW9uIGZpZWxkIC4uLlxuICAgICAgICAgICAgICAgICR0YXJnZXQucmVwbGFjZVdpdGgoXG4gICAgICAgICAgICAgICAgICAgIC8vIC4uLiB3aXRoIHRoZSByZXR1cm5lZCBvbmUgZnJvbSB0aGUgQUpBWCByZXNwb25zZS5cbiAgICAgICAgICAgICAgICAgICAgJChodG1sKS5maW5kKCcjZGF0YV90cmFuc2Zvcm1fdHJhbnNmb3JtZXJPcHRpb25zJylcbiAgICAgICAgICAgICAgICApXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgZXJyb3I6IChlcnJvcikgPT4ge1xuICAgICAgICAgICAgICAgICR0YXJnZXQucmVwbGFjZVdpdGgoJCgnPGRpdi8+JykuaHRtbCgneHh4JykpXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coYXJndW1lbnRzKVxuICAgICAgICAgICAgfVxuICAgICAgICB9KVxuICAgIH0pXG59KVxuIiwiLy8gZXh0cmFjdGVkIGJ5IG1pbmktY3NzLWV4dHJhY3QtcGx1Z2luIl0sInNvdXJjZVJvb3QiOiIifQ==