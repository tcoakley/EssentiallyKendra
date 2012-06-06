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
    <script type="text/javascript" src="/js/panels.js"></script>


    <!-- /js -->
</head>
<body>
    <script type='text/javascript'>

        window.addEvent('load', function() {
            var myYogaPanel = new yogaPanel();

            myYogaPanel.addEvent('started', function() {
                console.log('damn I am good');
            });
            myYogaPanel.addEvent('complete', function() {
                console.log('I am a pony');
            });

//            myYogaPanel.transition('slider', {delay: 2000});
            myYogaPanel.show( 2);
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
