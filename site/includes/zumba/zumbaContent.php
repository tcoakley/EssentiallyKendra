<!-- schedule -->
<div class="sectionCanister">
    <div class="sectionTitle">Schedule: <?php print formatDate(time(), '%A %B %d'); ?></div>
    <div class="sectionBody">
        <?php
            if (file_exists('includes/zumba/' . $currentDateFile)) {
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
       
        <div>Foundation Academy</div>
        <div class="indent">
            <div>1050 Wyandotte Ave</div>
            <div>Mansfield, OH</div>
<!--            <div>Off Trimble Rd (old Roseland School)</div>-->
        </div>
        
        <div class="spacer"></div>
        
        <div>TWE Church</div>
        <div class="indent">
            <div>374 Willowood Dr E</div>
            <div>Ontario, OH</div>
<!--            <div>Across from El Campestre on W. 4th St</div>-->
        </div>
        
        <div class="spacer"></div>
        
        <div>Mansfield YMCA</div>
        <div class="indent">
            <div>750 Scholl Road</div>
            <div>Mansfield, OH 44907</div>
<!--            <div>419.522.3511</div>-->
        </div>

        <div class="spacer"></div>

        <div>Burton Park</div>
        <div class="indent">
            <div>Sunset BLVD</div>
            <div>Mansfield, OH 44907</div>
            <!--            <div>419.522.3511</div>-->
        </div>
        
    </div>
</div>
<!-- /locations -->

<!-- Logo and Links -->
<!--<div class="sectionCanister" id='zumbaLogoArea'>-->
<!--    <div class="sectionTitle">Links</div>-->
<!--    <div class="center">-->
<!--        <a href="https://www.zumba.com/en-US/profiles/55573/" target="_blank">-->
<!--            <img src="/images/zumbaLogo.png" class="zumbaLogo" />-->
<!--        </a>-->
<!--        <div class='clear'></div>-->
<!--        <a href="https://www.zumba.com/en-US/profiles/55573/" target="_blank">My Zumba Profile Page</a>-->
<!--    </div>-->
<!--    <div class='clear'></div>-->
<!--</div>-->
<!-- /Logo and Links -->
