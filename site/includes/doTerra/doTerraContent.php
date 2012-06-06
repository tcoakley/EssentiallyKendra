<!-- store -->
<div class="sectionCanister">
    <div class="sectionTitle">Links</div>
    <div class="sectionBody">
        <div>Official Website</div>
        <div class="indent"><a class="offsiteLink" href="http://www.mydoterra.com/kendraoils" target="_blank">mydoterra.com/kendraoils</a></div>
    </div>
</div>
<!-- /store -->

<!-- CPTG -->
<div class="sectionCanister">
    <div class="sectionTitle">Certified Pure Therapeutic Grade CPTG&trade;</div>
    <div class="sectionBody">
        d&#333;Terra's Certified Pure Therapeutic Grade&trade; (CPTG) oils are guaranteed to be 100% pure
        and free of synthetic compounds or contaminates (including pesticides). They far exceed AFNOR and ISO
        quality standards and are subjected to rigorous mass spectrometry and gas chromatography analysis to ensure
        extract composition and activity.
    </div>
</div>
<!-- /CPTG -->

<!-- latest news -->
<div class="sectionCanister">
    <div class="sectionTitle">Latest d&#333;TERRA Corporate News</div>
    <div class="sectionBody">
        <?php getLatestNews(2); ?>
        
    </div>
</div>
<!-- /latest news -->


<?php

function getLatestNews($numArticles) {
    $xml = null;
    $xml = getRssFeed();
    if (!is_null($xml)) {

        $loc = 0;
        foreach ($xml as $item) {

            $title = utf8ToEntities($item['title']);
            $link = utf8ToEntities($item['link']);
            $date = utf8ToEntities($item['date']);
            $date = formatDate(strtotime($date), '%m.%d.%Y');
            $description = truncString(utf8ToEntities($item['desc']), 90);
            print "<div>{$date}</div>" .
                "<div class='indent'>" .
                "<div><a class='offsiteLink' href='{$link}' target='_blank'>{$title}</a></div>" .
                "<div>{$description}</div>" .
                "</div>";
            $loc++;
            if ($loc >= $numArticles) { break; }
        }
    }
}



