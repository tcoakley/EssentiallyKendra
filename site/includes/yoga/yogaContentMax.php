<div class="panelMax" id="yogaMax">
    <div class='panelMaxRightSmall'>
        <div class="sectionCanister">
            <div class='sectionTitle'>SCHEDULE:</div>
            <?php foreach($twoWeeksArray as $target) { ?>
            <div class='sectionDate'><?php print formatDate($target['date'], '%A %B %d'); ?></div>
            <div class="sectionBody">
                <?php
                if (file_exists('includes/yoga/' . $target['dateFile'])) {
                    include($target['dateFile']);
                } else {
                    include($target['dayFile']);
                }
                ?>
            </div>
            <div class="spacer"></div>
            <?php } ?>
        </div>
    </div>
    <div class='panelMaxMain'>
        This is an assload of content to see if it properly lays out of if I will need to set a max width
        in order to assure the layout remains clean.
    </div>
    <div class='clear'></div>
</div>
