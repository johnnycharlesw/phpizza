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

/***/ "./client_src/mistral-backed-agent-ui.ts"
/*!***********************************************!*\
  !*** ./client_src/mistral-backed-agent-ui.ts ***!
  \***********************************************/
(__unused_webpack_module, exports) {

eval("{\nObject.defineProperty(exports, \"__esModule\", ({ value: true }));\n// Handle the \"context\" input on the form (hidden field to allow state persistence)\nif (document) {\n    let contextInput = document.getElementById(\"context\") ? document.getElementById(\"context\") instanceof HTMLInputElement : new HTMLInputElement();\n    document.addEventListener(\"DOMContentLoaded\", function () {\n        document.querySelector(\"div.context\").addEventListener(\"DOMContentLoaded\", function () {\n            let contextDivChildren = this.children;\n            for (let i = 0; i < contextDivChildren.length; i++) {\n                if (contextDivChildren[i].classList.contains(\"user-input\") || contextDivChildren[i].classList.contains(\"agent-reply\")) {\n                    contextInput.value += \"USER:\\n\" + contextDivChildren.item(i).innerHTML + \"\\n\\n\";\n                }\n                if (contextDivChildren[i].classList.contains(\"agent-reply\")) {\n                    contextInput.value += \"MISTRAL:\\n\" + contextDivChildren.item(i).innerHTML + \"\\n\\n\";\n                }\n            }\n        });\n    });\n}\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9jbGllbnRfc3JjL21pc3RyYWwtYmFja2VkLWFnZW50LXVpLnRzIiwibWFwcGluZ3MiOiI7O0FBQUEsbUZBQW1GO0FBQ25GLElBQUksUUFBUSxFQUFDLENBQUM7SUFDVixJQUFJLFlBQVksR0FBRyxRQUFRLENBQUMsY0FBYyxDQUFDLFNBQVMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxRQUFRLENBQUMsY0FBYyxDQUFDLFNBQVMsQ0FBQyxZQUFZLGdCQUFnQixDQUFDLENBQUMsQ0FBQyxJQUFJLGdCQUFnQixFQUFFLENBQUM7SUFFaEosUUFBUSxDQUFDLGdCQUFnQixDQUFDLGtCQUFrQixFQUFFO1FBQzFDLFFBQVEsQ0FBQyxhQUFhLENBQUMsYUFBYSxDQUFFLENBQUMsZ0JBQWdCLENBQUMsa0JBQWtCLEVBQUU7WUFDeEUsSUFBSSxrQkFBa0IsR0FBRyxJQUFJLENBQUMsUUFBUSxDQUFDO1lBQ3ZDLEtBQUssSUFBSSxDQUFDLEdBQUcsQ0FBQyxFQUFFLENBQUMsR0FBRyxrQkFBa0IsQ0FBQyxNQUFNLEVBQUUsQ0FBQyxFQUFFLEVBQUUsQ0FBQztnQkFDakQsSUFBSSxrQkFBa0IsQ0FBQyxDQUFDLENBQUMsQ0FBQyxTQUFTLENBQUMsUUFBUSxDQUFDLFlBQVksQ0FBQyxJQUFJLGtCQUFrQixDQUFDLENBQUMsQ0FBQyxDQUFDLFNBQVMsQ0FBQyxRQUFRLENBQUMsYUFBYSxDQUFDLEVBQUUsQ0FBQztvQkFDcEgsWUFBWSxDQUFDLEtBQUssSUFBSSxTQUFTLEdBQUcsa0JBQWtCLENBQUMsSUFBSSxDQUFDLENBQUMsQ0FBQyxDQUFDLFNBQVMsR0FBRyxNQUFNLENBQUM7Z0JBQ3BGLENBQUM7Z0JBQ0QsSUFBSSxrQkFBa0IsQ0FBQyxDQUFDLENBQUMsQ0FBQyxTQUFTLENBQUMsUUFBUSxDQUFDLGFBQWEsQ0FBQyxFQUFFLENBQUM7b0JBQzFELFlBQVksQ0FBQyxLQUFLLElBQUksWUFBWSxHQUFHLGtCQUFrQixDQUFDLElBQUksQ0FBQyxDQUFDLENBQUMsQ0FBQyxTQUFTLEdBQUcsTUFBTSxDQUFDO2dCQUN2RixDQUFDO1lBRUwsQ0FBQztRQUNMLENBQUMsQ0FBQyxDQUFDO0lBQ1AsQ0FBQyxDQUFDLENBQUM7QUFDUCxDQUFDIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vcGhwaXp6YS8uL2NsaWVudF9zcmMvbWlzdHJhbC1iYWNrZWQtYWdlbnQtdWkudHM/YmQxNCJdLCJzb3VyY2VzQ29udGVudCI6WyIvLyBIYW5kbGUgdGhlIFwiY29udGV4dFwiIGlucHV0IG9uIHRoZSBmb3JtIChoaWRkZW4gZmllbGQgdG8gYWxsb3cgc3RhdGUgcGVyc2lzdGVuY2UpXG5pZiAoZG9jdW1lbnQpe1xuICAgIGxldCBjb250ZXh0SW5wdXQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChcImNvbnRleHRcIikgPyBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChcImNvbnRleHRcIikgaW5zdGFuY2VvZiBIVE1MSW5wdXRFbGVtZW50IDogbmV3IEhUTUxJbnB1dEVsZW1lbnQoKTsgIFxuXG4gICAgZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcihcIkRPTUNvbnRlbnRMb2FkZWRcIiwgZnVuY3Rpb24oKSB7XG4gICAgICAgIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoXCJkaXYuY29udGV4dFwiKSEuYWRkRXZlbnRMaXN0ZW5lcihcIkRPTUNvbnRlbnRMb2FkZWRcIiwgZnVuY3Rpb24oKSB7IC8vIFRoZSBjb250ZW50IGlzIGdlbmVyYXRlZCBvbiB0aGUgc2VydmVyIHNpZGUgYW5kIGlzIG5vdCBjaGFuZ2VkIGR5bmFtaWNhbGx5IG9uIHRoZSBjbGllbnQgc2lkZVxuICAgICAgICAgICAgbGV0IGNvbnRleHREaXZDaGlsZHJlbiA9IHRoaXMuY2hpbGRyZW47XG4gICAgICAgICAgICBmb3IgKGxldCBpID0gMDsgaSA8IGNvbnRleHREaXZDaGlsZHJlbi5sZW5ndGg7IGkrKykge1xuICAgICAgICAgICAgICAgIGlmIChjb250ZXh0RGl2Q2hpbGRyZW5baV0uY2xhc3NMaXN0LmNvbnRhaW5zKFwidXNlci1pbnB1dFwiKSB8fCBjb250ZXh0RGl2Q2hpbGRyZW5baV0uY2xhc3NMaXN0LmNvbnRhaW5zKFwiYWdlbnQtcmVwbHlcIikpIHtcbiAgICAgICAgICAgICAgICAgICAgY29udGV4dElucHV0LnZhbHVlICs9IFwiVVNFUjpcXG5cIiArIGNvbnRleHREaXZDaGlsZHJlbi5pdGVtKGkpLmlubmVySFRNTCArIFwiXFxuXFxuXCI7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGlmIChjb250ZXh0RGl2Q2hpbGRyZW5baV0uY2xhc3NMaXN0LmNvbnRhaW5zKFwiYWdlbnQtcmVwbHlcIikpIHtcbiAgICAgICAgICAgICAgICAgICAgY29udGV4dElucHV0LnZhbHVlICs9IFwiTUlTVFJBTDpcXG5cIiArIGNvbnRleHREaXZDaGlsZHJlbi5pdGVtKGkpLmlubmVySFRNTCArIFwiXFxuXFxuXCI7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIH0pO1xufSJdLCJuYW1lcyI6W10sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./client_src/mistral-backed-agent-ui.ts\n\n}");

/***/ }

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval-source-map devtool is used.
/******/ 	let __webpack_exports__ = {};
/******/ 	__webpack_modules__["./client_src/mistral-backed-agent-ui.ts"](0,__webpack_exports__);
/******/ 	
/******/ })()
;