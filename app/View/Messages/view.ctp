<?php echo $this->Html->script('jquery-1.7.2.min', false); ?>
<div class="mm-messages view">
	<h2>
		<?php
			if ($message['Message']['is_outbound']) {
				echo('Outbound');
			}
		?>
		Message
	</h2>
	<dl style="margin-bottom:3em">
		<?php
			if ($message['Message']['to_address']) { ?>
				<dt>
					To
				</dt>
				<dd>
					<?php echo h($message['Message']['to_address'])?>
					&nbsp;
				</dd>
		<?php } ?>

		<?php if ($message['Message']['parent_id']) { ?>
			<dt>
				In reply to 
			</dt>
			<dd>
				<a href="<?php echo $this->Html->url(array('action' => 'view', $message['Message']['parent_id'])); ?>">
					<span class="message-sender">
						<?php echo h($message['Parent']['from_address']); ?>
					</span>
					<?php echo h($message['Parent']['message']); ?>
				</a>&nbsp;
			</dd>
		<?php } ?> 
		<dt>
		    <a href="#" class="show-raw-text">Show raw text</a>
		</dt>
		<dd class="raw-text">
			<?php echo h($message['Message']['message_received'])?> &nbsp;
		</dd>
		<dt>
			Tag
		</dt>
		<dd>
			<?php echo h($message['Message']['tag'])?>
			&nbsp;
		</dd>
		<dt>
			Message
		</dt>
		<dd>
			<p class="message-text">
				<span class="message-sender">
					<?php echo($message['Message']['from_address'])?>
				</span>
				<?php echo h($message['Message']['message'])?>
			</p>
		</dd>
		<?php $this->MessageUtils->message_entry_in_thread($children, 0); ?>
		<dt>
			From
		</dt>
		<dd>
			<?php echo h($message['Message']['from_address'])?>
			&nbsp;
		</dd>
		<dt>Status</dt>
		<dd class="status-<?php echo h($message['Status']['name']) ?>">
			<strong><?php echo h($message['Status']['name']) ?></strong>
			&nbsp;
		</dd>
		<?php if ($message['Message']['status']==Status::$STATUS_HIDDEN) { ?>
			<dt>Hide reason</dt>
			<dd <?php if (! empty($message['Message']['hide_reason'])) { echo 'class="status-hidden"'; }?>>
				 <?php echo h($message['Message']['hide_reason']) ?>
				&nbsp;
			</dd>
		<?php } ?>
		<dt>Lock</dt>
		<dd class="status-<?php echo($is_locked?'locked':'unlocked'); ?>">
			<?php if ($message['Message']['lock_expires']) {
				echo $this->MessageUtils->pretty_lock_duration($seconds_until_lock_expiry);
				echo " (" . h($message['Message']['lock_expires']) . ")<br/>";
				echo 'owner: ' . h($message['Lockkeeper']['username']);
			} else {
				echo 'none';
			} ?>
			&nbsp;
		</dd>
		<?php foreach($message['Action'] as $action) { 
			if ($action['ActionType']['name']=='note') {
				echo('<dt><a href="#action_' . h($action['id']) . '">Note</a></dt>');
				echo('<dd><p class="note-text">' . h($action['note']) . '</p></dd>');
			}
		} ?>
		<dt>
			FMS ID
		</dt>
		<dd>
			<?php  if ($message['Message']['fms_id']) { 
				$fms_url = $this->MessageUtils->fms_report_url($message['Message']['fms_id']);
				echo $this->Html->link($fms_url, $fms_url, array('class'=>'no-decoration'));
			} ?>
			<?php if (!empty($message['Message']['assigned'])) {
				echo "<br/>assigned: " . h($message['Message']['assigned']);
			} ?>
			&nbsp;
		</dd>
		<dt>Received</dt>
		<dd>
			 <?php echo h($message['Message']['received']) ?>
			&nbsp;
		</dd>
		<dt>Created</dt>
		<dd>
			 <?php echo h($message['Message']['created']) ?>
			&nbsp;
		</dd>
		<dt>Modified</dt>
		<dd>
			 <?php
				if ($message['Message']['modified'] != $message['Message']['created']) {
					echo h($message['Message']['modified']);
				} ?>
			&nbsp;
		</dd>
		<dt>Replied</dt>
		<dd>
			 <?php echo h($message['Message']['replied'])?>
			&nbsp;
		</dd>
		<dt>
			Source
		</dt>
		<dd>
			<!-- join ',', source, external_id -->
			<?php echo h($message['Source']['name'])?>
			&nbsp;
		</dd>
		<dt>
			External ID
		</dt>
		<dd>
			<?php echo h($message['Message']['external_id'])?>
			&nbsp;
		</dd>
		<dt>Sender token</dt>
		<dd>
			<?php echo h($message['Message']['sender_token'])?>
			&nbsp;
		</dd>
		<dt>
			Send errors
		</dt>
		<dd>
			<?php if ($has_send_failures) { ?>
				<table>
					<tr>
						<td>
							Failed attempts
						</td>
						<td>
							<?php echo h($message['Message']['send_fail_count'])?>
						</td>
					</tr>
					<tr>
						<td>
							Fail reason
						</td>
						<td>
							<?php echo h($message['Message']['send_fail_reason'])?>
						</td>
					</tr>
					<tr>
						<td>
							Last attempted
						</td>
						<td>
							<?php echo h($message['Message']['send_failed_at'])?>
						</td>
					</tr>
				</table>
			<?php } else { ?>
				<em>No failed attempts</em>
			<?php }	 ?>
		</dd>
	</dl>
	
	<!-- add note -->
	<?php echo $this->Form->create('Action', array('controller'=>'Actions', 'action'=>'add'));?>
	<?php echo $this->Form->hidden('message_id', array('value'=>$message['Message']['id']));?>
	<!-- user_id inferred by login -->
	<?php echo $this->Form->hidden('type_id', array('value'=>ActionType::$ACTION_NOTE)); ?>
	<?php echo $this->Form->input('note', array('type' => 'textarea', 'label' => 'Add a note (only seen on Message Manager)', 'rows'=>2));?>
	<?php echo $this->Form->end(__('Add note'));?>
	

	<h3>History/activity</h3>
	<table>
		<tr>
			<th>Timestamp</th>
			<th>User</th>
			<th>Action</th>
			<th> </th>
		</tr>
		<tr>
			<td><?php echo h($message['Message']['created']) ?></td>
			<td>&nbsp;</td>
			<td>added to MessageManager</td>
			<td>&nbsp;</td>
	<?php foreach($message['Action'] as $action){ ?>
		<tr>
			<td><a name="action_<?php echo h($action['id']);?>"></a><?php echo h($action['created']); ?></td>
			<td>
				<?php if ($action['user_id']) {
					echo $this->Html->link($action['User']['username'],
						array('controller' => 'users', 'action' => 'view', $action['user_id']));
				} else { ?>
					&mdash;
				<?php } ?>
			</td>
			<td>
				<?php {
					echo h($action['ActionType']['description']);
					if ($action['note']) { 
						?> &mdash; <?php
						echo h($action['note']);
					}
				} ?>
			</td>
			<!-- TODO item_id links to whatever item type is relevant to this action -->
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'Actions', 'action' => 'view', $action['id']), null); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'Actions', 'action' => 'delete', $action['id']), null, __('Are you sure you want to delete # %s?', $action['id'])); ?>
			</td>
	<?php } ?>
		</tr>
	</table>
	<p class="footnote">Note: locks expire <?php echo(Configure::read('lock_expiry_seconds')) ?> seconds after being granted.</p>
