/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./client_src/phpizza-desktop.ts"
/*!***************************************!*\
  !*** ./client_src/phpizza-desktop.ts ***!
  \***************************************/
(__unused_webpack_module, exports) {

eval("{\nObject.defineProperty(exports, \"__esModule\", ({ value: true }));\nfunction setCookie(cname, cvalue, exdays) {\n    const d = new Date();\n    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));\n    let expires = \"expires=\" + d.toUTCString();\n    document.cookie = cname + \"=\" + cvalue + \";\" + expires + \";path=/\";\n}\nwindow.addEventListener('message', function (params) {\n    if (params['phpsessid']) {\n        setCookie('PHPSESSID', params['phpsessid'], 30);\n    }\n});\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9jbGllbnRfc3JjL3BocGl6emEtZGVza3RvcC50cyIsIm1hcHBpbmdzIjoiOztBQUFBLFNBQVMsU0FBUyxDQUFDLEtBQWEsRUFBRSxNQUFjLEVBQUUsTUFBYztJQUM5RCxNQUFNLENBQUMsR0FBRyxJQUFJLElBQUksRUFBRSxDQUFDO0lBQ3JCLENBQUMsQ0FBQyxPQUFPLENBQUMsQ0FBQyxDQUFDLE9BQU8sRUFBRSxHQUFHLENBQUMsTUFBTSxHQUFDLEVBQUUsR0FBQyxFQUFFLEdBQUMsRUFBRSxHQUFDLElBQUksQ0FBQyxDQUFDLENBQUM7SUFDaEQsSUFBSSxPQUFPLEdBQUcsVUFBVSxHQUFFLENBQUMsQ0FBQyxXQUFXLEVBQUUsQ0FBQztJQUMxQyxRQUFRLENBQUMsTUFBTSxHQUFHLEtBQUssR0FBRyxHQUFHLEdBQUcsTUFBTSxHQUFHLEdBQUcsR0FBRyxPQUFPLEdBQUcsU0FBUyxDQUFDO0FBQ3JFLENBQUM7QUFFRCxNQUFNLENBQUMsZ0JBQWdCLENBQUMsU0FBUyxFQUFFLFVBQVUsTUFBVztJQUNwRCxJQUFJLE1BQU0sQ0FBQyxXQUFXLENBQUMsRUFBRSxDQUFDO1FBQ3RCLFNBQVMsQ0FBQyxXQUFXLEVBQUUsTUFBTSxDQUFDLFdBQVcsQ0FBQyxFQUFFLEVBQUUsQ0FBQyxDQUFDO0lBQ3BELENBQUM7QUFDTCxDQUFDLENBQUMiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9waHBpenphLy4vY2xpZW50X3NyYy9waHBpenphLWRlc2t0b3AudHM/YmJlOSJdLCJzb3VyY2VzQ29udGVudCI6WyJmdW5jdGlvbiBzZXRDb29raWUoY25hbWU6IHN0cmluZywgY3ZhbHVlOiBzdHJpbmcsIGV4ZGF5czogbnVtYmVyKSB7XHJcbiAgY29uc3QgZCA9IG5ldyBEYXRlKCk7XHJcbiAgZC5zZXRUaW1lKGQuZ2V0VGltZSgpICsgKGV4ZGF5cyoyNCo2MCo2MCoxMDAwKSk7XHJcbiAgbGV0IGV4cGlyZXMgPSBcImV4cGlyZXM9XCIrIGQudG9VVENTdHJpbmcoKTtcclxuICBkb2N1bWVudC5jb29raWUgPSBjbmFtZSArIFwiPVwiICsgY3ZhbHVlICsgXCI7XCIgKyBleHBpcmVzICsgXCI7cGF0aD0vXCI7XHJcbn1cclxuXHJcbndpbmRvdy5hZGRFdmVudExpc3RlbmVyKCdtZXNzYWdlJywgZnVuY3Rpb24gKHBhcmFtczogYW55KSB7XHJcbiAgICBpZiAocGFyYW1zWydwaHBzZXNzaWQnXSkge1xyXG4gICAgICAgIHNldENvb2tpZSgnUEhQU0VTU0lEJywgcGFyYW1zWydwaHBzZXNzaWQnXSwgMzApO1xyXG4gICAgfVxyXG59KSJdLCJuYW1lcyI6W10sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./client_src/phpizza-desktop.ts\n\n}");

/***/ }

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval-source-map devtool is used.
/******/ 	let __webpack_exports__ = {};
/******/ 	__webpack_modules__["./client_src/phpizza-desktop.ts"](0,__webpack_exports__);
/******/ 	
/******/ })()
;