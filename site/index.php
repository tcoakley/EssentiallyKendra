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
    <link rel="stylesheet" type="text/css" href="/css/message.css" />
    <!-- /css -->
    
    <!-- js -->
    <script type="text/javascript" src="/js/mootools-core-1.4.5-full-compat-yc.js"></script>
    <script type="text/javascript" src="/js/mootools-more-1.4.0.1.js"></script>
    <script type="text/javascript" src="/js/message.js"></script>
    <script type="text/javascript" src="/js/layout.js"></script>

    <!-- /js -->
</head>
<body>
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
                    <div id="headerMenu" class="logoFade">
                        <a href='javascript:;' id='mailingList'>Mailing List</a>
                        <!-- Place this tag where you want the +1 button to render -->
                        <div class='floatRight'><g:plusone></g:plusone></div>
                        <!-- Place this render call where appropriate -->
                        <div class='floatRight'>
                            <iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.essentiallykendra.com&amp;send=false&amp;layout=button_count&amp;width=100&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21&amp;appId=111394618950399" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>
                        </div>

                    </div>
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

    <!-- Image Preload -->
    <div id='imagePreloadDiv'>
        <img src='/images/layout/contentBackground1.jpg' />
        <img src='/images/layout/contentPanelBackground.png' />
        <img src='/images/layout/doTerraHeaderBackground.png' />
        <img src='/images/layout/doterraIPCLogo.png' />
        <img src='/images/layout/headerBackground.png' />
        <img src='/images/layout/logo.png' />
        <img src='/images/layout/yogaHeaderBackground.png' />
        <img src='/images/layout/zumbaHeaderBackground.png' />
        <img src='/images/layout/text/doTerraEssentials.png' />
        <img src='/images/layout/text/yogaSpirit.png' />
        <img src='/images/layout/text/zumbaSoul.png' />
    </div>
    <!-- /Image Preload -->

    <!-- Google Code -->
    <script type="text/javascript">

        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-30161137-1']);
        _gaq.push(['_trackPageview']);

        (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();

        (function() {
            var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
            po.src = 'https://apis.google.com/js/plusone.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
        })();
    </script>
    <!-- /Google Code -->

</body>
</html>
