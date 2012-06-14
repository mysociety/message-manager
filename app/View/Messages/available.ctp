<div class="mm-messages">
	<h2>Available Messages</h2>
	<table>
	    <tr>
	        <th>created</th>
	        <th>status</th>
	        <th>source</th>
	        <th>tag</th>
	        <th>message</th>
	        <th> </th>
	    </tr>

	    <!-- Here is where we loop through our $posts array, printing out post info -->

	    <?php $c_locks = 0; foreach ($messages as $message): ?>
	    <tr>
	        <td><?php echo $message['Message']['created']; ?></td>
	        <td class="status-<?php echo $message['Status']['name']; ?>"><?php echo $message['Status']['name']; ?>
				<?php if (! empty($message['Message']['lock_expires'])) {
					echo('*');
					$c_locks++;
				} ?>
				</td>
	        <td><?php echo h($message['Source']['name']); ?></td>
	        <td><?php echo h($message['Message']['tag']); ?></td>
	        <td><?php echo h($message['Message']['message']); ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('action' => 'view', $message['Message']['id']), null); ?>
			</td>
	    </tr>
	    <?php endforeach; ?>

	</table>
	<p class="pagination-legend">
		Note: MSISDN (and some other fields) are not included in "available" responses.
	</p>
	<p class="pagination-legend">
	<?php
		if ($c_locks > 0) {
			echo '* record may be locked';
		} ?>
	</p>
</div>