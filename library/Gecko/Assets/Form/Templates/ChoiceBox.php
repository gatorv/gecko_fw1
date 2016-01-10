<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
?>
<table border="0" cellspacing="0" cellpadding="0"'<?php echo $class; ?>>
	<tr>
	<?php $counter = 0; ?>
	<?php foreach($options as $name => $option) { ?>
		<td><?php echo $name; ?></td>
		<td><?php echo $option; ?></td>
		<?php if( ($counter % $columns ) == 0) { ?></tr><tr><?php } ?>
	<?php $counter++; } ?>
	</tr>
</table>