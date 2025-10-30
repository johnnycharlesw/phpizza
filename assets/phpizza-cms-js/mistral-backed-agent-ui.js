// Handle the "context" input on the form (hidden field to allow state persistence)
let contextInput = document.getElementById("context");    

document.addEventListener("DOMContentLoaded", function() {
    document.querySelector("div.context").addEventListener("DOMContentLoaded", function() { // The content is generated on the server side and is not changed dynamically on the client side
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
