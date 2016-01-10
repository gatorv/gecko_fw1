<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
?>
<select name="<?php echo $name; ?>" <?php echo $onchange; ?>>
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