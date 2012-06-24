<?php require_once('includes/constants.php'); ?>
<?php require_once('includes/functions.php'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html>
<head>
    <title>Essentially Kendra</title>
    
    <meta name="description" content="Website for Kendra Coakley-Pense." />
    <meta name="keywords" content="Zumba, Yoga, doTERRA, Essential, Oils, aromatherapy, wellness" />
    
    <link rel="shortcut icon" type="image/x-icon" href="/images/layout/favicon.ico">
    
    <!-- css -->
    <link rel="stylesheet" type="text/css" href="/css/layout.css" />
    <!-- /css -->
    
    <!-- js -->
    <script type="text/javascript" src="/js/mootools-core-1.4.5-full-compat-yc.js"></script>
    <script type="text/javascript" src="/js/mootools-more-1.4.0.1.js"></script>
    <script type="text/javascript" src="/js/stringFunctions.js"></script>
    <script type="text/javascript" src="/js/panels.js"></script>
    <script type="text/javascript" src="/js/panelSets.js"></script>
    <!-- /js -->

</head>
<body>
    <script type='text/javascript'>


        window.addEvent('load', function() {
            var myYogaPanel = new yogaPanel();
            var myZumbaPanel = new zumbaPanel();
            var myDoTerraPanel = new doTerraPanel();

            myYogaPanel.addEvent('showComplete', function() {
                myYogaPanel.transition('slider', {'direction' : 'Left', 'delay' : 2000});
                myZumbaPanel.transition('slider', {'direction' : 'Left', 'delay' : 2000});
                myDoTerraPanel.transition('max', {'delay': 3000});
            });
            myYogaPanel.addEvent('transitionToSliderComplete', secondTransition);
            myZumbaPanel.addEvent('transitionToSliderComplete', thirdTransition);

            function thirdTransition() {
                myZumbaPanel.transition('standard', {
                    'delay' : 500
                });

            }

            function secondTransition() {
                myYogaPanel.transition('standard', {
                    'delay' : 500
                });
                myDoTerraPanel.transition('standard', {'delay': 500});

                myYogaPanel.removeEvent('transitionToSliderComplete', secondTransition);
            }

//            myYogaPanel.transition('slider', {delay: 2000});

            myYogaPanel.show();
            myZumbaPanel.show({'delay':500});
            myDoTerraPanel.show({'delay':1000});


//             =======================================================================================================
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
                displayColorBackground();
            });
            contentBackground1Morph.start({'opacity': 1});



        });
    </script>

    <div id='bodyWrapper'>
        <!-- contentWrapper -->
        <div id='contentWrapper'>
            <!-- Backgrounds -->
            <div id="contentBackground1"></div>
            <div id="contentBackground2"></div>

            <!-- /Backgrounds -->

            <!-- contentCanister -->
            <div id="contentCanister">
                <!-- Header -->
                <div id="header">
                    <div id="doterraLogo" class="logoFade"></div>
                    <div id="logo" class="logoFade"></div>
                </div>
                <!-- /Header -->

                <!-- Content -->
                <div id="content">

                    <?php require_once('includes/zumba/zumbaLayout.php'); ?>

                    <?php require_once('includes/yoga/yogaLayout.php'); ?>

                    <?php require_once('includes/doTerra/doTerraLayout.php'); ?>

                    <div class="clear"></div>
                    <!-- footer -->
                    <div id="footer">
                        <div id="copyright">Copyright &copy; 2012 all rights reserved.</div>
                        <div id="credits"><a href="http://www.essentiallytom.com">Website by Tom</a></div>
                    </div>
                    <!-- /footer -->
                </div>
                <!-- /Content -->
                <div class="clear"></div>

            </div>
            <!-- /contentCanister -->
        </div>
        <!-- /contentWrapper -->
    </div>
    <!-- /bodyWrapper -->


</body>
</html>
