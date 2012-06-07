//If you are reading this you are likely trying to figure out how something was
//done on this site. I was rushed and rusty on MooTools after 3 years of 
//jQuery for work.  Nothing is all that complex, and it is commented, but
//when time permits I plan to clean it up.  If you are
//stuck and want help you can email me at coakleytom@hotmail.com. Please
//include a link to the site you are working on in the email.

window.addEvent('domready', function() {
    $('mailingList').addEvent('click', function(e) {
        var obj = new Element('div', {
            'id': 'dummy',
            'events': {
                'click': function(){
                    sendComment();
                    this.destroy();
                }
            }
        });
        new Message({
            icon: "questionMedium.png",
            iconPath: "/images/",
            width: 300,
            fontSize: 14,
            passEvent: e,
            autoDismiss: false,
            title: 'Email Address:' ,
            message: '<input type="text" id="emailAddress" class="emailAddress"/>',
            callback: obj,
            yesLink: "Signup",
            noLink: "Cancel"
        }).say();
        $('emailAddress').focus();
        $('emailAddress').addEvent( 'keydown', function( evt ){
            if( evt.key == 'enter' ) {
                $('SignupLink').fireEvent('click');

            }

        });
    });
    var sendComment = function(){
        new Message({
            icon: "okMedium.png",
            iconPath: "/images/",
            title: "Received!",
            message: "Thank you for your interest."
        }).say();
        var emailAddress = $('emailAddress').value;
        new Request({
            url: '/emailSignup.php?emailAddress=' + emailAddress,
        }).send();
    }
});
window.addEvent('load', function() {
    
    // Control Variables
    var backgroundLoaded = false;
    var animationComplete = false;
    var backgroundShown = false;
    var clickable = false;
    var openPanel = null;
    var panelWidth = 320;
    var panelWidthOpen = 900;
    
    var panelLocs = {
        zumba: {
            panel: 10,
            slider: 160,
            sliderFull: 440,
            Left: 10
        },
        yoga: {
            panel: 340,
            slider: 490,
            sliderFull: 468,
            Left: 50,
            Right: 920
        },
        doTerra: {
            panel: 670,
            slider: 820,
            sliderFull: 500,
            Left: 90,
            Right: 960
        }
    };
    
    var panelContent = {
        zumba: {
            standard: '/getContent.php?sn=zumba&fn=zumbaContent',
            max: '/getContent.php?sn=zumba&fn=zumbaContentMax'
        },
        yoga: {
            standard: '/getContent.php?sn=yoga&fn=yogaContent',
            max: '/getContent.php?sn=yoga&fn=yogaContentMax'
        },
        doTerra:{
            standard: '/getContent.php?sn=doTerra&fn=doTerraContent',
            max: '/getContent.php?sn=doTerra&fn=doTerraContentMax'
        }
    }

    // Handle loading of the full color background
    // Must load from a different domain for it to work correctly in firefox. *boggle*
    var preventCache = Number.random(1,100000);
    var colorBackground = new Asset.image('/images/layout/contentBackground2.jpg?' + preventCache, {
        id: 'contentBackground2Image',
        onLoad: backgroundLoadComplete
    });

    function backgroundLoadComplete() {
        colorBackground.inject($('contentBackground2'));
        checkBackgroundLoad();
    }

    function checkBackgroundLoad() {
        if ($('contentBackground2').getSize().y > 790) {
            backgroundLoaded = true;
            if (animationComplete) {
                displayColorBackground();
            }
        } else {
            (function(){
                checkBackgroundLoad();
            }).delay(750, this);
        }
    }

    function displayColorBackground() {
        backgroundShown = true;
        $('contentBackground1').set('morph', {duration: 1000, transition: Fx.Transitions.Quart.easeInOut});
        $('contentBackground1').morph({opacity: [0]});
    }
    
    // Handle initial load animations
    var contentBackground1Morph = new Fx.Morph('contentBackground1', {
        duration: 400,
        transition: Fx.Transitions.Sine.easeOut
    });
    contentBackground1Morph.addEvent('complete', function() { 
        animationComplete = true;
        $('contentBackground2').setStyle('display', 'block');
        if (backgroundLoaded && !backgroundShown) {
            displayColorBackground();
        }
        showLogos();
        fadeContentPanels();
    });
    contentBackground1Morph.start({'opacity': 1});
    
    function showLogos() {
        $$('.logoFade').set('morph', {duration: 600, transition: Fx.Transitions.Quart.easeInOut});
        $$('.logoFade').morph({opacity: [1]});
    }
    
    function fadeContentPanels() {
        fadePanel('zumbaCanister', 'zumbaContent', 500);
        fadePanel('yogaCanister', 'yogaContent', 800);
        fadePanel('doTerraCanister', 'doTerraContent', 1100);

    }
    
    function fadePanel(panelName, contentName, delay){
        primeFade($(panelName));
        var spTween = new Fx.Tween(panelName, {
            duration: 900,
            transition: Fx.Transitions.Quart.easeInOut,
            property: 'opacity'
        });
        spTween.addEvent('complete', function() { 
            if (panelName == 'doTerraCanister') {
//                activateLinks();
            }
        });
        spTween.start.delay(delay, spTween, 1); 
    }
    
    function primeFade(element) {
        element.setStyle('opacity', 0);
        element.setStyle('display', 'block');
    }
    
    function activateLinks() {
        setClickable(true);
        switch(openPanel) {
            case null:
                $('yogaHeader').addEvent('click', function(){
                    if (clickable) {
                        openPanel = 'yoga';
                        panelToSlide('doTerra', 'Right');
                        panelToSlide('zumba', 'Left');
                        maximizePanel();
                    }
                });
                $('zumbaHeader').addEvent('click', function(){
                    if (clickable) {
                        openPanel = 'zumba';
                        panelToSlide('doTerra', 'Right');
                        panelToSlide('yoga', 'Right');
                        maximizePanel();
                    }
                });
                $('doTerraHeader').addEvent('click', function(){
                    if (clickable) {
                        openPanel = 'doTerra';
                        panelToSlide('zumba', 'Left');
                        panelToSlide('yoga', 'Left');
                        maximizePanel();
                    }
                });
                break;
            case 'yoga':
                $('yogaHeader').addEvent('click', function(){
                    if (clickable) {
                        minimizePanel();
                        slideToPanel('zumba');
                        slideToPanel('doTerra');
                    }
                });
                $('zumbaSlider').addEvent('click', function(){
                    if (clickable) {
                        panelToSlide(openPanel, 'Right');
                        slideToMax('zumba');
                    }
                });
                $('doTerraSlider').addEvent('click', function(){
                    if (clickable) {
                        panelToSlide('yoga', 'Left');
                        slideToMax('doTerra');
                    }
                });
                break;

            case 'zumba':
                $('zumbaHeader').addEvent('click', function(){
                    if (clickable) {
                        minimizePanel();
                        slideToPanel('yoga');
                        slideToPanel('doTerra');
                    }
                });
                $('yogaSlider').addEvent('click', function(){
                    if (clickable) {
                        panelToSlide('zumba', 'Left');
                        slideToMax('yoga');
                    }
                });
                $('doTerraSlider').addEvent('click', function(){
                    if (clickable) {
                        panelToSlide('zumba', 'Left');
                        slideSlide('yoga', 'Left');
                        slideToMax('doTerra');
                    }
                });
                break;

            case 'doTerra':
                $('doTerraHeader').addEvent('click', function(){
                    if (clickable) {
                        minimizePanel();
                        slideToPanel('yoga');
                        slideToPanel('zumba');
                    }
                });
                $('zumbaSlider').addEvent('click', function(){
                    if (clickable) {
                        panelToSlide(openPanel, 'Right');
                        slideSlide('yoga', 'Right');
                        slideToMax('zumba');
                    }
                });
                $('yogaSlider').addEvent('click', function(){
                    if (clickable) {
                        panelToSlide(openPanel, 'Right');
                        slideToMax('yoga');
                    }
                });
                break;

        }

    }

    function setClickable(canClick) {
        if (canClick) {
            clickable = true;
            $$('.panelHeader').addClass('clickable');
            $$('.panelSlider').addClass('clickable');
        } else {
            clickable = false;
            $$('.panelHeader').removeEvents('click');
            $$('.panelHeader').removeClass('clickable');
            $$('.panelSlider').removeEvents('click');
            $$('.panelSlider').removeClass('clickable');
        }
    }

    function slideSlide(slideName, direction) {
        var sliderMorph = new Fx.Morph($(slideName + "Slider"), {
            duration: 600,
            transition: Fx.Transitions.Quart.easeInOut
        });
        (function(){
            sliderMorph.start({
                'margin-left': panelLocs[slideName][direction]
            });
        }).delay(700);
    }
   
    function panelToSlide(sectionName, direction) {
        setClickable(false);
        var panelMargin = $(sectionName + 'Wrapper').getCoordinates('content').left;
        var panelMarginTop = $(sectionName + 'Wrapper').getCoordinates('content').top;
        var panelWidth = parseInt($(sectionName + 'Wrapper').getSize().x);
        panelMargin += parseInt(panelWidth/2) - 14;
        var sliderMarginLeft = panelLocs[sectionName].slider;
        if (panelWidth > 400) {
            sliderMarginLeft = panelLocs[sectionName].sliderFull;
        }
        
        var sliderText = new Element("div", {
           "id" : sectionName + 'SliderText' + direction,
           "class" : 'panelSliderText'
        });
        var slider = new Element("div", {
            "id" : sectionName + "Slider",
            "class" : 'panelSlider',
            styles: {
                'margin-left': sliderMarginLeft,
                'margin-top': panelMarginTop
            }
        });
        sliderText.inject(slider);
        slider.inject('content');
        
        // show the slide panel
        $(sectionName + 'Wrapper').set('morph', {duration: 500, transition: Fx.Transitions.Quart.easeInOut});
        $(sectionName + 'Wrapper').morph({
            opacity: [0],
            'margin-left': panelMargin,
            'width': 28
        });
        primeFade($(sectionName + "Slider"));
        var sliderMorph = new Fx.Morph($(sectionName + "Slider"), {
            duration: 600,
            transition: Fx.Transitions.Quart.easeInOut
        });
        sliderMorph.start({opacity:[1]}).chain(function(){
            var sliderMargin = panelLocs[sectionName][direction];
            this.start({'margin-left': sliderMargin});
        });
    }

    function slideToPanel(sectionName) {
        setClickable(false);
        fadeAway(sectionName + "Content", 'standard', sectionName);
        var sliderMorph = new Fx.Morph($(sectionName + "Slider"), {
            duration: 600,
            transition: Fx.Transitions.Quart.easeInOut
        });
        sliderMorph.addEvent('complete', function(el) {
            $(sectionName + 'Wrapper').set('morph', {duration: 500, transition: Fx.Transitions.Quart.easeInOut});
            $(sectionName + 'Wrapper').morph({
                opacity: [1],
                'margin-left': [panelLocs[sectionName].slider,panelLocs[sectionName].panel],
                'width': [28,panelWidth]
            });
            fadeIn(sectionName + "Content");
            fadeAway(sectionName + "Slider");
        });
        (function(){
            sliderMorph.start({
                'margin-left': panelLocs[sectionName].slider
            });
        }).delay(400);
    }

    function slideToMax(sectionName) {
        setClickable(false);
        openPanel = sectionName;
        fadeAway(sectionName + "Content", 'max');
        var sliderMorph = new Fx.Morph($(sectionName + "Slider"), {
            duration: 600,
            transition: Fx.Transitions.Quart.easeInOut
        });
        var sliderMargin = panelLocs[sectionName].Left + (panelWidthOpen/2);
        sliderMorph.addEvent('complete', function(el) {
            $(sectionName + 'Wrapper').set('morph', {duration: 500, transition: Fx.Transitions.Quart.easeInOut});
            $(sectionName + 'Wrapper').morph({
                opacity: [0,1],
                'margin-left': [sliderMargin,panelLocs[sectionName].Left],
                'width': [28, panelWidthOpen]
            });
            fadeAway(sectionName + "Slider");
            openPanel = sectionName;
            (function(){
                activateLinks();
                fadeIn(sectionName + "Content");
            }).delay(500);
        });

        (function(){
            sliderMorph.start({
                'margin-left': sliderMargin
            });
        }).delay(1000);
    }
    
    function maximizePanel() {
        setClickable(false);
        fadeAway(openPanel + "Content", 'max');
        var panelMorph = new Fx.Morph($(openPanel + "Wrapper"), {
            duration: 700,
            transition: Fx.Transitions.Quart.easeInOut
        });
        panelMorph.addEvent('complete', function(el) {
            activateLinks();
            $(openPanel + "Content").addClass('scrollable');
            fadeIn(openPanel + "Content");
        });
        (function(){
            panelMorph.start({
                'width': panelWidthOpen,
                'margin-left': panelLocs[openPanel].Left
            });
        }).delay(600);
    }

    function minimizePanel() {
        setClickable(false);
        targetPanel = openPanel;
        fadeAway(targetPanel + "Content",'standard');
        var panelMorph = new Fx.Morph($(targetPanel + "Wrapper"), {
            duration: 700,
            transition: Fx.Transitions.Quart.easeInOut
        });
        panelMorph.addEvent('complete', function(el) {
            $(targetPanel + "Content").removeClass('scrollable');
            openPanel = null;
            fadeIn(targetPanel + "Content", true);

        });
        panelMorph.start({
            'width': panelWidth,
            'margin-left': panelLocs[targetPanel].panel
        });
    }

    function fadeAway(fadeElement, loadContent, targetPanel) {
        if (targetPanel == undefined) {
            targetPanel = openPanel;
        }
        var fadeTween = new Fx.Tween(fadeElement, {
            duration: 300,
            transition: Fx.Transitions.Quart.easeInOut,
            property: 'opacity'
        });
        if (loadContent !== undefined) {
            fadeTween.addEvent('complete', function() {
                $(targetPanel + 'Content').empty();
                $(targetPanel + 'Content').load(panelContent[targetPanel][loadContent]);
            });
        }
        fadeTween.start(1,0);
    }
    function fadeIn(fadeElement, activate) {
        var fadeTween = new Fx.Tween(fadeElement, {
            duration: 400,
            transition: Fx.Transitions.Quart.easeInOut,
            property: 'opacity'
        });
        if (activate !== undefined) {
            fadeTween.addEvent('complete', function() {
                openPanel = null;
                activateLinks();
            });
        }
        fadeTween.start(0,1);
    }


// Notes that may be handy for other development
    
//    $$('.panelContent, .panelHeaderText').set('morph', {duration: 900, transition: Fx.Transitions.Quart.easeInOut});
//    $$('.panelContent, .panelHeaderText').morph({opacity: [1]});
//    
//
//    var logo1Tween = new Fx.Tween('doterraLogo', {
//        duration: 400,
//        transition: Fx.Transitions.Quart.easeInOut,
//        property: 'opacity'
//    });
//    logo1Tween.addEvent('complete', function() { 
//        $$('.panelContent, .panelHeaderText').set('morph', {duration: 900, transition: Fx.Transitions.Quart.easeInOut});
//        $$('.panelContent, .panelHeaderText').morph({opacity: [1]});
//    });
//    logo1Tween.start(0,1);
//    
//    var logo2Tween = new Fx.Tween('logo', {
//        duration: 400,
//        transition: Fx.Transitions.Quart.easeInOut,
//        property: 'opacity'
//    });
//    logo2Tween.start(0,1);

//    function slideContentPanels() {
//        slidePanel('zumbaCanister', 'zumbaContent', 500);
//        slidePanel('yogaCanister', 'yogaContent', 800);
//        slidePanel('doTerraCanister', 'doTerraContent', 1100);
//
//    }
//    
//    function slidePanel(panelName, contentName, delay){
//        $(panelName).setStyle('margin-top', '-565px');
//        $(panelName).setStyle('display', 'block');
//        var spTween = new Fx.Tween(panelName, {
//            duration: 900,
//            transition: Fx.Transitions.Quart.easeInOut,
//            property: 'margin-top'
//        });
//        spTween.addEvent('complete', function() { 
//            $(contentName).set('morph', {duration: 400, transition: Fx.Transitions.Quart.easeInOut});
//            $(contentName).morph({opacity: [1]});
//            if (panelName == 'doTerraCanister') {
//                activateLinks();
//            }
//        });
//        spTween.start.delay(delay, spTween, 0); 
//    }
    


});