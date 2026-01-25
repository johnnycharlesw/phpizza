"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
// Handle the "context" input on the form (hidden field to allow state persistence)
if (document) {
    let contextInput = document.getElementById("context") ? document.getElementById("context") instanceof HTMLInputElement : new HTMLInputElement();
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelector("div.context").addEventListener("DOMContentLoaded", function () {
            let contextDivChildren = this.children;
            for (let i = 0; i < contextDivChildren.length; i++) {
                if (contextDivChildren[i].classList.contains("user-input") || contextDivChildren[i].classList.contains("agent-reply")) {
                    contextInput.value += "USER:\n" + contextDivChildren.item(i).innerHTML + "\n\n";
                }
                if (contextDivChildren[i].classList.contains("agent-reply")) {
                    contextInput.value += "MISTRAL:\n" + contextDivChildren.item(i).innerHTML + "\n\n";
                }
            }
        });
    });
}
//# sourceMappingURL=mistral-backed-agent-ui.js.map