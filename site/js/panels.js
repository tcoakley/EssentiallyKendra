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
            currentState: null,
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
        this.setCurrentSpecs(Object.merge(this.options.panelSizes['standard'],{'currentState': 'standard'}));
    },
    setCurrentSpecs: function(specs) {
        this.options.currentSpecs = Object.merge(this.options.currentSpecs, specs);
    },
    addSize: function(name, specs) {
        this.options.panelSizes[name] = specs;
    },
    transition: function(targetSize,options) {
        // init
        if (options == undefined) {
            options = new Object();
        }
        this.primeOptions(options);

        var currentSpecs = this.options.currentSpecs;
        var currentState = currentSpecs.currentState;
        var panelSizes = this.options.panelSizes[targetSize];
        if (currentState != 'slider') {

            if (targetSize == 'slider') {
                //fade content
                this.fadeContent('out');

            } else {
                //fade content
                this.fadeContent('out', panelSizes.contentUrl);
                var panelMorph = new Fx.Morph($(this.options.name + "Wrapper"), {
                    duration: options.duration,
                    transition: Fx.Transitions.Quart.easeInOut
                });
                panelMorph.addEvent('complete', function(el) {
                    this.activateLinks();
                    this.options.currentSpecs = Object.merge(panelSizes,{'currentState': targetSize});
                    this.fadeContent('in');
                }.bind(this));
                (function(){
                    this.fireEvent('transitionStarted');
                    console.log(panelSizes);
                    panelMorph.start({
                        'width': panelSizes.width,
                        'margin-left': panelSizes.leftMargin
                    });
                }).delay(options.delay, this);
            }
        } else {

        }


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
    show: function(options) {
        if (options == undefined) {
            options = new Object();
            this.primeOptions(options);
            options = Object.merge(options, {'delay' : 1, 'duration' : 900});
        }
        (function(){
            this.fireEvent('showStarted');
            var targetPanel = $(this.options.name + 'Canister');
            targetPanel.setStyle('opacity', 0);
            targetPanel.setStyle('display', 'block');
            var spTween = new Fx.Tween(targetPanel, {
                duration: options.duration,
                transition: Fx.Transitions.Quart.easeInOut,
                property: 'opacity'
            });
            spTween.addEvent('complete', function() {
                this.fireEvent('showComplete');
            }.bind(this));
            spTween.start(0,1);

        }).delay(options.delay, this);

    },
    fadeContent: function(direction, loadContent) {

        var fadeTween = new Fx.Tween(this.options.name + 'Content', {
            duration: 300,
            transition: Fx.Transitions.Quart.easeInOut,
            property: 'opacity'
        });

        if (loadContent !== undefined) {
            $(this.options.name + 'Content').empty();
            fadeTween.addEvent('complete', function() {
                $(this.options.name + 'Content').load(loadContent);
            }.bind(this));
        }
        if (direction != 'in') {
            fadeTween.start(1,0);
        } else {
            console.log(this.options.currentSpecs.currentState);
            if (this.options.currentSpecs.currentState == 'max') {
                console.log('here');
                $(this.options.name + "Content").addClass('scrollable');
            }
            fadeTween.start(0,1);
        }
    },
    activateLinks: function() {

    }
});

/* ====================================================================================== */
/* ====================================================================================== */
/* ====================================================================================== */
/* ====================================================================================== */
/* ====================================================================================== */

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
        name: 'yoga',
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
