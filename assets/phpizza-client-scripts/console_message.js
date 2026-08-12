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

/***/ "./client_src/console_message.ts"
/*!***************************************!*\
  !*** ./client_src/console_message.ts ***!
  \***************************************/
(__unused_webpack_module, exports, __webpack_require__) {

eval("{\nObject.defineProperty(exports, \"__esModule\", ({ value: true }));\n// Client-side console message for PHPizza's browser environment\n// This script intentionally avoids Node.js-specific APIs so it can be compiled and run in the browser.\nconst devTools = __webpack_require__(/*! devtools-detect */ \"./node_modules/devtools-detect/index.js\");\n(() => {\n    try {\n        if (typeof console.clear === 'function') {\n            console.clear();\n        }\n    }\n    catch {\n        // Ignore environments where console.clear might throw\n    }\n    console.log(`\n# Hello, developer.\n## Welcome to this site's developer console.\n\nIf you are here to eat, we'll give you this pizza to eat. 🍕\nRaw JavaScript. Food or not food? Not food!\n\\`WebAssembly.compile\\`. Food or not food? Certainly not food!\n\nIf you are here to squash this bug that somehow got in our TypeScript code 🐜, good luck! We believe in you.\n\n\nBy the way:\nThis site is [powered by PHPizza](https://github.com/johnnycharlesw/phpizza).\n\n`);\n})();\nfunction iNowAgree() {\n    localStorage.setItem('userAgreedToAgpl', \"true\");\n}\nif (!localStorage.getItem('userAgreedToAgpl') && devTools.default.isOpen) {\n    let agreed = localStorage.getItem('userAgreedToAgpl') ?? confirm(\"Do you agree to [the AGPL?](https://www.gnu.org/licenses/agpl-3.0-standalone.html) It looks like you opened the console.\");\n    if (Boolean(agreed) === true) {\n        iNowAgree();\n    }\n    else {\n        localStorage.setItem('userAgreedToAgpl', \"false\");\n        setInterval(() => {\n            if (devTools.default.isOpen) {\n                console.warn(`You did not agree to the AGPL, so you do not have the legal ability to modify this CMS in any way.\nAnd oh, if you were wondering, maybe type \\`iNowAgree()\\` to change your mind.`);\n            }\n        }, 5000);\n    }\n}\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9jbGllbnRfc3JjL2NvbnNvbGVfbWVzc2FnZS50cyIsIm1hcHBpbmdzIjoiOztBQUFBLGdFQUFnRTtBQUNoRSx1R0FBdUc7QUFDdkcsdUdBQTZDO0FBRzdDLENBQUMsR0FBRyxFQUFFO0lBQ0osSUFBSSxDQUFDO1FBQ0gsSUFBSSxPQUFPLE9BQU8sQ0FBQyxLQUFLLEtBQUssVUFBVSxFQUFFLENBQUM7WUFDeEMsT0FBTyxDQUFDLEtBQUssRUFBRSxDQUFDO1FBQ2xCLENBQUM7SUFDSCxDQUFDO0lBQUMsTUFBTSxDQUFDO1FBQ1Asc0RBQXNEO0lBQ3hELENBQUM7SUFFRCxPQUFPLENBQUMsR0FBRyxDQUFDOzs7Ozs7Ozs7Ozs7OztDQWNiLENBQUMsQ0FBQztBQUNILENBQUMsQ0FBQyxFQUFFLENBQUM7QUFFTCxTQUFTLFNBQVM7SUFDZCxZQUFZLENBQUMsT0FBTyxDQUFDLGtCQUFrQixFQUFFLE1BQU0sQ0FBQyxDQUFDO0FBQ3JELENBQUM7QUFFRCxJQUFJLENBQUMsWUFBWSxDQUFDLE9BQU8sQ0FBQyxrQkFBa0IsQ0FBQyxJQUFJLFFBQVEsQ0FBQyxPQUFPLENBQUMsTUFBTSxFQUFFLENBQUM7SUFDdkUsSUFBSSxNQUFNLEdBQUMsWUFBWSxDQUFDLE9BQU8sQ0FBQyxrQkFBa0IsQ0FBQyxJQUFJLE9BQU8sQ0FBQywwSEFBMEgsQ0FBQyxDQUFDO0lBQzNMLElBQUksT0FBTyxDQUFDLE1BQU0sQ0FBQyxLQUFLLElBQUksRUFBRSxDQUFDO1FBQzNCLFNBQVMsRUFBRSxDQUFDO0lBQ2hCLENBQUM7U0FBTSxDQUFDO1FBQ0osWUFBWSxDQUFDLE9BQU8sQ0FBQyxrQkFBa0IsRUFBRSxPQUFPLENBQUMsQ0FBQztRQUNsRCxXQUFXLENBQUMsR0FBRyxFQUFFO1lBQ2IsSUFBSSxRQUFRLENBQUMsT0FBTyxDQUFDLE1BQU0sRUFBRSxDQUFDO2dCQUMxQixPQUFPLENBQUMsSUFBSSxDQUFDOytFQUNrRCxDQUFDLENBQUM7WUFDckUsQ0FBQztRQUNMLENBQUMsRUFBRSxJQUFJLENBQUMsQ0FBQztJQUNiLENBQUM7QUFDTCxDQUFDIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vcGhwaXp6YS8uL2NsaWVudF9zcmMvY29uc29sZV9tZXNzYWdlLnRzP2FlN2MiXSwic291cmNlc0NvbnRlbnQiOlsiLy8gQ2xpZW50LXNpZGUgY29uc29sZSBtZXNzYWdlIGZvciBQSFBpenphJ3MgYnJvd3NlciBlbnZpcm9ubWVudFxuLy8gVGhpcyBzY3JpcHQgaW50ZW50aW9uYWxseSBhdm9pZHMgTm9kZS5qcy1zcGVjaWZpYyBBUElzIHNvIGl0IGNhbiBiZSBjb21waWxlZCBhbmQgcnVuIGluIHRoZSBicm93c2VyLlxuaW1wb3J0IGRldlRvb2xzID0gcmVxdWlyZShcImRldnRvb2xzLWRldGVjdFwiKTtcblxuXG4oKCkgPT4ge1xuICB0cnkge1xuICAgIGlmICh0eXBlb2YgY29uc29sZS5jbGVhciA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgY29uc29sZS5jbGVhcigpO1xuICAgIH1cbiAgfSBjYXRjaCB7XG4gICAgLy8gSWdub3JlIGVudmlyb25tZW50cyB3aGVyZSBjb25zb2xlLmNsZWFyIG1pZ2h0IHRocm93XG4gIH1cblxuICBjb25zb2xlLmxvZyhgXG4jIEhlbGxvLCBkZXZlbG9wZXIuXG4jIyBXZWxjb21lIHRvIHRoaXMgc2l0ZSdzIGRldmVsb3BlciBjb25zb2xlLlxuXG5JZiB5b3UgYXJlIGhlcmUgdG8gZWF0LCB3ZSdsbCBnaXZlIHlvdSB0aGlzIHBpenphIHRvIGVhdC4g8J+NlVxuUmF3IEphdmFTY3JpcHQuIEZvb2Qgb3Igbm90IGZvb2Q/IE5vdCBmb29kIVxuXFxgV2ViQXNzZW1ibHkuY29tcGlsZVxcYC4gRm9vZCBvciBub3QgZm9vZD8gQ2VydGFpbmx5IG5vdCBmb29kIVxuXG5JZiB5b3UgYXJlIGhlcmUgdG8gc3F1YXNoIHRoaXMgYnVnIHRoYXQgc29tZWhvdyBnb3QgaW4gb3VyIFR5cGVTY3JpcHQgY29kZSDwn5CcLCBnb29kIGx1Y2shIFdlIGJlbGlldmUgaW4geW91LlxuXG5cbkJ5IHRoZSB3YXk6XG5UaGlzIHNpdGUgaXMgW3Bvd2VyZWQgYnkgUEhQaXp6YV0oaHR0cHM6Ly9naXRodWIuY29tL2pvaG5ueWNoYXJsZXN3L3BocGl6emEpLlxuXG5gKTtcbn0pKCk7XG5cbmZ1bmN0aW9uIGlOb3dBZ3JlZSgpe1xuICAgIGxvY2FsU3RvcmFnZS5zZXRJdGVtKCd1c2VyQWdyZWVkVG9BZ3BsJywgXCJ0cnVlXCIpO1xufVxuXG5pZiAoIWxvY2FsU3RvcmFnZS5nZXRJdGVtKCd1c2VyQWdyZWVkVG9BZ3BsJykgJiYgZGV2VG9vbHMuZGVmYXVsdC5pc09wZW4pIHtcbiAgICBsZXQgYWdyZWVkPWxvY2FsU3RvcmFnZS5nZXRJdGVtKCd1c2VyQWdyZWVkVG9BZ3BsJykgPz8gY29uZmlybShcIkRvIHlvdSBhZ3JlZSB0byBbdGhlIEFHUEw/XShodHRwczovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2FncGwtMy4wLXN0YW5kYWxvbmUuaHRtbCkgSXQgbG9va3MgbGlrZSB5b3Ugb3BlbmVkIHRoZSBjb25zb2xlLlwiKTtcbiAgICBpZiAoQm9vbGVhbihhZ3JlZWQpID09PSB0cnVlKSB7XG4gICAgICAgIGlOb3dBZ3JlZSgpO1xuICAgIH0gZWxzZSB7XG4gICAgICAgIGxvY2FsU3RvcmFnZS5zZXRJdGVtKCd1c2VyQWdyZWVkVG9BZ3BsJywgXCJmYWxzZVwiKTtcbiAgICAgICAgc2V0SW50ZXJ2YWwoKCkgPT4ge1xuICAgICAgICAgICAgaWYgKGRldlRvb2xzLmRlZmF1bHQuaXNPcGVuKSB7XG4gICAgICAgICAgICAgICAgY29uc29sZS53YXJuKGBZb3UgZGlkIG5vdCBhZ3JlZSB0byB0aGUgQUdQTCwgc28geW91IGRvIG5vdCBoYXZlIHRoZSBsZWdhbCBhYmlsaXR5IHRvIG1vZGlmeSB0aGlzIENNUyBpbiBhbnkgd2F5LlxuQW5kIG9oLCBpZiB5b3Ugd2VyZSB3b25kZXJpbmcsIG1heWJlIHR5cGUgXFxgaU5vd0FncmVlKClcXGAgdG8gY2hhbmdlIHlvdXIgbWluZC5gKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSwgNTAwMCk7XG4gICAgfVxufSJdLCJuYW1lcyI6W10sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./client_src/console_message.ts\n\n}");

/***/ },

