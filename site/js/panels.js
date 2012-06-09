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

        // delay start
        (function(){
            // transition
            this.fireEvent('started');

            if (currentState != 'slider') {

                if (targetSize == 'slider') {
                    this.fadeContent('out');
                    this.panelToSlide(options.direction);

                } else {
                    var panelSizes = this.options.panelSizes[targetSize];
                    if(options.contentUrl == undefined || options.contentUrl.length < 4) {
                        options.contentUrl = panelSizes.contentUrl;
                    }
                    this.fadeContent('out', options.contentUrl);
                    var panelMorph = new Fx.Morph($(this.options.name + "Wrapper"), {
                        duration: options.duration,
                        transition: Fx.Transitions.Quart.easeInOut
                    });
                    panelMorph.addEvent('complete', function(el) {
                        this.fireEvent('transitionComplete');
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
                if (targetSize == 'slider') {
                    var sliderMorph = new Fx.Morph($(this.options.name + "Slider"), {
                        duration: 600,
                        transition: Fx.Transitions.Quart.easeInOut
                    });
                    var sliderMargin = this.options.slider[options.direction.toLowerCase()].leftMargin;
                    console.log(sliderMargin);
                    sliderMorph.start({'margin-left': sliderMargin});
                }
            }

        }).delay(options.delay, this);
    },
    panelToSlide: function(direction) {
        var panelMargin = $(this.options.name + 'Wrapper').getCoordinates('content').left;
        var panelMarginTop = $(this.options.name + 'Wrapper').getCoordinates('content').top;
        var panelWidth = parseInt($(this.options.name + 'Wrapper').getSize().x);
        panelMargin += parseInt(panelWidth/2) - 14;

        var sliderText = new Element("div", {
            "id" : this.options.name + 'SliderText' + direction,
            "class" : 'panelSliderText'
        });
        var slider = new Element("div", {
            "id" : this.options.name + "Slider",
            "class" : 'panelSlider',
            styles: {
                'margin-left': panelMargin,
                'margin-top': panelMarginTop
            }
        });
        sliderText.inject(slider);
        slider.inject('content');

        // show the slide panel
        $(this.options.name + 'Wrapper').set('morph', {duration: 500, transition: Fx.Transitions.Quart.easeInOut});
        $(this.options.name + 'Wrapper').morph({
            opacity: [0],
            'margin-left': panelMargin,
            'width': 28
        });
        this.primeFade($(this.options.name + "Slider"));
        var sliderMorph = new Fx.Morph($(this.options.name + "Slider"), {
            duration: 600,
            transition: Fx.Transitions.Quart.easeInOut
        });
        sliderMorph.start({opacity:[1]}).chain(function(){
            var sliderMargin = this.options.slider[direction.toLowerCase()].leftMargin;
            sliderMorph.addEvent('complete', function(el) {
                this.options.currentSpecs = Object.merge(this.options.currentSpecs,{'currentState': 'slider'});
                this.fireEvent('transitionComplete');

            }.bind(this));
            sliderMorph.start({'margin-left': sliderMargin});
        }.bind(this));
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
    primeFade: function(element) {
        element.setStyle('opacity', 0);
        element.setStyle('display', 'block');
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
            this.primeFade(targetPanel);
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
                $(this.options.name + "Content").addClass('scrollable');
            } else {
                $(this.options.name + "Content").removeClass('scrollable');
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
