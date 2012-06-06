/**
* Created with JetBrains PhpStorm.
* User: Tom Coakley
* Date: 4/30/12
* Time: 9:06 PM
*/


var panel = new Class({
    Implements: [Options, Events],
    options: {
        name: null,
        clickable: false,
        currentSpecs: {
            state: null,
            width: null,
            leftMargin: null,
            contentUrl: null,
            position: null
        },
        panelSizes: {},
        slider: {}
    },
    initialize: function(options){
        this.setOptions(options);
    },
    addSize: function(name, specs) {
        this.options.panelSizes[name] = specs;
    },
    transition: function(targetSize,options) {
        //fade content

        if (options == undefined) {
            options = new Object();
        }
        this.primeOptions(options);

        // create morphs and completions

        // delay start
        (function(){
            // transition
            this.fireEvent('started');
        }).delay(options.delay, this);
    },
    primeOptions: function(options){

        if (options.delay == undefined) {
            options.delay = 0;
        }
        options.delay = parseInt(options.delay);
        if (options.duration == undefined) {
            options.duration = 500;
        }
        options.duration = parseInt(options.duration);
    },
    show: function(delay) {
        (function(){
            this.fireEvent('started');

            var targetPanel = $(this.options.name);
            targetPanel.setStyle('opacity', 0);
            targetPanel.setStyle('display', 'block');
            var spTween = new Fx.Tween(targetPanel, {
                duration: 900,
                transition: Fx.Transitions.Quart.easeInOut,
                property: 'opacity'
            });
            spTween.addEvent('complete', function() {
                this.fireEvent('complete');
            }.bind(this));
            spTween.start(0,1);

        }).delay(delay, this);

    },

    activateLinks: function() {
        options.clickable = true;
    }
});


var zumbaPanel = new Class({
    Extends: panel,
    options: {
        name: 'zumba',
        panelSizes: {
            standard: {
                leftMargin: 10,
                width: 320,
                contentUrl: '/getContent.php?sn=zumba&fn=zumbaContent'
            },
            max: {
                leftMargin: 10,
                width: 900,
                contentUrl: '/getContent.php?sn=zumba&fn=zumbaContentMax'
            }
        },
        slider: {
            left: {
                leftMargin: 10
            },
            right: {
                leftMargin: null
            }
        }
    },
    initialize: function(options) {
        this.parent(options);
    }
});

var yogaPanel = new Class({
    Extends: panel,
    options: {
        name: 'yogaCanister',
        panelSizes: {
            standard: {
                leftMargin: 340,
                width: 320,
                contentUrl: '/getContent.php?sn=yoga&fn=yogaContent'
            },
            max: {
                leftMargin: 50,
                width: 900,
                contentUrl: '/getContent.php?sn=yoga&fn=yogaContentMax'
            }
        },
        slider: {
            left: {
                leftMargin: 50
            },
            right: {
                leftMargin: 920
            }
        }
    },
    initialize: function(options) {
        this.parent(options);
    }
});

var doTerraPanel = new Class({
    Extends: panel,
    options: {
        name: 'doTerra',
        panelSizes: {
            standard: {
                leftMargin: 340,
                width: 320,
                contentUrl: '/getContent.php?sn=yoga&fn=yogaContent'
            },
            max: {
                leftMargin: 50,
                width: 900,
                contentUrl: '/getContent.php?sn=yoga&fn=yogaContentMax'
            }
        },
        slider: {
            left: {
                leftMargin: 50
            },
            right: {
                leftMargin: 920
            }
        }
    },
    initialize: function(options) {
        this.parent(options);
    }
});
