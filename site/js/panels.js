
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
    transition: function(targetSize,options) {
        this.debug("Transition to " + targetSize, this.options);

        this.fireEvent('transitionTo' + this.sentenceCase(targetSize));
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
            this.fireEvent('transitionTo' +  this.sentenceCase(targetSize) + 'Started');
            this.deactivateLinks();

            if (currentState != 'slider') {

                if (targetSize == 'slider') {
                    this.fadeContent('out');
                    this.panelToSlide(options.direction);

                } else {
                    if (options.contentUrl === undefined) {
                        options.contentUrl = this.options.panelSizes[targetSize].contentUrl;
                    }
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
                        this.completeTransition(targetSize);
                        this.fadeContent('in');
                    }.bind(this));
                    (function(){
                        this.fireEvent('transitionStarted');
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
                    sliderMorph.addEvent('complete', function(el) {
                        this.completeTransition(targetSize);
                    }.bind(this));
                    var sliderMargin = this.options.slider[options.direction.toLowerCase()].leftMargin;
                    sliderMorph.start({'margin-left': sliderMargin});
                } else {
                    if (options.contentUrl === undefined) {
                        options.contentUrl = this.options.panelSizes[targetSize].contentUrl;
                    }
                    var panelSizes = this.options.panelSizes[targetSize];
                    if(options.contentUrl == undefined || options.contentUrl.length < 4) {
                        options.contentUrl = panelSizes.contentUrl;
                    }
                    this.slideToPanel(targetSize, options);
                }
            }

        }).delay(options.delay, this);
    },
    completeTransition: function(targetSize) {
        this.activateLinks();
        this.debug('Transition complete for ' + targetSize);

        if (targetSize != 'slider') {
            this.options.currentSpecs = Object.merge(
                this.options.panelSizes[targetSize],
                {'currentState': targetSize}
            );
        } else {
            this.options.currentSpecs.currentState = targetSize;
        }
        targetSize = this.sentenceCase(targetSize);
        this.fireEvent('transitionTo' + targetSize + 'Complete');
    },
    sentenceCase: function(stringIn) {
        return stringIn.toLowerCase().replace(/(^\s*\w|[\.\!\?]\s*\w)/g,function(c){return c.toUpperCase()});
    },
    debug: function(message, object) {
        if (this.options.debug !== undefined && this.options.debug) {
            console.log(this.options.name + ": " + message);
            if (object !== undefined) {
                console.log(object);
            }
            console.log("--------------");
        }
    },
    slideToPanel: function(targetSize, options) {
        this.loadContent(options.contentUrl);
        this.debug("slideToPanel " + targetSize, options);

        var panelSizes = this.options.panelSizes[targetSize];
        var sliderMargin = panelSizes.leftMargin + ((panelSizes.width/2) - 14);

        var sliderMorph = new Fx.Morph($(this.options.name + "Slider"), {
            duration: 600,
            transition: Fx.Transitions.Quart.easeInOut
        });
        sliderMorph.addEvent('complete', function(el) {
            sliderMorph.removeEvents('complete');
            sliderMorph.start({opacity: [0]});
            $(this.options.name + "Wrapper").set('morph', {duration: 500, transition: Fx.Transitions.Quart.easeInOut});
            $(this.options.name + 'Wrapper').morph({
                opacity: [1],
                'margin-left': panelSizes.leftMargin,
                'width': [28,panelSizes.width]
            });
            this.completeTransition(targetSize);
            this.fadeContent('in');
        }.bind(this));
        sliderMorph.start({
            'margin-left': sliderMargin
        });
    },
    panelToSlide: function(direction) {
        this.debug("panelToSlide " + direction);
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
                this.completeTransition('slider');

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
        if (options.delay == undefined) {
            options.delay = 1;
        }
        if (options.duration == undefined) {
            options.duration = 900;
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
    fadeContent: function(direction, contentUrl) {
        this.debug("fadeContent: " + direction + " ["  + contentUrl + "]");
        var fadeTween = new Fx.Tween(this.options.name + 'Content', {
            duration: 300,
            transition: Fx.Transitions.Quart.easeInOut,
            property: 'opacity'
        });

        if (contentUrl !== undefined) {
            fadeTween.addEvent('complete', function() {
                this.loadContent(contentUrl);
            }.bind(this));

        }
        if (direction != 'in') {
            fadeTween.start(1,0);
        } else {
            if (this.options.currentSpecs.currentState == 'max') {
                $(this.options.name + "Content").addClass('scrollable');
            } else {
                $(this.options.name + "Content").removeClass('scrollable');
            }
            fadeTween.start(0,1);
        }
    },
    loadContent: function(contentUrl) {
        this.debug(contentUrl);
        $(this.options.name + 'Content').empty();
        $(this.options.name + 'Content').load(contentUrl);
    },
    activateLinks: function() {
        this.options.clickable = true;
    },
    deactivateLinks: function() {
        this.options.clickable = false;
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
        debug: true,
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
