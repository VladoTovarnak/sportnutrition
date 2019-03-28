<?php header("Content-type: text/xml");?>
<?php
echo '<?xml version="1.0" encoding="utf-8"?>' ?>
<SHOP xmlns="http://www.zbozi.cz/ns/offer/1.0" >
    <?php
    // Prevent nbs;
    $feed = preg_replace("/\xEF\xBB\xBF/", "", $content_for_layout);
    // Prevent long unsupported dashes
    $feed = str_replace("–", "-", $feed);
    echo $feed;
    ?>
</SHOP>