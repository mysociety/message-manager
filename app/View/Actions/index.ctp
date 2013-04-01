<!-- unusual admin view: normal users only see actions in context of messages -->
<h2><?php echo($title); ?></h2>
<p><strong>
	<?php if (count($actions)==0) { 
 		echo(__("No actions found."));
	} else {
		echo(__("Actions found: %s", count($actions)));
	} ?>
</strong></p>
<div id="mini-search">
	<?php echo $this->Form->create('Action', array('type' => 'get'));?>
		<div>
			<?php
				echo $this->Form->input('search_term', 
					array('name' => 'q', 'type' => 'text', 'escape' => false, 'label' => "Action text contains", 'value' => htmlspecialchars($search_term)));
			?>
		</div>
		<?php echo $this->Form->submit(__('Search')); ?>
	<?php echo $this->Form->end(); ?>
</div>

<div class="mm-action-choices">
	Types available:
	<?php
		$q = empty($search_term)? "": ("?q=" . htmlspecialchars($search_term));
		echo $this->Html->link(__('all'), "index$q", 
			array('title' => 'activity of all types', 'style' => 'border-width:2px'));
		foreach ($action_types as $name => $desc) {
			echo $this->Html->link($name, "index/$name$q", array('title' => $desc, 'class' => ($name == $type? 'active':'not-active')));
		}
	?>
</div>
<table>
	<tr>
		<th><?php echo $this->Paginator->sort('created');?></th>
	    <th><?php echo $this->Paginator->sort('type');?></th>
	    <th><?php echo $this->Paginator->sort('user');?></th>
	    <th><?php echo $this->Paginator->sort('Message');?></th>
	    <th><?php echo $this->Paginator->sort('item_id');?></th>
	    <th><?php echo $this->Paginator->sort('note');?></th>
		<th></th>
    </tr>

    <?php foreach ($actions as $action): ?>
    <tr>
        <td><?php echo h($action['Action']['created']); ?></td>
        <td><?php echo h($action['ActionType']['name']); ?></td>
        <td>
            <?php
                if ($action['Action']['user_id']) {
                    echo $this->Html->link($action['User']['username'],
                        array('controller' => 'users', 'action' => 'view', $action['Action']['user_id']));
                } else { ?>
                    &mdash;
                <?php }
            ?>
        </td>
        <td>
            <?php
                if ($action['Action']['message_id']) {
                    echo $this->Html->link($action['Message']['from_address'],
                        array('controller' => 'messages', 'action' => 'view', $action['Action']['message_id']));
                } else { ?>
                    &mdash;
                <?php }
            ?>
        </td>
        <td><?php echo h($action['Action']['item_id']); ?></td>
        <td><?php echo h($action['Action']['note']); ?></td>
        <td class="actions">
            <?php echo $this->Html->link(__('View'), array('action' => 'view', $action['Action']['id']), null); ?>
            <?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $action['Action']['id']), null, __('Are you sure you want to delete # %s?', $action['Action']['id'])); ?>
        </td>
    </tr>
    <?php endforeach; ?>

</table>
<p class="pagination-legend">
<?php
	echo $this->Paginator->counter(array(
		'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
?>
</p>
<div class="paging">
<?php
	echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
	echo $this->Paginator->numbers(array('separator' => ''));
	echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
?>
</div>
