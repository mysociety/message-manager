<div class="mm-messages">
	<h2>
		Available Messages
		<?php if (! empty($allowed_tags)) {echo h("(tags: $allowed_tags)");} ?>
	</h2>
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
			<tr>
				<td><?php echo h($message['Message']['created']); ?></td>
				<td class="status-<?php echo ($message['Status']['name']); ?>"><?php echo h($message['Status']['name']); ?>
					<?php if (! empty($message['Message']['lock_expires'])) {
						echo('*');
						$c_locks++;
					} ?>
				</td>
				<td>
					<?php
						if (!empty($message['Source'])) {
							echo h($message['Source']['name']);
						}
					?>
				</td>
				<td><?php echo h($message['Message']['tag']); ?></td>
				<td><?php echo h($message['Message']['message']); ?></td>
				<td class="actions">
					<?php echo $this->Html->link(__('View'), array('action' => 'view', $message['Message']['id']), null); ?>
				</td>
			</tr>
			<?php foreach ($message['Reply'] as $reply) { ?>
				<tr class="message-reply">
					<td class="message-reply">
						<?= $reply['created']?>
					</td>
					<td class="status-<?php echo ($reply['Status']['name']); ?>"><?php echo h($reply['Status']['name']); ?>
						<?php if (! empty($reply['lock_expires'])) {
							echo('*');
							$c_locks++;
						} ?>
					</td>
					<td class="message-reply">
						<?php
							if (!empty($reply['Source'])) {
								echo h($reply['Source']['name']);
							}
						?>
					</td>
					<td class="message-reply"><?php echo h($reply['tag']); ?></td>
					<td class="message-reply"><?php echo h($reply['message']); ?></td>
					<td class="actions">
						<?php echo $this->Html->link(__('View'), array('action' => 'view', $reply['id']), null); ?>
					</td>
				</tr>
			<?php } ?>
				
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
