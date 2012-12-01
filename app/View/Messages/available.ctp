<div class="mm-messages">
	<h2>
		Available Messages (applying <?php echo h(AuthComponent::user('username')); ?>'s tags: 
		<?php echo $this->MessageUtils->pretty_tag_list_html($allowed_tags); ?>)
	</h2>
	<p>
		Specifically, these are the  messages that would be sent in response to the
		<tt>messages/available</tt> API call (e.g., in a client) to user <?php echo h(AuthComponent::user('username')); ?>.
	</p>

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
