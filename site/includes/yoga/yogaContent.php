<!-- schedule -->
<div class="sectionCanister">
    <div class="sectionTitle">Schedule: <?php print formatDate(time(), '%A %B %d'); ?></div>
    <div class="sectionBody">
        <?php
            if (file_exists('includes/yoga/' . $currentDateFile)) {
                require_once($currentDateFile);
            } else {
                require_once($currentDayFile);
            }
        ?>
    </div>
</div>
<!-- /schedule -->

<!-- locations -->
<div class="sectionCanister">
    <div class="sectionTitle">Locations</div>
    <div class="sectionBody">
        
        <div>Mansfield YMCA</div>
        <div class="indent">
            <div>750 Scholl Road</div>
            <div>Mansfield, OH 44907</div>
<!--            <div>419.522.3511</div>-->
        </div>
        
    </div>
</div>
<!-- /locations -->

<!-- links -->
<div class="sectionCanister">
    <div class="sectionTitle">Links</div>
    <div class="sectionBody">
        <div>
            <a href="http://www.essentialsforyoga.com/essential-oils/" target="_blank">d&#333;Terra Essential Oils for Yoga</a>
        </div>
        <div>
            <a href="http://www.yogafit.com/about.shtml" target="_blank">Yoga for Every Body</a>
        </div>

    </div>
</div>
<!-- /links -->

<div class='sectionCanister'>
    <div class="sectionTitle">My Credentials</div>
    <div>
        200 RYT YogaFit-Inspired
    </div>
    <br/>
    <div class='center'><img src='/images/om.png' width="120" height="120" /></div>

</div>

