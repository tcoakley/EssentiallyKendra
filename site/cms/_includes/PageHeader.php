<!-- Header -->
<div id="Header">
    <div class="Name">Park Auto Group</div>
    <div class="Location">
        <?php
            print($PageTitle);
            if (strlen($PageFunction) > 0) {
                print(" - " . $PageFunction);
            }
        ?>
    </div>
    <div class="clear"></div>
</div>
<!-- /Header -->