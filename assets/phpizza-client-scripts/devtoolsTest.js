"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
function detectDevTools() {
    var minimalUserResponseInMiliseconds = 100;
    var before = new Date().getTime();
    console.log("please resume the script now");
    debugger;
    var after = new Date().getTime();
    if (after - before > minimalUserResponseInMiliseconds) { // user had to resume the script manually via opened dev tools 
        return true;
    }
}
//# sourceMappingURL=devtoolsTest.js.map