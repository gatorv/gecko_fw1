<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
?>
<select name="<?php echo $name; ?>" id="select_<?php echo $rawName; ?>">
<?php if( $emptyLabel ) { ?>
	<option><?php echo $emptyLabel; ?></option>
<?php } ?>
<?php foreach( $data as $value => $label ) { ?>
	<?php if( $selected == $value ) { ?>
	<option value="<?php echo $value; ?>" selected="selected"><?php echo $label; ?></option>
	<?php } else { ?>
	<option value="<?php echo $value; ?>"><?php echo $label; ?></option>
	<?php } ?>
<?php } ?>
</select>
<span id="loading_<?php echo $rawName; ?>" style="display: none;">
	<img src="<?php echo $spinnerDir; ?>" border="0" alt="Loading..." /></span>