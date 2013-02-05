<div class="mm-page">

    <ul class="mm-help">
        <li><?php echo $this->Html->link(__('Contents'), array('action' => 'help')); ?></li>
    </ul>

	<h2>
        Message statuses
    </h2>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut 
        labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco 
        laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor.
    </p>
    <table style="width:auto;" class="mm-messages">
		<tr>
			<td class="status-available">available</td>
			<td>
				Messages that have not yet been assigned to an FMS problem report are available. 
				These are the only messages that are provided over the JSON API and the only
				messages that an FMS user can lock. A lock is required before an FMS ID can be
				assigned.
			</td>
		</tr>
		<tr>
			<td class="status-assigned">assigned</td>
			<td>
				Messages that have been assigned to an FMS problem report will also have
				an FMS ID. They are not visible over the JSON API.
			</td>
		</tr>
		<tr>
			<td class="status-hidden">hidden</td>
			<td>
				A manager or administrator can mark a message as <em>hidden</em> in which case
				it is no longer shown.
			</td>
		</tr>
	</table>
	
</div>    






