<div class="mm-page">

    <ul class="mm-help">
        <li><?php echo $this->Html->link(__('Contents'), array('action' => 'help')); ?></li>
    </ul>

	<h2>
        Message statuses
    </h2>
    <p>
        In general, FMS shows the messages that are "available" for assigning an FMS ID,
        together with any replies (this via the <code>available</code> API call).
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
				an FMS ID.
			</td>
		</tr>
		<tr>
			<td class="status-hidden">hidden</td>
			<td>
				A manager or administrator can mark a message as <em>hidden</em> in which case
				it is no longer shown.
			</td>
		</tr>
		<tr>
			<td class="status-pending">pending</td>
			<td>
				A reply that has just been sent from the message manager, but has not yet actually
				been sent out of the SMS gateway, is pending.
			</td>
		</tr>
        <tr>
            <td class="status-sent_pending">sent_pending</td>
            <td>
                sent_pending
            </td>
        </tr>
        <tr>
            <td class="status-sent">sent</td>
            <td>
                sent
            </td>
        </tr>
        <tr>
            <td class="status-sent_ok">sent_ok</td>
            <td>
                sent_ok
            </td>
        </tr>
        <tr>
            <td class="status-sent_unknown">sent_unknown</td>
            <td>
                sent_unknown
            </td>
        </tr>
        <tr>
            <td class="status-sent_fail">sent_fail</td>
            <td>
                sent_fail
            </td>
        </tr>
        <tr>
            <td class="status-error">error</td>
            <td>
                error
            </td>
        </tr>
        <tr>
            <td class="status-locked">locked</td>
            <td>
                locked
            </td>
        </tr>
        <tr>
            <td class="status-unlocked">unlocked</td>
            <td>
                unlocked
            </td>
        </tr>
        <tr>
            <td class="status-unknown">unknown</td>
            <td>
                unknown
            </td>
        </tr>
	</table>
	
</div>    






