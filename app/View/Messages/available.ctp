<div class="mm-messages">
	<h2>
		Available Messages
		<?php if (! empty($pretty_allowed_tags)) {echo "(tags: $pretty_allowed_tags)";} ?>
	</h2>
	<div class="actions inline-buttons">
		<?php 	
	echo $this->Html->link(__('All messages'), array('controller' => 'Messages', 'action' => 'index'));
	echo "&nbsp;";
	echo $this->Html->link(__('Available messages'), array('controller' => 'Messages', 'action' => 'available'));
		?>
	</div>
	<table>
		<tr>
			<th>Created</th>
			<th>Status</th>
			<th>Source</th>
			<th>Tag</th>
			<th>Message</th>
			<th> </th>
		</tr>
		<?php $c_locks = 0; foreach ($messages as $message): ?>
			<?php echo $this->element('message', array(
				"message" => $message,
				"depth" => 0,
			)); ?>
		<?php endforeach; ?>
	</table>
	<p class="pagination-legend">
		Note: from_address (and some other fields) are not included in "available" responses.
	</p>
	<p class="pagination-legend">
	<?php
		if ($c_locks > 0) {
			echo '* record may be locked';
		} ?>
	</p>
</div>
