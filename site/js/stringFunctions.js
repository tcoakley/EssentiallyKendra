
var stringFunctions = new Class({

    sentenceCase: function(stringIn) {
        return stringIn.toLowerCase().replace(/(^\s*\w|[\.\!\?]\s*\w)/g,function(c){return c.toUpperCase()});
    },
    debug: function(message, object) {
        if (this.options.debug !== undefined && this.options.debug) {
            var date = new Date();

            if (this.options.name !== undefined) {
                console.log(this.options.name + ": " + message);
            }
            console.log(message);
            if (object !== undefined) {
                console.log(object);
            }
            console.log("-----------------------------");
        }
    }
});