</div>
<div class="actions">
	<ul>
		<?php if ($message['Message']['lock_expires']) { ?>
			<li>
				<?php echo $this->Form->postLink($is_locked? __('Unlock') : __('Purge lock'), 
					array('action' => 'unlock', $message['Message']['id']), null, 
					__('Are you sure you want to release the lock on this message?',
					$message['Message']['id'])); ?>
			</li>
		<?php } else { ?>
			<li>
				<?php echo $this->Form->postLink(__('Lock'), 
					array('action' => 'lock', $message['Message']['id']), null, 
						__('Are you sure you want to claim the lock on this message?',
						$message['Message']['id'])); ?> 
			</li>
			<li>
				<?php echo $this->Form->postLink(__('Exclusive lock'), 
					array('action' => 'lock_unique', $message['Message']['id']), null, 
						__('Are you sure you want to claim the lock on this message and release the lock on any others held by the same user?',
						$message['Message']['id'])); ?> </li>
		<?php } ?>
		<?php if ($message['Message']['status']==Status::$STATUS_HIDDEN) { ?>
			<li>
				<?php echo $this->Form->postLink(__('Reveal/un-hide'), 
					array('action' => 'unhide', $message['Message']['id']), null, 
						__('Are you sure you want to reveal this currently hidden message?',
						$message['Message']['id'])); ?> 
			</li>
		<?php } else { ?>
			<li>
				<?php echo $this->Html->link(__('Hide'), array('action' => 'hide', $message['Message']['id']));?>
			</li>
		<?php } ?>
		<?php if ($message['Message']['fms_id']) { ?>
			<li>
				<?php echo $this->Form->postLink(__('Unassign FMS'), 
					array('action' => 'unassign_fms_id', $message['Message']['id']), null, 
						__('Are you sure you want to unassign this message from FMS report %s?',
						$message['Message']['fms_id'])); ?>
			</li>
		<?php } else { ?>
			<li id='assign-fms-id-action'>
				<?php 
					echo $this->Form->create("Message", array('action'=>'assign_fms_id/'.$message['Message']['id']));
					echo $this->Form->submit(__('Assign FMS'));
					echo $this->Form->input('fms_id', array('label'=>'FMS ID', 'type'=>'text', 'name'=>'fms_id', 'id'=>'fms_id'));
					echo $this->Form->end(); ?>
			</li>
		<?php } ?>
		<li>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $message['Message']['id']));?>
		</li>
		<?php if (! $message['Message']['is_outbound']) { ?>
			<li>
				<?php echo $this->Html->link(__('Reply'),
					array('action' => 'reply', $message['Message']['id'])); ?>
			</li>
		<?php } ?>
		<?php if ($message['Message']['parent_id']) { ?>
			<li>
				<?php echo $this->Html->link(__('Not a reply'),
					array('action' => 'mark_as_not_a_reply', $message['Message']['id'])); ?>
			</li>
		<?php } ?>
		<li>
			<?php echo $this->Form->postLink(__('Delete'),
				array('action' => 'delete', $message['Message']['id']), null, 
					__('Are you sure you want to delete this message?')); ?>
		</li>
		<li> 
			&nbsp; <!-- separator: this record vs. all records -->
		</li>
		<li>
			<?php echo $this->Form->postLink(__('Purge all locks'), array('action' => 'purge_locks')); ?> 
		</li>
		<li>
			<?php echo $this->Html->link(__('List all mesages'), array('action' => 'index'));?>
		</li>
	</ul>
</div>
<script>
$(document).ready(function() {
    $('.raw-text').hide();
    $('a.show-raw-text').on('click', function(e){
       e.preventDefault();
       $('.raw-text').slideDown();
       $(this).replaceWith('Raw text');
    });
});
</script>
