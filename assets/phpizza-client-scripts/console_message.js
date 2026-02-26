"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
// Client-side console message for PHPizza's browser environment
// This script intentionally avoids Node.js-specific APIs so it can be compiled and run in the browser.
const devTools = require("devtools-detect");
(() => {
    try {
        if (typeof console.clear === 'function') {
            console.clear();
        }
    }
    catch {
        // Ignore environments where console.clear might throw
    }
    console.log(`
# Hello, developer.
## Welcome to this site's developer console.

If you are here to eat, we'll give you this pizza to eat. 🍕
Raw JavaScript. Food or not food? Not food!
\`WebAssembly.compile\`. Food or not food? Certainly not food!

If you are here to squash this bug that somehow got in our TypeScript code 🐜, good luck! We believe in you.


By the way:
This site is [powered by PHPizza](https://github.com/johnnycharlesw/phpizza).

`);
})();
function iNowAgree() {
    localStorage.setItem('userAgreedToAgpl', "true");
}
if (!localStorage.getItem('userAgreedToAgpl') && devTools.default.isOpen) {
    let agreed = localStorage.getItem('userAgreedToAgpl') ?? confirm("Do you agree to [the AGPL?](https://www.gnu.org/licenses/agpl-3.0-standalone.html) It looks like you opened the console.");
    if (Boolean(agreed) === true) {
        iNowAgree();
    }
    else {
        localStorage.setItem('userAgreedToAgpl', "false");
        setInterval(() => {
            if (devTools.default.isOpen) {
                console.warn(`You did not agree to the AGPL, so you do not have the legal ability to modify this CMS in any way.
And oh, if you were wondering, maybe type \`iNowAgree()\` to change your mind.`);
            }
        }, 5000);
    }
}
//# sourceMappingURL=console_message.js.map