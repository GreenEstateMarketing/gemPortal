/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**********************************************************!*\
  !*** ./platform/themes/real-scout/assets/js/wishlist.js ***!
  \**********************************************************/
(function ($) {
  "use strict";

  var showSuccess = function showSuccess(message) {
    window.showAlert("alert-success", message);
  };
  var __ = function __(key) {
    window.trans = window.trans || {};
    return window.trans[key] !== "undefined" && window.trans[key] ? window.trans[key] : key;
  };
  window.showAlert = function (messageType, message) {
    if (messageType && message !== "") {
      var alertId = Math.floor(Math.random() * 1000);
      var html = "<div class=\"alert ".concat(messageType, " alert-dismissible\" id=\"").concat(alertId, "\">\n                            <span class=\"close far fa-times\" data-dismiss=\"alert\" aria-label=\"close\"></span>\n                            <i class=\"far fa-") + (messageType === "alert-success" ? "check" : "times") + " message-icon\"></i>\n                            ".concat(message, "\n                        </div>");
      $("#alert-container").append(html).ready(function () {
        window.setTimeout(function () {
          $("#alert-container #".concat(alertId)).remove();
        }, 6000);
      });
    }
  };
  $(document).ready(function () {
    setWishListCount();
    function setCookie(cname, cvalue, exdays) {
      var d = new Date();
      var url = new URL(window.siteUrl);
      d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
      var expires = "expires=" + d.toUTCString();
      document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/" + "; domain=" + url.hostname;
    }
    function getCookie(cname) {
      var name = cname + "=";
      var ca = document.cookie.split(";");
      for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == " ") {
          c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
          return c.substring(name.length, c.length);
        }
      }
      return "";
    }
    function clearCookies(name) {
      var url = new URL(window.siteUrl);
      document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/" + "; domain=" + url.hostname;
    }
  });
})(jQuery);
/******/ })()
;