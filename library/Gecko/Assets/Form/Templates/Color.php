<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

if(!$JSIncluded) {
	if( $inRouter ) {
		Gecko_Template::registerLibrary( $colorDir . "/colorpicker.js" );
	} else { ?>
		<script src="<?php echo $colorDir; ?>/colorpicker.js" type="text/javascript"></script>
	<?php } ?>
<?php } ?>
<?php echo $html; ?>
<input type="text" id="<?php echo $rawName; ?>" name="<?php echo $name; ?>" size="9" value="" />&nbsp;
<input type="text" id="<?php echo $watchId; ?>" size="1" value="" />&nbsp;
<input type="button" onclick="showColorGrid2('<?php echo $rawName; ?>','<?php echo $watchId; ?>');" value="..." />