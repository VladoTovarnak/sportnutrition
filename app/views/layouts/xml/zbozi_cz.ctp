<?php header("Content-type: text/xml");?>
<?php
echo '<?xml version="1.0" encoding="utf-8"?>' ?>
<SHOP xmlns="http://www.zbozi.cz/ns/offer/1.0" >
    <?php echo preg_replace("/\xEF\xBB\xBF/", "", $content_for_layout); ?>
</SHOP>