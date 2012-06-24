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
        myZumbaCanister.show({'delay': 500});
        myYogaCanister.show({'delay': 800});
        myDoTerraCanister.show({'delay': 1100});

    }
    
});