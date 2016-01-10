<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

if(!$JSIncluded) {
	if( $inRouter ) {
		Gecko_Template::registerLibrary( $popcalDir . "/popcalendar.js" );
	} else { ?>
		<script src="<?php echo $popcalDir; ?>/popcalendar.js" type="text/javascript"></script>
	<?php } ?>
<?php } ?>
<input type="text" name="<?php echo $name; ?>" id="fields:<?php echo $rawName; ?>" value="<?php echo $value; ?>" size="<?php echo $size; ?>" maxlength="<?php echo $size; ?>" />
<img src="<?php echo $popcalDir; ?>/calendar.jpg" id="datepopup_<?php echo $rawName; ?>" alt="Select Date" style="cursor: pointer;" onclick="showCalendar( this, document.getElementById('fields:<?php echo $rawName; ?>'), '<?php echo $format; ?>', '<?php echo $lang; ?>', '<?php echo $past; ?>', -1, -1 <?php if(isset($fxFunction) && !empty($fxFunction) ) { echo ", " . $fxFunction; } ?> );" />