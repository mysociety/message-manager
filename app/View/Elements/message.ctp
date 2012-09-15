<tr class="reply-<?php echo $depth; ?>">
	<td><?php echo h($message['Message']['created']); ?></td>
	<td class="status-<?php echo ($message['Status']['name']); ?>"><?php echo h($message['Status']['name']); ?>
		<?php if (! empty($message['Message']['lock_expires'])) {
			echo('*');
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
<?php foreach ($message['children'] as $child): ?>
	<?php echo $this->element('message', array(
		"message" => $child,
		"depth" => $depth+1
	)); ?>
<?php endforeach; ?>