/***/ "./node_modules/devtools-detect/index.js"
/*!***********************************************!*\
  !*** ./node_modules/devtools-detect/index.js ***!
  \***********************************************/
(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

eval("{__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (__WEBPACK_DEFAULT_EXPORT__)\n/* harmony export */ });\n/*!\ndevtools-detect\nhttps://github.com/sindresorhus/devtools-detect\nBy Sindre Sorhus\nMIT License\n*/\n\nconst devtools = {\n\tisOpen: false,\n\torientation: undefined,\n};\n\nconst threshold = 170;\n\nconst emitEvent = (isOpen, orientation) => {\n\tglobalThis.dispatchEvent(new globalThis.CustomEvent('devtoolschange', {\n\t\tdetail: {\n\t\t\tisOpen,\n\t\t\torientation,\n\t\t},\n\t}));\n};\n\nconst main = ({emitEvents = true} = {}) => {\n\tconst widthThreshold = globalThis.outerWidth - globalThis.innerWidth > threshold;\n\tconst heightThreshold = globalThis.outerHeight - globalThis.innerHeight > threshold;\n\tconst orientation = widthThreshold ? 'vertical' : 'horizontal';\n\n\tif (\n\t\t!(heightThreshold && widthThreshold)\n\t\t&& ((globalThis.Firebug && globalThis.Firebug.chrome && globalThis.Firebug.chrome.isInitialized) || widthThreshold || heightThreshold)\n\t) {\n\t\tif ((!devtools.isOpen || devtools.orientation !== orientation) && emitEvents) {\n\t\t\temitEvent(true, orientation);\n\t\t}\n\n\t\tdevtools.isOpen = true;\n\t\tdevtools.orientation = orientation;\n\t} else {\n\t\tif (devtools.isOpen && emitEvents) {\n\t\t\temitEvent(false, undefined);\n\t\t}\n\n\t\tdevtools.isOpen = false;\n\t\tdevtools.orientation = undefined;\n\t}\n};\n\nmain({emitEvents: false});\nsetInterval(main, 500);\n\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (devtools);\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9ub2RlX21vZHVsZXMvZGV2dG9vbHMtZGV0ZWN0L2luZGV4LmpzIiwibWFwcGluZ3MiOiI7Ozs7QUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEdBQUc7QUFDSCxFQUFFO0FBQ0Y7O0FBRUEsZUFBZSxtQkFBbUIsSUFBSTtBQUN0QztBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBLEdBQUc7QUFDSDtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7O0FBRUEsTUFBTSxrQkFBa0I7QUFDeEI7O0FBRUEsaUVBQWUsUUFBUSxFQUFDIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vcGhwaXp6YS8uL25vZGVfbW9kdWxlcy9kZXZ0b29scy1kZXRlY3QvaW5kZXguanM/NTQ0YSJdLCJzb3VyY2VzQ29udGVudCI6WyIvKiFcbmRldnRvb2xzLWRldGVjdFxuaHR0cHM6Ly9naXRodWIuY29tL3NpbmRyZXNvcmh1cy9kZXZ0b29scy1kZXRlY3RcbkJ5IFNpbmRyZSBTb3JodXNcbk1JVCBMaWNlbnNlXG4qL1xuXG5jb25zdCBkZXZ0b29scyA9IHtcblx0aXNPcGVuOiBmYWxzZSxcblx0b3JpZW50YXRpb246IHVuZGVmaW5lZCxcbn07XG5cbmNvbnN0IHRocmVzaG9sZCA9IDE3MDtcblxuY29uc3QgZW1pdEV2ZW50ID0gKGlzT3Blbiwgb3JpZW50YXRpb24pID0+IHtcblx0Z2xvYmFsVGhpcy5kaXNwYXRjaEV2ZW50KG5ldyBnbG9iYWxUaGlzLkN1c3RvbUV2ZW50KCdkZXZ0b29sc2NoYW5nZScsIHtcblx0XHRkZXRhaWw6IHtcblx0XHRcdGlzT3Blbixcblx0XHRcdG9yaWVudGF0aW9uLFxuXHRcdH0sXG5cdH0pKTtcbn07XG5cbmNvbnN0IG1haW4gPSAoe2VtaXRFdmVudHMgPSB0cnVlfSA9IHt9KSA9PiB7XG5cdGNvbnN0IHdpZHRoVGhyZXNob2xkID0gZ2xvYmFsVGhpcy5vdXRlcldpZHRoIC0gZ2xvYmFsVGhpcy5pbm5lcldpZHRoID4gdGhyZXNob2xkO1xuXHRjb25zdCBoZWlnaHRUaHJlc2hvbGQgPSBnbG9iYWxUaGlzLm91dGVySGVpZ2h0IC0gZ2xvYmFsVGhpcy5pbm5lckhlaWdodCA+IHRocmVzaG9sZDtcblx0Y29uc3Qgb3JpZW50YXRpb24gPSB3aWR0aFRocmVzaG9sZCA/ICd2ZXJ0aWNhbCcgOiAnaG9yaXpvbnRhbCc7XG5cblx0aWYgKFxuXHRcdCEoaGVpZ2h0VGhyZXNob2xkICYmIHdpZHRoVGhyZXNob2xkKVxuXHRcdCYmICgoZ2xvYmFsVGhpcy5GaXJlYnVnICYmIGdsb2JhbFRoaXMuRmlyZWJ1Zy5jaHJvbWUgJiYgZ2xvYmFsVGhpcy5GaXJlYnVnLmNocm9tZS5pc0luaXRpYWxpemVkKSB8fCB3aWR0aFRocmVzaG9sZCB8fCBoZWlnaHRUaHJlc2hvbGQpXG5cdCkge1xuXHRcdGlmICgoIWRldnRvb2xzLmlzT3BlbiB8fCBkZXZ0b29scy5vcmllbnRhdGlvbiAhPT0gb3JpZW50YXRpb24pICYmIGVtaXRFdmVudHMpIHtcblx0XHRcdGVtaXRFdmVudCh0cnVlLCBvcmllbnRhdGlvbik7XG5cdFx0fVxuXG5cdFx0ZGV2dG9vbHMuaXNPcGVuID0gdHJ1ZTtcblx0XHRkZXZ0b29scy5vcmllbnRhdGlvbiA9IG9yaWVudGF0aW9uO1xuXHR9IGVsc2Uge1xuXHRcdGlmIChkZXZ0b29scy5pc09wZW4gJiYgZW1pdEV2ZW50cykge1xuXHRcdFx0ZW1pdEV2ZW50KGZhbHNlLCB1bmRlZmluZWQpO1xuXHRcdH1cblxuXHRcdGRldnRvb2xzLmlzT3BlbiA9IGZhbHNlO1xuXHRcdGRldnRvb2xzLm9yaWVudGF0aW9uID0gdW5kZWZpbmVkO1xuXHR9XG59O1xuXG5tYWluKHtlbWl0RXZlbnRzOiBmYWxzZX0pO1xuc2V0SW50ZXJ2YWwobWFpbiwgNTAwKTtcblxuZXhwb3J0IGRlZmF1bHQgZGV2dG9vbHM7XG4iXSwibmFtZXMiOltdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./node_modules/devtools-detect/index.js\n\n}");

/***/ }

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	const __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		const cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		const module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		if (!(moduleId in __webpack_modules__)) {
/******/ 			delete __webpack_module_cache__[moduleId];
/******/ 			const e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
/******/ 		}
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter/value functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			if(Array.isArray(definition)) {
/******/ 				var i = 0;
/******/ 				while(i < definition.length) {
/******/ 					var key = definition[i++];
/******/ 					var binding = definition[i++];
/******/ 					if(!__webpack_require__.o(exports, key)) {
/******/ 						if(binding === 0) {
/******/ 							Object.defineProperty(exports, key, { enumerable: true, value: definition[i++] });
/******/ 						} else {
/******/ 							Object.defineProperty(exports, key, { enumerable: true, get: binding });
/******/ 						}
/******/ 					} else if(binding === 0) { i++; }
/******/ 				}
/******/ 			} else {
/******/ 				for(var key in definition) {
/******/ 					if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 						Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 					}
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval-source-map devtool is used.
/******/ 	let __webpack_exports__ = __webpack_require__("./client_src/console_message.ts");
/******/ 	
/******/ })()
;